<?php

namespace App\Controller\Content;

use App\DTO\Content\RateContentDTO;
use App\Entity\RateContent;
use App\Repository\ContentRepositoryInterface;
use App\Repository\RateContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RateContentController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route(path: '/api/content/{id}/rate', name: 'app_content_rate', methods: ['POST'])]
    public function action(Request $request, string $id): JsonResponse
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            RateContentDTO::class,
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

        $content = $this->contentRepository->findById($id);
        if (!$content) {
            return $this->json(['error' => 'Content not found to be rated'], 404);
        }

        try {
            $content->setRate($dto->rate);

            $this->contentRepository->update($content);
        } catch (\Exception $e) {
            // If there is an error, return a 500 Internal Server Error response
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // Return a 201 Created response
        return $this->json(['message' => 'Content rated successfully'], 202);
    }
}
