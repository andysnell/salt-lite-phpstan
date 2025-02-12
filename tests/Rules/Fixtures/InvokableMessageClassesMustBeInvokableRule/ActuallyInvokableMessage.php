<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules\Fixtures\InvokableMessageClassesMustBeInvokableRule;

use PhoneBurner\SaltLite\Framework\MessageBus\Message\InvokableMessage;

class ActuallyInvokableMessage implements InvokableMessage
{
    public function __invoke(): self
    {
        return new self();
    }
}
