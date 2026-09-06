# EcomDesk — Support client multicanal pour e-commerce

Application web Laravel développée pendant mon stage de fin d'année (PFA, juillet 2026).
EcomDesk centralise les conversations clients d'une boutique en ligne dans une boîte de
réception partagée, avec un widget de chat intégrable, des réponses assistées par IA et des
règles d'automatisation.

## Fonctionnalités

- **Boîte de réception partagée** : conversations, affectation à un agent, notes internes, tags, pièces jointes
- **Widget de chat** intégrable sur n'importe quel site (iframe), avec limitation de débit
- **Réponses assistées par IA** : brouillons générés par OpenAI, validés par l'agent avant envoi
- **Règles d'automatisation** et intégrations de canaux
- **Gestion des clients et des entreprises**, champs personnalisés
- **Équipes et agents** avec rôles (admin, gestion d'équipe, agent actif)
- **Tableau de bord et analytics**, notifications, journal d'audit

## Architecture

- 15 modèles Eloquent (Conversation, Reponse, Client, Company, Team, Tag, Attachment,
  AutomationRule, ChannelIntegration, CustomField, AuditLog…)
- 15 contrôleurs, 26 migrations, 67 vues Blade
- Middlewares : `EnsureIsAdmin`, `EnsureCanManageTeam`, `EnsureIsActive`
- Service `OpenAiReplyService` pour la génération de brouillons

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# configurer la base de données et OPENAI_API_KEY dans .env, puis :
php artisan migrate --seed
npm run dev
php artisan serve
```

## Technologies

Laravel 12 · PHP 8 · MySQL · Blade · Alpine.js · Tailwind CSS · Vite · OpenAI API
