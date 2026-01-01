<?php

namespace App\Controller;

use App\Document\Booking;
use App\Document\Session;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gère les réservations utilisateur (création, consultation, annulation)
 */
#[Route('/api/bookings')]
class BookingController extends AbstractController
{
    public function __construct(private DocumentManager $dm) {}

    /**
     * Récupère toutes les réservations de l'utilisateur connecté
     * 
     * @return JsonResponse Liste des réservations avec détails des sessions
     */
    #[Route('', methods: ['GET'])]
    public function myBookings(): JsonResponse
    {
        $user = $this->getUser();
        
        // Récupération des réservations de l'utilisateur
        $bookings = $this->dm->getRepository(Booking::class)->findBy(['user' => $user]);

        // Formatage avec données de session incluses
        $data = array_map(function($b) {
            $session = $b->getSession();
            return [
                'id' => $b->getId(),
                'createdAt' => $b->getCreatedAt()->format('Y-m-d H:i:s'),
                'session' => [
                    'id' => $session->getId(),
                    'langue' => $session->getLangue(),
                    'date' => $session->getDate()->format('Y-m-d'),
                    'heure' => $session->getHeure(),
                    'lieu' => $session->getLieu()
                ]
            ];
        }, $bookings);

        return $this->json(['bookings' => $data]);
    }

    /**
     * Crée une nouvelle réservation pour une session
     * 
     * Règles métier:
     * - Vérifie places disponibles
     * - Empêche double réservation (1 user = 1 booking par session)
     * - Décrémente automatiquement le nombre de places
     * 
     * @param Request $request Contient session_id
     * @return JsonResponse Réservation créée ou erreur métier
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $sessionId = $data['session_id'] ?? null;

        if (!$sessionId) {
            return $this->json(['error' => 'session_id required'], 400);
        }

        // Vérification existence session
        $session = $this->dm->getRepository(Session::class)->find($sessionId);
        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        // Vérification places disponibles
        if (!$session->hasAvailablePlaces()) {
            return $this->json(['error' => 'No available places'], 400);
        }

        // Vérification pas déjà réservé (règle métier)
        $existingBooking = $this->dm->getRepository(Booking::class)
            ->findOneBy(['user' => $user, 'session' => $session]);

        if ($existingBooking) {
            return $this->json(['error' => 'Already booked this session'], 400);
        }

        // Création réservation
        $booking = new Booking();
        $booking->setUser($user);
        $booking->setSession($session);

        // Décrément places disponibles
        $session->decrementPlaces();

        $this->dm->persist($booking);
        $this->dm->flush();

        return $this->json([
            'message' => 'Booking created successfully',
            'booking' => [
                'id' => $booking->getId(),
                'createdAt' => $booking->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ], 201);
    }

    /**
     * Annule une réservation
     * 
     * Vérifie que l'utilisateur est propriétaire de la réservation
     * Réincrémente automatiquement le nombre de places
     * 
     * @param string $id Identifiant de la réservation
     * @return JsonResponse Confirmation ou erreur 403/404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function cancel(string $id): JsonResponse
    {
        $user = $this->getUser();
        $booking = $this->dm->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return $this->json(['error' => 'Booking not found'], 404);
        }

        // Vérification ownership (sécurité)
        if ($booking->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        // Réincrément places disponibles
        $session = $booking->getSession();
        $session->incrementPlaces();

        $this->dm->remove($booking);
        $this->dm->flush();

        return $this->json(['message' => 'Booking cancelled successfully']);
    }
}