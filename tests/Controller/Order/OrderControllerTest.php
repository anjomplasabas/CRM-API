<?php

namespace App\Tests\Controller\Order;

use App\Entity\User\Page;
use App\Entity\User\User;
use App\Tests\Support\ApiTestCase;

class OrderControllerTest extends ApiTestCase
{
    public function testStoreCreatesOrder(): void
    {
        $em = $this->em();
        $owner = new User(type: 'EMAIL', roles: ['ROLE_SELLER'], authId: 'owner@example.com', status: 'ACTIVE');
        $em->persist($owner);
        $em->flush();

        $page = new Page(
            userId: $owner->getId(),
            facebookPageId: 'fb_page_1',
            name: 'My Shop',
            facebookPageAccessToken: 'token',
            metadata: null,
        );
        $em->persist($page);
        $em->flush();

        $this->postJson('/api/order/store', [
            'userId' => $owner->getId(),
            'pageId' => 'fb_page_1',
            'facebookProfileId' => 'cust_1',
            'fullName' => 'Juan Dela Cruz',
            'mobileNumber' => '09171234567',
            'email' => 'juan@example.com',
            'addressLine' => '123 Main St',
            'region' => 'NCR',
            'province' => 'Metro Manila',
            'city' => 'Quezon City',
            'barangay' => 'Diliman',
            'zipCode' => '1100',
            'totalAmount' => 150.50,
            'metadata' => null,
        ]);

        self::assertResponseIsSuccessful();
        $payload = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotNull($payload['data']);
    }

    public function testStoreRejectsMissingFields(): void
    {
        $this->postJson('/api/order/store', [
            'userId' => 1,
            'pageId' => 'fb_page_1',
            'facebookProfileId' => 'cust_1',
            'fullName' => '',
            'mobileNumber' => '',
            'email' => 'not-an-email',
            'addressLine' => '',
            'region' => null,
            'province' => '',
            'city' => '',
            'barangay' => '',
            'zipCode' => '',
            'totalAmount' => null,
            'metadata' => null,
        ]);

        self::assertResponseStatusCodeSame(422);
    }
}
