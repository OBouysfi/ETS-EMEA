# ETS EMEA - Frontend

Application Next.js pour la réservation de sessions de tests de langues.

## Stack

- **Framework**: Next.js 14 (App Router)
- **Language**: TypeScript
- **HTTP Client**: Axios
- **Styling**: CSS modules
- **Tests**: Jest + React Testing Library

## Installation

### Avec Docker
```bash
# À la racine du projet
docker-compose up --build
```

Frontend accessible sur `http://localhost:3003`

### Installation Manuelle
```bash
cd frontend
npm install
npm run dev
```

## Structure
```
frontend/
├── app/
│   ├── login/              # Page connexion/inscription
│   ├── sessions/           # Liste sessions disponibles
│   ├── my-bookings/        # Mes réservations
│   ├── profile/            # Profil utilisateur
│   ├── layout.tsx          # Layout global
│   └── globals.css         # Styles globaux
├── lib/
│   ├── api.ts              # Configuration Axios
│   └── auth.ts             # Gestion JWT
└── __tests__/              # Tests Jest
```

## Fonctionnalités

### Authentification
- Inscription avec nom, email, password
- Connexion JWT
- Token stocké dans localStorage
- Auto-redirect si non authentifié

### Sessions
- Liste paginée (10 par page)
- Filtrage par disponibilité
- Réservation en 1 clic
- Design moderne avec cards

### Réservations
- Liste de mes réservations
- Annulation possible
- Affichage détails session

### Profil
- Modification nom et email
- Feedback succès/erreur

## Configuration

**.env.local**
```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

## Design

- Gradient violet moderne
- Cards avec shadows
- Buttons avec hover effects
- Responsive grid layout
- Navbar sticky
- Badges pour statuts

## Tests
```bash
# Lancer les tests
npm test

# Mode watch
npm run test:watch
```

## Routes

| Route | Description | Auth |
|-------|-------------|------|
| `/` | Redirect auto | Non |
| `/login` | Connexion/Inscription | Non |
| `/sessions` | Liste sessions | Oui |
| `/my-bookings` | Mes réservations | Oui |
| `/profile` | Mon profil | Oui |

## API Calls

Toutes les requêtes passent par `lib/api.ts` avec:
- Base URL configurable
- JWT auto dans headers
- Interceptors pour auth

## Sécurité

- JWT vérifié avant chaque page
- Auto-redirect si token absent
- Ownership vérifié côté backend

## Troubleshooting

**CORS errors:**
```
Vérifier que backend CORS est configuré pour http://localhost:3003
```

**Token expired:**
```
Se reconnecter, JWT expire après 1h
```