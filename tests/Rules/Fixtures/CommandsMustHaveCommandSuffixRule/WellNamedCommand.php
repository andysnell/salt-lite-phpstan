<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules\Fixtures\CommandsMustHaveCommandSuffixRule;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand('baz:qux', description: 'A well named command')]
class WellNamedCommand extends Command
{
}