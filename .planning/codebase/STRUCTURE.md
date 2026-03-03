# Codebase Structure

**Analysis Date:** 2026-03-03

## Directory Layout

```
CoreGREJS/
├── apps/
│   ├── backend/                    # NestJS backend API
│   │   ├── src/
│   │   │   ├── main.ts             # Application bootstrap entry point
│   │   │   ├── app.module.ts       # Root NestJS module
│   │   │   ├── common/             # Shared utilities and middleware
│   │   │   │   ├── decorators/     # Custom method decorators
│   │   │   │   ├── guards/         # Authentication and permission guards
│   │   │   │   ├── interceptors/   # Request/response interceptors
│   │   │   │   ├── filters/        # Global exception filters
│   │   │   │   ├── exceptions/     # Custom exception classes
│   │   │   │   └── services/       # Shared services (logger, cache, webhook)
│   │   │   ├── modules/            # Feature modules (modular architecture)
│   │   │   │   ├── auth/           # Authentication and user sessions
│   │   │   │   ├── users/          # User management and profiles
│   │   │   │   ├── produzione/     # Production records and phases
│   │   │   │   ├── quality/        # Quality control records
│   │   │   │   ├── riparazioni/    # Repair management
│   │   │   │   ├── export/         # Export documents and articles master
│   │   │   │   ├── analitiche/     # Analytics, reports, data analysis
│   │   │   │   ├── jobs/           # Async job queue and handlers
│   │   │   │   ├── scm/            # Supply chain management
│   │   │   │   ├── tracking/       # Track lot and cartel information
│   │   │   │   ├── mrp/            # MRP planning
│   │   │   │   ├── etichette/      # Labeling
│   │   │   │   ├── storage/        # File storage (MinIO integration)
│   │   │   │   ├── notifications/  # User notifications
│   │   │   │   ├── widgets/        # Dashboard widgets
│   │   │   │   ├── search/         # Full-text search (Meilisearch)
│   │   │   │   ├── activity-log/   # Activity audit logs
│   │   │   │   ├── settings/       # System settings management
│   │   │   │   ├── cron/           # Scheduled jobs
│   │   │   │   ├── health/         # Health check endpoints
│   │   │   │   ├── database/       # Database utilities
│   │   │   │   ├── data-management/# Data import/export utilities
│   │   │   │   ├── email/          # Email service integration
│   │   │   │   ├── mobile/         # Mobile API endpoints
│   │   │   │   ├── inwork/         # Work-in-progress tracking
│   │   │   │   └── file-manager/   # File upload/download management
│   │   │   ├── prisma/             # Prisma ORM service
│   │   │   └── services/           # Standalone services (MinIO)
│   │   ├── prisma/                 # Database schema and migrations
│   │   │   ├── schema.prisma       # Database model definitions
│   │   │   ├── seed.ts             # Database seeding script
│   │   │   └── migrations/         # Generated migration files
│   │   ├── public/                 # Static assets (served via Express static)
│   │   ├── storage/                # Runtime storage (exports, jobs, quality)
│   │   ├── dist/                   # Compiled JavaScript output
│   │   ├── package.json
│   │   ├── tsconfig.json
│   │   └── nest-cli.json           # NestJS CLI configuration
│   │
│   ├── frontend/                   # Next.js web application
│   │   ├── src/
│   │   │   ├── app/                # Next.js App Router
│   │   │   │   ├── (auth)/         # Route group for login
│   │   │   │   │   └── login/      # Login page
│   │   │   │   ├── (dashboard)/    # Route group for authenticated pages
│   │   │   │   │   ├── produzione/ # Production management
│   │   │   │   │   ├── quality/    # Quality control
│   │   │   │   │   ├── riparazioni/# Repair management
│   │   │   │   │   ├── export/     # Export documents
│   │   │   │   │   ├── analitiche/ # Analytics and reports
│   │   │   │   │   ├── tracking/   # Tracking information
│   │   │   │   │   ├── settings/   # Settings management
│   │   │   │   │   └── [other]/    # Other modules (widgets, etichette, etc.)
│   │   │   │   ├── db-error/       # Database error page
│   │   │   │   ├── layout.tsx      # Root layout with React Query provider
│   │   │   │   ├── globals.css     # Global styles
│   │   │   │   └── error.tsx       # Error boundary
│   │   │   ├── components/         # Reusable React components
│   │   │   │   ├── layout/         # Layout components (sidebar, header)
│   │   │   │   ├── dashboard/      # Dashboard-specific components
│   │   │   │   ├── auth/           # Auth-related components
│   │   │   │   ├── ui/             # Base UI components
│   │   │   │   ├── errors/         # Error display components
│   │   │   │   ├── export/         # Export feature components
│   │   │   │   ├── riparazioni/    # Repair feature components
│   │   │   │   └── ServerStatusOverlay.tsx # Connectivity status indicator
│   │   │   ├── store/              # Zustand state management
│   │   │   │   ├── auth.ts         # Authentication state (user, token, permissions)
│   │   │   │   ├── dashboard.ts    # Dashboard state (sidebar, modules)
│   │   │   │   ├── modules.ts      # Module visibility state
│   │   │   │   └── notifications.ts# Notification queue state
│   │   │   ├── lib/                # Utility functions and API client
│   │   │   │   ├── api.ts          # Axios instance with interceptors
│   │   │   │   └── utils.ts        # Helper functions
│   │   │   ├── types/              # TypeScript type definitions
│   │   │   └── hooks/              # Custom React hooks (currently empty)
│   │   ├── public/                 # Static assets
│   │   ├── next.config.js
│   │   ├── tsconfig.json
│   │   ├── package.json
│   │   └── tailwind.config.ts      # Tailwind CSS configuration
│   │
│   └── mobile/                     # Next.js PWA for mobile
│       ├── src/                    # Same structure as frontend (app, components, store)
│       ├── public/
│       ├── pwa-manifest.json       # PWA configuration
│       ├── package.json
│       └── tsconfig.json
│
├── .planning/                      # Planning and documentation
│   └── codebase/                   # Codebase analysis documents
│
├── volumes/                        # Docker volumes for persistent data
├── node_modules/                   # Monorepo root dependencies
├── docker-compose.yml              # Services orchestration (MySQL, Redis, MinIO, Meilisearch)
├── package.json                    # Monorepo root workspace definition
├── package-lock.json
├── tsconfig.json                   # Base TypeScript configuration
└── README.md
```

