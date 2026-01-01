# Installation

Require the ``republique-et-canton-de-geneve/response-headers-bundle`` package in your composer.json and update your dependencies:
```
composer require republique-et-canton-de-geneve/response-headers-bundle
```


The bundle should be automatically enabled by Symfony Flex. If you don't use Flex, you'll need to manually enable the bundle by adding the following line in the config/bundles.php file of your project:

```
<?php
// config/bundles.php

return [
    // ...
    EtatGeneve\ResponseHeadersBundle\ResponseHeadersBundle::class => ['all' => true],
    // ...
];
```

Add your config file response-header.yaml in config/package

A example of response-header.yml
```
---
response_headers:
  headers:
    Expires: 0
    Content-Security-Policy:
      - "default-src 'none';"
      - "script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';"
      - "script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval' *.localhost mysite.com;"
      - "script-src-attr 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';"
      - "style-src 'self' 'unsafe-inline' *.localhost mysite.com;"
      - "connect-src 'self'  *.localhost mysite.com;"
      - "font-src 'self';"
      - "media-src 'self';"
      - "form-action 'self' *.localhost mysite.com;"
      - "img-src 'self' data: *.localhost mysite.com;"
      - "frame-src 'none';"

    Cache-Control : max-age=0, must-revalidate, no-cache, no-store, private"
    Referrer-Policy: strict-origin
    X-Content-Type-Options: nosniff
    X-XSS-Protection: 1; mode=block

    X-Frame-Options:
      value: SAMEORIGIN
      condition: "'%env(APP_SERVER_TYPE)%' == 'local'"
...

```

