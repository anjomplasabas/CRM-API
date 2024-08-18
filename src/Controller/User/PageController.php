<?php

namespace App\Controller\User;

use App\Repository\User\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository
    )
    {}

    #[Route('/pages', name: 'app_pages', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $pageSize = 10,
        #[MapQueryParameter] string|null $searchQuery = null
    ): JsonResponse
    {
        try {
            $pagesQuery = $this->pageRepository->findByUserId(
                $this->getUser()?->getId(),
                $page,
                $pageSize,
                $searchQuery
            );

            return $this->json($pagesQuery);
        } catch (\Exception) {
            return $this->json([
                'message' => 'Oops. Something went wrong'
            ]);
        }
    }
}