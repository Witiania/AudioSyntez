<?php
//
//namespace App\Controller\Api;
//
//use App\Entity\Orders;
//use App\Entity\Users;
//use App\Repository\VoicesRepository;
//use Doctrine\ORM\EntityManager;
//use OpenApi\Attributes as OA;
//use Symfony\Bundle\SecurityBundle\Security;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Routing\Annotation\Route;
//
//#[Route('/api/order', name: 'orders')]
//#[OA\Tag(name: 'Orders', description: 'Orders API')]
//class OrderController
//{
//    #[Route('/newOrder',name: 'orderUser', methods: ['POST'])]
//    public function orderCreate(Request $request, VoicesRepository $listVoicesRepository,Security $security, EntityManager $em):JsonResponse
//    {
//        $data =json_decode($request->getContent(), true);
//
//        $order = (new Orders())
//            ->setText($data['text'])
//            ->setVoice($listVoicesRepository->findVoice($data['voice']));
//
//        $currentUserID = $security->getUser()->getUserIdentifier();
//        $userRep = $em->getRepository(Users::class);
//        $user = $userRep->find($currentUserID);
//        $order->setUser($user);
//
//        $user = $OrderRepository->findUser($security->getUser()->getUserIdentifier());
//
//        $em->persist($order);
//        $em->flush();
//
//        $wallet = $user->getWallet();
//
//        $balance = $wallet->setBalance($wallet->getBalance() + $order->getFullPrice());
//
//        return $this->json($order);
//    }
//    }
//
//}