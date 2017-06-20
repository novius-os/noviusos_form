<?php

// Documentation: http://cs.sensiolabs.org/

$excludes = [
    'vendor',
    'node_modules',
];

$rules = [
    '@PSR2' => true,
    'psr0' => false,
    'concat_space' => ['spacing' => 'none'],
    'cast_spaces' => true,
    'new_with_braces' => true,
    'blank_line_before_return' => true,
    'single_quote' => true,
    'standardize_not_equals' => true,
    'blank_line_after_opening_tag' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->exclude($excludes)
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(true);
