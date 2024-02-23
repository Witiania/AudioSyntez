<?php

namespace App\Tests;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exception\BalanceTransactionException;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Service\BalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class BalanceServiceTest extends TestCase
{
    private MockObject|Security $mockSecurity;
    private MockObject|EntityManagerInterface $mockEntityManager;
    private MockObject|EntityRepository $mockUserRepository;
    private MockObject|Users $user;
    private MockObject|Wallet $wallet;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockSecurity = $this->createMock(Security::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserRepository = $this->createMock(EntityRepository::class);
        $this->wallet = (new Wallet())
            ->setBalance(5000);

        $this->user = (new Users())
            ->setName('alex')
            ->setPhone('123123')
            ->setEmail('pony.123987@yandex.ru')
            ->setPassword('123321')
            ->setWallet($this->wallet)
            ->setId('123087659236');

        $this->mockEntityManager
            ->method('getRepository')
            ->with(Users::class)
            ->willReturn($this->mockUserRepository);
    }

    public static function viewDataProvider(): array
    {
        return [
            // Success
            [100, 100],
            // Success
            [0, 0],
            // Success
            [-100, -100],
        ];
    }

    public static function replenishDataProvider(): array
    {
        return [
            // Success
            [100, 5100],
            // Success
            [0, 5000],
            // Success
            [-100, 4900],
        ];
    }

    /**
     * @dataProvider viewDataProvider
     *
     * @throws UserNotFoundException
     */
    public function testViewSuccess(int $amount, int $expected): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->wallet->setBalance($amount);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->with($this->user->getUserIdentifier())
            ->willReturn($this->user);

        $balance = $mockBalanceService->view();
        self::assertEquals($expected, $balance);
    }

    public function testBalanceViewUserNotFoundException(): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $mockBalanceService->view();
    }

    /**
     * @dataProvider replenishDataProvider
     *
     * @throws BalanceTransactionException
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    public function testReplenishSuccess(int $amount, int $expected): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->willReturn($this->user);

        $this->mockSecurity
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush');

        $mockBalanceService->replenish($amount, 'test');
        self::assertEquals($expected, $this->wallet->getBalance());
    }

    /**
     * @throws BalanceTransactionException
     * @throws IllegalAccessException
     */
    public function testReplenishUserNotFoundException(): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->mockUserRepository
            ->method('find')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $mockBalanceService->replenish(0, $this->user->getId());
    }

    /**
     * @throws BalanceTransactionException
     * @throws UserNotFoundException
     */
    public function testReplenishIllegalAccessException(): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->willReturn($this->user);

        self::expectException(IllegalAccessException::class);
        $mockBalanceService->replenish(1, $this->user->getId());
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     * @throws BalanceTransactionException
     */
    public function testReplenishBalanceTransactionException(): void
    {
        $mockBalanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->willReturn($this->user);

        $this->wallet->setBalance(-1);

        self::expectException(BalanceTransactionException::class);
        $mockBalanceService->replenish(-1, $this->user->getId());
    }
}
