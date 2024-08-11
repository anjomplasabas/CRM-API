<?php

namespace App\Controller\Auth;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/callback')]
class WebhookController extends AbstractController
{
    const WEBHOOK_TOKEN = 'CGX2OCAFBV';

    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/facebook-verify', name: 'app_auth_webhook_facebook_verify', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $this->logger->info($request);
        if (!$request->query->has('hub_mode')) {
            return $this->json([
                'message' => 'Invalid callback request.'
            ], 400);
        }
        if ($request->query->get('hub_verify_token') != self::WEBHOOK_TOKEN) {
            return $this->json([
                'message' => 'Invalid callback token.'
            ], 401);
        }
        return $this->json(
            intval($request->query->get('hub_challenge'))
        );
    }

    #[Route('/facebook-verify', name: 'app_auth_webhook_facebook_callback', methods: ['POST'])]
    public function subscribe(Request $request)
    {
        return $this->json(
            $request->toArray()
        );
    }
}
