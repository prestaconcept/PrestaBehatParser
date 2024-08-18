<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\ExpressionLanguage\Evaluator;

use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\DateTime\ArgumentGuesserInterface;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\DateTime\FormatArgumentGuesser;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\DateTime\TimeArgumentGuesser;

/**
 * @phpstan-import-type IntlFormats from ArgumentGuesserInterface
 */
final class DateTimeEvaluator
{
    public function __construct(private readonly \Closure $createDateTime, private readonly string $culture)
    {
    }

    /**
     * @param array<string, mixed> $arguments
     * @param IntlFormats|string|null $time
     * @param IntlFormats|string|null $format
     */
    public function __invoke(array $arguments, string|array|null $time = null, string|array|null $format = null): mixed
    {
        $originalTime = $time;
        $originalFormat = $format;

        $time = (new TimeArgumentGuesser())($originalTime, $originalFormat);
        $format = (new FormatArgumentGuesser())($originalTime, $originalFormat);

        \assert(\is_string($time));

        $datetime = ($this->createDateTime)($time);
        \assert($datetime instanceof \DateTimeInterface);

        if (!\is_string($format) && !\is_array($format)) {
            return $datetime;
        }

        if (\is_array($format)) {
            return $this->intl($datetime, $format);
        }

        return $datetime->format($format);
    }

    /**
     * @param IntlFormats $formats
     */
    private function intl(\DateTimeInterface $datetime, array $formats): string
    {
        $date = $formats['date'] ?? 'NONE';
        $date = \constant("\IntlDateFormatter::$date");
        if (!\is_int($date)) {
            throw new \RuntimeException('The intl date format should be a valid \IntlDateFormatter format');
        }

        $time = $formats['time'] ?? 'NONE';
        $time = \constant("\IntlDateFormatter::$time");
        if (!\is_int($time)) {
            throw new \RuntimeException('The intl time format should be a valid \IntlDateFormatter format');
        }

        $formatter = new \IntlDateFormatter($this->culture, $date, $time);

        return $formatter->format($datetime)
            ?: throw new \RuntimeException('Could not format the given datetime with the IntlDateFormatter.')
        ;
    }
}
