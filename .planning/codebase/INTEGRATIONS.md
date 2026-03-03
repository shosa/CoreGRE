# External Integrations

**Analysis Date:** 2026-03-03

## APIs & External Services

**Email Service:**
- SMTP Mail Server - For sending production reports and notifications
  - SDK/Client: nodemailer 6.9.0
  - Auth: User credentials (email + password) stored per-user in `User.mail` and `User.mailPassword`
  - Configuration: Via `Setting` table keys starting with `smtp.` (host, port, secure, user, password)
  - Usage: Production reports, custom email notifications
  - Implementation: `apps/backend/src/modules/email/email.service.ts`

**HTTP Client:**
- Generic HTTP API calls via axios
  - SDK/Client: axios 1.13.5 (backend), axios 1.6.0 (frontend)
  - Usage: Generic API calls, webhook delivery, external service communication
  - Implementation: `apps/backend/src/common/services/webhook.service.ts`, `apps/frontend/src/lib/api.ts`, `apps/mobile/src/lib/api.ts`

## Data Storage

**Databases:**
- MySQL 5.7+
  - Connection: `DATABASE_URL` env var (format: `mysql://[user]:[pass]@[host]:[port]/[database]`)
  - Client: @prisma/client 5.22.0 (Prisma ORM)
  - Host: `core-mysql` (shared CoreServices Docker network)
  - Database: `coregrejs`
  - Schema location: `apps/backend/prisma/schema.prisma`
  - Migrations: `apps/backend/prisma/migrations/`

**File Storage:**
- MinIO (S3-compatible object storage)
  - Connection: HTTP/HTTPS to `core-minio` service
  - Client: minio 8.0.2 (MinIO JS SDK)
  - Configuration:
    - Endpoint: `MINIO_ENDPOINT` (default: `core-minio`)
    - Port: `MINIO_PORT` (default: 9000)
    - SSL: `MINIO_USE_SSL` (default: false)
    - Access Key: `MINIO_ACCESS_KEY` (default: minioadmin)
    - Secret Key: `MINIO_SECRET_KEY` (default: minioadmin123)
    - Bucket: `MINIO_BUCKET` (default: `coregre-storage`)
  - Usage: PDF exports, job outputs, file uploads, document archival
  - Service wrapper: `apps/backend/src/services/minio.service.ts`
  - Implementation: `apps/backend/src/modules/storage/storage.service.ts`

**Caching:**
- Redis (via ioredis)
  - Connection: `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`
  - Host: `core-redis` (shared CoreServices Docker network)
  - Port: 6379
  - Key prefix: `coregre:cache:`
  - Password: `coresuite_redis`
  - Usage: Request caching, temporary data storage
  - Service: `apps/backend/src/common/services/cache.service.ts`
  - Graceful fallback: When Redis is unavailable, system operates normally without caching

**Job Queue:**
- BullMQ with Redis backend
  - Connection: Uses same Redis instance as cache
  - Queue name: `coregre-jobs`
  - Job types: PDF generation, Excel exports, tracking reports, production analytics
  - Implementation: `apps/backend/src/modules/jobs/jobs.queue.ts`
  - Job handlers: `apps/backend/src/modules/jobs/handlers/`
  - Retry strategy: 2 attempts with exponential backoff (initial 2000ms)
  - Status tracking: `Job` model in Prisma schema

## Authentication & Identity

**Auth Provider:**
- Custom JWT-based authentication (no external provider)
  - Implementation: `apps/backend/src/modules/auth/`
  - Token generation: @nestjs/jwt 10.2.0
  - Strategies:
    - Local (username/password) - `apps/backend/src/modules/auth/strategies/local.strategy.ts`
    - JWT (token validation) - `apps/backend/src/modules/auth/strategies/jwt.strategy.ts`
  - Guard: `apps/backend/src/common/guards/jwt-auth.guard.ts`
  - Configuration:
    - Secret: `JWT_SECRET` env var
    - Expiration: `JWT_EXPIRATION` env var (default: 24h)
  - Password hashing: bcrypt 5.1.1
  - Token storage:
    - Frontend: localStorage `coregre-auth`
    - Mobile: localStorage `coregre-mobile-auth`

**Authorization:**
- Granular permission system
  - Model: `Permission` table (one-to-one with `User`)
  - Permissions stored as JSON object in `Permission.permessi` field
  - Module-level access control via `InworkModulePermission` for mobile operators
  - Decorator-based: `@Permissions()` decorator for endpoint authorization
  - Guard: `apps/backend/src/common/guards/permissions.guard.ts`
  - Activity logging: `@LogActivity()` decorator tracks user actions

## Monitoring & Observability

