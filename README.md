# Tindahub API

A Symfony-based CRM / order-management API for Philippine SMBs selling through
Facebook Messenger. It bridges Meta's Graph API, a Firebase Realtime Database
mirror of conversation state, and a MySQL store of orders, customers, pages,
and address data (province / city / barangay).

## Stack

| Layer         | Choice                                                                 |
|---------------|------------------------------------------------------------------------|
| Language      | PHP 8.3+ (local dev runs on 8.4)                                       |
| Framework     | Symfony 8.1                                                            |
| ORM           | Doctrine ORM 3 / DBAL 4                                                |
| Database      | MySQL (prod), SQLite (tests)                                           |
| Auth          | JWT via `lexik/jwt-authentication-bundle` + custom `KaljeAuthenticator`|
| External APIs | Meta Graph API v20.0, Firebase Realtime Database (`kreait/firebase`)   |
| HTTP server   | Nginx + PHP-FPM via supervisord (`docker/Dockerfile`)                  |

## Project layout

```
src/
├── Controller/        HTTP entry points, grouped by domain
│   ├── Address/       Province, City, Barangay lookups (public)
│   ├── Auth/          Register, login, password reset
│   ├── Order/         Orders, customers, report templates
│   ├── Organization/  Organizations and their members
│   ├── User/          Profile, pages
│   └── Webhook/       Facebook Messenger webhook + proxy
├── DTO/               Request payload shapes with validator attributes
├── Entity/            Doctrine entities, grouped by bounded context
├── Enum/              Enums (UserType)
├── Event/             Domain events (PageEvent)
├── EventListener/     Listeners (PageListener syncs Facebook pages on auth)
├── Repository/        Doctrine repositories
├── Security/          KaljeAuthenticator (multi-type: email/password + OAuth)
└── Service/           Application services
    └── ThirdParty/    Firebase + Facebook integration adapters
```

## Getting started

### Prerequisites

- Docker + Docker Compose, or local PHP 8.3+ with Composer 2
- MySQL (for dev / prod); SQLite is used automatically for tests

### Local setup

```bash
cp .env .env.local       # fill in DATABASE_URL, JWT keys, Firebase creds
composer install
php bin/console lexik:jwt:generate-keypair
php bin/console doctrine:migrations:migrate
```

### Docker setup

```bash
docker compose up --build
# API listens on http://localhost:8005
```

## Running the test suite

The functional test suite boots the full kernel against SQLite, stubs Firebase
and the Meta Graph API, and asserts response shapes end-to-end.

```bash
composer test
```

What is covered:
- Kernel + container boot on the configured Symfony version
- Public routes (address lookups, Facebook webhook verify)
- Authentication flow (register, login, failed login) with the custom authenticator
- Authenticated routes via a real-JWT helper (`ApiTestCase::authenticate`)
- Request-payload mapping and validation (OrderDTO happy path and 422)
- Firebase and Facebook third-party services via in-memory fakes

What is **not** covered:
- Live Meta Graph API behaviour — stubbed by `tests/Support/FakeFacebookService`
- Live Firebase realtime DB behaviour — stubbed by `tests/Support/FakeFirebaseDatabase`
- Prod env config and secrets

Those should be smoke-tested manually against a real page / Firebase project
after any dependency bump.

## API surface

Everything is under `/api`. Prefix `JWT` means `Authorization: Bearer <token>`
is required; the firewall is defined in `config/packages/security.yaml`.

| Group        | Method | Path                                            | Auth |
|--------------|--------|-------------------------------------------------|------|
| Auth         | POST   | `/api/auth/register`                            | –    |
|              | POST   | `/api/auth/login`                               | –    |
|              | POST   | `/api/auth/forgot-password`                     | –    |
|              | POST   | `/api/auth/validate-token`                      | –    |
|              | PUT    | `/api/auth/reset-password`                      | –    |
| Addresses    | GET    | `/api/addresses/provinces`                      | –    |
|              | GET    | `/api/addresses/cities/{provinceId}`            | –    |
|              | GET    | `/api/addresses/barangays/{cityId}`             | –    |
| Users        | GET    | `/api/profile`                                  | JWT  |
|              | PUT    | `/api/profile/update`                           | JWT  |
|              | GET    | `/api/pages`                                    | JWT  |
| Organizations| GET    | `/api/organizations/`                           | JWT  |
|              | POST   | `/api/organizations/store`                      | JWT  |
|              | GET    | `/api/members/`                                 | JWT  |
|              | POST   | `/api/members/store`                            | JWT  |
|              | GET    | `/api/members/{memberId}`                       | JWT  |
|              | PUT    | `/api/members/update/{memberId}`                | JWT  |
| Orders       | GET    | `/api/order`                                    | JWT  |
|              | GET    | `/api/order/{orderId}`                          | JWT  |
|              | POST   | `/api/order/store`                              | –    |
|              | PUT    | `/api/order/update-status/{orderId}`            | JWT  |
| Customers    | GET    | `/api/customer`                                 | JWT  |
|              | GET    | `/api/customer/{customerId}`                    | JWT  |
| Reports      | GET    | `/api/templates`                                | JWT  |
|              | GET    | `/api/templates/{templateId}`                   | JWT  |
|              | POST   | `/api/templates/import/{pageId}`                | JWT  |
|              | GET    | `/api/templates/export/{templateId}/{pageId}`   | JWT  |
| Webhooks     | GET    | `/api/callback/facebook-verify`                 | –    |
|              | POST   | `/api/callback/facebook-verify`                 | JWT  |
|              | GET    | `/api/facebook/conversations`                   | JWT  |
|              | GET    | `/api/facebook/messages`                        | JWT  |
|              | POST   | `/api/facebook/messages`                        | JWT  |
|              | POST   | `/api/facebook/messages/order-form`             | JWT  |

Run `php bin/console debug:router` for the full live list.

## External integrations

- **Meta Graph API v20.0** — called by `App\Service\ThirdParty\FacebookService`.
  Base URL hardcoded; per-page access tokens are stored on the `Page` entity
  and refreshed via `me/accounts`.
- **Firebase Realtime Database** — `App\Service\ThirdParty\FirebaseDatabase`
  mirrors conversation + message state so the mobile client reads from
  Firebase and the webhook writes to it. Credentials live at
  `config/firebase.json` (not committed).

## Deployment

GitLab CI builds a Docker image and deploys to the AWS ECR cluster `wano`.
Pipeline config: `.gitlab-ci.yml`, `docker/deployment/dev-build.yml`,
`docker/deployment/dev-deploy.yml`.

## Why "Tindahub"?

Tindahan (store) + hub — a hub for small Filipino sellers routing orders
across Facebook Pages. The old project name `crm-api` and in-tree `wano` /
`Kalje` names are retained in deployment identifiers for continuity.
