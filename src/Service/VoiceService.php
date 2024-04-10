<?php

namespace App\Service;

use App\Entity\Voices;
use App\Exception\IllegalAccessException;
use App\Exception\UserNotFoundException;
use App\Repository\VoicesRepository;
use Doctrine\ORM\EntityManager;
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
    public function createVoice(string $voice, int $price, string $format, string $gender, string $language): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = (new Voices())
            ->setVoice($voice)
            ->setPrice($price)
            ->setFormat($format)
            ->setGender($gender)
            ->setLanguage($language);

        $this->voicesRepository->save($voice);
        return $voice;
    }

    /**
     * @throws IllegalAccessException|UserNotFoundException
     */
    public function deleteVoice(string $id): void
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = $this->voicesRepository->find($id);
        if (null === $voice) {
            throw new UserNotFoundException();
        }

        $this->voicesRepository->delete($voice);
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    public function getVoice(string $id): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }
        $voice = $this->voicesRepository->find($id);
        if (null === $voice) {
            throw new UserNotFoundException();
        }
        return $voice;
    }

    /**
     * @throws IllegalAccessException
     * @throws UserNotFoundException
     */
    public function updateVoice(string $id, string $nameVoice, int $price, string $format, string $gender, string $language): Voices
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            throw new IllegalAccessException();
        }

        $voice = $this->voicesRepository->find($id);
        if (null === $voice) {
            throw new UserNotFoundException();
        }

        $voice
            ->setVoice($nameVoice)
            ->setPrice($price)
            ->setFormat($format)
            ->setGender($gender)
            ->setLanguage($language);

        $this->voicesRepository->save($voice);
        return $voice;
    }
}