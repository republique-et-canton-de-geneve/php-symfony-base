<?php

namespace App\Service\Datacontent;


use App\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Security\Core\Security;
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
class Datacontent
{

    public const GINA_TOKEN_KEY_CACHE = 'ged_data_content_token';

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string|null
     */
    protected $symfonyUsername;
    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $checkSSL = true;

    /**
     * Application ID
     * @var string
     */
    protected $applicationId;

    /**
     * Gina user name
     * @var string
     */
    protected $username;

    /**
     * Gina password
     * @var string
     */
    protected $password;

    /**
     * Gina SSO login url
     * @var string
     */
    protected $ginaUrlSso;

    /**
     * Service client password
     * @var
     */
    protected $clientSecret;


    /**
     * Service Client Id
     * @var string
     */
    protected $clientId;

    /**
     * GED rest Url
     * @var string
     */
    protected $restUrl;


    /**
     * Base Id
     * @var string
     */
    protected $baseId;

    /**
     * @var Parameter
     */
    protected $parameter;

    /**
     * Ged constructor.
     * @param HttpClientInterface $httpClient
     * @param Security $security
     * @param LoggerInterface $faoLogger
     * @param Parameter $parameter
     * @param array $settings
     */
    public function __construct(
        HttpClientInterface $httpClient,
        Security $security,
        LoggerInterface $faoLogger,
        Parameter $parameter,
        $settings
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $faoLogger;
        $this->parameter = $parameter;
        $user = $security->getUser();
        if ($user) {
            $this->symfonyUsername = $user->getUserIdentifier();
        }
        foreach ($settings as $key => $value) {
            $this->{$key} = $value;
        }
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearCache()
    {
        $this->logger->debug('GED: clear gina token cache');
        $this->cache->delete(self::GINA_TOKEN_KEY_CACHE);
    }

    /**
     * Return a gina sso token, use symfony system cache
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getGinaToken()
    {
        return $this->cache->get(
            self::GINA_TOKEN_KEY_CACHE,
            function (ItemInterface $item) {
                try {
                    $this->logger->debug('GED: get gina token');
                    $parameters = [
                        'verify_host' => $this->checkSSL,
                        'verify_peer' => $this->checkSSL,
                        'headers' => ['X-Application-ID' => $this->applicationId],
                        'body' =>
                            [
                                'client_id' => $this->clientId,
                                'client_secret' => $this->clientSecret,
                                'grant_type'=>'password',
                                'username' => $this->username,
                                'password' => $this->password,
                                'audience' => 'GED.DFCE',
                                'timeout' => (int)$this->parameter->timeGinaTokenDataContent,
                                'max_duration' => (int)$this->parameter->timeGinaTokenDataContent,
                            ],
                    ];
                    $response = $this->httpClient->request('POST', $this->ginaUrlSso, $parameters);
                    $data = json_decode($response->getContent());
                    if (isset($data->id_token) && isset($data->expires_in)) {
                        $item->expiresAfter($data->expires_in - 10);

                        return $data->id_token;
                    }
                } catch (Throwable $e) {
                }
                $this->clearCache();
                throw new Exception('GED Invalid SSO response');
            },
            0.1
        );
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
        $this->logger->debug('GED REST : '.$type.' '.$command);
        $url = $this->restUrl.$command;
        $headers['X-Application-ID'] = $this->applicationId;
        $headers['X-Tenant-ID'] = 'admin';
        if ($this->symfonyUsername) {
            $headers['connectedAs'] = $this->symfonyUsername;
        }

        $options = [
            'headers' => $headers,
            'verify_host' => $this->checkSSL,
            'verify_peer' => $this->checkSSL,
            'auth_bearer' => $this->getGinaToken(),
            'body' => $body,
            'timeout' => (int)$this->parameter->timeDataContent + $addtionalTimeout,
            'max_duration' => (int)$this->parameter->timeDataContent + $addtionalTimeout,
        ];
        $response = $this->httpClient->request($type, $url, $options);
        $status = $response->getStatusCode();
        if (400 <= $status) {
            $this->clearCache();
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
            $error = 'GED error : not a json response';
            if ('application/json' == $headers['content-type'][0] && isset($data->exceptionCode)) {
                $error = 'GED error for command '.$command.' : '.$data->exceptionCode.' '.$data->exceptionMessage ?? '';
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
        $this->logger->debug('GED method getBases : ');

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
        $this->logger->debug('GED method getBase : '.$this->baseId);

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
        $this->logger->debug('GED method searchByQuery : '.$query, [$options]);
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
        $this->logger->debug('GED method searchByUuid : '.$uuid);
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
        $this->logger->debug('GED method getDocument : '.$uuid, ['raw' => $raw, 'httpResponse' => $httpResponse]);
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
        $this->logger->debug('GED method deleteDocument : '.$uuid);

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
        $this->logger->debug('GED method storeDocument : '.$filePath.' '.$title, [$criterions, $options]);
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
