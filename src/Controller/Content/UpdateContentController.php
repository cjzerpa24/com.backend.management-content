<?php

namespace App\Controller\Content;

use App\DTO\Content\UpdateContentDTO;
use App\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateContentController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route(path: '/api/content/{id}', name: 'app_content_update', methods: ['PUT'])]
    public function action(Request $request, int $id): JsonResponse
    {
        $content = $this->contentRepository->findById($id);
        if (!$content) {
            return $this->json(['error' => 'Content not found'], 404);
        }

        $dto = $this->serializer->deserialize(
            $request->getContent(),
            UpdateContentDTO::class,
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
            $content->setTitle($dto->title)
                ->setDescription($dto->description);

            $this->contentRepository->update($content);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['message' => 'Content updated successfully'], 202);
    }
}