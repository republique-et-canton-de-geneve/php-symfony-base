<?php

namespace App\Service\ApplicationInfo\Output;

class SymfonyOutput extends PhpOutput
{
    public string $html = '';

    public function top(): void
    {
        $this->output($this->style());
    }

    public function bottom(): void
    {
        $this->output('<br/>');
    }

    public function output(string $str): void
    {
        $this->html .= $str;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
