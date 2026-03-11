<?php
namespace EtatGeneve\DataContentBundle\Service;
use App\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;
use Exception;
use App\Security\User;
use EtatGeneve\DataContentBundle\DataContentBundle;
use EtatGeneve\DataContentBundle\Service\TokenAuthenticator;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @phpstan-import-type TokenAuthenticatorConfig from DataContentBundle
 */




class Datacontent
{


    protected string $baseId;
/**
 * Undocumented function
 *
 * @param HttpClientInterface $httpClient
 * @param LoggerInterface $logger
 * @param Security $security
 * @param TokenAuthenticator $tokenAuthenticator
 * @param TokenAuthenticatorConfig $config
 */
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private Security $security,
        private TokenAuthenticator $tokenAuthenticator,
        private array $config
    ) {
        $this->baseId = $this->config["baseId"];
    }


    protected function getUserIdentifier(): ?string
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            return $user->getUserIdentifier();
        }

        return null;
    }


    /**
     * @param string $type // 'GET', 'PUT', 'DELETE', ....
     * @param string $command
     * @param null|array|string|resource|\Traversable|\Closure $body
     * @param array $headers
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     */
    public function command($type, $command, $body = null, $headers = [], $addtionalTimeout = 0)
    {
        $this->logger->debug(
            'Datacontent : execute command ',
            [
                'type' => $type,
            'command' => $command,
            'body' => $body,
            'headers' => $headers,
            'addtionalTimeout' => $addtionalTimeout
            ]);
        $url = $this->config['restUrl'].$command;
        $headers['X-Application-ID'] = $this->config['applicationId'];
        $headers['X-Tenant-ID'] = 'admin';
        $username = $this->getUserIdentifier();
        if ($username) {
            $headers['connectedAs'] = $username;
        }

        $options = [
            'headers' => $headers,
            'verify_host' => $this->config['checkSSL'],
            'verify_peer' => $this->config['checkSSL'],
            'auth_bearer' => $this->tokenAuthenticator->getToken(),
            'body' => $body,
            'timeout' => $this->config['timeout'] + $addtionalTimeout,
            'max_duration' => $this->config['timeout'] + $addtionalTimeout,
        ];
        $response = $this->httpClient->request($type, $url, $options);
        $status = $response->getStatusCode();
        if (400 <= $status) {
            $this->tokenAuthenticator->clearCache();
        }

        return $response;
    }


    /**
     * @param string $type // 'GET', 'PUT', 'DELETE', ....
     * @param string $command
     * @param null|array|string|resource|\Traversable|\Closure $body
     * @param null|iterable|string[]|string[][] $headers
     * @return mixed|null
     * @throws \Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function commandJsonRsp($type, $command, $body = null, $headers = null, $addtionalTimeout = 0)
    {
        $response = $this->command($type, $command, $body, $headers, $addtionalTimeout);
        $headers = $response->getHeaders(false);
        $status = $response->getStatusCode();
        $content = $response->getContent(false);
        $data = json_decode($content);
        if (400 <= $status) {
            $error = 'Datacontent :  Error, the response id not a json';
            if ('application/json' == $headers['content-type'][0] && isset($data->exceptionCode)) {
                $error = 'Datacontent : Error for command '.$command.' : '.$data->exceptionCode.' '.$data->exceptionMessage ?? '';
            }
            throw new Exception($error);
        }

        return $data;
    }


    /**
     * @return mixed|null
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getBases()
    {
        $this->logger->debug('Datacontent : get bases ');

        return $this->commandJsonRsp('GET', '/bases');
    }

    /**
     * @param string $baseId
     * @return void
     */
    public function setBaseId($baseId)
    {
        $this->baseId = $baseId;
    }

    /**
     * @return string
     */
    public function getBaseId()
    {
        return $this->baseId;
    }


    /**
     * @return mixed|null
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getBase()
    {
        $this->logger->debug('Datacontent : get base ');

        return $this->commandJsonRsp('GET', '/bases/'.$this->baseId);
    }


    /**
     * @param string|null $query
     * @param array options
     * @param int $addtionalTimeout tiemout additonnel pour une transaction
     * @return mixed|null
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function searchByQuery($query, $options = [], $addtionalTimeout = 0)
    {
        $this->logger->debug(
            'Datacontent : search by query' ,
            ['query'=>$query, 'options'=>$options,'addtionalTimeout'=>$addtionalTimeout]
            );
        $parameters = [
            '@class' => 'net.docubase.toolkit.model.search.SortedSearchQuery',
            'query' => $query,
            'fullText' => $options['fullText'] ?? null,
            // ! be careful,  a exception is throw if the base is a non fulltext
            'pageSize' => $options['pageSize'] ?? null,
            'offset' => $options['offset'] ?? null,
            'sortCategoryName' => $options['sortCategoryName'] ?? null,
            'reversedSort' => $options['reversedSort'] ?? null,
            'indexOrderPreference' => $options['indexOrderPreference'] ?? null,
            'searchLimit' => $options['searchLimit'] ?? null,
            'base' => [
                'baseId' => $this->baseId,
            ],
            'timeZone' => $options['timeZone'] ?? 'Europe/Zurich',
        ];
        if (isset($options['searchLimit'])) {
            $parameters['searchLimit'] = $options['searchLimit'];
        }
        $json = json_encode($parameters);

        return $this->commandJsonRsp('POST', '/search/query', $json, ['Content-Type:application/json'],
            $addtionalTimeout);
    }


    /**
     * @param $uuid
     * @return mixed|null
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function searchByUuid($uuid)
    {
        $this->logger->debug('Datacontent : search by uuid',['uuid'=>$uuid]);
        if (null === $this->baseId) {
            return $this->commandJsonRsp('GET', '/search/'.$uuid);
        } else {
            return $this->commandJsonRsp('GET', '/search/'.$this->baseId.'/'.$uuid);
        }
    }

    /**
     * @param string $uuid
     * @param bool $httpResponse
     * @param bool $raw
     * @return string|Response
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getDocument($uuid, $httpResponse = true, $raw = false)
    {
        $this->logger->debug(
            'Datacontent : get document',
            ['uuid'=>$uuid, 'httpResponse'=>$httpResponse, 'raw'=>$raw]
            );
        $document = $this->command('GET', '/store/'.($raw ? 'raw/' : '').$uuid);
        if ($httpResponse) {
            $info = $this->searchByUuid($uuid);
            $response = new Response($document->getContent());
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $info->filename.'.'.$info->extension
            );
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-Type', 'application/'. $info->extension);

            return $response;
        }

        return ($document->getContent());
    }

    /**
     * @param string $uuid
     * @return null
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function deleteDocument($uuid)
    {
        $this->logger->debug('Datacontent :  delete document', ['uuid'=>$uuid]  );

        return $this->commandJsonRsp('DELETE', '/store/'.$uuid);
    }

    /**
     * @param string $filePath
     * @param null|string $title
     * @param array $criterions
     * @param array $options
     * @return mixed
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */

    public function storeDocument($filePath, $title = null, $criterions = [], $options = [])
    {
        $this->logger->debug(
            'Datacontent :  storeDocument  ',
            ['filePath'=>$filePath, 'title'=>$title, 'criterions'=>$criterions, 'options'=>$options]
            );
        $path_parts = pathinfo($filePath);
        if (null === $title) {
            $title = $path_parts['basename'];
        }
        $parameters = [
            "@class" => 'net.docubase.toolkit.model.document.Document',
            'baseId' => $this->baseId,
            'title' => $title,
            'creationDate' => $options['creationDate'] ?? '',
            'filename' => $options['filename'] ?? $path_parts['filename'],
            'extension' => $options['extension'] ?? $path_parts['extension'] ?? '',
        ];
        $gedCriterions = [];
        foreach ($criterions as $key => $value) {
            $gedCriterions[] = ['categoryName' => $key, 'wordValue' => $value];
        }
        $parameters['criterions'] = $gedCriterions;
        $formFields = [
            'document' => new DataPart(json_encode($parameters), 'document.json', 'application/json'),
            'inputStream' => DataPart::fromPath($filePath, 'inputStream', 'application/octet-stream'),
        ];
        $formData = new FormDataPart($formFields);
        $headers = $formData->getPreparedHeaders()->toArray();
        $body = $formData->bodyToIterable();

        return $this->commandJsonRsp('POST', '/store', $body, $headers);
    }
}
