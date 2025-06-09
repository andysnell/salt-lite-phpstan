<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhoneBurner\SaltLite\Container\DeferrableServiceProvider;
use PhoneBurner\SaltLite\Phpstan\Collectors\ServiceProviderClassCollector;
use PhoneBurner\SaltLite\Phpstan\Collectors\ServiceProviderRegistrationsCollector;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<CollectedDataNode>
 */
class DeferrableServiceProvidersMustProvideAllServicesRule implements Rule
{
    public const string IDENTIFIER = 'saltlite.deferrableServiceProviderRegistration';

    public const string NOT_IN_BIND_OR_REGISTER = 'Deferrable service provider "%s" has service "%s" in its provides() method that is not defined in the bind() or register() methods';

    public const string NOT_IN_PROVIDES = 'Deferrable service provider "%s" defines service "%s" in the bind() or register() methods but not in the provides() method';

    public function __construct(private readonly ReflectionProvider $reflection_provider)
    {
    }

    #[\Override]
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    #[\Override]
    public function processNode(Node $node, Scope $scope): array
    {
        \assert($node instanceof CollectedDataNode);

        $definitions = $node->get(ServiceProviderRegistrationsCollector::class);
        $errors = [];

        foreach ($node->get(ServiceProviderClassCollector::class) as $file => $collected) {
            foreach ($collected as [$provider, $line]) {
                if (! $this->reflection_provider->getClass($provider)->implementsInterface(DeferrableServiceProvider::class)) {
                    continue;
                }

                $registers = [...\array_keys($provider::bind()), ...\array_column($definitions[$file] ?? [], 0)];

                foreach (\array_diff($registers, $provider::provides()) as $service) {
                    $errors[] = RuleErrorBuilder::message(\sprintf(self::NOT_IN_PROVIDES, $provider, $service))
                        ->file($file)
                        ->line($line)
                        ->identifier(self::IDENTIFIER)
                        ->build();
                }

                foreach (\array_diff($provider::provides(), $registers) as $service) {
                    $errors[] = RuleErrorBuilder::message(\sprintf(self::NOT_IN_BIND_OR_REGISTER, $provider, $service))
                        ->file($file)
                        ->line($line)
                        ->identifier(self::IDENTIFIER)
                        ->build();
                }
            }
        }

        return $errors;
    }
}
