<?php

namespace App\Tests\Controller\Webhook;

use App\Tests\Support\ApiTestCase;

class FacebookControllerTest extends ApiTestCase
{
    public function testVerifyEchoesChallengeOnValidToken(): void
    {
        $this->client->request('GET', '/api/callback/facebook-verify', [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'CGX2OCAFBV',
            'hub_challenge' => '424242',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('424242', $this->client->getResponse()->getContent());
    }

    public function testVerifyRejectsBadToken(): void
    {
        $this->client->request('GET', '/api/callback/facebook-verify', [
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'wrong',
            'hub_challenge' => '424242',
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testVerifyRejectsMissingMode(): void
    {
        $this->client->request('GET', '/api/callback/facebook-verify');
        self::assertResponseStatusCodeSame(400);
    }
}
