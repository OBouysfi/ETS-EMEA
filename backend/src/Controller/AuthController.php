<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Gère l'authentification des utilisateurs (inscription et connexion)
 */
#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private DocumentManager $dm,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    /**
     * Inscription d'un nouvel utilisateur
     * 
     * @param Request $request Contient nom, email, password
     * @return JsonResponse Message de succès ou erreurs de validation
     */
    #[Route('/register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Création de l'utilisateur
        $user = new User();
        $user->setNom($data['nom'] ?? '');
        $user->setEmail($data['email'] ?? '');
        
        // Validation des données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        // Vérification email unique
        $existingUser = $this->dm->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
        if ($existingUser) {
            return $this->json(['error' => 'Email already exists'], 400);
        }

        // Hash du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password'] ?? '');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        // Sauvegarde en base
        $this->dm->persist($user);
        $this->dm->flush();

        return $this->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail()
            ]
        ], 201);
    }

    /**
     * Connexion utilisateur
     * Gérée automatiquement par LexikJWTAuthenticationBundle
     * 
     * @return JsonResponse Token JWT en cas de succès
     */
    #[Route('/login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return $this->json(['message' => 'Login handled by JWT']);
    }
}