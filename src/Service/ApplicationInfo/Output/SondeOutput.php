<?php

namespace App\Service\ApplicationInfo\Output;

class SondeOutput implements InterfaceOutput
{
    protected bool $ok;

    public function top(): void
    {
        $this->ok = true;
    }

    public function bottom(): void
    {
        printf('%s', $this->ok ? 'OK' : 'KO');
    }

    public function erreur(string $str): void
    {
        $this->ok = false;
    }

    public function ko(string $str): string
    {
        $this->ok = false;

        return $str;
    }

    public function output(string $str): void
    {
        // on n'affiche pas les infos
    }

    public function title(string $str): void
    {
        // on n'affiche pas les titres
    }

    public function line(string $str): void
    {
        // on n'affiche de ligne
    }

    public function ok(string $str): string
    {
        return $str;
    }

    public function filter(string $str): string
    {
        return $str;
    }
}
