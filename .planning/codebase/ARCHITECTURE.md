# Architecture

**Analysis Date:** 2026-03-03

## Pattern Overview

**Overall:** Monorepo with modular backend (NestJS) and separate frontend/mobile clients (Next.js). Feature-driven module organization on backend with centralized cross-cutting concerns.

**Key Characteristics:**
- Modular NestJS backend with feature-based module structure
- Layered architecture (Controllers → Services → Data Access via Prisma)
- Granular permission system with bitmask-based access control
- Asynchronous job queue (BullMQ) for report generation and data processing
- Multi-client frontend (web/mobile) consuming unified REST API
- Event-driven logging and webhooks for audit trails

## Layers

**API Controller Layer:**
- Purpose: HTTP request handling, validation, routing to business logic
- Location: `apps/backend/src/modules/*/[module].controller.ts`
- Contains: Request/response mapping, decorator-based metadata (permissions, logging)
- Depends on: Service layer, Guards, Interceptors
- Used by: HTTP clients (frontend, mobile, external services)
- Pattern: Controllers decorated with `@Controller`, routes exposed through `@Get/@Post/@Put/@Delete`

**Service Layer:**
- Purpose: Core business logic, data transformation, orchestration between domain entities
- Location: `apps/backend/src/modules/*/[module].service.ts`
- Contains: Business rules, validation, Prisma queries, integration with other services
- Depends on: PrismaService, other services, external SDKs
- Used by: Controllers, other services, job handlers
- Pattern: Injectable services using dependency injection, public methods for each operation

**Data Access Layer (Prisma):**
- Purpose: Type-safe database queries and migrations
- Location: `apps/backend/src/prisma/prisma.service.ts` (service wrapper)
- Contains: PrismaClient configuration, connection lifecycle management
- Depends on: MySQL database, environment configuration
- Used by: All services
- Pattern: Single `PrismaService` instance injected across modules

**Authentication & Authorization:**
- Purpose: User identity verification and permission enforcement
- Location: `apps/backend/src/modules/auth/` (auth), `apps/backend/src/common/guards/` (enforcement)
- Contains: JWT strategy, permission evaluation, role-based access control
- Depends on: PrismaService, JwtService, request context
- Used by: All protected endpoints
- Pattern: JWT tokens issued on login, validated on each request via `JwtAuthGuard` and `PermissionsGuard`

**Cross-Cutting Concerns:**
- Purpose: Centralized handling of logging, permissions, error handling, activity tracking
- Location: `apps/backend/src/common/` (guards, filters, interceptors, decorators, exceptions)
- Contains: Global exception filters, activity logging interceptor, permission decorators
- Depends on: Reflector (NestJS metadata), ActivityLogService, WebhookService
- Used by: All modules and endpoints
- Pattern: Global pipes and filters in `main.ts`, method-level decorators on endpoints

**Frontend State & API Integration:**
- Purpose: Client-side state management and API communication
- Location: `apps/frontend/src/store/` (Zustand stores), `apps/frontend/src/lib/api.ts` (axios instance)
- Contains: Auth state, dashboard state, API request/response handling, token injection
- Depends on: localStorage, axios, external API
- Used by: Pages and components
- Pattern: Zustand stores with persistent middleware, axios interceptors for auth and error handling

**Queue & Job Processing:**
- Purpose: Asynchronous background work for reports, exports, long-running tasks
- Location: `apps/backend/src/modules/jobs/`
- Contains: BullMQ queue configuration, job handlers, Redis connection
- Depends on: Redis, Prisma, file system, PDF/Excel generation libraries
- Used by: Controllers triggering async work (export, analitiche, production reports)
- Pattern: Job type constants, handler functions per job type, queue lifecycle in module init

## Data Flow

**User Login Flow:**

1. User submits credentials via `/api/auth/login` (POST)
2. `AuthController.login()` calls `AuthService.validateUser()`
3. Service queries `User` via Prisma, validates password with bcrypt
4. On success, `JwtService.sign()` creates token containing `{sub: userId, username}`
5. Response includes token + user metadata (including permissions object)
6. Frontend stores token in Zustand `useAuthStore` with localStorage persistence
7. Subsequent requests include `Authorization: Bearer {token}` via axios interceptor

**Protected Endpoint Access Flow:**

1. Request arrives with JWT in Authorization header
2. `JwtAuthGuard` extracts token, validates signature and expiration
3. Guard injects `user` object (decoded JWT payload) into request
4. `PermissionsGuard` checks `@RequirePermissions()` decorator metadata
5. Guard queries database to fetch user's `Permission` record (cached 1 minute)
6. Guard evaluates bitmask permissions against required level (`PERM.READ|CREATE|UPDATE|DELETE`)
7. Also checks if module is enabled via `Setting` table (`module.{name}.enabled`)
8. On success, endpoint executes; on failure, `403 Forbidden` returned
9. After execution, `ActivityLogInterceptor` logs action asynchronously