## Directory Purposes

**apps/backend/src/main.ts:**
- Purpose: Bootstrap NestJS application, configure global middleware
- Responsibilities: Create app instance, register global filters and pipes, enable CORS, setup Swagger documentation, start HTTP server

**apps/backend/src/app.module.ts:**
- Purpose: Root module importing all feature modules
- Responsibilities: Module orchestration, global configuration setup

**apps/backend/src/common/:**
- Purpose: Centralized cross-cutting concerns shared across all modules
- Decorators: `@RequirePermissions()`, `@LogActivity()`, `@Public()`
- Guards: JWT validation (`JwtAuthGuard`), Permission checking (`PermissionsGuard`)
- Interceptors: Activity logging and webhook dispatch (`ActivityLogInterceptor`)
- Filters: Exception mapping to HTTP responses (`AllExceptionsFilter`, `PrismaExceptionFilter`)
- Services: Logger, cache, webhook dispatcher

**apps/backend/src/modules/:**
- Purpose: Feature-driven modular architecture
- Each module self-contained with controller, service, DTOs
- Modules declare dependencies via imports in module decorator
- Examples: production records, quality control, repairs, exports, analytics

**apps/backend/prisma/schema.prisma:**
- Purpose: Database schema definition (single source of truth for data model)
- Contains: User, Permission, ActivityLog, ProductionRecord, QualityRecord, all entity models
- Maps: Prisma schema → MySQL tables, indexes, relationships

**apps/backend/prisma/migrations/:**
- Purpose: Version-controlled database changes
- Generated: `prisma migrate dev` creates new migration files
- Deployed: `prisma migrate deploy` applies migrations in production

**apps/backend/storage/:**
- Purpose: Runtime directory for file output
- Subdirs: `/export` (DDT, article files), `/jobs` (report PDFs), `/quality` (quality records)
- Served: Via Express static middleware at `/storage/` prefix

**apps/frontend/src/app/:**
- Purpose: Next.js routing and page structure
- Route groups: `(auth)` for login, `(dashboard)` for authenticated area
- Dynamic routes: `[progressivo]`, `[id]` for entity-specific pages
- Root layout: Provides React Query client and global providers

