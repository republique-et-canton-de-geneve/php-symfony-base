<?php

namespace App\Service\ApplicationInfo\Output;

interface InterfaceOutput
{
    public function output(string $str): void;

    public function top(): void;

    public function bottom(): void;

    public function title(string $str): void;

    public function line(string $str): void;

    public function erreur(string $str): void;

    public function ok(string $str): string;

    public function ko(string $str): string;

    public function filter(string $str): string;
}
