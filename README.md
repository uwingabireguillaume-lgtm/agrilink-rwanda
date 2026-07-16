# AgriLink Rwanda 🌾

**A Smart Digital Marketplace for Farmers and Consumers**

AgriLink Rwanda is a multi-vendor agricultural e-commerce platform that connects local Rwandan farmers directly with consumers. Farmers register, list their own produce, and manage their own orders; consumers browse products from many different farms, add items to a single cart, and check out in one transaction — even when the order spans multiple farmers.

Built for **EWA408510 — E-Commerce and Web Application**, Faculty of Computing and Information Sciences, University of Lay Adventists of Kigali (UNILAK).

---

## Features

- **Three user roles:** Consumer, Farmer, Admin
- **Multi-vendor marketplace:** each product belongs to a specific farmer; a single cart/order can contain products from several farmers, and is automatically split into per-farmer line items for independent fulfillment
- **Product catalog:** categories, search, filtering by category/district, sorting by price
- **Shopping cart:** add/update/remove items, automatic totals
- **Checkout:** customer details, order summary, stock validation, order confirmation with a unique order number
- **Farmer dashboard:** manage own products (create/edit/delete), view and update status of incoming orders, basic sales stats
- **Admin dashboard:** platform-wide stats, farmer approval, category management, recent orders overview
- **Security:** password hashing (bcrypt via `password_hash`), prepared statements (PDO) everywhere, server-side input validation, session-based auth with role checks

## Tech Stack

| Layer         | Technology                                  |
|---------------|----------------------------------------------|
| Frontend      | HTML5, CSS3, Bootstrap 5, vanilla JavaScript |
| Backend       | PHP 8.3 (plain PHP, no framework)             |
| Database      | MySQL 8 (PDO with prepared statements)        |
| Local dev     | XAMPP / PHP built-in server                   |
| Version control | Git & GitHub                                |
| CI/CD         | GitHub Actions                                |
| Containerization | Docker & Docker Compose                    |
| Deployment    | Render (Docker runtime)                       |

## Project Structure

```
agrilink-php/
├── auth/                  # login.php, register.php, logout.php
├── farmer/                # farmer dashboard, product CRUD, order management
├── admin/                 # admin dashboard
├── classes/               # PHP model classes (User, Product, Order, Cart, ...)
├── config/                # database.php (PDO), bootstrap.php (loads everything)
├── includes/              # header, navbar, footer, auth helpers, functions
├── assets/                # css, js, images
├── database/
│   ├── schema.sql         # full MySQL schema (DDL)
│   └── seed.sql           # sample farmers, categories & products
├── uploads/                # farmer-uploaded product images
├── index.php, products.php, product.php, cart.php, checkout.php, ...
├── Dockerfile
├── docker-compose.yml
├── render.yaml
└── .github/workflows/ci-cd.yml
```

## Database Design

Core entities and relationships:

- **users** (1) ──── (1) **farmer_profiles** — a user with role `farmer` has exactly one farmer profile
- **farmer_profiles** (1) ──── (M) **products** — a farmer lists many products
- **categories** (1) ──── (M) **products**
- **users** (1) ──── (1) **carts** ──── (M) **cart_items** ──── (M:1) **products**
- **users** (1) ──── (M) **orders** ──── (M) **order_items**
- **order_items** references both **products** and **farmer_profiles**, which is what makes multi-vendor order-splitting possible: one `orders` row can have `order_items` belonging to different farmers, each with its own `farmer_status` for independent fulfillment tracking.
- **products** (1) ──── (M) **reviews** (M:1) **users**

See `database/schema.sql` for full column definitions, constraints, and indexes.

## Getting Started — Local Development (XAMPP)

1. Copy the project into `htdocs/agrilink` (or your XAMPP web root).
2. Start Apache and MySQL from the XAMPP control panel.
3. In phpMyAdmin (or the `mysql` CLI), import the schema then the seed data:
   ```
   mysql -u root < database/schema.sql
   mysql -u root < database/seed.sql
   ```
4. `config/database.php` defaults to `localhost` / user `root` / no password, which matches a stock XAMPP install. If your MySQL user differs, set environment variables `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` accordingly.
5. Visit `http://localhost/agrilink/index.php`.

### Demo accounts (seed data, password for all: `Password123`)

| Role    | Email                            |
|---------|-----------------------------------|
| Admin   | admin@agrilink.rw                 |
| Farmer  | jean.habimana@agrilink.rw          |
| Farmer  | marie.uwase@agrilink.rw            |
| Farmer  | emmanuel.n@agrilink.rw              |
| Consumer| alice.mukamana@example.com          |
| Consumer| david.iradukunda@example.com         |

## Running with Docker

```bash
docker compose up --build
```

This starts two containers:
- `agrilink_app` — PHP 8.3 + Apache, serving the app on **http://localhost:8080**
- `agrilink_db` — MySQL 8, automatically initialized with `database/schema.sql` and `database/seed.sql` on first run

To stop: `docker compose down` (add `-v` to also wipe the database volume).

## CI/CD Pipeline

`.github/workflows/ci-cd.yml` runs on every push/PR to `main`:

1. **Lint** — `php -l` syntax-checks every PHP file
2. **Test** — spins up MySQL, loads schema + seed data, boots the app with PHP's built-in server, and verifies the homepage returns HTTP 200
3. **Build** — builds the Docker image to confirm it's deployable
4. **Deploy** — on push to `main`, triggers a Render deploy hook (configured via the `RENDER_DEPLOY_HOOK_URL` repository secret)

## Deployment (Render)

1. Push this repository to GitHub.
2. In Render, create a new **Web Service** from the repo, runtime **Docker**.
3. Provision a MySQL database (Render's free tier has no managed MySQL — use a free instance from Railway, Aiven, or Clever Cloud) and set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` in Render's Environment tab.
4. On first deploy, run `database/schema.sql` and `database/seed.sql` against that database (via its provider's console or `mysql` CLI).
5. Copy the Render service's **Deploy Hook URL** into the GitHub repo secret `RENDER_DEPLOY_HOOK_URL` to enable automatic redeploys from the CI/CD pipeline.

## Security Notes

- Passwords are hashed with `password_hash()` (bcrypt) and verified with `password_verify()`.
- All database queries use PDO prepared statements — no raw string interpolation.
- All user-facing output is escaped via `htmlspecialchars()` (the `e()` helper).
- Role-based route guards (`requireLogin()`, `requireRole()`) protect farmer/admin areas.
- Server-side validation on registration, login, product, and checkout forms.

## Author

Guillaume — Final-year Software Engineering student, UNILAK (Reg. No. 24217/2024)
