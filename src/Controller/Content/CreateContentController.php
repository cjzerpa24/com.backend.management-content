<?php

namespace App\Controller\Content;

use App\DTO\Content\CreateContentDTO;
use App\Entity\Content;
use App\Repository\ContentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateContentController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly UserRepositoryInterface $userRepository,
        private readonly JWTTokenManagerInterface $tokenManager,
    ) {}

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route(path: '/api/content', name: 'app_content_create', methods: ['POST'])]
    public function action(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $userEmail = $this->tokenManager->decode($tokenStorage->getToken())['email'];
        $user = $this->userRepository->findByEmail($userEmail);

        $dto = $this->serializer->deserialize(
            $request->getContent(),
            CreateContentDTO::class,
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
            $content = (new Content())
                ->setTitle($dto->title)
                ->setDescription($dto->description)
                ->setUser($user);

            $this->contentRepository->save($content);
        } catch (\Exception $e) {
            // If there is an error, return a 500 Internal Server Error response
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // Return a 201 Created response
        return $this->json(['message' => 'Content registered successfully'], 201);
    }
}
