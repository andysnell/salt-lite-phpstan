<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Command\Command;

/**
 * @implements Rule<InClassNode>
 */
class CommandsMustHaveCommandSuffixRule implements Rule
{
    public const string IDENTIFIER = 'saltlite.commandNameSuffix';

    public const string MESSAGE = 'Command Class Name Must End with "Command" Suffix';

    #[\Override]
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        \assert($node instanceof InClassNode);

        $class = $node->getClassReflection();
        if (! $class->is(Command::class) || $class->isAbstract()) {
            return [];
        }

        $file = $class->getFileName();
        if ($file === null || \str_ends_with($file, 'Command.php')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->build(),
        ];
    }
}
