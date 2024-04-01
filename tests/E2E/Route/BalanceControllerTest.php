<?php

namespace App\Tests\E2E\Route;

use App\Entity\Users;
use App\Tests\E2E\DataFixture\UsersFixture;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BalanceControllerTest extends WebTestCase
{
    /**
     * @return array<int, array<int, int|string>>
     */
    public static function putProvider(): array
    {
        return [
            ['retrieve@trows.ms', 1, Response::HTTP_OK],
            ['retrieve@trows.ms', -2, Response::HTTP_PAYMENT_REQUIRED],
            ['retriev@trows.ms', 1, Response::HTTP_NOT_FOUND],
            ['o', 0, Response::HTTP_BAD_REQUEST],
        ];
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function getProvider(): array
    {
        return [
            ['retrieve@trows.ms', Response::HTTP_OK],
            ['retriev@trows.ms', Response::HTTP_NOT_FOUND],
        ];
    }

    /**
     * @dataProvider putProvider
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testBalancePUT(string $email, int $amount, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        UsersFixture::create($container, 'retrieve@trows.ms', '+79213891239');

        /**
         * @var ManagerRegistry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');

        /**
         * @var Users $user
         */
        $user = $doctrine
            ->getManager()
            ->getRepository(Users::class)
            ->findOneBy(['email' => $email]);

        $uuidExample = '381490ef-21a2-4085-9312-7a72baf1733b';
        $id = null === $user ? $uuidExample : $user->getId();
        $data = ['amount' => $amount, 'id' => $id];

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode($data);

        if (null !== $user) {
            $client->loginUser($user);
        }

        $client->request(
            'PUT',
            '/api/balance',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        UsersFixture::destruct($container, 'retrieve@trows.ms');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider getProvider
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testBalanceGET(string $email, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();

        UsersFixture::create($container, 'retrieve@trows.ms', '+79123818212');

        /**
         * @var ManagerRegistry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');

        /**
         * @var Users $user
         */
        $user = $doctrine
            ->getManager()
            ->getRepository(Users::class)
            ->findOneBy(['email' => $email]);

        if (null !== $user) {
            $client->loginUser($user);
        }

        $client->request(
            'GET',
            '/api/balance',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        UsersFixture::destruct($container, 'retrieve@trows.ms');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }
}
