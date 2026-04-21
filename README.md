# Food Delivery System (Module 1: Restaurant & Menu)

## Overview
This repository contains the foundational **Restaurant & Menu Module** built with Laravel following SOLID principles and the Repository Pattern. It strictly handles restaurant profiles, operability, menu categorization, item management, and batch-processing order validation endpoints designed to integrate cleanly with future modules (like the Order Module).

### 🚀 Technical Stack
- **Framework:** Laravel 13.x (PHP 8.3+)
- **Architecture Pattern:** Repository-Service Pattern
- **Testing:** Pest (Feature Testing)
- **Database:** MySQL 8.0
- **Containerization:** Docker & Docker Compose
- **Documentation:** OpenAPI (via `darkaonline/l5-swagger`)

---

## Architecture Flow

1. **Routing:** `routes/api.php` handles `/api/v1` and internal endpoints.
2. **Controllers:** Keep HTTP logic isolated, parsing requests into validated DTOs array (`FormRequests`) and passing downward. Formats responses using `ApiResponseHelper` and `ApiResources`.
3. **Services:** Core business logic (Idempotent updates, validation rules, batch fetching).
4. **Repositories:** Abstraction layer separating Eloquent Logic/Database logic away from Business logic. Avoids `N+1` queries using optimal indexing and batching.

---

## 🔗 Key Endpoints

| Resource | Endpoints | Description |
|---|---|---|
| **Restaurant** | `GET /api/v1/restaurants` <br> `POST /api/v1/restaurants` <br> `GET /api/v1/restaurants/{id}` <br> `PATCH /api/v1/restaurants/{id}/operational-status` | Manage restaurants and toggle open/closed status. |
| **Category** | `GET /api/v1/restaurants/{rest}/categories` <br> `POST ...` | Manage menu categories. |
| **Menu Items** | `GET /api/v1/restaurants/{rest}/menus` <br> `PATCH /api/v1/menus/{id}/availability` | Add items and toggle availability constraints. |
| **Internal (Orders)** | `POST /api/v1/internal/order-items/validate` | Validates duplicate items, closed states, item availability, and returns canonical data payloads. |

---

## 🛠 Setup & Commands

To quickly bootstrap and test this setup with Docker:

```bash
# 1. Install dependencies and Swagger package
composer install
composer require "darkaonline/l5-swagger"

# 2. Setup env (make sure to set connection to MySQL or DB container)
cp .env.example .env
php artisan key:generate

# 3. Spin up local container stack
docker compose up -d --build

# 4. Migrate and Seed dummy data (30+ Restaurants, Categories, Items)
# Use docker exec if running via containers: docker compose exec app php artisan migrate
php artisan migrate --seed

# 5. Run Tests
php artisan test

# 6. Generate Swagger Docs
php artisan l5-swagger:generate

# 7. Check Routes
php artisan route:list
```

## Readiness Checklist
- [x] Restaurant CRUD & Operational logic implemented
- [x] Menu Category mapping implemented
- [x] Menu Item manipulation & Status flag isolated
- [x] Order Validation batch query optimized (no N+1)
- [x] Response structure standardized
- [x] Full Dockerization provided
- [x] Swagger docs layout initialized

*Modul 1 siap diintegrasikan dengan Modul 2 & 3.*
