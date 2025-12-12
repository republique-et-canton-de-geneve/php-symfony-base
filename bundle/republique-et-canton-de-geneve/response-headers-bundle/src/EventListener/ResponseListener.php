<?php

namespace EtatGeneve\ResponseHeadersBundle\EventListener;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseListener
{
    /**
     * @var array<string,array{condition:string}|array{string:string|array<string>}>
     **/
    private array $headers;

    /**
     * @param array<string,array{condition:string}|array{string:string|array<string>}> $headers
     **/
    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->isMainRequest()) {
            $expressionLanguage = new ExpressionLanguage();
            $reponse = $event->getResponse();
            $request = $event->getRequest();
            foreach ($this->headers as $name => $headerConfig) {
                if (isset($headerConfig['condition'])) {
                    if (!$expressionLanguage->evaluate(
                        $headerConfig['condition'],
                        ['response' => $reponse, 'request' => $request]
                    )) {
                        continue;
                    }
                }
                $value = $headerConfig['value'] ?? null;
                $replace = $headerConfig['replace'] ?? true;

                $value = is_array($value) ? implode('', $value) : $value;
                $reponse->headers->set($name, $value, $replace);
            }
        }
    }
}
