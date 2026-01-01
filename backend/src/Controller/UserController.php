<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Gère le profil utilisateur (consultation et modification)
 */
#[Route('/api/user')]
class UserController extends AbstractController
{
    public function __construct(
        private DocumentManager $dm,
        private ValidatorInterface $validator
    ) {}

    /**
     * Récupère les informations du profil de l'utilisateur connecté
     * 
     * @return JsonResponse Données du profil (id, nom, email)
     */
    #[Route('/profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail()
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur
     * 
     * @param Request $request Peut contenir nom et/ou email
     * @return JsonResponse Données mises à jour ou erreurs de validation
     */
    #[Route('/profile', methods: ['PUT'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        // Mise à jour des champs fournis
        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        // Validation des nouvelles données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $this->dm->flush();

        return $this->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail()
            ]
        ]);
    }
}