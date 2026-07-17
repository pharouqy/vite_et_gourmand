# Vite & Gourmand

> Plateforme de commande en ligne pour un traiteur événementiel.  
> ECF Studi — Titre Professionnel Développeur Web et Web Mobile

## Présentation

Application web MVC développée en PHP 8.3 / MySQL 8.4 / MongoDB 8,  
avec une interface Bootstrap 5 et du JavaScript Vanilla (Fetch API).

## Prérequis

- Docker Desktop (ou Docker Engine + Compose Plugin)
- Git

## Installation locale

> ⚠️ Instructions complètes à finaliser en Sprint 5 (US-5.5)

```bash
git clone https://github.com/<ton-username>/vite-et-gourmand.git
cd vite-et-gourmand
cp .env.example .env   # puis éditer les valeurs
docker compose up -d
```

Accès :
- Application : http://localhost:8080
- phpMyAdmin  : http://localhost:8081

## Structure des branches

| Branche | Rôle |
|---|---|
| `main` | Production — merge depuis `develop` uniquement |
| `develop` | Intégration — merge depuis les branches `feature/` |
| `feature/us-X.Y-nom` | Une branche par User Story |

## Gestion de projet

Lien Notion/Trello : *(à ajouter en US-0.1)*

## Licence

Projet pédagogique — tous droits réservés.