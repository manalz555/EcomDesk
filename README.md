# EcomDesk — Support client multicanal pour e-commerce

Application web Laravel développée pendant mon stage de fin d'année (PFA, 2026), au sein de
l'agence web **WebCinq** à Marrakech.

Une boutique en ligne reçoit ses clients sur WhatsApp, Instagram, Messenger, Telegram, par
email et par formulaire. Sans point de convergence, chaque canal devient un silo : des demandes
restent sans réponse, deux agents traitent la même requête, et rien n'est mesurable. EcomDesk
réunit ces canaux dans une **boîte de réception partagée**, avec des rôles, un suivi et des
indicateurs — et un moteur d'automatisation qui rédige des brouillons de réponse **soumis à la
validation d'un agent avant tout envoi**.

## Fonctionnalités

- **Boîte de réception partagée** — conversations sur sept canaux, affectation à un agent ou à
  une équipe, étiquettes, notes internes, pièces jointes stockées sur un disque privé
- **Widget de chat** intégrable en une ligne de code sur n'importe quel site marchand, isolé dans
  une iframe, avec limitation de débit et identification du visiteur par jeton — c'est le premier
  canal réellement opérationnel de la plateforme
- **Automatisation par IA** — des règles par canal et par catégorie déclenchent la génération d'un
  brouillon ; l'IA ne répond jamais directement au client
- **CRM** — fiches clients, entreprises, champs personnalisés définissables sans migration
- **Rôles hiérarchisés** — agent, manager, administrateur, avec permissions vérifiées côté serveur
- **Analytique** — délai moyen de première réponse, taux de résolution, satisfaction, volume par
  jour, répartition par canal et performance par agent
- **Journal d'audit** — chaque action sensible laisse une trace

## Le parti pris sur l'IA

L'intelligence artificielle ne répond jamais directement à un client. Elle génère un brouillon,
systématiquement soumis à la validation d'un agent, qui peut le corriger avant envoi ou le rejeter.

Ce principe — *human-in-the-loop* — répond à un risque métier concret : dans un service client, une
réponse fausse envoyée sans contrôle peut faire perdre un client. Automatiser l'envoi aurait été
**techniquement plus simple** ; c'est donc une position de conception assumée, pas une limite.

Le service est déclaré sous forme d'interface (`App\Contracts\AiReplyService`), son implémentation
étant liée dans `AppServiceProvider`. Changer de fournisseur, ou lui substituer un simulateur en
test, se fait en modifiant une seule ligne.

## Architecture

| | |
|---|---|
| Modèles Eloquent | 15 |
| Contrôleurs | 22 |
| Migrations | 26 |
| Vues Blade | 68 |
| Tests automatisés | 40 (87 assertions) |

- **Middlewares** : `EnsureIsAdmin`, `EnsureCanManageTeam`, `EnsureIsActive`
- **Tâche asynchrone** : `GenerateAutomatedReply`, mise en file pour ne pas bloquer la réponse HTTP
- **`App\Support\SqlDialect`** regroupe les rares expressions SQL qui diffèrent selon le SGBD
  (écart en minutes, ordre métier des énumérations), afin que le module analytique garde ses
  agrégations en SQL sans enfermer le projet dans MySQL

## Tests

Chaque test porte le numéro de la règle de gestion qu'il vérifie, de sorte que la spécification et
sa vérification restent traçables l'une vers l'autre. RG12 — « un brouillon non validé n'est jamais
visible du client » — correspond ainsi à un test qui interroge réellement le point d'accès public du
widget et vérifie que le contenu du brouillon en est absent.

```bash
php artisan test
```

- `ReglesDeGestionTest` — règles RG1 à RG15 : accès, comptes désactivés, validité des statuts,
  priorités et satisfaction, permissions des trois rôles, cycle complet des brouillons
- `AutomatisationEtWidgetTest` — déclenchement et non-déclenchement des règles, dégradation
  contrôlée en cas d'échec de l'API externe, parcours complet d'un visiteur sur le widget
- `SqlDialectTest` — les fragments SQL assemblés à la main : ordre métier, échappement des
  apostrophes, traduction de l'écart en minutes selon le pilote

Plusieurs tests vérifient délibérément un **refus** : qu'un agent ne puisse pas supprimer un client,
qu'un brouillon ne puisse pas être validé depuis une autre conversation. Ce sont ceux-là qui
protègent réellement les règles.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Configurer la base MySQL dans .env, puis :
php artisan migrate:fresh --seed

npm run build
php artisan serve
```

Le jeu de démonstration est daté par rapport au jour d'exécution : il produit trois semaines
d'activité réaliste, un brouillon en attente de validation et un échange complet arrivé par le
widget. Relancer `migrate:fresh --seed` remet la démonstration à neuf.

### Comptes de démonstration

Mot de passe : `password`

| Rôle | Email |
|---|---|
| Administrateur | `admin@ecomdesk.test` |
| Manager | `manager@ecomdesk.test` |
| Agent | `agent1@ecomdesk.test` · `agent2@ecomdesk.test` |

La clé OpenAI se configure depuis l'interface (Intégrations), pas dans le `.env`. Sans clé, le
service produit une réponse de repli et l'application reste pleinement utilisable.

## Technologies

Laravel 12 · PHP 8.2 · MySQL · Blade · Alpine.js · Tailwind CSS 4 · Vite · Chart.js · API OpenAI
