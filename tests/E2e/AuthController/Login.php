<?php

namespace App\Tests\E2e\AuthController;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Login extends WebTestCase
{
    public static function authProvider(): array
    {
        return [
            ['admin@mail.ru', 'admin@mail.ru123', 200],
            ['admin@mail.com', 'admin@mail.ru123', 401],
        ];
    }

    #[DataProvider('authProvider')]
    public function testLogin(string $email, string $password, int $responseCode): void
    {
        $client = static::createClient();

        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($responseCode, $response->getStatusCode());
    }
}
