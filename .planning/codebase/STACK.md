# Technology Stack

**Analysis Date:** 2026-03-03

## Languages

**Primary:**
- TypeScript 5.3.3 - Backend (NestJS), Frontend (Next.js), Mobile (Next.js)
- JavaScript - Runtime and build tools

**Secondary:**
- Shell scripts - Docker setup and initialization

## Runtime

**Environment:**
- Node.js 18.0.0+ (specified in root `package.json` engines)
- npm 9.0.0+

**Package Manager:**
- npm - Monorepo management with workspaces
- Lockfile: `package-lock.json` present (807 KB)

## Frameworks

**Core:**
- NestJS 10.3.0 - Backend REST API and business logic (`apps/backend`)
- Next.js 14.2.35 - Frontend SPA (`apps/frontend`) and Mobile PWA (`apps/mobile`)
- React 18.3.0 - UI component framework (overridden globally via root `package.json`)
- Express - Underlying HTTP server via `@nestjs/platform-express`

**UI Components:**
- Material-UI (@mui/material) 6.0.0 - Material Design components
- @mui/x-data-grid 7.0.0 - Data tables
- @mui/x-date-pickers 7.0.0 - Date selection
- Framer Motion 12.23.24 - Animations and transitions
- Recharts 2.12.0 - Charts and graphs
- notistack 3.0.0 - Toast notifications

**Testing:**
- Jest 29.7.0 - Unit testing framework
- @nestjs/testing 10.3.0 - NestJS testing utilities
- ts-jest 29.1.1 - TypeScript support for Jest

**Build/Dev:**
- Webpack (via Next.js and NestJS) - Module bundling
- TypeScript Compiler - Native TS compilation
- ESLint 8.56.0 - Code linting
- Prettier 3.2.4 - Code formatting
- ts-loader 9.5.1 - TypeScript webpack loader
- tsx 4.7.0 - TypeScript execution without compilation
- Tailwind CSS 4.1.17 - Utility-first CSS framework (frontend only)
- PostCSS 8.5.6 - CSS transformations

## Key Dependencies

**Critical:**
- @prisma/client 5.22.0 - Type-safe ORM for MySQL database
- prisma 5.22.0 - Schema management and migrations
- axios 1.13.5 (backend), 1.6.0 (frontend) - HTTP client for API communication
- bullmq 5.7.16 - Task queue and job processor using Redis
- nodemailer 6.9.0 - Email sending via SMTP

**Infrastructure & Storage:**
- minio 8.0.2 - S3-compatible object storage client
- ioredis - Redis client for caching and job queue (via bullmq)

**Authentication & Security:**
- @nestjs/jwt 10.2.0 - JWT token generation and validation
- @nestjs/passport 10.0.3 - Authentication middleware integration
- passport 0.7.0 - Authentication framework
- passport-jwt 4.0.1 - JWT strategy for Passport
- passport-local 1.0.0 - Local username/password strategy
- bcrypt 5.1.1 - Password hashing and verification

**Data Validation & Transformation:**
- class-validator 0.14.1 - Data validation decorators
- class-transformer 0.5.1 - Object transformation and serialization
- yup 1.4.0 - Schema validation (frontend)
- react-hook-form 7.51.0 - Form state management (frontend)
- @hookform/resolvers 3.3.0 - Validation library integration

**PDF & Document Generation:**
- pdfkit 0.15.2 (backend), 0.17.2 (root) - PDF generation
- pdf-lib 1.17.1 - PDF manipulation
- archiver 7.0.1 - ZIP archive creation for exports
- exceljs 4.4.0 - Excel file generation and parsing
- xlsx 0.18.5 - Excel/CSV reading (frontend)

**Code Generation & Data Encoding:**
- bwip-js 4.8.0 - Barcode generation
- qrcode 1.5.3 - QR code generation (backend and frontend)

**UI Libraries:**
- @emotion/react 11.11.0, @emotion/styled 11.11.0 - CSS-in-JS styling
- @emotion/cache 11.11.0 - Emotion cache setup
- recharts 2.12.0 - Data visualization
- react-chartjs-2 5.3.1 - Chart.js integration
- chart.js 4.5.1 - Chart library
- react-grid-layout 1.5.3 - Dashboard widget layout system
- sonner 2.0.7 - Toast notifications (alternative to notistack)

**Data Management (Frontend):**
- @tanstack/react-query 5.0.0 - Server state and caching
- @tanstack/react-table 8.21.3 - Headless table library
- zustand 4.5.0 - Global state management
- date-fns 3.0.0 - Date manipulation

**Utilities:**
- reflect-metadata 0.2.1 - Reflection API for decorators (NestJS dependency)
- rxjs 7.8.1 - Reactive programming (NestJS core)
- tsconfig-paths 4.2.0 - Path alias resolution
- source-map-support 0.5.21 - Stack trace mapping for errors

## Configuration

**Environment:**
- Environment variables via `.env` file (dotenv)
- Key configs: Database URL, Redis connection, MinIO credentials, JWT secrets, SMTP settings, API URLs
- .env file present (contains configuration, not readable for security)
- .env.example file present for template reference (`/c/Users/Stefano/Desktop/CoreSuite/CoreGREJS/.env.example`)

**Build:**
- NestJS: `nest build` compiles TypeScript to `dist/` directory
- Frontend: `next build` creates optimized Next.js build
- Mobile: `next build` creates mobile PWA build

**TypeScript Configuration:**
- `apps/backend/tsconfig.json` - Backend-specific TS config
- `apps/frontend/tsconfig.json` - Frontend-specific TS config
- Base configuration with strict mode and strict null checks enabled

## Platform Requirements

**Development:**
- Docker and Docker Compose for containerized services
- MySQL 5.7+ (connected via connection string in .env)
- Redis (for caching and job queue)
- MinIO (for file storage, S3-compatible)
- Node.js 18.0.0+
- CUPS (printer system) for barcode/label printing support

**Production:**
- Docker containers for backend, frontend, and mobile apps
- External MySQL database (shared CoreServices MySQL)
- External Redis instance (shared CoreServices Redis)
- External MinIO instance (shared CoreServices MinIO)
- External network connectivity for SMTP server
- Port 3011 - Backend API
- Port 3010 - Frontend web UI
- Port 3012 - Mobile PWA
- Timezone: Europe/Rome (set in docker-compose.yml)

**Deployment:**
- Docker Compose orchestration
- Three independent services: backend, frontend, mobile
- Health checks configured (HTTP endpoints) with 30s intervals
- Portainer support (labels configured in docker-compose.yml)
- Shared external network `core-network` for CoreSuite integration

---

*Stack analysis: 2026-03-03*
