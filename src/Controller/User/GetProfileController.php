<?php

namespace App\Controller\User;

use App\DTO\User\GetProfileDTO;
use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly JWTTokenManagerInterface $tokenManager,
    ) {}

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route(path: 'api/user', name: 'app_get_profile_user', methods: ['GET'])]
    public function action(TokenStorageInterface $jwtStorage): JsonResponse
    {
        // Extract from token the user email
        $tokenData = $this->tokenManager->decode($jwtStorage->getToken());
        $email = $tokenData['email'];

        // Search in DB user given email
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return $this->json('User not found', 404);
        }

        $dto = new GetProfileDTO(
            email: $user->getEmail(),
            name: $user->getName(),
            roles: $user->getRoles()
        );

        // Response with the data mapped
        return $this->json($dto);
    }
}