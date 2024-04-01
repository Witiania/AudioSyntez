<?php

namespace App\Tests\Unit;

use App\Entity\Users;
use App\Exception\DuplicateException;
use App\Exception\EmailTransactionException;
use App\Exception\UserNotFoundException;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthServiceTest extends TestCase
{
    private MockObject $mockEntityManager;
    private MockObject $mockUserRepository;
    private MockObject $mockMailerInterface;
    private AuthService $authService;
    private Users $user;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserRepository = $this->createMock(EntityRepository::class);
        $this->mockMailerInterface = $this->createMock(MailerInterface::class);
        $this->user = (new Users())->setToken('123123');
        $mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->mockEntityManager
            ->method('getRepository')
            ->with(Users::class)
            ->willReturn($this->mockUserRepository);

        $this->authService = new AuthService(
            $this->mockMailerInterface,
            $this->mockEntityManager,
            $mockPasswordHasher,
            new NullLogger(),
            'dev',
            'test@test.test'
        );
    }

    /**
     * @throws DuplicateException
     * @throws EmailTransactionException
     */
    public function testRegisterSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush');

        $this->authService->register('test@test.test', 'test', 'test', 'test');
    }

    /**
     * @throws EmailTransactionException
     * @throws DuplicateException
     */
    public function testRegisterDuplicateException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        self::expectException(DuplicateException::class);
        $this->authService->register('test@test.test', 'test', 'test', 'test');
    }

    /**
     * @throws EmailTransactionException
     * @throws UserNotFoundException
     */
    public function testSendResetCodeSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->sendResetCode('test@test.test');
    }

    /**
     * @throws UserNotFoundException
     * @throws EmailTransactionException
     */
    public function testSendResetCodeUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->sendResetCode('test@test.test');
    }

    /**
     * @throws UserNotFoundException
     */
    public function testResetPasswordSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->resetPassword('test@test.test', (string) $this->user->getToken(), 'test');
    }

    /**
     * @throws UserNotFoundException
     */
    public function testResetPasswordUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->resetPassword('test@test.test', 'test', 'test');
    }

    /**
     * @throws UserNotFoundException
     */
    public function testVerifySuccess(): void
    {
        $this->mockUserRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockEntityManager
            ->expects(self::once())
            ->method('flush');

        $this->authService->verify('test@test.test', (string) $this->user->getToken());
    }

    /**
     * @throws UserNotFoundException
     */
    public function testVerifyUserNotFoundException(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->verify('test@test.test', 'test');
    }
}
