<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

/**
 * @implements Rule<InClassNode>
 */
class CommandsMustHaveAsCommandAttributeRule implements Rule
{
    private const string IDENTIFIER = 'saltlite.commandRegistration';

    private const string MESSAGE = 'Commands must declare the ' . AsCommand::class . ' attribute';

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
        if (! $class->isSubclassOf(Command::class) || $class->isAbstract()) {
            return [];
        }

        if (\count($class->getNativeReflection()->getAttributes(AsCommand::class)) === 1) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->build(),
        ];
    }
}
