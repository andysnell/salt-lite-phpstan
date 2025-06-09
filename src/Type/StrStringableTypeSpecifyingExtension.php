<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Type;

use PhoneBurner\SaltLite\String\Str;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;

/**
 * @link https://phpstan.org/developing-extensions/type-specifying-extensions
 */
class StrStringableTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private TypeSpecifier|null $type_specifier = null;

    #[\Override]
    public function setTypeSpecifier(TypeSpecifier $type_specifier): void
    {
        $this->type_specifier = $type_specifier;
    }

    #[\Override]
    public function getClass(): string
    {
        return Str::class;
    }

    #[\Override]
    public function isStaticMethodSupported(
        MethodReflection $method_reflection,
        StaticCall $node,
        TypeSpecifierContext $context,
    ): bool {
        return $method_reflection->getName() === 'stringable' && $context->true() && isset($node->getArgs()[0]);
    }

    #[\Override]
    public function specifyTypes(
        MethodReflection $method_reflection,
        StaticCall $node,
        Scope $scope,
        TypeSpecifierContext $context,
    ): SpecifiedTypes {
        return $this->type_specifier?->create(
            $node->getArgs()[0]->value,
            new UnionType([new StringType(), new ObjectType(\Stringable::class)]),
            TypeSpecifierContext::createTruthy(),
            $scope,
        ) ?? throw new \LogicException('TypeSpecifier is not set');
    }
}
