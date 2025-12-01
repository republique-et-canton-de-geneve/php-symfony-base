<?php

namespace EtatGeneve\ResponseHeadersBundle\EventListener;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseListener
{
    private array $headers;

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $expressionLanguage = new ExpressionLanguage();
        $reponse = $event->getResponse();
        $request = $event->getRequest();
        foreach ($this->headers as $name => $headerConfig) {
            $condition = $headerConfig['condition'] ?? null;
            if ($condition) {
                if (!$expressionLanguage->evaluate($condition, ['response' => $reponse, 'request' => $request])) {
                    continue;
                }
            }
            $value = $headerConfig['value'];
            $value = is_array($value) ? implode('', $value) : $value;
            $reponse->headers->set($name, $value, true);
        }
    }
}
