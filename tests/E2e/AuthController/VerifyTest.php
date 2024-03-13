<?php

namespace App\Tests\E2e\AuthController;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VerifyTest extends WebTestCase
{
    public static function verifyProvider(): array
    {
        return [
            ['admin@mail.ru', '374445', 200],
            ['admin', '374400', 400],
            ['admin@mail.ru', '374400', 404],
        ];
    }

    #[DataProvider('verifyProvider')]
    public function testVerify(string $email, string $token, int $code): void
    {
        $client = static::createClient();

        $data = [
            'email' => $email,
            'token' => $token,
        ];

        $client->request(
            'POST',
            '/api/verify',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }
}
