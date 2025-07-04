<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
    ])
    ->name('*.php')
    ->exclude([
        'vendor',
        'storage',
        'bootstrap/cache',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => ['allow_single_line_anonymous_class_with_empty_body' => true],
    'cast_spaces' => ['space' => 'single'],
    'concat_space' => ['spacing' => 'one'],
    'declare_strict_types' => true,
    'function_declaration' => ['closure_function_spacing' => 'one'],
    'indentation_type' => true,
    'line_ending' => true,
    'lowercase_keywords' => true,
    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
    'no_closing_tag' => true,
    'no_extra_blank_lines' => ['tokens' => ['extra']],
    'no_spaces_after_function_name' => true,
    'no_spaces_inside_parenthesis' => true,
    'no_trailing_whitespace' => true,
    'no_unused_imports' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    // 'single_quote' => true,
    'visibility_required' => ['elements' => ['method', 'property']],
];

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
