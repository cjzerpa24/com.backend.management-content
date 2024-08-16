<?php

namespace App\Controller\Auth;

use App\DTO\LoginDTO;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $tokenManager,
    ) {}

    #[Route(path: 'api/login', name: 'app_login', methods: ['POST'])]
    public function action(Request $request): JsonResponse
    {
        try {
            // Deserialize the request body into a DTO
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                LoginDTO::class,
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

            $user = $this->userRepository->findByEmail($dto->email);
            if (!$user || !$this->passwordHasher->isPasswordValid($user, $dto->password)) {
                return $this->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $this->tokenManager->create($user);

            return $this->json([
                'token' => $token,
                'type' => 'Bearer',
                'expire_in' => $_ENV['JWT_EXPIRE_TIME']
            ], );
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage(), $exception->getCode()]);
        }
    }
}