**Error Tracking:**
- None configured (no Sentry, Rollbar, or similar)
- Error handling via NestJS exception filters:
  - `apps/backend/src/common/filters/all-exceptions.filter.ts` - Global exception handler
  - `apps/backend/src/common/filters/prisma-exception.filter.ts` - Database errors
  - `apps/backend/src/common/filters/redis-exception.filter.ts` - Cache errors

**Logs:**
- Console logging via NestJS Logger service
  - Implementation: `apps/backend/src/common/services/logger.service.ts`
  - Activity audit trail: `ActivityLog` table with user, module, action, entity tracking
  - Log rotation: Local file storage at `storage/logs/`
  - Cron logs: `CronLog` table for scheduled job execution tracking

**Health Checks:**
- HTTP endpoints at `GET /api/health` and `GET /api/health/db`
- Implementation: `apps/backend/src/modules/health/health.controller.ts`
- Used by Docker Compose health checks (30s interval, 5s timeout, 3 retries)

## CI/CD & Deployment

**Hosting:**
- Docker containers (backend, frontend, mobile)
- Docker Compose orchestration
- External shared network: `core-network` (CoreSuite infrastructure)
- Port mapping:
  - Backend: 3011:3011
  - Frontend: 3010:3000
  - Mobile: 3012:3012

**CI Pipeline:**
- None detected (no GitHub Actions, GitLab CI, Jenkins configs)
- Manual build via `npm run build` or Docker builds

**Container Configuration:**
- Backend Dockerfile: `apps/backend/Dockerfile`
- Frontend Dockerfile: `apps/frontend/Dockerfile` (with build arg: NEXT_PUBLIC_API_URL)
- Mobile Dockerfile: `apps/mobile/Dockerfile` (with build arg: NEXT_PUBLIC_API_URL for Mobile IP)

## Environment Configuration

**Required env vars:**
- `NODE_ENV` - Execution environment (production/development)
- `PORT` - Backend port (default: 3011)
- `DATABASE_URL` - MySQL connection string
- `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD` - Redis connection
- `MINIO_ENDPOINT`, `MINIO_PORT`, `MINIO_USE_SSL`, `MINIO_ACCESS_KEY`, `MINIO_SECRET_KEY`, `MINIO_BUCKET` - MinIO storage
- `JWT_SECRET` - JWT signing secret
- `JWT_EXPIRATION` - JWT token lifetime
- `FRONTEND_URL` - Frontend origin for CORS
- `NEXT_PUBLIC_API_URL` - API endpoint for frontend (runtime config)
- `NEXT_PUBLIC_API_TIMEOUT` - Mobile API timeout (default: 30000ms)
- `MOBILE_LISTEN_IP` - IP address exposed to mobile clients
- `MOBILE_BACKEND_URL` - Backend URL for mobile (overrides default IP:port)
- `NEXT_PUBLIC_DEBUG` - Debug mode for mobile API

**Secrets location:**
- `.env` file in root and `apps/backend/` directories (contains actual secrets)
- `.env.example` files provided as templates for development
- `.env.local` file for frontend overrides (Next.js convention)
- Never commit `.env` files to git

## Webhooks & Callbacks

**Incoming:**
- Mobile login: `POST /api/mobile/login` - Unified login for mobile/inwork apps
- Mobile API endpoints: `POST /api/mobile/check-data`, `GET /api/mobile/system-data`, `GET /api/mobile/daily-summary`
- Quality control: `POST /api/quality/save-hermes-cq` - Quality check submission
- File uploads: `POST /api/quality/upload-photo` - Defect photos
- Job monitoring: `GET /api/jobs/{jobId}` - Track async operations

**Outgoing:**
- Email notifications: SMTP via nodemailer (production reports, custom alerts)
- Webhook service: Generic webhook delivery
  - Implementation: `apps/backend/src/common/services/webhook.service.ts`
  - Uses axios with timeout

## Data Formats

**Import/Export:**
- Excel (.xlsx): exceljs, xlsx - Used for analytics imports and production data
- PDF: pdfkit, pdf-lib - Report generation and archive exports
- CSV: Via exceljs - Production data export
- ZIP: archiver - Multi-file export archives
- JSON: Standard API requests/responses
- QR Codes: qrcode library - Barcode/label generation

## Cross-System Integration

**CoreSuite Integration:**
- Shared Docker network: `core-network`
- Shared database: Connects to `core-mysql` (not isolated)
- Shared cache: Connects to `core-redis`
- Shared storage: Connects to `core-minio`
- Timezone synchronized: Europe/Rome (docker-compose)
- Labels for Portainer management: `com.coresuite.app`, `com.coresuite.environment`

---

*Integration audit: 2026-03-03*
