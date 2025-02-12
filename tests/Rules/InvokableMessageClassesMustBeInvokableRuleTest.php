<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules;

use PhoneBurner\SaltLite\Phpstan\Rules\InvokableMessageClassesMustBeInvokableRule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<InvokableMessageClassesMustBeInvokableRule>
 */
final class InvokableMessageClassesMustBeInvokableRuleTest extends RuleTestCase
{
    private const string FIXTURE_BASE_DIR = __DIR__ . '/Fixtures/InvokableMessageClassesMustBeInvokableRule/';

    protected function getRule(): InvokableMessageClassesMustBeInvokableRule
    {
        return new InvokableMessageClassesMustBeInvokableRule();
    }

    public function testRule(): void
    {
        $this->analyse([self::FIXTURE_BASE_DIR . '/ActuallyInvokableMessage.php'], []);
        $this->analyse([self::FIXTURE_BASE_DIR . '/NotInvokableMessage.php'], [
            [InvokableMessageClassesMustBeInvokableRule::MESSAGE, 9],
        ]);
    }

    #[\Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../config/extension.neon'];
    }
}
