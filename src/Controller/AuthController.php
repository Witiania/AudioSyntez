<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    #[Route('/reset', name: 'reset', methods: ['POST'])]
    public function reset(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy([
                'email' => $data['email'],
                'token' => $data['token'],
            ]);
        } catch (\Exception) {
            return new JsonResponse([
                'message' => 'It is impossible connect to the Database',
            ], 500);
        }

        if (null === $user) {
            return new JsonResponse(['message' => 'email or token do not match'], 401);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword']));

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception) {
            return new JsonResponse([
                'message' => 'It is impossible to add a new password for this user to the Database',
            ], 500);
        }

        return new JsonResponse(['message' => 'New password added'], 200);
    }

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $postData = json_decode($request->getContent(), true);
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
            ->setToken($postData['token']);

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
