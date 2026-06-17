<p align="center"><strong>OpsCommand</strong></p>

## OpsCommand — Application Support Operations Management Platform

OpsCommand is an enterprise-grade operations management platform built for Application Support teams. It centralizes incident tracking, activity management, escalations, handovers, service health monitoring, and team coordination into a single dark-themed command center.

---

## Features

- **Dashboard** — Real-time operational overview with KPI metrics, charts, and activity summaries
- **Activity Management** — Create, assign, track, and escalate activities with priority levels and status workflows
- **Incident Management** — Track incidents with severity levels (P1–P4), investigation notes, resolution records, and linked activities
- **Escalation Management** — Escalate activities/incidents to teams with full history tracking
- **Handover Management** — Shift-based handovers with auto-populated critical items and acknowledgement tracking
- **Service Health Monitoring** — Monitor services with health states (Healthy/Warning/Critical), SLA records, and alerts
- **Watch Team** — Real-time view of on-duty personnel and shift schedules
- **Reporting & Analytics** — Generate reports with configurable date ranges, CSV export, and KPI snapshots
- **Notification Center** — Real-time notifications with critical/warning/info categories and poll-based updates
- **User Management** — RBAC with roles (Admin, Ops Manager, Team Lead, Support Personnel) and permissions
- **Personnel & Team Management** — Departments, teams, personnel profiles, and shift assignments
- **Audit & History** — Full audit trail of all operational actions with historical records

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3+ |
| Database | PostgreSQL |
| Frontend | Blade + Livewire |
| CSS | Tailwind CSS v4 |
| JavaScript | Alpine.js |
| Icons | Material Symbols Outlined |
| Charts | ApexCharts |
| Auth | Laravel Sanctum |
| Realtime | Livewire polling (15s–30s) |
| Queue | Laravel Queues (database driver) |
| Deployment | Docker Compose + Nginx + Supervisor |

---

## Requirements

- PHP 8.3+
- PostgreSQL 16+
- Node.js 20+
- Composer
- Docker & Docker Compose (for deployment)

---

## Local Development

```bash
# Clone the repository
git clone <repo-url>
cd opscommand

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env .env.example
php artisan key:generate

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=opscommand
DB_USERNAME=postgres
DB_PASSWORD=secret

# Run migrations and seed
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

Or use Docker Compose:

```bash
docker compose up -d
docker compose run --rm app php artisan migrate --seed
```

---

## Deployment

### Prerequisites
- DigitalOcean VPS (or any VPS with Docker)
- Domain pointing to VPS IP
- GitHub repository

### Server Setup (One-Time)
```bash
ssh root@YOUR_VPS_IP
bash /opt/opscommand/scripts/setup-server.sh
```

### Environment
Edit `.env.production` with your database credentials, app key, and domain.

### GitHub Actions
Add these secrets in GitHub → Settings → Secrets:
- `SERVER_HOST` — VPS IP address
- `SERVER_USER` — SSH username
- `SERVER_SSH_KEY` — Private SSH key
- `SERVER_PORT` — SSH port (default: 22)

Push to `master` to trigger automatic deployment.

### Manual Deploy
```bash
ssh root@YOUR_VPS_IP
cd /opt/opscommand
bash scripts/deploy.sh
```

---

## Default Credentials

| Email | Password | Role |
|---|---|---|
| nanayawosei429@gmail.com | admin342 | Administrator |

---

## Project Structure

```
opscommand/
├── app/
│   ├── Http/Controllers/    # Web + API controllers
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   ├── Policies/            # Authorization policies
│   └── Services/            # Business logic services
├── database/migrations/     # Database migrations
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Tailwind CSS
│   └── js/                  # Alpine.js + ApexCharts
├── tests/Feature/           # Feature tests
├── docker/                  # Docker configs
├── Dockerfile.prod          # Production Docker build
├── docker-compose.prod.yml  # Production compose
└── scripts/                 # Deployment scripts
```

---

## License

Proprietary — Npontu Technologies
