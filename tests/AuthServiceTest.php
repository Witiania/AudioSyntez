<?php

namespace App\Tests;

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
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthServiceTest extends TestCase
{
    private MockObject|EntityManagerInterface $mockEntityManager;
    private MockObject|EntityRepository $mockUserRepository;
    private MockObject|UserPasswordHasherInterface $mockPasswordHasher;
    private MockObject|MailerInterface $mockMailerInterface;
    private Users $user;
    private AuthService $authService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserRepository = $this->createMock(EntityRepository::class);
        $this->mockMailerInterface = $this->createMock(MailerInterface::class);
        $this->mockPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->user = new Users();

        $this->mockEntityManager
            ->method('getRepository')
            ->with(Users::class)
            ->willReturn($this->mockUserRepository);

        $this->authService = new AuthService(
            $this->mockMailerInterface,
            $this->mockEntityManager,
            $this->mockPasswordHasher,
            '123@123.123');
    }

    /**
     * @throws EmailTransactionException
     * @throws Exception
     */
    public function testRegisterDuplicatedException()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        self::expectException(DuplicateException::class);
        $this->authService->register('123@123.123', '123123123', 'alex', '321123123');
    }

    public function testRegisterEmailException()
    {
        $this->mockMailerInterface
            ->method('send')
            ->willThrowException(new TransportException());

        self::expectException(EmailTransactionException::class);
        $this->authService->sendEmail('123@123.123', 'test', 'test');
    }

    /**
     * @throws DuplicateException
     * @throws EmailTransactionException
     * @throws Exception
     */
    public function testRegisterSuccess(): void
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush');

        $this->mockMailerInterface
            ->expects($this->once())
            ->method('send');

        $this->authService->register('123@123.123', '123123123', 'alex', '321123123');
    }

    /**
     * @throws EmailTransactionException
     */
    public function testSendEmailSuccess(): void
    {
        $this->mockMailerInterface
            ->expects($this->once())
            ->method('send');

        $this->authService->sendEmail('test@test.test', 'test', 'test');
    }

    public function testSendEmailEmailException(): void
    {
        $this->mockMailerInterface
            ->method('send')->willThrowException(new TransportException());

        self::expectException(EmailTransactionException::class);
        $this->authService->sendEmail('test@test.test', 'test', 'test');
    }

    /**
     * @throws Exception
     * @throws EmailTransactionException
     */
    public function testSendResetCodeUserNotFoundException()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->sendResetCode('123@123.123');
    }

    /**
     * @throws Exception
     * @throws EmailTransactionException
     * @throws UserNotFoundException
     */
    public function testSendResetCodeSuccess()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository->expects(self::once())->method('findOneBy')->willReturn($this->user);
        $this->mockEntityManager->expects(self::once())->method('flush');
        $this->mockMailerInterface->expects(self::once())->method('send');

        $this->authService->sendResetCode('123@123.123');
    }

    /**
     * @throws Exception
     */
    public function testResetPasswordUserNotFoundException()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->resetPassword('123@123.123', '123123', '123123');
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function testResetPasswordSuccess()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository->expects(self::once())->method('findOneBy')->willReturn($this->user);
        $this->mockEntityManager->expects(self::once())->method('flush');

        $this->authService->resetPassword('123@123.123', '123123', '123123');
    }

    /**
     * @throws Exception
     */
    public function testVerifyUserNotFoundException()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn(null);

        self::expectException(UserNotFoundException::class);
        $this->authService->verify('123@123.123', '123123');
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function testVerifySuccess()
    {
        $this->mockUserRepository
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->mockUserRepository->expects(self::once())->method('findOneBy')->willReturn($this->user);
        $this->mockEntityManager->expects(self::once())->method('flush');

        $this->authService->verify('123@123.123', '123123');
    }
}
