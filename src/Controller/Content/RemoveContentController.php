<?php

namespace App\Controller\Content;

use App\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RemoveContentController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
    ) {}

    #[Route(path: 'api/content/{id}', name: 'app_remove_content', methods: ['DELETE'])]
    public function action(int $id): JsonResponse
    {
        $content = $this->contentRepository->findById($id);
        if (!$content) {
            return $this->json(['error' => 'Content not found'], 404);
        }

        try {
            $this->contentRepository->remove($content);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(null, 204);
    }
}
