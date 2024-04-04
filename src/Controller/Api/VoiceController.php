<?php

namespace App\Controller\Api;

use App\Entity\Voice;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/voices', name: 'voices')]
#[OA\Tag(name: 'Voice', description: 'Voice API')]
class VoiceController extends AbstractController
{
    #[Route('/list', name: 'voice', methods: ['GET'])]
    public function listVoices(EntityManagerInterface $entityManager): JsonResponse
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('v')
            ->from(Voice::class, 'v')
            ->setMaxResults(50);

        $query = $qb->getQuery();
        $voices = $query->getResult();

        return $this->json($voices);
    }

    #[Route('/new', name: 'new_voice', methods: ['POST'])]
    public function newVoice(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $voice = (new Voice())
            ->setName($data['name'])
            ->setPrice($data['price']);

        $entityManager->persist($voice);
        $entityManager->flush();

        return $this->json($voice);
    }
}
