# Installation

Require the republique-et-canton-de-geneve/headers-bundle package in your composer.json and update your dependencies:
```
composer require republique-et-canton-de-geneve/headers-bundle
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
