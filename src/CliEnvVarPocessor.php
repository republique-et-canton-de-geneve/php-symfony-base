<?php

namespace App;

use Closure;
use Exception;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class CliEnvVarPocessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, Closure $getEnv): string
    {
        $env = $getEnv($name);
        if (defined('STDIN')) {
            try {
                $env = $getEnv($name . '_CLI');
            } catch (Exception) {
                // Do nothing
            }
        }

        return is_scalar($env) ? strval($env) : '';
    }

    public static function getProvidedTypes(): array
    {
        return [
            'cli' => 'string',
        ];
    }
}
