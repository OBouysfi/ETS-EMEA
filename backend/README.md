# ETS EMEA - Backend API

API REST Symfony avec authentification JWT pour la gestion de réservations de sessions de tests de langues.

## Stack Technique

- **Framework**: Symfony 7.0
- **Base de données**: MongoDB avec Doctrine ODM
- **Authentification**: JWT (LexikJWTAuthenticationBundle)
- **Validation**: Symfony Validator
- **Tests**: PHPUnit

## Architecture
```
backend/
├── src/
│   ├── Controller/        # Controllers API REST
│   ├── Document/          # Entities MongoDB (User, Session, Booking)
│   └── Kernel.php
├── config/
│   ├── packages/          # Configuration bundles
│   └── routes.yaml
├── tests/                 # Tests PHPUnit
├── public/
│   └── index.php
└── composer.json
```

## Installation

### Avec Docker (Recommandé)
```bash
# À la racine du projet
docker-compose up --build
```

L'API sera accessible sur `http://localhost:8000`

### Installation Manuelle
```bash
cd backend
composer install
php bin/console doctrine:mongodb:schema:create
php -S localhost:8000 -t public
```

## Configuration

### Variables d'environnement (.env)
```env
APP_ENV=dev
APP_SECRET=your-secret-key

MONGODB_URL=mongodb://root:root123@mongodb:27017
MONGODB_DB=ets_emea

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-secret-passphrase
```

## API Endpoints

### Authentification

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| POST | `/api/auth/register` | Inscription utilisateur | Non |
| POST | `/api/auth/login` | Connexion (retourne JWT) | Non |

**Exemple Register:**
```json
POST /api/auth/register
{
  "nom": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Exemple Login:**
```json
POST /api/auth/login
{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### Profil Utilisateur

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/api/user/profile` | Récupérer profil | JWT |
| PUT | `/api/user/profile` | Modifier profil | JWT |

**Exemple Update Profile:**
```json
PUT /api/user/profile
Authorization: Bearer {token}
{
  "nom": "John Updated",
  "email": "newemail@example.com"
}
```

### Sessions

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/api/sessions?page=1&limit=10` | Liste paginée | JWT |
| GET | `/api/sessions/{id}` | Détails session | JWT |
| POST | `/api/sessions` | Créer session | JWT |
| PUT | `/api/sessions/{id}` | Modifier session | JWT |
| DELETE | `/api/sessions/{id}` | Supprimer session | JWT |

**Exemple Create Session:**
```json
POST /api/sessions
Authorization: Bearer {token}
{
  "langue": "English",
  "date": "2026-03-15",
  "heure": "14:00",
  "lieu": "Paris",
  "places": 20
}
```

### Réservations

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| GET | `/api/bookings` | Mes réservations | JWT |
| POST | `/api/bookings` | Créer réservation | JWT |
| DELETE | `/api/bookings/{id}` | Annuler réservation | JWT |

**Exemple Create Booking:**
```json
POST /api/bookings
Authorization: Bearer {token}
{
  "session_id": "507f1f77bcf86cd799439011"
}
```

## Règles Métier

- Un utilisateur ne peut réserver qu'**une seule fois** la même session
- Les places sont **décrémentées automatiquement** lors d'une réservation
- Les places sont **réincrémentées** lors d'une annulation
- Impossible de réserver si **places = 0**

## Tests
```bash
# Exécuter tous les tests
docker-compose exec backend vendor/bin/phpunit

# Avec couverture
docker-compose exec backend vendor/bin/phpunit --coverage-html var/coverage
```

## Structure MongoDB

### Collection: users
```json
{
  "_id": "ObjectId",
  "nom": "string",
  "email": "string",
  "password": "string (hashed)",
  "roles": ["ROLE_USER"]
}
```

### Collection: sessions
```json
{
  "_id": "ObjectId",
  "langue": "string",
  "date": "ISODate",
  "heure": "string",
  "lieu": "string",
  "places": "int"
}
```

### Collection: bookings
```json
{
  "_id": "ObjectId",
  "session": "ObjectId (ref)",
  "user": "ObjectId (ref)",
  "createdAt": "ISODate"
}
```

## Sécurité

- Mots de passe hashés avec `bcrypt`
- JWT avec expiration 1h
- CORS configuré pour frontend
- Validation stricte des inputs
- Protection des routes avec `IS_AUTHENTICATED_FULLY`

## Troubleshooting

**Erreur MongoDB connection:**
```bash
# Vérifier que MongoDB est up
docker-compose ps

# Restart MongoDB
docker-compose restart mongodb
```

**Erreur JWT keys:**
```bash
# Regénérer les clés
docker-compose exec backend mkdir -p config/jwt
docker-compose exec backend openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
docker-compose exec backend openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```