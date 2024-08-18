<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\DateTime;

/**
 * @phpstan-import-type IntlFormats from ArgumentGuesserInterface
 */
final class FormatArgumentGuesser implements ArgumentGuesserInterface
{
    private const DEFAULT = null;

    /**
     * @return IntlFormats|string|null
     */
    public function __invoke(string|array|null $time = null, string|array|null $format = null): string|array|null
    {
        foreach ([$time, $format] as $argument) {
            if (\is_array($argument)) {
                return $argument;
            }
        }

        if (!\is_string($time)) {
            return self::DEFAULT;
        }

        try {
            new \DateTime($time);

            if (!\is_string($format)) {
                return self::DEFAULT;
            }

            return $this->format($format);
        } catch (\Throwable) {
        }

        return $this->format($time);
    }

    /**
     * @return IntlFormats|string
     */
    private function format(string $argument): array|string
    {
        $argument = \trim(\str_replace(['format:', 'intl:'], '', $argument), ' "\'');

        try {
            /** @var IntlFormats $formats */
            $formats = \json_decode($argument, true, JSON_THROW_ON_ERROR);
            if (\is_array($formats)) {
                return $formats;
            }
        } catch (\Throwable) {
        }

        return $argument;
    }
}
