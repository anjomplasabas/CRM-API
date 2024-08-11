<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/callback')]
class WebhookController extends AbstractController
{
    const WEBHOOK_TOKEN = 'CGX2OCAFBV';

    #[Route('/facebook-verify', name: 'app_auth_webhook_facebook_verify')]
    public function index(Request $request): JsonResponse
    {
        if (!$request->query->has('hub_mode')) {
            return $this->json([
                'message' => 'Invalid callback request.'
            ], 400);
        }
        if ($request->query->get('hub_token') != self::WEBHOOK_TOKEN) {
            return $this->json([
                'message' => 'Invalid callback token.'
            ], 401);
        }
        return $this->json([
            'challenge' => $request->query->get('hub_challenge')
        ]);
    }
}
