<?php

namespace EtatGeneve\DataContentBundle;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * @phpstan-type TokenAuthenticatorConfig array{
 * checkSSL?: bool,
 * applicationId?: ?string,
 * clientId: string,
 * clientSecret: string,
 * username : string,
 * password : string,
 * timeout? : int,
 * audience : string
 * }
 */


class TokenAuthenticator
{

    private bool $checkSSL;
    private ?string $applicationId = null;
    private string $clientId;
    private string $clientSecret;
    private string $username;
    private string $password;
    /**
     * timeout in second
     *
     */
    private int $timeout;


    public const DATA_CONTENT_TOKEN_CACHE_KEY = "data_content_token_cache_key";

    /**
     *
     * @param HttpClientInterface $httpClient
     * @param LoggerInterface $logger
     * @param CacheInterface $cache
     * @param TokenAuthenticatorConfig $config
     */
    public function __construct(
        private  HttpClientInterface $httpClient,
        private  LoggerInterface $logger,
        private CacheInterface $cache,
        private array $config

    ) {
        $this->checkSSL = $this->config["checkSSL"] ?? true;
        $this->applicationId = $this->config["applicationId"] ?? null;
        $this->clientId = $this->config["clientId"];
        $this->clientSecret = $this->config['clientSecret'];
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
        $this->timeout= $this->config['timeout']??10;
    }

    public function clearCache(): void
    {
        $this->logger->debug('Clear cache datacontent token');
        $this->cache->delete(self::DATA_CONTENT_TOKEN_CACHE_KEY);
    }

    /**
     * Return a sso token, use symfony system cache
     *
     */
    protected function getGinaToken(): string
    {
        return $this->cache->get(
            self::DATA_CONTENT_TOKEN_CACHE_KEY,
            function (ItemInterface $item) {
                try {
                    $this->logger->debug('get datacontent token');
                    $parameters = [
                        'verify_host' => $this->checkSSL,
                        'verify_peer' => $this->checkSSL,
                        'body' =>
                        [
                            'client_id' => $this->clientId,
                            'client_secret' => $this->clientSecret,
                            'grant_type' => 'password',
                            'username' => $this->username,
                            'password' => $this->password,
                            'audience' => 'GED.DFCE',
                            'timeout' => $this->timeout,
                            'max_duration' => $this->timeout,
                        ],
                    ];
                    if ($this->applicationId) {
                        $parameters['headers'] = ['X-Application-ID' => $this->applicationId];
                    }

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
}
