<?php

namespace App\Controller\User;

use App\DTO\UpdateUserDTO;
use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly JWTTokenManagerInterface $tokenManager,
    ) {
    }

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/api/user', name: 'app_update_user', methods: ['PUT'])]
    public function action(Request $request, TokenStorageInterface $jwtStorage): JsonResponse
    {
        // Extract from token the user email
        $tokenData = $this->tokenManager->decode($jwtStorage->getToken());
        $email = $tokenData['email'];

        // Search in DB user given email
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        // Deserialize the request body into a DTO
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            UpdateUserDTO::class,
            'json',
            [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ]
        );

        // Validate the DTO
        $errors = $this->validator->validate($dto);

        // If there are errors, return a 400 Bad Request response
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], 400);
        }

        try {
            $user->setName($dto->name)
                ->setEmail($dto->email);
            $this->userRepository->update($user);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['message' => 'User updated successfully'], 202);
    }
}

