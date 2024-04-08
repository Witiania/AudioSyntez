<?php

namespace App\Controller\Api;

use App\Entity\ListVoices;
use App\Repository\ListVoicesRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/voices', name: 'voices')]
#[OA\Tag(name: 'Voice', description: 'Voice API')]
class VoiceListController extends AbstractController
{
    #[Route('', name: 'voiceList', methods: ['GET'])]
    public function listVoices(ListVoicesRepository $listVoiceRep): JsonResponse
    {
       $voices = $listVoiceRep->allVoices();
        return $this->json($voices);
    }

    #[Route('', name: 'new_voice', methods: ['POST'])]
    public function newVoice(Request $request, ListVoicesRepository $listVoiceRep): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $voice = (new ListVoices())
            ->setVoice($data['voice'])
            ->setPrice($data['price']);

        $listVoiceRep->save($voice);
        return $this->json($voice);
    }

    #[Route('/{name}', name: 'delete_voice', methods: ['DELETE'])]
    public function deleteVoice($name, ListVoicesRepository $listVoiceRep): JsonResponse
    {
        $listVoiceRep->delete($listVoiceRep->findVoice($name));
        return $this->json('successfully deleted', 204);
    }

    #[Route('/{name}', name: 'showVoice', methods: ['GET'])]
    public function showVoice($name, ListVoicesRepository $listVoiceRep): JsonResponse
    {
        $voice = $listVoiceRep->findVoice($name);
        return $this->json($voice);
    }

    #[Route('/{name}', name: 'editVoice', methods: ['POST'])]
    public function editVoice(string $name, Request $request, ListVoicesRepository $listVoiceRep): JsonResponse
    {
        $voice = $listVoiceRep->findVoice($name);

        $data = json_decode($request->getContent(),true);

        if (isset($data['voice'])) {
            $voice->setVoice($data['voice']);
        }

        if (isset($data['price'])) {
            $voice->setPrice($data['price']);
        }

        $listVoiceRep->edit();

        return $this->json($voice);
    }
}