**Report Generation (Async Job):**

1. User requests export via `/api/export/reports` (POST) with parameters
2. Controller validates, extracts entity IDs, invokes `JobsQueueService.enqueueJob()`
3. Job pushed to BullMQ queue stored in Redis with metadata
4. Response immediately returned with `jobId` (fire-and-forget)
5. BullMQ Worker processes queue, dequeues oldest job
6. Job handler (e.g., `analitiche.report-pdf.ts`) retrieves metadata from database
7. Handler generates PDF/Excel using data from multiple Prisma queries
8. Writes output to MinIO storage or file system
9. Updates `Job` record with completion status and file path
10. Frontend polls `/api/jobs/{jobId}` or uses polling to detect completion

**Frontend Page Load (Dashboard):**

1. User navigates to `/dashboard/produzione`
2. Layout checks `useAuthStore` hydration status (async from localStorage)
3. Route guard redirects unauthenticated users to `/login`
4. Page component renders, queries API using axios (with token from store)
5. Request triggers axios interceptor: adds `Authorization` header from store
6. Backend JWT guard validates, returns data if authorized
7. Response interceptor injects token into axios response/error handling
8. On 401, axios interceptor triggers logout, redirects to `/login`
9. On 503 or database error, redirects to `/db-error` page
10. Component renders data, Zustand dashboard store caches module state

**Error Handling Flow:**

1. Unhandled exception thrown in service layer
2. NestJS catches exception before response sent
3. `PrismaExceptionFilter` matches Prisma-specific errors (unique constraint, not found, etc.)
4. Falls back to `AllExceptionsFilter` for generic exceptions
5. Filter formats error response (Italian localization, developer details in dev mode)
6. Logs to `LoggerService` with context (method, URL, status, IP, user-agent)
7. Response sent with standardized format: `{statusCode, error, message[], timestamp, path}`
8. Frontend axios interceptor catches response error
9. Checks error type (database vs server vs network)
10. Conditionally redirects to error pages or shows toast notification

## Key Abstractions

**Module:**
- Purpose: Encapsulate feature domain with its controller, service, and internal logic
- Examples: `ProduzioneModule`, `QualityModule`, `ExportModule`, `JobsModule`
- Pattern: NestJS `@Module()` decorator with imports, controllers, providers, exports
- Structure: Each module in `apps/backend/src/modules/{name}/` contains `{name}.module.ts`, `{name}.controller.ts`, `{name}.service.ts`, plus DTOs and nested subdirectories

**Service:**
- Purpose: Encapsulate business logic for a single domain responsibility
- Examples: `ProduzioneService` (production record CRUD), `ExportService` (export document management), `JobsQueueService` (async job enqueueing)
- Pattern: `@Injectable()` class with public async methods, dependency injection via constructor
- Responsibility: Data validation, Prisma queries, external API calls, cross-domain orchestration

**Permission Decorator:**
- Purpose: Declare required permissions at endpoint level
- Examples: `@RequirePermissions('produzione')`, `@RequirePermissions('quality')`, `@RequirePermLevel(PERM.UPDATE)`
- Pattern: Custom decorators storing metadata via NestJS `Reflector`, evaluated at runtime by `PermissionsGuard`
- Storage: User permissions stored as JSON object with module names as keys and bitmask numbers as values

**Job Handler:**
- Purpose: Asynchronous task execution for long-running operations
- Examples: `analitiche.report-pdf.ts`, `export.ddt-completo-pdf.ts`, `quality.report-pdf.ts`
- Pattern: Async function taking job payload, returning status/result; registered in `jobHandlers` map
- Execution: Invoked by BullMQ worker when job dequeued from Redis

**DTO (Data Transfer Object):**
- Purpose: Type-safe request/response contract between client and API
- Location: `apps/backend/src/modules/{module}/dto/`
- Pattern: Class decorators with `class-validator` for input validation, `class-transformer` for serialization
- Usage: Request bodies validated by `ValidationPipe`, response serialized by framework

**Zustand Store:**
- Purpose: Client-side state management with persistence
- Examples: `useAuthStore` (user, token, permissions), `useDashboardStore` (sidebar, module state)
- Pattern: `create<T>()` factory with middleware for localStorage persistence
- Hydration: Loads from localStorage on app init, waits for `_hasHydrated` flag before rendering

## Entry Points

**Backend Application:**
- Location: `apps/backend/src/main.ts`
- Triggers: `npm run dev` or `npm run start:prod`
- Responsibilities: Initialize NestJS app, register global guards/filters/pipes, configure Swagger, start HTTP server on port 3011
- Bootstrap flow: Create app → configure assets → setup CORS → register exception filters → apply validation → setup Swagger docs → listen on port