**apps/frontend/src/components/:**
- Purpose: Reusable React components organized by feature
- Patterns: Functional components, hooks for local state
- Styling: Tailwind CSS classes, emotion for dynamic styles, MUI for data tables

**apps/frontend/src/store/:**
- Purpose: Global state management via Zustand
- Auth store: Persists user, token, permissions to localStorage
- Dashboard store: Sidebar state, module visibility
- Middleware: `persist` with `localStorage` backend for hydration

**apps/frontend/src/lib/api.ts:**
- Purpose: Axios HTTP client with global interceptors
- Request interceptor: Injects JWT token from localStorage
- Response interceptor: Handles 401 (logout), 503 (database error page), generic error handling

## Key File Locations

**Entry Points:**
- `apps/backend/src/main.ts`: Backend HTTP server bootstrap
- `apps/frontend/src/app/layout.tsx`: Frontend root layout and React Query setup
- `apps/mobile/src/app/layout.tsx`: Mobile PWA layout

**Database:**
- `apps/backend/prisma/schema.prisma`: Data model definitions
- `apps/backend/src/prisma/prisma.service.ts`: Prisma client wrapper

**Authentication & Authorization:**
- `apps/backend/src/modules/auth/auth.service.ts`: Login, password reset, profile management
- `apps/backend/src/modules/auth/auth.controller.ts`: Auth endpoints (login, logout, profile)
- `apps/backend/src/modules/users/users.service.ts`: User CRUD, permission assignment
- `apps/backend/src/common/guards/jwt-auth.guard.ts`: JWT token validation
- `apps/backend/src/common/guards/permissions.guard.ts`: Permission bitmask evaluation
- `apps/frontend/src/store/auth.ts`: Client-side auth state and helpers

**API Communication:**
- `apps/frontend/src/lib/api.ts`: Axios instance with interceptors
- `apps/backend/src/modules/*/[module].controller.ts`: API endpoint definitions

**Job Processing:**
- `apps/backend/src/modules/jobs/jobs.queue.ts`: BullMQ queue configuration and worker
- `apps/backend/src/modules/jobs/handlers/`: Job handler functions for async work
- `apps/backend/src/modules/jobs/jobs.service.ts`: Job status tracking and management

**Export/Report Generation:**
- `apps/backend/src/modules/export/export.service.ts`: Export document management
- `apps/backend/src/modules/jobs/handlers/export.*.ts`: PDF/Excel generation handlers
- `apps/backend/src/modules/analitiche/analitiche.service.ts`: Analytics data queries

**Error Handling:**
- `apps/backend/src/common/filters/all-exceptions.filter.ts`: Global exception handler
- `apps/backend/src/common/filters/prisma-exception.filter.ts`: Prisma-specific error mapping
- `apps/frontend/src/components/ServerStatusOverlay.tsx`: Connectivity status display

**Activity Logging:**
- `apps/backend/src/modules/activity-log/activity-log.service.ts`: Log record persistence
- `apps/backend/src/common/interceptors/activity-log.interceptor.ts`: Log event capture

**State Management:**
- `apps/frontend/src/store/auth.ts`: User state with permissions and token
- `apps/frontend/src/store/dashboard.ts`: UI state (sidebar, module state)

## Naming Conventions

**Files:**
- `*.controller.ts`: HTTP endpoint handlers (one per module typically)
- `*.service.ts`: Business logic implementation (one per domain responsibility)
- `*.module.ts`: NestJS module definitions
- `*.dto.ts`: Request/response data transfer objects
- `*.spec.ts`: Jest test files (none currently present)
- `page.tsx`: Next.js route files
- `layout.tsx`: Next.js layout files
- `store/`: Zustand store files, one per feature (`auth.ts`, `dashboard.ts`)

**Directories:**
- `modules/`: Feature modules named after domain (`produzione`, `quality`, `export`)
- `handlers/`: Job handler files named as `{domain}.{job-type}-{format}.ts` (e.g., `export.ddt-pdf.ts`)
- `src/app/(groupname)/`: Route groups using parentheses for organization
- `src/components/{feature}/`: Components grouped by feature area

**Variables & Functions:**
- camelCase: Function/variable names (`getUserPermissions`, `isModuleEnabled`, `validateRecord`)
- PascalCase: Class/interface names (`ProduzioneService`, `User`, `PermissionGuard`)
- UPPER_SNAKE_CASE: Constants and permission levels (`PERM.READ`, `QUEUE_NAME`)
- Bitmask permissions: Single-letter + number (`R=1, C=2, U=4, D=8` corresponding to bitmask bits)

