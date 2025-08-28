<?php

namespace App;

use Attribute;

/**
 * Attribut parameters
 * Allows you to describe a parameter, by defining a type, a description of the value
 * and a description of the parameter.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Param
{
    public const INPUT = 'input';
    public const TEXTAREA = 'textarea';
    public const RADIO = 'radio';

    public ?string $description;
    public ?string $valeur;
    public bool $readOnly;
    public ?string $type;

    public function __construct(
        ?string $description = null,
        ?string $valeur = null,
        bool $readOnly = false,
        string $type = self::INPUT,
    ) {
        $this->description = $description;
        $this->valeur = $valeur;
        $this->readOnly = $readOnly;
        $this->type = $type;
    }
}