**Frontend Application:**
- Location: `apps/frontend/src/app/layout.tsx` and `apps/frontend/src/app/(dashboard)/layout.tsx`
- Triggers: `npm run dev` (Next.js dev server on port 3010) or `npm run build && npm run start`
- Responsibilities: Initialize React Query client, render root layout with providers, setup ServerStatusOverlay for connectivity detection
- Hydration: Client-side Zustand auth store loads from localStorage, gates dashboard routes

**Mobile Application:**
- Location: `apps/mobile/src/app/layout.tsx`
- Triggers: `npm run dev` (Next.js dev server on port 3012)
- Responsibilities: Similar to frontend but optimized for mobile viewport, limited features for quality control and repairs

**Health Check Endpoint:**
- Location: `apps/backend/src/modules/health/health.controller.ts`
- Purpose: System status verification (database connectivity, basic availability)
- Used by: Frontend ServerStatusOverlay, monitoring, CI/CD pipelines

## Error Handling

**Strategy:** Layered exception handling with custom filters, type-safe Prisma error mapping, and user-facing localization.

**Patterns:**

**HTTP Exception Mapping:**
- Status 400: Validation errors (whitelist=true enforces strict body validation)
- Status 401: Unauthorized (missing/invalid JWT or wrong credentials)
- Status 403: Forbidden (insufficient permissions or module disabled)
- Status 404: Resource not found (entity doesn't exist or deleted)
- Status 500: Internal server error (unhandled exception, logged with stack trace)
- Status 503: Service unavailable (database connection failure, Redis down)

**Prisma Exception Filter:**
- Catches `PrismaClientKnownRequestError` (unique constraint, foreign key, etc.)
- Maps to appropriate HTTP status: unique constraint → 409 Conflict, not found → 404
- Location: `apps/backend/src/common/filters/prisma-exception.filter.ts`

**Validation Pipeline:**
- Global `ValidationPipe` enforces request body shape
- Decorator: `@Body(new ValidationPipe({...}))`
- Transforms plain objects to typed DTOs
- Returns 400 with field-level error messages in Italian

**Error Response Format:**
```json
{
  "statusCode": 400,
  "error": "Errore Validazione",
  "message": ["Il campo \"name\" è obbligatorio"],
  "timestamp": "2026-03-03T10:00:00.000Z",
  "path": "/api/produzione/records",
  "details": "..." // Development only
}
```

**Frontend Error Handling:**
- Axios interceptor detects 401 → logout and redirect to `/login`
- Axios interceptor detects 503/500 with database error → redirect to `/db-error`
- Notistack toast notifications for user-facing errors
- ServerStatusOverlay shows connectivity status overlay

## Cross-Cutting Concerns

**Logging:**
- Backend: `LoggerService` in `apps/backend/src/common/services/logger.service.ts`
- Levels: log (info), success (green), warn, error
- Context: Method names passed as second parameter for filtering
- Flow: Logged at bootstrap, endpoint entry/exit, error occurrence, job lifecycle
- Storage: Console output in development, structured logs in production

**Validation:**
- Backend: `class-validator` decorators on DTOs + global `ValidationPipe`
- Frontend: `react-hook-form` + `yup` for form validation
- Pattern: DTO classes define schema, framework auto-validates on request
- Error messages: Italian translations in ValidationPipe exception factory

**Authentication:**
- Strategy: JWT (stateless, bearer token in Authorization header)
- Issuance: `AuthService.login()` via `JwtService.sign()`
- Validation: `JwtAuthGuard` on protected routes
- Token payload: `{sub: userId, username}`
- Expiration: 7 days (configured in `AuthModule`)
- Storage: Frontend localStorage via Zustand persistence

**Permission Enforcement:**
- System: Granular bitmask-based permissions per module
- Bitmask levels: `PERM.READ=1, CREATE=3 (READ|CREATE), UPDATE=7, DELETE=15`
- Storage: `Permission` table with JSON `permessi` column
- Enforcement: `PermissionsGuard` checks metadata on endpoint
- Module gating: Modules can be disabled via `Setting` table keys
- Frontend: Zustand helpers `hasPermission()` and `hasPermLevel()` gate UI conditionally

**Activity Logging:**
- Backend: `ActivityLogInterceptor` fires on every endpoint decorated with `@LogActivity()`
- Capture: Method, URL, params, body (sanitized), response ID, user IP, user-agent
- Storage: `ActivityLog` table in database
- Event dispatch: Fire-and-forget webhooks via `WebhookService`
- Use case: Audit trails, compliance, user action tracking

---

*Architecture analysis: 2026-03-03*