**API Routes:**
- `/api/{module}/{resource}`: Standard REST pattern (e.g., `/api/produzione/records`)
- `/api/{module}/{resource}/{id}`: Single resource (e.g., `/api/produzione/records/123`)
- `/api/{module}/{action}`: Actions on collection (e.g., `/api/export/reports`)
- Query params for filtering: `?search=term&status=active`

## Where to Add New Code

**New Feature Module:**

1. Create directory: `apps/backend/src/modules/{module-name}/`
2. Create module class: `{module-name}.module.ts` with `@Module()` decorator
3. Create controller: `{module-name}.controller.ts` with `@Controller()` and endpoints
4. Create service: `{module-name}.service.ts` with `@Injectable()` and business logic
5. Create DTOs: `dto/{create|update}-{entity}.dto.ts` with validation decorators
6. Import module in `apps/backend/src/app.module.ts`
7. Add corresponding page in frontend: `apps/frontend/src/app/(dashboard)/{module-name}/page.tsx`
8. Add component folder in frontend: `apps/frontend/src/components/{module-name}/`
9. Add permissions decorator to endpoints: `@RequirePermissions('{module-name}')`

**New API Endpoint:**

1. Add method to existing `{module}.controller.ts` with HTTP decorator (`@Get`, `@Post`, etc.)
2. Implement business logic in `{module}.service.ts`
3. Create DTO in `dto/` folder for request validation
4. Apply guards: `@UseGuards(JwtAuthGuard, PermissionsGuard)`
5. Apply decorators: `@RequirePermissions()`, `@LogActivity()` if logging needed
6. Add Swagger docs: `@ApiOperation()`, `@ApiResponse()`, `@ApiTags()`
7. Corresponding frontend API call in component using axios from `src/lib/api.ts`

**New Background Job:**

1. Define job type in `apps/backend/src/modules/jobs/types.ts`
2. Create handler function: `apps/backend/src/modules/jobs/handlers/{domain}.{job-type}.ts`
3. Export handler in `apps/backend/src/modules/jobs/handlers/index.ts` map
4. Call `JobsQueueService.enqueueJob()` from controller
5. Frontend polls `/api/jobs/{jobId}` or listens for completion event

**New Page (Frontend):**

1. Create file: `apps/frontend/src/app/(dashboard)/{path}/page.tsx`
2. Mark as `'use client'` if using hooks or state
3. Import data fetching hooks using axios from `src/lib/api.ts`
4. Use Zustand auth store via `useAuthStore()` for permission checks
5. Render components from `src/components/` folder
6. Wrap with permission checks: `hasPermission()` from auth store

**New Database Entity:**

1. Add model to `apps/backend/prisma/schema.prisma`
2. Run `prisma migrate dev` to generate and create migration
3. Create service with CRUD methods
4. Create controller with endpoints
5. Create module to wrap service and controller
6. Import module in `app.module.ts`

## Special Directories

**apps/backend/dist/:**
- Purpose: Compiled JavaScript output from TypeScript
- Generated: By `npm run build` (NestJS compilation)
- Committed: No (in .gitignore)
- Usage: Served by `node dist/main` in production

**apps/backend/storage/:**
- Purpose: Runtime file storage for exports, reports, quality documents
- Generated: By application at runtime (job handlers, export service)
- Committed: No (not tracked in git)
- Cleanup: Manual via `/api/storage/cleanup` or cron job

**apps/frontend/.next/:**
- Purpose: Next.js build cache and compiled bundle
- Generated: By `npm run build`
- Committed: No (in .gitignore)
- Usage: Served by `next start` in production

**apps/backend/prisma/migrations/:**
- Purpose: Version-controlled database schema changes
- Generated: By `prisma migrate dev` when schema changes
- Committed: Yes (critical for database reproducibility)
- Naming: YYYYMMDDHHMMSS_{description} (timestamp-based)

**.planning/codebase/:**
- Purpose: Codebase analysis and architecture documentation
- Contents: ARCHITECTURE.md, STRUCTURE.md, CONVENTIONS.md, TESTING.md, CONCERNS.md
- Generated: By `/gsd:map-codebase` orchestrator command
- Committed: Yes (for team reference and planning)

---

*Structure analysis: 2026-03-03*
