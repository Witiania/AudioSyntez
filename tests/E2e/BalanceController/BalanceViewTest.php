<?php

namespace App\Tests\E2e\BalanceController;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BalanceViewTest extends WebTestCase
{
    public static function showBalanceProvider(): array
    {
        return [
            ['test@mail.ru', 200],
            ['sdvsdvwvc@mail.ru', 404],
        ];
    }

    #[DataProvider('showBalanceProvider')]
    public function testViewBalance(string $email, int $code): void
    {
        $client = static::createClient();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

        if (null !== $user) {
            $client->loginUser($user);
        }

        $client->request('GET', '/api/balance');

        $response = $client->getResponse();

        $this->assertEquals($code, $response->getStatusCode());
    }
}
