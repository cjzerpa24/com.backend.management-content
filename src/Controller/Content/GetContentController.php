<?php

namespace App\Controller\Content;

use App\Repository\ContentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GetContentController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
    ) {}

    #[Route(path: 'api/content', name: 'app_get_all_content', methods: ['GET'])]
    public function action(Request $request): JsonResponse
    {
        $criteria['limit'] = $request->query->get('limit');
        $criteria['page'] = $request->query->get('page');
        $criteria['title'] = $request->query->get('title');
        $criteria['description'] = $request->query->get('description');

        $contents = $this->contentRepository->findByCriteria($criteria);
        if (empty($contents['data'])) {
            return $this->json(['total' => 0, 'data' => []]);
        }

        return $this->json([
            'total' => $contents['total'],
            'data' => $contents['data'],
        ]);
    }
}
