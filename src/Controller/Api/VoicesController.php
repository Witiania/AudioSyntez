<?php

namespace App\Controller\Api;

use App\DTO\VoicesEditDTO;
use App\DTO\VoicesRequestDTO;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Repository\VoicesRepository;
use App\Service\VoiceService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/voices', name: 'voices')]
#[OA\Tag(name: 'Orders', description: 'Orders API')]
class VoicesController extends AbstractController
{
    private readonly VoiceService $VoiceService;

    #[Route('', name: 'voiceList', methods: ['GET'])]
    public function listVoices(VoicesRepository $listVoiceRep): JsonResponse
    {
        $voices = $listVoiceRep->allVoices();
        return $this->json($voices);
    }

    /**
     * @throws IllegalAccessException
     */
    #[Route('', name: 'new_voice', methods: ['POST'])]
    public function newVoice(VoicesRequestDTO $requestDTO): JsonResponse
    {

        $voice = $this->VoiceService->newVoice(
            $requestDTO->getVoice(),
            $requestDTO->getPrice()
        );


        return $this->json($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{name}', name: 'delete_voice', methods: ['DELETE'])]
    public function deleteVoice(string $name): JsonResponse
    {
        $this->VoiceService->deleteVoice($name);

        return $this->json('Successfully deleted');
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{name}', name: 'showVoice', methods: ['GET'])]
    public function showVoice($name): JsonResponse
    {
        $voice = $this->VoiceService->showVoice($name);
        return $this->json($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{name}', name: 'editVoice', methods: ['POST'])]
    public function editVoice(string $name, VoicesEditDTO $voiceEdDTO): JsonResponse
    {
        $voice = $this->VoiceService->updateVoice(
            $name,
            $voiceEdDTO->getPrice(),
            $voiceEdDTO->getVoice()
        );
        return $this->json($voice);
    }
}
