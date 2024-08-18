<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Evaluator;

final class EvaluatorContext implements Context
{
    /**
     * @var list<mixed>
     */
    private array $localStorage = [];

    #[Given('the local storage contains:')]
    public function set(TableNode $table): void
    {
        $this->localStorage = Evaluator::evaluateMany(\array_keys($table->getRowsHash()));
    }

    #[When('I format the datetime entries of the local storage with :format')]
    public function formatDateTimes(string $format): void
    {
        foreach ($this->localStorage as $key => $value) {
            if (!$value instanceof \DateTimeInterface) {
                continue;
            }

            $this->localStorage[$key] = $value->format($format);
        }
    }

    #[Then('the local storage should contain:')]
    public function assertLocalStorageContains(TableNode $table): void
    {
        TestCase::assertSame(
            Evaluator::evaluateMany(\array_keys($table->getRowsHash())),
            $this->localStorage,
        );
    }
}
