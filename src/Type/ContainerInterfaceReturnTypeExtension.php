<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Psr\Container\ContainerInterface;

class ContainerInterfaceReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    #[\Override]
    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    #[\Override]
    public function isMethodSupported(MethodReflection $method_reflection): bool
    {
        return $method_reflection->getName() === 'get';
    }

    #[\Override]
    public function getTypeFromMethodCall(
        MethodReflection $method_reflection,
        MethodCall $method_call,
        Scope $scope,
    ): Type|null {
        if ($method_call->getArgs() === []) {
            return null;
        }

        $type = $scope->getType($method_call->getArgs()[0]->value)->getObjectTypeOrClassStringObjectType();

        return $type->isObject()->yes() ? $type : null;
    }
}
