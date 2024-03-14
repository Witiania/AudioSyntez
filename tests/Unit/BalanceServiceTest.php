<?php

namespace App\Tests\Unit;

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
    private MockObject $mockSecurity;
    private MockObject $mockEntityManager;
    private MockObject $mockUserRepository;
    private BalanceService $balanceService;
    private Users $user;
    private Wallet $wallet;

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

        $this->balanceService = new BalanceService($this->mockSecurity, $this->mockEntityManager);
    }

    /**
     * @return array<array<int>>
     */
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

    /**
     * @return array<array<int>>
     */
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
        $this->wallet->setBalance($amount);

        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->with($this->user->getUserIdentifier())
            ->willReturn($this->user);

        $balance = $this->balanceService->view();
        self::assertEquals($expected, $balance);
    }

    public function testBalanceViewUserNotFoundException(): void
    {
        $this->mockSecurity
            ->method('getUser')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->balanceService->view();
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

        $this->balanceService->replenish($amount, 'test');
        self::assertEquals($expected, $this->wallet->getBalance());
    }

    /**
     * @throws BalanceTransactionException
     * @throws IllegalAccessException
     */
    public function testReplenishUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('find')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->balanceService->replenish(0, $this->user->getId());
    }

    /**
     * @throws BalanceTransactionException
     * @throws UserNotFoundException
     */
    public function testReplenishIllegalAccessException(): void
    {
        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->willReturn($this->user);

        self::expectException(IllegalAccessException::class);
        $this->balanceService->replenish(1, $this->user->getId());
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     * @throws BalanceTransactionException
     */
    public function testReplenishBalanceTransactionException(): void
    {
        $this->mockSecurity
            ->method('getUser')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->method('find')
            ->willReturn($this->user);

        $this->wallet->setBalance(-1);

        self::expectException(BalanceTransactionException::class);
        $this->balanceService->replenish(-1, $this->user->getId());
    }
}
