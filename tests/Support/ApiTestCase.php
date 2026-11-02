<?php

namespace App\Tests\Support;

use App\Entity\User\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
    }

    protected function em(): EntityManagerInterface
    {
        return $this->container->get(EntityManagerInterface::class);
    }

    protected function loadFixtures(string ...$classes): void
    {
        $em = $this->em();
        $fixtures = [];
        foreach ($classes as $class) {
            $instance = new $class();
            if (!$instance instanceof FixtureInterface) {
                throw new \InvalidArgumentException("$class must implement FixtureInterface and have a no-arg constructor");
            }
            $fixtures[] = $instance;
        }
        $executor = new ORMExecutor($em, new ORMPurger($em));
        $executor->execute($fixtures, append: true);
    }

    protected function authenticate(User $user): void
    {
        $jwt = $this->container->get(JWTTokenManagerInterface::class)->create($user);
        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer '.$jwt);
    }

    protected function postJson(string $uri, array $body): void
    {
        $this->client->request(
            'POST',
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body, JSON_THROW_ON_ERROR),
        );
    }

    protected function putJson(string $uri, array $body): void
    {
        $this->client->request(
            'PUT',
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($body, JSON_THROW_ON_ERROR),
        );
    }
}
