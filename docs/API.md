# API Reference

## Base URL

```
http://localhost/CRL/backend/public
```

## Format de Réponse

### Succès (200)

```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

### Erreur

```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

## Endpoints

### Authentification

#### POST `/auth/login`

Connexion utilisateur

```json
Request:
{
    "email": "user@example.com",
    "password": "password123"
}

Response:
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "admin"
    }
}
```

#### POST `/auth/register`

Créer un nouvel utilisateur

```json
Request:
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123",
    "role": "student"
}

Response:
{
    "success": true,
    "data": { "id": 2, "name": "Jane Doe" }
}
```

#### GET `/auth/me`

Récupérer l'utilisateur courant

```
Header: Authorization: Bearer <session_token>

Response:
{
    "success": true,
    "data": { ... }
}
```

#### POST `/auth/logout`

Déconnexion

```
Header: Authorization: Bearer <session_token>

Response:
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Utilisateurs

#### GET `/user`

Lister les utilisateurs

```
Response:
{
    "success": true,
    "data": [
        { "id": 1, "name": "Admin", "email": "admin@example.com", "role": "admin" },
        { "id": 2, "name": "Teacher", "email": "teacher@example.com", "role": "teacher" }
    ]
}
```

#### GET `/user/{id}`

Détails d'un utilisateur

#### POST `/user`

Créer un utilisateur (Admin only)

#### PUT `/user/{id}`

Modifier un utilisateur

#### DELETE `/user/{id}`

Supprimer un utilisateur

### Classes

#### GET `/class`

Lister les classes

#### POST `/class`

Créer une classe

```json
Request:
{
    "name": "Classe A",
    "level": "6th",
    "capacity": 30
}
```

#### GET `/class/{id}`

Détails d'une classe

#### PUT `/class/{id}`

Modifier une classe

#### DELETE `/class/{id}`

Supprimer une classe

### Étudiants

#### GET `/student`

Lister les étudiants

#### POST `/student`

Créer un étudiant

```json
Request:
{
    "name": "Student Name",
    "email": "student@example.com",
    "class_id": 1,
    "enrollment_date": "2024-01-15"
}
```

#### GET `/student/{id}`

Détails d'un étudiant

#### PUT `/student/{id}`

Modifier un étudiant

#### DELETE `/student/{id}`

Supprimer un étudiant

## Codes d'Erreur

| Code | Signification |
| ---- | ------------- |
| 200  | OK            |
| 201  | Created       |
| 400  | Bad Request   |
| 401  | Unauthorized  |
| 403  | Forbidden     |
| 404  | Not Found     |
| 500  | Server Error  |

## Authentification

Les requêtes protégées nécessitent un token de session:

```
Authorization: Bearer <session_token>
```

## Pagination (optionnel)

```
GET /student?page=1&limit=10

Response:
{
    "success": true,
    "data": [ ... ],
    "pagination": {
        "page": 1,
        "limit": 10,
        "total": 25,
        "pages": 3
    }
}
```
