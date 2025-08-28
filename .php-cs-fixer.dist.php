<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'node_modules']);

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PSR12' => true,
            '@Symfony' => true,
            'global_namespace_import' => [
                'import_classes' => true,
                'import_constants' => true,
                'import_functions' => true,
            ],
            'ordered_class_elements' => [
                'order' => [
                    'use_trait',
                    'case',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic']
            ],
            'concat_space' => ['spacing' => 'one'],
            'phpdoc_to_comment' => [
                'ignored_tags' => ['var'],
                'allow_before_return_statement' => true
            ]
        ]
    )
    ->setFinder($finder);
