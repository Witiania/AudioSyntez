<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
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
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_subclass_of($argumentType, DTOResolverInterface::class)) {
            return [];
        }

        /** @var DTOResolverInterface $requestDTO */
        $requestDTO = $this->serializer->deserialize(
            $request->getContent(),
            $argumentType,
            'json',
            json_decode($request->getContent(), true));

        $errors = $this->validator->validate($requestDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException('validation failed', $errors);
        }

        return [$requestDTO];
    }
}
