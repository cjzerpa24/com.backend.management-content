<?php

namespace App\Controller\User;

use App\DTO\User\RegisterUserDTO;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/register', name: 'app_register_user', methods: ['POST'])]
    public function action(Request $request): JsonResponse
    {
        // Deserialize the request body into a DTO
        $dto = $this->serializer->deserialize(
            $request->getContent(), 
            RegisterUserDTO::class, 
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
            $userExists = $this->userRepository->findByEmail($dto->email);
            if ($userExists) {
                throw new \Exception('User already registered', 400);
            }
            // Create a new user object
            $user = new User();
            $user->setEmail($dto->email);
            $user->setName($dto->name);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));
            $user->setRoles(['ROLE_USER']);
            // Save the user to the database
            $this->userRepository->save($user);
        } catch (\Exception $e) {
            // If there is an error, return a 500 Internal Server Error response
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // Return a 201 Created response
        return $this->json(['message' => 'User registered successfully'], 201);
    }
}
