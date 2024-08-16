<?php

namespace App\Controller\Content;

use App\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetContentByIdController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
    ) {}

    #[Route(path: 'api/content/{id}', name: 'app_get_content_by_id', requirements: ["id" => "\d+"], methods: ['GET'])]
    public function action(int $id): JsonResponse
    {
        return $this->json($this->contentRepository->findById($id));
    }
}
