<?php

namespace App\Service;

use App\Entity\Voices;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Repository\VoicesRepository;
use Symfony\Bundle\SecurityBundle\Security;

class VoiceService
{
    public function __construct(
        private readonly VoicesRepository       $voicesRepository,
        private readonly Security $security
    )
    {
    }

    /**
     * @throws IllegalAccessException
     */
    public function newVoice(string $voice, int $price): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = (new Voices())
            ->setVoice($voice)
            ->setPrice($price);

        $this->voicesRepository->save($voice);
        return $voice;
    }

    /**
     * @throws IllegalAccessException|UserNotFoundException
     */
    public function deleteVoice(string $name): void
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = $this->voicesRepository->findVoice($name);
        if (null === $voice) {
            throw new UserNotFoundException();
        }

        $this->voicesRepository->delete($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    public function showVoice(string $name): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }
        $voice = $this->voicesRepository->findVoice($name);
        if (null === $voice) {
            throw new UserNotFoundException();
        }
        return $voice;
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    public function updateVoice(string $name, ?int $price, ?string $nameOfVoice): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = $this->voicesRepository->findVoice($name);
        if (null === $voice) {
            throw new UserNotFoundException();
        }

        if (null !== $nameOfVoice) {
            $voice->setVoice($nameOfVoice);
        }
        if (null !== $price) {
            $voice->setPrice($price);
        }

        $this->voicesRepository->edit();
        return $voice;
    }
}