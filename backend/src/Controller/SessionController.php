<?php

namespace App\Controller;

use App\Document\Session;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Gère les sessions de tests de langues (CRUD complet avec pagination)
 */
#[Route('/api/sessions')]
class SessionController extends AbstractController
{
    public function __construct(
        private DocumentManager $dm,
        private ValidatorInterface $validator
    ) {}

    /**
     * Liste toutes les sessions avec pagination
     * 
     * @param Request $request Query params: page (défaut 1), limit (défaut 10)
     * @return JsonResponse Liste paginée des sessions avec métadonnées
     */
    #[Route('', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Récupération des paramètres de pagination
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $offset = ($page - 1) * $limit;

        // Comptage total pour la pagination
        $qb = $this->dm->createQueryBuilder(Session::class);
        $total = $qb->count()->getQuery()->execute();

        // Récupération des sessions triées par date
        $sessions = $this->dm->getRepository(Session::class)
            ->findBy([], ['date' => 'ASC'], $limit, $offset);

        // Formatage des données
        $data = array_map(fn($s) => [
            'id' => $s->getId(),
            'langue' => $s->getLangue(),
            'date' => $s->getDate()->format('Y-m-d'),
            'heure' => $s->getHeure(),
            'lieu' => $s->getLieu(),
            'places' => $s->getPlaces()
        ], $sessions);

        return $this->json([
            'sessions' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Récupère une session spécifique par son ID
     * 
     * @param string $id Identifiant de la session
     * @return JsonResponse Données de la session ou erreur 404
     */
    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $session = $this->dm->getRepository(Session::class)->find($id);
        
        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        return $this->json([
            'id' => $session->getId(),
            'langue' => $session->getLangue(),
            'date' => $session->getDate()->format('Y-m-d'),
            'heure' => $session->getHeure(),
            'lieu' => $session->getLieu(),
            'places' => $session->getPlaces()
        ]);
    }

    /**
     * Crée une nouvelle session de test
     * 
     * @param Request $request Contient langue, date, heure, lieu, places
     * @return JsonResponse Session créée ou erreurs de validation
     */
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Création de la session
        $session = new Session();
        $session->setLangue($data['langue'] ?? '');
        $session->setDate(new \DateTime($data['date'] ?? 'now'));
        $session->setHeure($data['heure'] ?? '');
        $session->setLieu($data['lieu'] ?? '');
        $session->setPlaces($data['places'] ?? 0);

        // Validation des données
        $errors = $this->validator->validate($session);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        // Sauvegarde en base
        $this->dm->persist($session);
        $this->dm->flush();

        return $this->json([
            'message' => 'Session created successfully',
            'session' => [
                'id' => $session->getId(),
                'langue' => $session->getLangue(),
                'date' => $session->getDate()->format('Y-m-d'),
                'heure' => $session->getHeure(),
                'lieu' => $session->getLieu(),
                'places' => $session->getPlaces()
            ]
        ], 201);
    }

    /**
     * Met à jour une session existante
     * 
     * @param string $id Identifiant de la session
     * @param Request $request Champs à modifier
     * @return JsonResponse Session mise à jour ou erreur
     */
    #[Route('/{id}', methods: ['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $session = $this->dm->getRepository(Session::class)->find($id);
        
        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        // Mise à jour des champs fournis
        if (isset($data['langue'])) $session->setLangue($data['langue']);
        if (isset($data['date'])) $session->setDate(new \DateTime($data['date']));
        if (isset($data['heure'])) $session->setHeure($data['heure']);
        if (isset($data['lieu'])) $session->setLieu($data['lieu']);
        if (isset($data['places'])) $session->setPlaces($data['places']);

        // Validation
        $errors = $this->validator->validate($session);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->dm->flush();

        return $this->json([
            'message' => 'Session updated successfully',
            'session' => [
                'id' => $session->getId(),
                'langue' => $session->getLangue(),
                'date' => $session->getDate()->format('Y-m-d'),
                'heure' => $session->getHeure(),
                'lieu' => $session->getLieu(),
                'places' => $session->getPlaces()
            ]
        ]);
    }

    /**
     * Supprime une session
     * 
     * @param string $id Identifiant de la session
     * @return JsonResponse Confirmation de suppression ou erreur 404
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $session = $this->dm->getRepository(Session::class)->find($id);
        
        if (!$session) {
            return $this->json(['error' => 'Session not found'], 404);
        }

        $this->dm->remove($session);
        $this->dm->flush();

        return $this->json(['message' => 'Session deleted successfully']);
    }
}