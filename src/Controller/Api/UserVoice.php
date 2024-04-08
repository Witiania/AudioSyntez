<?php

namespace App\Controller\Api;

use App\Entity\Users;
use App\Entity\Voice;
use App\Repository\ListVoicesRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/order', name: 'orders')]
#[OA\Tag(name: 'Orders', description: 'Order API')]

class UserVoice extends AbstractController
{
    #[Route('',name: 'orderUser', methods: ['POST'])]
    public function orderCreate(Request $request, ListVoicesRepository $listVoicesRepository,Security $security, EntityManager $em):JsonResponse
    {
        $data =json_decode($request->getContent(), true);

        $order = (new Voice())
            ->setText($data['text'])
            ->setVoice($listVoicesRepository->findVoice($data['voice']));

        $currentUserID = $security->getUser()->getUserIdentifier();
        $userRep = $em->getRepository(Users::class);
        $user = $userRep->find($currentUserID);
        $order->setUser($user);

        $em->persist($order);
        $em->flush();

//        $wallet = $user->getWallet();
//
//        $balance = $wallet->setBalance($wallet->getBalance() + $order->getFullPrice());

        return $this->json($order);
    }


}