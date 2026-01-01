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
  "lieu": "Casablanca",
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

## Importer les données de démonstration

Le projet inclut un dump MongoDB avec des données de test (utilisateurs, sessions, réservations).

**Pour importer:**
```bash
# Décompresser le fichier
unzip mongodb_dump.zip

# Copier dans le container MongoDB
docker cp mongodb_dump ets_mongodb:/dump

# Importer les données
docker-compose exec mongodb mongorestore --username=root --password=root123 --authenticationDatabase=admin /dump
```

**Données incluses:**
- 1 utilisateur de test
- 3 sessions de test (English, Arabe, Français)
- 1 réservation exemple

Vous pouvez aussi créer vos propres données via Postman (voir section Utilisation).

## Dépannage
```bash
# Reset complet
docker-compose down -v
docker-compose up --build

# Logs
docker-compose logs -f
```
<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/3f486290-4bc4-4b2d-bec4-22f038eaffb9" />
<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/4b592975-7bf1-47c1-a771-3d3aec4c1c40" />
<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/c191dd59-b8a3-47c2-89d3-298a7aa04248" />
<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/cf82a02d-6989-4112-a364-3638469499f6" />
<img width="1920" height="869" alt="image" src="https://github.com/user-attachments/assets/8c923395-4aaf-40a4-85e4-d14fadac2f28" />






