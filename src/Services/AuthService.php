<?php

namespace App\Services;

use App\Entity\Users;
use App\Entity\Wallet;
use App\Exceptions\DuplicatedException;
use App\Exceptions\EmailException;
use App\Exceptions\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
        private readonly string $email,
    ) {
        $this->userRepository = $this->entityManager->getRepository(Users::class);
    }

    /**
     * @param array<string> $data
     *
     * @throws DuplicatedException
     * @throws EmailException
     * @throws \Exception
     */
    public function register(array $data): void
    {
        if (null !== $this->userRepository->findOneBy(['email' => $data['email']])) {
            throw new DuplicatedException();
        }

        $user = (new Users())
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setToken((string) random_int(100000, 999999))
            ->setPhone($data['phone'])
            ->setWallet(new Wallet());

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data['password']));

        $this->entityManager->persist($user);
        $this->entityManager->persist($user->getWallet());
        $this->entityManager->flush();

        $this->sendEmail($data['email'], self::VERIFY_EMAIL_SUBJECT, $user->getToken());
    }

    /**
     * @throws EmailException
     */
    public function sendEmail(string $email, string $subject, string $token): void
    {
        try {
            $email = (new Email())
                ->from($this->email)
                ->to($email)
                ->subject($subject)
                ->text($token);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
            throw new EmailException();
        }
    }

    /**
     * @throws UserNotFoundException
     * @throws EmailException
     * @throws \Exception
     */
    public function sendResetCode(string $email): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setToken((string) random_int(100000, 999999));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendEmail($email, self::RESET_CODE_SUBJECT, $user->getToken());
    }

    /**
     * @throws UserNotFoundException
     */
    public function resetPassword(string $email, string $token, string $newPassword): void
    {
        /** @var Users|null $user */
        $user = $this->userRepository->findOneBy([
            'email' => $email,
            'token' => $token,
        ]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $newPassword));

        $this->entityManager->persist($user);
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

        $user->setVerified(true);

        $this->entityManager->flush();
    }
}
