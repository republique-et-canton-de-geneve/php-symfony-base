<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @phpstan-type Settings array{
 *     APP_AUTH_FROM_ENV_VAR?:bool|string|null,
 *     APP_USER_LOGIN?:string|null,
 *     APP_USER_ROLES?:string|null,
 *     APP_USER_EMAIL?:string|null,
 *     APP_USER_FIRSTNAME?:string|null,
 *     APP_USER_NAME?:string|null,
 *     APP_USER_FULLNAME?:string|null
 * }
 */
class EnvAuthenticator extends AbstractAuthenticator
{
    /** @var Settings */
    private array $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    protected function getLoginEnvVar(): ?string
    {
        $login = $this->settings['APP_USER_LOGIN'] ?? null;
        if (is_string($login)) {
            return mb_strtoupper($login);
        }

        return null;
    }

    public function supports(Request $request): ?bool
    {
        return filter_var($this->settings['APP_AUTH_FROM_ENV_VAR'] ?? false, FILTER_VALIDATE_BOOLEAN)
            && null !== $this->getLoginEnvVar();
    }

    public function authenticate(Request $request): Passport
    {
        $login = $this->getLoginEnvVar();
        if (null === $login) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($login, function ($login): User {
                $user = new User();
                /** @var string $ginaRoles */
                $ginaRoles = $this->settings['APP_USER_ROLES'] ?? '';
                /** @var array<string, list<string>> $attributes */
                $attributes = [
                    User::USERNAME => [$login],
                    User::EMAIL => [$this->settings['APP_USER_EMAIL'] ?? null],
                    User::NAME => [$this->settings['APP_USER_NAME'] ?? null],
                    User::FIRSTNAME => [$this->settings['APP_USER_FIRSTNAME'] ?? null],
                    User::FULLNAME => [$this->settings['APP_USER_FULLNAME'] ?? null],
                    User::ROLES => explode('|', mb_strtoupper($ginaRoles)),
                ];
                $user->setEnvAttributes($attributes);

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
