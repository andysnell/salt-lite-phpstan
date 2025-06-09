<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Phpstan\Tests\Rules\Fixtures\DeferrableServiceProvidersMustProvideAllServicesRule;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\DeferrableServiceProvider;
use PhoneBurner\SaltLite\Framework\Database\Doctrine\ConnectionProvider;
use PhoneBurner\SaltLite\Framework\Database\Redis\RedisManager;
use PhoneBurner\SaltLite\Framework\MessageBus\Container\MessageBusContainer;
use PhoneBurner\SaltLite\Framework\MessageBus\SymfonyMessageBusAdapter;
use PhoneBurner\SaltLite\Framework\MessageBus\SymfonyRoutableMessageBusAdapter;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory\AmazonSqsTransportFactory;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory\AmqpTransportFactory;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory\DoctrineTransportFactory;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory\RedisTransportFactory;
use PhoneBurner\SaltLite\MessageBus\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

use function PhoneBurner\SaltLite\Framework\ghost;

class FooServiceProvider implements DeferrableServiceProvider
{
    public static function provides(): array
    {
        return [
            MessageBusInterface::class,
            MessageBus::class,
            RoutableMessageBus::class,
            MessageBusContainer::class,
            SymfonyMessageBusAdapter::class,
            SymfonyRoutableMessageBusAdapter::class,
            RedisTransportFactory::class,
            DoctrineTransportFactory::class,
            AmqpTransportFactory::class,
            AmazonSqsTransportFactory::class,
        ];
    }

    public static function bind(): array
    {
        return [
            MessageBusInterface::class => SymfonyMessageBusAdapter::class,
            MessageBus::class => SymfonyMessageBusAdapter::class,
            RoutableMessageBus::class => SymfonyRoutableMessageBusAdapter::class,
        ];
    }

    #[\Override]
    public static function register(App $app): void
    {
        $app->set(
            MessageBusContainer::class,
            static fn(App $app): MessageBusContainer => new MessageBusContainer(\array_map(
                static fn(array $bus): SymfonyMessageBusAdapter => ghost(
                    static fn(SymfonyMessageBusAdapter $ghost): null => $ghost->__construct(
                        \array_map($app->services->get(...), $bus['middleware'] ?: []),
                    ),
                ),
                $app->config->get('message_bus.bus') ?: [],
            )),
        );

        $app->set(
            SymfonyMessageBusAdapter::class,
            static fn(App $app): SymfonyMessageBusAdapter => $app->get(MessageBusContainer::class)->default(),
        );

        $app->set(
            SymfonyRoutableMessageBusAdapter::class,
            static fn(App $app): SymfonyRoutableMessageBusAdapter => new SymfonyRoutableMessageBusAdapter(
                $app->get(MessageBusContainer::class),
                $app->get(MessageBusContainer::class)->default(),
            ),
        );

        $app->set(
            RedisTransportFactory::class,
            static fn(App $app): RedisTransportFactory => new RedisTransportFactory(
                $app->get(RedisManager::class),
                $app->environment,
            ),
        );

        $app->set(
            DoctrineTransportFactory::class,
            static fn(App $app): DoctrineTransportFactory => new DoctrineTransportFactory(
                $app->get(ConnectionProvider::class),
                new PhpSerializer(),
            ),
        );

        $app->set(
            AmqpTransportFactory::class,
            static fn(App $app): AmqpTransportFactory => new AmqpTransportFactory(),
        );

        $app->set(
            AmazonSqsTransportFactory::class,
            static fn(App $app): AmazonSqsTransportFactory => new AmazonSqsTransportFactory(),
        );
    }
}
