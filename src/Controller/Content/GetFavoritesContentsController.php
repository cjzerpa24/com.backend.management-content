<?php

namespace App\Controller\Content;

use App\Repository\ContentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetFavoritesContentsController extends AbstractController
{
    public function __construct(
        private readonly ContentRepositoryInterface $contentRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly JWTTokenManagerInterface $tokenManager,
    ) {}

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route(path: 'api/content/favorites', name: 'app_get_content_favorites', methods: ['GET'])]
    public function action(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $emailUser = $this->tokenManager->decode($tokenStorage->getToken())['email'];
        $user = $this->userRepository->findByEmail($emailUser);
        $criteria['limit'] = $request->query->get('limit');
        $criteria['page'] = $request->query->get('page');
        $criteria['isFavorite'] = true;
        $criteria['user'] = $user;

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
