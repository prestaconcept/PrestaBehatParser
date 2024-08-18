<?php

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Presta\BehatEvaluator\Adapter\ScalarAdapter;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayListItemNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer;
use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ],
    )
    ->withPreparedSets(
        psr12: true,
        arrays: true,
        comments: true,
        docblocks: true,
        namespaces: true,
        phpunit: true,
        strict: true,
    )
    ->withConfiguredRule(
        BlankLineBeforeStatementFixer::class,
        [
            'statements' => ['case', 'continue', 'declare', 'default', 'return', 'throw', 'try'],
        ],
    )
    ->withConfiguredRule(
        CastSpacesFixer::class,
        [
            'space' => 'none',
        ],
    )
    ->withConfiguredRule(
        ForbiddenFunctionsSniff::class,
        [
            'forbiddenFunctions' => ['dump' => null, 'dd' => null, 'var_dump' => null, 'die' => null],
        ],
    )
    ->withConfiguredRule(
        LineLengthFixer::class,
        [
            LineLengthFixer::INLINE_SHORT_LINES => false,
        ],
    )
    ->withConfiguredRule(
        NativeFunctionInvocationFixer::class,
        [
            'scope' => 'namespaced',
            'include' => ['@all'],
        ],
    )
    ->withConfiguredRule(
        NullableTypeDeclarationFixer::class,
        [
            'syntax' => 'union',
        ],
    )
    ->withConfiguredRule(
        YodaStyleFixer::class,
        [
            'equal' => true,
            'identical' => true,
            'less_and_greater' => false,
        ],
    )
    ->withRules([
        FunctionDeclarationFixer::class,
    ])
    ->withSkip(
        [
            ArrayListItemNewlineFixer::class,
            ArrayOpenerAndCloserNewlineFixer::class,
            ExplicitStringVariableFixer::class,
            StandaloneLineInMultilineArrayFixer::class,
            StrictComparisonFixer::class => [
                'src/Adapter/ScalarAdapter.php',
            ],
        ],
    )
;
