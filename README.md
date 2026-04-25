# Pharma System

Laravel 12 reference implementation — multi-role auth module for a pharmacy management system.

## Stack

- **Backend:** PHP 8.2+, Laravel 12, `firebase/php-jwt`
- **Frontend:** Blade, Tailwind CSS v4, Alpine.js (CDN), Vite
- **Database:** PostgreSQL 16 (via Docker)

## Requirements

- PHP 8.2+ — `brew install php@8.2`
- Composer 2 — `brew install composer`
- Node.js 20+ — `brew install node`
- Docker Desktop — [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop)

## First-time setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Generate a JWT secret and paste it as `JWT_SECRET=` in `.env`:

```bash
php artisan tinker --execute="echo base64_encode(random_bytes(32));"
```

### 3. Start the database

```bash
docker compose up -d
```

### 4. Run migrations and seed

```bash
php artisan migrate --seed
```

### 5. Build frontend assets

```bash
npm run build
```

### 6. Start the dev server

```bash
php artisan serve
```

App runs at **http://localhost:8000**

---

## Test accounts

All accounts use password: `password`

| Email | Role |
|---|---|
| admin@pharma.test | Administrator |
| sales@pharma.test | Salesperson |
| inventory@pharma.test | Inventory Manager |
| pharmacist@pharma.test | Pharmacist |

---

## API endpoints

Base URL: `http://localhost:8000/api/v1/auth`

| Method | Endpoint | Auth |
|---|---|---|
| POST | `/register` | — |
| POST | `/login` | — |
| POST | `/logout` | Bearer token |
| POST | `/refresh` | Bearer token |
| GET | `/profile` | Bearer token |
| PUT | `/profile` | Bearer token |

---

## Web routes

| URL | Role required |
|---|---|
| `/login` | — |
| `/register` | — |
| `/admin/dashboard` | administrator |
| `/sales/dashboard` | salesperson |
| `/inventory/dashboard` | inventory_manager |
| `/pharmacy/dashboard` | pharmacist |

---

## Daily workflow

```bash
docker compose up -d    # start database
php artisan serve       # start Laravel server
npm run dev             # start Vite (hot reload)
```

```bash
docker compose down     # stop database
docker compose down -v  # stop + wipe database volume
```
