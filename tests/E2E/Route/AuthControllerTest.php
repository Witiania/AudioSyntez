<?php

namespace App\Tests\E2E\Route;

use App\Entity\Users;
use App\Tests\E2E\DataFixture\UsersFixture;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setUpBeforeClass(): void
    {
        $client = static::createClient();
        /**
         * @var ManagerRegistry $doctrine
         */
        $doctrine = $client->getContainer()->get('doctrine');
        if (null !== $doctrine
                ->getManager()
                ->getRepository(Users::class)
                ->findOneBy(['email' => 'retrieved@trows.ms'])
        ) {
            UsersFixture::destruct($client->getContainer(), 'retrieved@trows.ms');
        }
    }

    public function setUp(): void
    {
        $this->tearDown();
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function registerProvider(): array
    {
        return [
            ['Anatoliy', 'retrieved@trows.ms', '+79213891238', '!1_Password', Response::HTTP_OK],
            ['Anatoliy', 'retrieved@trows.ms', '+79213891238', '!1_Password', Response::HTTP_CONFLICT],
            ['oo', 'www', '+79213891238', '!1_Password', Response::HTTP_BAD_REQUEST],
        ];
    }

    /**
     * @return array<array<int, int|string>>
     */
    public static function resetPasswordProvider(): array
    {
        return [
            ['retrieved@trows.ms', Response::HTTP_OK],
            ['retrieve@trows.ms', Response::HTTP_NOT_FOUND],
            ['o', Response::HTTP_BAD_REQUEST],
        ];
    }

    /**
     * @return array<array<int, int|string>>
     */
    public static function sendForResetPasswordProvider(): array
    {
        return [
            ['retrieved@trows.ms', Response::HTTP_OK],
            ['retrieve@trows.ms', Response::HTTP_NOT_FOUND],
            ['o', Response::HTTP_BAD_REQUEST],
        ];
    }

    /**
     * @return array<array<int, int|string>>
     */
    public static function verifyProvider(): array
    {
        return [
            ['retrieve@trows.ms', Response::HTTP_OK],
            ['retriev@trows.ms', Response::HTTP_NOT_FOUND],
            ['o', Response::HTTP_BAD_REQUEST],
        ];
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public static function loginProvider(): array
    {
        return [
            ['retrieved@trows.ms', '!1_Password', Response::HTTP_OK],
            ['retrieve@trows.ms', '!1_Password', Response::HTTP_UNAUTHORIZED],
        ];
    }

    /**
     * @dataProvider registerProvider
     */
    public function testRegister(string $name, string $email, string $phone, string $password, int $code): void
    {
        $data = ['name' => $name, 'email' => $email, 'phone' => $phone, 'password' => $password];

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode($data);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider resetPasswordProvider
     */
    public function testResetPassword(string $email, int $code): void
    {
        $client = $this->createClient();

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
            ->findOneBy(['email' => 'retrieved@trows.ms']);

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $user->getEmail()]);

        $client->request(
            'POST',
            '/api/send_for_reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'token' => $user->getToken(), 'password' => '!1_Password']);

        $client->request(
            'POST',
            '/api/reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider sendForResetPasswordProvider
     */
    public function testSendForReset(string $email, int $code): void
    {
        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email]);

        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/send_for_reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider verifyProvider
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testVerify(string $email, int $code): void
    {
        $client = $this->createClient();
        $container = $client->getContainer();
        UsersFixture::create($container, 'retrieve@trows.ms', '+78912398129');

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'token' => '999999']);
        $client->request(
            'POST',
            '/api/verify',
            [],
            [],
            ['CONTENT_TYPE' => 'application_json'],
            $jsonData
        );

        UsersFixture::destruct($client->getContainer(), 'retrieve@trows.ms');
        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider loginProvider
     */
    public function testLogin(string $email, string $password, int $code): void
    {
        $client = $this->createClient();

        /**
         * @var string|null $jsonData
         */
        $jsonData = json_encode(['email' => $email, 'password' => $password]);
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $this->assertResponseStatusCodeSame($code, (string) $client->getResponse()->getStatusCode());
    }
}
