<?php

namespace App\Security;

use Nbgrp\OneloginSamlBundle\Security\User\SamlUserInterface;

class User implements SamlUserInterface
{
    // definition saml2 attribut name
    public const string USERNAME = 'Login';
    public const string ROLES = 'Roles';
    public const string FULLNAME = 'FullName';
    public const string FIRSTNAME = 'Firstname';
    public const string NAME = 'Name';
    public const string EMAIL = 'Email';

    public const string AUTHENTIFICATION_SAML = 'SAML2';
    public const string AUTHENTIFICATION_ENV_VAR = 'ENV VAR';
    public string $authentificationMethode;
    /** @var array<string, list<string|null>> */
    protected array $attributes = [];
    protected string $userIdentifier;

    /**
     * @var string[]
     */
    protected array $roles = [];

    /**
     * @param array<string, list<string>> $attributes
     */
    public function setSamlAttributes(array $attributes): void
    {
        $this->authentificationMethode = self::AUTHENTIFICATION_SAML;
        $this->attributes = $attributes;
        $this->userIdentifier = (string) mb_strtoupper($attributes[self::USERNAME][0] ?? '');
    }

    /**
     * @param array<string, list<string>> $attributes
     */
    public function setEnvAttributes(array $attributes): void
    {
        $this->authentificationMethode = self::AUTHENTIFICATION_ENV_VAR;
        $this->attributes = $attributes;
        $this->userIdentifier = (string) mb_strtoupper($attributes[self::USERNAME][0] ?? '');
    }

    public function getUserIdentifier(): string
    {
        return '' === $this->userIdentifier ? '?' : $this->userIdentifier;
    }

    /**
     * @return array<int,string|null>
     */
    public function getGinaRoles(): array
    {
        return $this->attributes[self::ROLES] ?? [];
    }

    public function getName(): ?string
    {
        return $this->attributes[self::NAME][0] ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->attributes[self::FIRSTNAME][0] ?? null;
    }

    public function getFullName(): ?string
    {
        return $this->attributes[self::FULLNAME][0] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->attributes[self::EMAIL][0] ?? null;
    }

    /**
     * @return array<string,list<string|null>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAuthentificationMethode(): string
    {
        return $this->authentificationMethode;
    }

    public function hasLogout(): bool
    {
        return self::AUTHENTIFICATION_SAML === $this->authentificationMethode;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
        // not used
    }
}
