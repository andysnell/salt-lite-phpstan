<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhoneBurner\SaltLite\Framework\Container\ServiceProvider;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use const PhoneBurner\SaltLite\Framework\APP_ROOT;

/**
 * @implements Rule<InClassNode>
 */
class ServiceProvidersMustBeRegistered implements Rule
{
    private const string IDENTIFIER = 'saltlite.serviceProviderRegistration';

    private const string MESSAGE = 'Service Provider Not Registered in ' . APP_ROOT . '/config/container.php';

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

        if (! $class->implementsInterface(ServiceProvider::class)) {
            return [];
        }

        if ($class->isAbstract() || $class->isInterface()) {
            return [];
        }

        if (\in_array($class->getName(), $this->getRegisteredProviders(), true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->build(),
        ];
    }

    public function getRegisteredProviders(): array
    {
        static $registered_providers;

        if ($registered_providers) {
            return $registered_providers;
        }

        $configuration = include APP_ROOT . '/config/container.php';
        $registered_providers = $configuration['container']['service_providers'] ?? [];
        return $registered_providers;
    }
}
