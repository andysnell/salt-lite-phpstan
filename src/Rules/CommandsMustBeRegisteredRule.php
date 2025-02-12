<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Rules;

use PhoneBurner\SaltLite\Framework\Console\ConsoleServiceProvider;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\Console\Command\Command;

use const PhoneBurner\SaltLite\Framework\APP_ROOT;

/**
 * @implements Rule<InClassNode>
 */
class CommandsMustBeRegisteredRule implements Rule
{
    public const string IDENTIFIER = 'saltlite.commandRegistration';

    public const string MESSAGE = 'Command Not Registered in "config/commands.php"';

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

        // Is the class registered in the command configuration or hardcoded by the framework?
        if (\in_array($class->getName(), $this->getRegisteredCommands(), true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(self::MESSAGE)->identifier(self::IDENTIFIER)->build(),
        ];
    }

    public function getRegisteredCommands(): array
    {
        static $registered_commands = (static function () {
            $configuration = include APP_ROOT . '/config/console.php';
            $application_commands = $configuration['container']['commands'] ?? [];
            return [...$application_commands, ...ConsoleServiceProvider::FRAMEWORK_COMMANDS];
        })();

        return $registered_commands;
    }
}
