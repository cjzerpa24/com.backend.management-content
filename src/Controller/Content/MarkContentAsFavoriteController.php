<?php

namespace App\Controller\Content;

use App\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MarkContentAsFavoriteController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route(path: '/api/content/{id}/favorite', name: 'app_content_marked_as_favorite', methods: ['POST'])]
    public function action(string $id): JsonResponse
    {
        $content = $this->contentRepository->findById($id);
        if (!$content) {
            return $this->json(['error' => 'Content not found to be marked as favorite'], 404);
        }

        try {
            $content->setIsFavorite(true);

            $this->contentRepository->update($content);
        } catch (\Exception $e) {
            // If there is an error, return a 500 Internal Server Error response
            return $this->json(['error' => $e->getMessage()], 500);
        }

        // Return a 201 Created response
        return $this->json(['message' => 'Content marked as favorite successfully'], 202);
    }
}