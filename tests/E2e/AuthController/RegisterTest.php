<?php

namespace App\Tests\E2e\AuthController;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public static function registerProvider(): array
    {
        return [
            ['witiania@mail.ru', '+79999999991', 'witiania', 'witiania@mail.ru123', 200],
            ['admin', '+99999', 'admin', 'admin@mail.ru123', 400],
            ['admin@mail.ru', '+79999999999', 'admin', 'admin@mail.ru123', 409],
            ['witiania@mail.rurbu', '+79999999991', 'witiania', 'witiania@mail.ru123', 500],
        ];
    }

    #[DataProvider('registerProvider')]
    public function testRegister(string $email, string $phone, string $name, string $password, int $code): void
    {
        $client = static::createClient();

        $data = [
            'email' => $email,
            'phone' => $phone,
            'name' => $name,
            'password' => $password,
        ];

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }
}
