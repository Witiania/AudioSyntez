<?php

namespace App\Tests\E2e\BalanceController;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChangeBalanceTest extends WebTestCase
{
    public static function changeProvider(): array
    {
        return [
            ['admin@mail.ru', 20000, 'admin@mail.ru', 200],
            ['admin@mail.ru', -5000, 'admin@mail.ru', 200],
            ['admin@mail.ru', -5000, 'test@mail.ru', 200],
            ['admin@mail.ru', 20000, 'test@mail.ru', 200],
            ['admin@mail.ru', -200000, 'test@mail.ru', 403],
            ['admin@mail.ru', -200000, 'admin@mail.ru', 403],
            ['test@mail.ru', -5000,  'test@mail.ru', 200],
            ['test@mail.ru', 5000,  'test@mail.ru', 403],
        ];
    }

    #[DataProvider('changeProvider')]
    public function testChangeBalanceTest(string $email, int $amount, string $customerEmail, int $code): void
    {
        $client = static::createClient();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

        $customer = $entityManager->getRepository(Users::class)->findOneBy(['email' => $customerEmail]);

        if (null !== $user && null !== $customer) {
            $client->loginUser($user);
            $data = [
                'amount' => $amount,
                'id' => $customer->getId(),
            ];
        }

        $client->request(
            'PUT',
            '/api/balance',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();
        $this->assertSame($code, $response->getStatusCode());
    }
}
