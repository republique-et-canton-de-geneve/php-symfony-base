<?php

namespace EtatGeneve\DataContentBundle\Service;

use EtatGeneve\DataContentBundle\DataContentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

use function intval;
use function is_numeric;
use function is_object;
use function is_string;

/**
 * @phpstan-import-type DataContentConfig from DataContent
 */
class TokenAuthenticator implements InterfaceTokenAuthenticator
{
    public const DATA_CONTENT_TOKEN_CACHE_KEY = 'data_content_token_cache_key';

    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CacheInterface $cache;
    /** @var DataContentConfig */
    private array $config;

    /**
     * @param DataContentConfig $config
     */
    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        CacheInterface $cache,
        array $config,
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->config = $config;
    }

    public function reset(): void
    {
        $this->logger->debug('DatatContent : Clear cache token');
        $this->cache->delete(self::DATA_CONTENT_TOKEN_CACHE_KEY);
    }

    /**
     * Return a sso token, use symfony system cache.
     */
    public function getToken(): string
    {
        $token = $this->cache->get(
            self::DATA_CONTENT_TOKEN_CACHE_KEY,
            function (ItemInterface $item) {
                try {
                    $this->logger->debug('DatatContent : get token');
                    $parameters = [
                        'verify_host' => $this->config['checkSSL'],
                        'verify_peer' => $this->config['checkSSL'],
                        'headers' => ['X-Application-ID' => $this->config['applicationId']],
                        'body' => [
                            'client_id' => $this->config['clientId'] ?? '',
                            'client_secret' => $this->config['clientSecret'] ?? '',
                            'grant_type' => 'password',
                            'username' => $this->config['username'] ?? '',
                            'password' => $this->config['password'] ?? '',
                            'audience' => $this->config['audience'] ?? '',
                            'timeout' => $this->config['tokenTimeout'] ?? 15,
                            'max_duration' => $this->config['tokenTimeout'] ?? 15,
                        ],
                    ];

                    $response = $this->httpClient->request('POST', $this->config['tokenAuthSsoUrl'] ?? '', $parameters);
                    $data = json_decode($response->getContent());
                    if (is_object($data) && ($data->id_token ?? false)
                    && isset($data->expires_in) && is_numeric($data->expires_in)) {
                        $item->expiresAfter(intval($data->expires_in) - 10);

                        return $data->id_token;
                    }
                } catch (Throwable $e) {
                }
                $this->reset();
                throw new DataContentException('DatatContent : Invalid SSO token response');
            },
            0.1
        );

        return is_string($token) ? $token : '';
    }
}
