<?php

namespace App;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class CliEnvVarPocessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv): string
    {
        $env = $getEnv($name);
        if (defined('STDIN')) {
            try {
                $envcli = $getEnv($name . '_CLI');
                return $envcli ?? $env;
            } catch (\Exception ) {
                // Do nothing
            }
        }
        return $env;
    }

    public static function getProvidedTypes(): array
    {
        return [
            'cli' => 'string',
        ];
    }
}
