<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    private string $token;

    protected function setUp(): void
    {
        $client = static::createClient();
        
        // Create user
        $client->request('POST', '/api/auth/register', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'nom' => 'Booking Test',
                'email' => 'booking@test.com',
                'password' => 'password123'
            ])
        );

        // Login
        $client->request('POST', '/api/auth/login', [], [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'booking@test.com',
                'password' => 'password123'
            ])
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->token = $response['token'];
    }

    public function testCreateBookingSuccess(): void
    {
        $client = static::createClient();
        
        // Create session first
        $client->request('POST', '/api/sessions', [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode([
                'langue' => 'English',
                'date' => '2026-02-01',
                'heure' => '10:00',
                'lieu' => 'Paris',
                'places' => 10
            ])
        );

        $sessionResponse = json_decode($client->getResponse()->getContent(), true);
        $sessionId = $sessionResponse['session']['id'];

        // Create booking
        $client->request('POST', '/api/bookings', [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode(['session_id' => $sessionId])
        );

        $this->assertResponseStatusCodeSame(201);
    }

    public function testCannotBookTwice(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/api/sessions', [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode([
                'langue' => 'French',
                'date' => '2026-02-15',
                'heure' => '14:00',
                'lieu' => 'Lyon',
                'places' => 5
            ])
        );

        $sessionResponse = json_decode($client->getResponse()->getContent(), true);
        $sessionId = $sessionResponse['session']['id'];

        // First booking
        $client->request('POST', '/api/bookings', [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode(['session_id' => $sessionId])
        );

        // Second booking (should fail)
        $client->request('POST', '/api/bookings', [], [], 
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode(['session_id' => $sessionId])
        );

        $this->assertResponseStatusCodeSame(400);
    }
}