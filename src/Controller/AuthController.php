<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly string $email
    )
    {}

    #[Route('/send_verify_code', name: 'send_verify_code', methods: ['POST'])]
    public function sendVerifyCode(MailerInterface $mailer, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['email'])) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 400);
        }

        $emailUser = $data['email'];

        try {
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' => $emailUser]);
        } catch (\Exception) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 500);
        }

        if ($user === null) {
            return new JsonResponse("This user exists in the database", 409);
        }

        try {
            $entityManager->persist($user);
        } catch (\Exception) {
            return new JsonResponse("Failed to record user with data token", 500);
        }

        $email = (new Email())->from($this->email)
            ->to($emailUser)
            ->subject("verify email")
            ->text($user->getToken());

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface) {
            return new JsonResponse(['message' => 'it is not possible to send a message'], 500);
        }

        try {
            $entityManager->flush();
        } catch (\Exception) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 500);
        }

        return new JsonResponse(['message' => 'the key has been sent by email']);
    }

    #[Route('/verify_code', name: 'verify_code', methods: ['POST'])]
    public function verifyCode(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['email'])) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 400);
        }

        try {
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' => $data['email'], 'token' => $data['token']]);
        } catch (\Exception) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 500);
        }

        if ($user !== null) {
            return new JsonResponse(['message' => 'Verify is access!']);
        }

        return new JsonResponse(['message' => 'the key does not match', 409]);
    }

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $postData = json_decode($request->getContent(), true);

        if (empty($data['email'])) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 400);
        }

        $email = $postData['email'];

        try {
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' => $email]);
        } catch (\Exception) {
            return new JsonResponse(['message' => 'It is impossible to connect in Database'], 500);
        }

        if (null !== $user) {
            return new JsonResponse("this user exists in the database", 409);
        }

        $user = (new Users())
            ->setName($postData['name'])
            ->setEmail($email)
            ->setPhone($postData['phone'])
            ->setToken(uniqid());

        $user->setPassword($userPasswordHasher->hashPassword($user, $postData['password']));

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception) {
            return new JsonResponse(['message' => 'It is impossible to add a new user to the Database'], 500);
        }

        return new JsonResponse("Success");
    }
}
