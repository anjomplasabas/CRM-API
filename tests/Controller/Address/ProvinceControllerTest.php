<?php

namespace App\Tests\Controller\Address;

use App\DataFixtures\AddressFixtures;
use App\Tests\Support\ApiTestCase;

class ProvinceControllerTest extends ApiTestCase
{
    public function testListsProvincesWithoutAuth(): void
    {
        $this->loadFixtures(AddressFixtures::class);

        $this->client->request('GET', '/api/addresses/provinces');

        self::assertResponseIsSuccessful();
        $payload = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('data', $payload);
        self::assertNotEmpty($payload['data']);
        self::assertSame('Metro Manila', $payload['data'][0]['name']);
    }
}
