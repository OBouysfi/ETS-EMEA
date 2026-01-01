# ETS EMEA - Réservation de Tests de Langues

Application web pour réserver des sessions de tests de langues.

## Technologies

**Backend:** Symfony 7.4 + MongoDB + JWT  
**Frontend:** Next.js 14 + TypeScript  
**Infrastructure:** Docker Compose

## Installation
```bash
git clone https://github.com/OBouysfi/ETS-EMEA
cd ets-emea
docker-compose up --build
```

Accès:
- Frontend: http://localhost:3003
- API: http://localhost:8000/api

## Utilisation

1. Créer un compte sur http://localhost:3003
2. Se connecter
3. Utiliser Postman pour créer des sessions (voir collection fournie)
4. Réserver des sessions via l'interface

## Créer une session (Postman)
```bash
POST http://localhost:8000/api/sessions
Authorization: Bearer {votre_token}

{
  "langue": "English",
  "date": "2026-03-15",
  "heure": "10:00",
  "lieu": "Paris",
  "places": 20
}
```

## API Endpoints

**Auth:**
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion

**User:**
- `GET /api/user/profile` - Profil
- `PUT /api/user/profile` - Modifier profil

**Sessions:**
- `GET /api/sessions` - Liste (paginée)
- `POST /api/sessions` - Créer
- `PUT /api/sessions/{id}` - Modifier
- `DELETE /api/sessions/{id}` - Supprimer

**Bookings:**
- `GET /api/bookings` - Mes réservations
- `POST /api/bookings` - Réserver
- `DELETE /api/bookings/{id}` - Annuler

## Tests
```bash
# Backend
docker-compose exec backend vendor/bin/phpunit

# Frontend
docker-compose exec frontend npm test
```

## Dépannage
```bash
# Reset complet
docker-compose down -v
docker-compose up --build

# Logs
docker-compose logs -f
```