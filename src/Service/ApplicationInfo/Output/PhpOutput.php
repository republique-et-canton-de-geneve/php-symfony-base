<?php

namespace App\Service\ApplicationInfo\Output;

class PhpOutput implements InterfaceOutput
{
    public function output(string $str): void
    {
        printf('%s', $str);
    }

    public function style(): string
    {
        return <<<EOT
            <style>
                h1, h2, h3, h4, h5, h6 {
                    margin-top: 0;
                    margin-bottom: 0.5rem;
                }
            
                body {
                    font-family: system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, serif;
                    font-size: 1rem;
                    font-weight: normal;
                    line-height: 1.5;
                    color: #292b2c;
                    background-color: #fff;
                }
            
                .warning {
                    background-color: #f2dede;
                    color: #a94442;
                    padding: 0.1rem 0.1rem;
                    margin-bottom: 1rem;
                    border: 1px solid #ebcccc;
                    border-radius: 0.25rem;
                }
            
                .green {
                    background-color: #dff0d8;
                    color: #3c763d;
                    padding: 0.1rem 0.1rem;
                    margin-bottom: 1rem;
                    border: 1px solid #d0e9c6;
                    border-radius: 0.25rem;
                }
            </style>
EOT;
    }

    public function top(): void
    {
        $top = <<<EOT
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
            <title>ApplicationInfo</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            {$this->style()}
        </head>
        <body>
EOT;
        $this->output($top);
    }

    public function bottom(): void
    {
        $bottom = <<<EOT
        <br/>
        </body>
        </html>
EOT;
        $this->output($bottom);
    }

    public function title(string $str): void
    {
        $this->output('<h2>' . $str . '</h2>');
    }

    public function line(string $str): void
    {
        $this->output($str . '</br>');
    }

    public function erreur(string $str): void
    {
        $this->line('Erreur : ' . $this->ko($str));
    }

    public function ok(string $str): string
    {
        return '<span class="green">' . $str . '</span>';
    }

    public function ko(string $str): string
    {
        return '<span class="warning">' . $str . '</span>';
    }

    public function filter(string $str): string
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
}
