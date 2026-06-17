# NPONTU TECHNOLOGIES

## Platforms Developer Interview Assignment — Submission

---

### Candidate Information

**Name of Interviewee:** __________________________

**Date of Submission:** __________________________

---

### Project Overview

**OpsCommand** is an Application Support Operations Management Platform built for tracking daily activities, incidents, escalations, and team handovers for Applications Support teams.

Built with **Laravel 11**, **PostgreSQL**, **Blade + Livewire**, and **Tailwind CSS** in a dark-themed, desktop-first interface.

---

### Repository

**GitHub Repository:** [INSERT REPO LINK HERE]

---

### Live Deployment

**Live URL:** [INSERT LIVE URL HERE]

---

### Features Implemented

| # | Requirement | Implementation |
|---|---|---|
| 1 | Activity Recording | Full CRUD with priorities, statuses, owner assignment, and linked incidents |
| 2 | Activity Status Updates | Real-time status updates (Done/Pending) with remarks via Livewire |
| 3 | Personnel & Timestamp Tracking | Automatic capture of user, date, and time for every update |
| 4 | Daily Activity Visibility & Handover | Activity history view, handover board with auto-populated critical items |
| 5 | Reporting | Date range queries, CSV export, KPI snapshots |
| 6 | User Authentication | Login/logout, session management, RBAC (4 roles), MFA support |

---

### Additional Features

- Incident management with severity levels (P1–P4)
- Escalation tracking with full history
- Service health monitoring with SLA records
- Notification center with real-time polling
- Personnel, department, team, and shift management
- Audit logging and historical records
- 247 automated test assertions passing

---

### Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11, PHP 8.3+ |
| Database | PostgreSQL 16 |
| Frontend | Blade + Livewire |
| CSS | Tailwind CSS v4 |
| JavaScript | Alpine.js |
| Icons | Material Symbols Outlined |
| Charts | ApexCharts |
| Auth | Laravel Sanctum |
| Deployment | Docker Compose + Nginx + Supervisor + GitHub Actions |

---

### Non-Functional Requirements Considered

- **Security**: RBAC, MFA support, encrypted sessions, CSRF protection, security headers
- **Performance**: Database indexing, query optimization, Redis caching
- **Scalability**: Docker-based deployment, queue workers, horizontal scaling ready
- **Auditability**: Full audit trail on all operational actions
- **Reliability**: Soft deletes (archived_at), data integrity constraints

---

### Evaluation Criteria Coverage

| Criteria | How It's Addressed |
|---|---|
| Logic | Clean separation of concerns, service layer, policies, Eloquent relationships |
| Code Clarity | PSR-12 standards, consistent naming, modular architecture |
| UI Innovation | Material Design 3 dark theme, Livewire real-time components, ApexCharts |
| Requirement Interpretation | All 6 requirements fully implemented plus enterprise features |
| Non-Functional Requirements | Security, performance, audit, scalability considered |

---

*Prepared by Npontu Technologies interview candidate*
