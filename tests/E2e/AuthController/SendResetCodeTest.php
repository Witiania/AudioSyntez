<?php

namespace App\Tests\E2e\AuthController;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendResetCodeTest extends WebTestCase
{
    public static function senResetProvider(): array
    {
        return [
            ['admin@mail.ru', 200],
            ['admins.ru', 400],
            ['admins@mail.ru', 404],
            ];
    }

    #[DataProvider('senResetProvider')]
    public function testSendResetCode(string $email, int $code): void
    {
        $data = ['email' => $email];

        $client = static::createClient();

        $client->request(
            'POST',
            '/api/send_for_reset',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }
}
