<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhoneBurner\SaltLite\Framework\Container\ServiceContainer\ServiceContainerFactory;
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
class ServiceProvidersMustBeRegisteredRule implements Rule
{
    public const string IDENTIFIER = 'saltlite.serviceProviderRegistration';

    public const string MESSAGE = 'Service Provider Not Registered in "config/container.php"';

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

        // Is the class registered in the container configuration?
        if (\in_array($class->getName(), $this->getRegisteredProviders(), true)) {
            return [];
        }

        // Is the class a framework provider that is hardcoded in the service container factory?
        if (\in_array($class->getName(), ServiceContainerFactory::FRAMEWORK_PROVIDERS, true)) {
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
