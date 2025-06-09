<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules\Fixtures\InvokableMessageClassesMustBeInvokableRule;

use PhoneBurner\SaltLite\MessageBus\Message\InvokableMessage;

class NotInvokableMessage implements InvokableMessage
{
    public function make(): self
    {
        return new self();
    }
}
