<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Collectors;

use PhoneBurner\SaltLite\Container\ServiceProvider;
use PhoneBurner\SaltLite\Framework\App\App;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\ObjectType;

/**
 * @implements Collector<MethodCall, array{class-string, int}>
 */
class ServiceProviderRegistrationsCollector implements Collector
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array|null
    {
        \assert($node instanceof MethodCall);

        if ($node->name instanceof Identifier && $node->name->toString() !== 'set') {
            return null;
        }

        if (! $scope->getType($node->var)->isSuperTypeOf(new ObjectType(App::class))->yes()) {
            return null;
        }

        $service_provider = $scope->getClassReflection();
        if (! $service_provider?->implementsInterface(ServiceProvider::class)) {
            return null;
        }

        if (! $node->args[0] instanceof Arg) {
            return null;
        }

        $service = $scope->getType($node->args[0]->value);
        if (\method_exists($service, 'getValue')) {
            return [$service->getValue(), $node->getStartLine()];
        }

        return null;
    }
}
