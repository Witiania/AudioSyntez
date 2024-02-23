<?php

namespace App\DTO;

use App\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @return array<mixed|object|string|null>
     *
     * @throws ValidationFailedException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_subclass_of($argumentType, DTOResolverInterface::class)) {
            return [];
        }

        $requestDTO = $this->serializer->deserialize(
            $request->getContent(),
            $argumentType,
            'json',
            json_decode($request->getContent(), true)
        );

        $errors = $this->validator->validate($requestDTO);
        $errorsAmount = count($errors);
        $errorsString = '';

        if ($errorsAmount > 0) {
            for ($i = 0; $i < $errorsAmount; ++$i) {
                $errorsString .= $errors->get($i)->getMessage().PHP_EOL;
            }

            $errorsString = substr($errorsString, 0, -1);
            throw new ValidationFailedException($errorsString);
        }

        return [$requestDTO];
    }
}
