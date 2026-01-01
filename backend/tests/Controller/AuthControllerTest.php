<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/auth/register', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'nom' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123'
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('User registered successfully', $response['message']);
    }

    public function testRegisterDuplicateEmail(): void
    {
        $client = static::createClient();
        
        $userData = [
            'nom' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password123'
        ];

        $client->request('POST', '/api/auth/register', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $client->request('POST', '/api/auth/register', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertResponseStatusCodeSame(400);
    }
}