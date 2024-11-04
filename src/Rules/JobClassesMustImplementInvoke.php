<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhoneBurner\SaltLite\Framework\Queue\Job;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassNode>
 */
class JobClassesMustImplementInvoke implements Rule
{
    private const string IDENTIFIER = 'saltlite.callableClassesMustImplementInvoke';

    private const string MESSAGE = 'Implementations of ' . Job::class . ' must define a __invoke() method';

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

        if (! $class->implementsInterface(Job::class)) {
            return [];
        }

        if ($class->hasMethod('__invoke') || $class->isAbstract() || $class->isInterface()) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->build(),
        ];
    }
}
