<?php

namespace App\Tests\Controller\User;

use App\Entity\User\Profile;
use App\Entity\User\User;
use App\Tests\Support\ApiTestCase;

class ProfileControllerTest extends ApiTestCase
{
    public function testGetProfileRequiresAuth(): void
    {
        $this->client->request('GET', '/api/profile');
        self::assertResponseStatusCodeSame(401);
    }

    public function testGetProfileReturnsAuthenticatedUserProfile(): void
    {
        $em = $this->em();
        $user = new User(
            type: 'EMAIL',
            roles: ['ROLE_SELLER'],
            authId: 'profile@example.com',
            status: 'ACTIVE',
        );
        $em->persist($user);
        $em->flush();

        $profile = new Profile(
            userId: $user->getId(),
            firstName: 'Pro',
            lastName: 'File',
            email: 'profile@example.com',
            mobileNumber: '09171234567',
            metadata: null,
            user: $user,
        );
        $em->persist($profile);
        $em->flush();

        $this->authenticate($user);
        $this->client->request('GET', '/api/profile');

        self::assertResponseIsSuccessful();
        $payload = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('Pro', $payload['data']['firstName']);
    }
}
