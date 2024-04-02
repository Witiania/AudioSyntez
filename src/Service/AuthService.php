<?php

namespace App\Service;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exception\DuplicateException;
use App\Exception\EmailTransactionException;
use App\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    public const VERIFY_EMAIL_SUBJECT = 'Verify email';
    public const RESET_CODE_SUBJECT = 'Reset code';

    private readonly EntityRepository $userRepository;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly LoggerInterface $logger,
        private readonly string $environment,
        private readonly string $email
    ) {
        $this->userRepository = $this->entityManager->getRepository(Users::class);
    }

    /**
     * @throws DuplicateException
     * @throws EmailTransactionException
     * @throws \Exception
     */
    public function register(string $email, string $phone, string $name, string $password): void
    {
        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            throw new DuplicateException();
        }

        $token = (string) random_int(100000, 999999);

        $user = (new Users())
            ->setName($name)
            ->setEmail($email)
            ->setToken($token)
            ->setPhone($phone)
            ->setWallet(new Wallet());

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->persist($user->getWallet());
        $this->entityManager->flush();

        $this->sendEmail($email, self::VERIFY_EMAIL_SUBJECT, $token);
    }

    /**
     * @throws EmailTransactionException
     */
    public function sendEmail(string $email, string $subject, string $token): void
    {
        try {
            if ('prod' !== $this->environment) {
                $this->logger->debug('Email was sent successfully');

                return;
            }

            $email = (new Email())
                ->from($this->email)
                ->to($email)
                ->subject($subject)
                ->text($token);
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
            throw new EmailTransactionException();
        }
    }

    /**
     * @throws UserNotFoundException
     * @throws EmailTransactionException
     * @throws \Exception
     */
    public function sendResetCode(string $email): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $token = (string) random_int(100000, 999999);

        $user->setToken($token);

        $this->entityManager->flush();

        $this->sendEmail($email, self::RESET_CODE_SUBJECT, $token);
    }

    /**
     * @throws UserNotFoundException
     */
    public function resetPassword(string $email, string $token, string $password): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->findOneBy([
            'email' => $email,
            'token' => $token,
        ]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password))->setToken(null);

        $this->entityManager->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function verify(string $email, string $token): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->findOneBy([
            'email' => $email,
            'token' => $token,
        ]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setVerified(true)->setToken(null);

        $this->entityManager->flush();
    }
}
