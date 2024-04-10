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
    public function list(VoicesRepository $listVoiceRep): JsonResponse
    {
        return $this->json($listVoiceRep->findAll());
    }

    /**
     * @throws IllegalAccessException
     */
    #[Route('', name: 'create_voice', methods: ['POST'])]
    public function create(VoicesRequestDTO $requestDTO): JsonResponse
    {
        $voice = $this->VoiceService->createVoice(
            $requestDTO->getVoice(),
            $requestDTO->getPrice(),
            $requestDTO->getFormat(),
            $requestDTO->getGender(),
            $requestDTO->getLanguage()
        );

        return $this->json($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{id}', name: 'delete_voice', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->VoiceService->deleteVoice($id);

        return $this->json('Successfully deleted');
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{id}', name: 'getVoice', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $voice = $this->VoiceService->getVoice($id);
        return $this->json($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    #[Route('/{id}', name: 'editVoice', methods: ['PUT'])]
    public function update(string $id, VoicesRequestDTO $voiceEdDTO): JsonResponse
    {
        $voice = $this->VoiceService->updateVoice(
            $id,
            $voiceEdDTO->getVoice(),
            $voiceEdDTO->getPrice(),
            $voiceEdDTO->getFormat(),
            $voiceEdDTO->getGender(),
            $voiceEdDTO->getLanguage()

        );
        return $this->json($voice);
    }
}
