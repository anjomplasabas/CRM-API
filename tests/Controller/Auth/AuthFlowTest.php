<?php

namespace App\Tests\Controller\Auth;

use App\Tests\Support\ApiTestCase;

class AuthFlowTest extends ApiTestCase
{
    public function testRegisterIssuesJwt(): void
    {
        $this->postJson('/api/auth/register', [
            'roles' => ['ROLE_SELLER'],
            'type' => 'EMAIL',
            'authId' => 'alice@example.com',
            'password' => 'Str0ngP@ssword!',
            'firstName' => 'Alice',
            'lastName' => 'Doe',
            'email' => 'alice@example.com',
            'mobileNumber' => '09171234567',
            'isForgot' => false,
            'status' => 'ACTIVE',
            'pages' => [],
        ]);

        self::assertResponseIsSuccessful();
        $payload = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('data', $payload);
        self::assertArrayHasKey('token', $payload['data']);
        self::assertNotEmpty($payload['data']['token']);
    }

    public function testRegisterRejectsWeakPassword(): void
    {
        $this->postJson('/api/auth/register', [
            'roles' => ['ROLE_SELLER'],
            'type' => 'EMAIL',
            'authId' => 'bob@example.com',
            'password' => '123',
            'firstName' => 'Bob',
            'lastName' => 'Doe',
            'email' => 'bob@example.com',
            'mobileNumber' => '09171234567',
            'isForgot' => false,
            'status' => 'ACTIVE',
            'pages' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testLoginAfterRegistrationReturnsJwt(): void
    {
        $this->postJson('/api/auth/register', [
            'roles' => ['ROLE_SELLER'],
            'type' => 'EMAIL',
            'authId' => 'carol@example.com',
            'password' => 'Str0ngP@ssword!',
            'firstName' => 'Carol',
            'lastName' => 'Doe',
            'email' => 'carol@example.com',
            'mobileNumber' => '09171234567',
            'isForgot' => false,
            'status' => 'ACTIVE',
            'pages' => [],
        ]);
        self::assertResponseIsSuccessful();

        $this->postJson('/api/auth/login', [
            'type' => 'EMAIL',
            'authId' => 'carol@example.com',
            'password' => 'Str0ngP@ssword!',
        ]);

        self::assertResponseIsSuccessful();
        $payload = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($payload['data']['token']);
    }

    public function testLoginFailsOnWrongPassword(): void
    {
        $this->postJson('/api/auth/register', [
            'roles' => ['ROLE_SELLER'],
            'type' => 'EMAIL',
            'authId' => 'dan@example.com',
            'password' => 'Str0ngP@ssword!',
            'firstName' => 'Dan',
            'lastName' => 'Doe',
            'email' => 'dan@example.com',
            'mobileNumber' => '09171234567',
            'isForgot' => false,
            'status' => 'ACTIVE',
            'pages' => [],
        ]);

        $this->postJson('/api/auth/login', [
            'type' => 'EMAIL',
            'authId' => 'dan@example.com',
            'password' => 'wrong',
        ]);

        self::assertResponseStatusCodeSame(401);
    }
}
