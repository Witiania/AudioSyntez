<?php

namespace App\Tests\E2e\AuthController;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordTest extends WebTestCase
{
    public static function resetProvider(): array
    {
        return [
            ['admin@mail.ru', '374445', 'admin@mail.ru111', 200],
            ['admins.ru', '374445', 'admins.ru111', 400],
            ['admins@mail.ru', '374445', 'admins.ru111', 404],
        ];
    }

    #[DataProvider('resetProvider')]
    public function testResetPassword(string $email, string $token, string $password, int $code): void
    {
        $data = [
            'email' => $email,
            'token' => $token,
            'password' => $password,
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }
}
