<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules;

use PhoneBurner\SaltLite\Phpstan\Rules\CommandsMustHaveCommandSuffixRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<CommandsMustHaveCommandSuffixRule>
 */
final class CommandsMustHaveCommandSuffixRuleTest extends RuleTestCase
{
    private const string FIXTURE_BASE_DIR = __DIR__ . '/Fixtures/CommandsMustHaveCommandSuffixRule/';

    protected function getRule(): Rule
    {
        return new CommandsMustHaveCommandSuffixRule();
    }

    public function testRule(): void
    {
        $this->analyse([self::FIXTURE_BASE_DIR . '/WellNamedCommand.php'], []);
        $this->analyse([self::FIXTURE_BASE_DIR . '/PoorlyNamed.php'], [
            [CommandsMustHaveCommandSuffixRule::MESSAGE, 10],
        ]);
    }

    #[\Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../config/extension.neon'];
    }
}
