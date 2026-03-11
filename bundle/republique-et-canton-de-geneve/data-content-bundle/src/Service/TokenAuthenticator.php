<?php

namespace EtatGeneve\DataContentBundle\Service;

use EtatGeneve\DataContentBundle\DataContentBundle;
use EtatGeneve\DataContentBundle\DataContentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

/**
 * @phpstan-import-type TokenAuthenticatorConfig from DataContentBundle
 */
class TokenAuthenticator
{
    public const DATA_CONTENT_TOKEN_CACHE_KEY = 'data_content_token_cache_key';

    /**
     * @param TokenAuthenticatorConfig $config
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private array $config,
    ) {
    }

    public function clearCache(): void
    {
        $this->logger->debug('DatatContent : Clear cache token');
        $this->cache->delete(self::DATA_CONTENT_TOKEN_CACHE_KEY);
    }

    /**
     * Return a sso token, use symfony system cache.
     */
    public function getToken(): string
    {
        return $this->cache->get(
            self::DATA_CONTENT_TOKEN_CACHE_KEY,
            function (ItemInterface $item) {
                try {
                    $this->logger->debug('DatatContent : get token');
                    $parameters = [
                        'verify_host' => $this->config['checkSSL'],
                        'verify_peer' => $this->config['checkSSL'],
                        'headers' => ['X-Application-ID' => $this->config['applicationId']],
                        'body' => [
                            'client_id' => $this->config['clientId'],
                            'client_secret' => $this->config['clientSecret'],
                            'grant_type' => 'password',
                            'username' => $this->config['username'],
                            'password' => $this->config['password'],
                            'audience' => $this->config['audience'],
                            'timeout' => $this->config['tokenTimeout'],
                            'max_duration' => $this->config['tokenTimeout'],
                        ],
                    ];

                    $response = $this->httpClient->request('POST', $this->config['tokenAuthSsoUrl'], $parameters);
                    $data = json_decode($response->getContent());
                    if (isset($data->id_token) && isset($data->expires_in)) {
                        $item->expiresAfter($data->expires_in - 10);

                        return $data->id_token;
                    }
                } catch (Throwable $e) {
                }
                $this->clearCache();
                throw new DataContentException('DatatContent : Invalid SSO token response');
            },
            0.1
        );
    }
}
