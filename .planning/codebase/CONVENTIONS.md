# Coding Conventions

**Analysis Date:** 2026-03-03

## Naming Patterns

**Files:**
- `*.controller.ts` - NestJS controllers (e.g., `auth.controller.ts`)
- `*.service.ts` - Business logic services (e.g., `auth.service.ts`)
- `*.dto.ts` - Data transfer objects with validation (e.g., `login.dto.ts`)
- `*.decorator.ts` - Custom NestJS decorators (e.g., `permissions.decorator.ts`)
- `*.filter.ts` - Global exception filters (e.g., `all-exceptions.filter.ts`)
- `*.guard.ts` - Authentication/authorization guards (e.g., `jwt-auth.guard.ts`)
- `*.module.ts` - NestJS feature modules (e.g., `auth.module.ts`)
- `*.tsx` / `*.ts` - React components and utilities in Next.js

**Functions & Methods:**
- camelCase for all function and method names: `validateUser`, `getProfile`, `updateProfile`
- Action verbs for service methods: `create`, `update`, `delete`, `get`, `find`, `log`
- Async functions prefixed with `async` keyword: `async login()`, `async getProfile()`

**Variables & Constants:**
- camelCase for variables and parameters: `userId`, `userName`, `isAuthenticated`
- UPPER_SNAKE_CASE for constants and keys: `PERMISSIONS_KEY`, `PERM_ACTION_KEY`, `API_URL`
- Prefix boolean variables/parameters with verbs: `isAuthenticated`, `isLoading`, `success`

**Types & Interfaces:**
- PascalCase for interfaces and types: `LoginDto`, `AuthState`, `BreadcrumbItem`, `BreadcrumbProps`
- Suffix DTOs with `Dto`: `LoginDto`, `ChangePasswordDto`, `UpdateProfileDto`
- Suffix state types with `State`: `AuthState`
- Suffix props types with `Props`: `BreadcrumbProps`

**Backend Naming (NestJS):**
- Modules group related features: `AuthModule`, `UsersModule`, `RiparazioniModule`
- Controllers handle HTTP: `AuthController`, `ActivityLogController`
- Services contain logic: `AuthService`, `ActivityLogService`
- DTOs validate input with class-validator decorators
- Decorators for cross-cutting concerns: `@RequirePermissions`, `@LogActivity`, `@Public`

**Frontend Naming (Next.js/React):**
- Page components in `app/` directory: `page.tsx` (lowercase)
- Layout components: `layout.tsx` (lowercase)
- Reusable components in `components/`: PascalCase (e.g., `Breadcrumb.tsx`)
- Store files in `store/`: camelCase (e.g., `auth.ts`, `notifications.ts`)
- Utility/API files in `lib/`: camelCase (e.g., `api.ts`)

## Code Style

**Formatting:**
- Prettier 3.2.4 configured for consistent formatting
- Single quotes for strings (configured in ESLint)
- Semicolons at end of statements
- 2-space indentation

**Linting:**
- ESLint 8.56.0 for TypeScript code
- ESLint rules: `eslint-plugin-prettier`, `eslint-config-prettier`
- Backend ESLint config: uses `@typescript-eslint/parser` and `@typescript-eslint/eslint-plugin`
- Frontend uses Next.js ESLint config: `eslint-config-next`
- Run linting with: `npm run lint` (auto-fixes formatting issues)

**Backend TypeScript Config** (`apps/backend/tsconfig.json`):
- Target: ES2021
- Module: CommonJS
- Strict mode disabled (strictNullChecks: false, noImplicitAny: false)
- Path alias: `@/*` maps to `src/*`
- Source maps enabled for debugging

**Frontend TypeScript Config** (`apps/frontend/tsconfig.json`):
- Target: ES5 (for browser compatibility)
- Module: ESNext
- Module resolution: bundler
- Strict mode enabled (strict: true)
- JSX: preserve (for Next.js processing)
- Path alias: `@/*` maps to `./src/*`
- Incremental compilation enabled

## Import Organization

**Order (Backend):**
1. NestJS core imports
2. NestJS feature imports (guards, decorators, etc.)
3. Third-party libraries
4. Project internal imports (relative paths starting with `./` or using `@/`)

Example from `auth.service.ts`:
```typescript
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { PrismaService } from '../../prisma/prisma.service';
import * as bcrypt from 'bcrypt';
```

**Order (Frontend):**
1. React and Next.js core imports
2. Third-party UI and animation libraries
3. Store and state management imports
4. Custom hooks and utilities
5. Internal component and API imports

Example from `login/page.tsx`:
```typescript
import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { motion } from 'framer-motion';
import { useAuthStore } from '@/store/auth';
import { authApi } from '@/lib/api';
import { showError } from '@/store/notifications';
```

**Path Aliases:**
- Backend: `@/*` resolves to `src/` directory
- Frontend: `@/*` resolves to `src/` directory
- Use absolute imports with aliases instead of relative `../../../` paths

## Error Handling

**Patterns:**
- Backend uses global exception filters for consistent error responses
- Throw NestJS HTTP exceptions: `UnauthorizedException`, `BadRequestException`, `NotFoundException`
- Include `statusCode`, `error`, `message`, and `timestamp` in error responses
- Development mode includes `details` field with stack traces

**Custom Exceptions:**
- `DatabaseException` for database-related errors (`src/common/exceptions/database.exception.ts`)
- `FileNotFoundException` for missing files (`src/common/exceptions/file-not-found.exception.ts`)
- Wrapped in filters to provide consistent JSON responses

**Frontend Error Handling:**
- API interceptor checks for network, server, and database errors
- Errors are caught in try-catch blocks and displayed via notification system
- Database errors redirect to `/db-error` page
- Server errors handled separately from database errors

**Logging in Error Context:**
- Use `LoggerService` for structured logging with context
- Include method, path, status code, and duration
- Development environment includes full stack traces
- Production environment sanitizes error details

## Logging

**Framework:** Custom `LoggerService` implementing NestJS `LoggerService`

**Patterns:**
- Structured logging with timestamp, context, and colored output
- Methods: `log()`, `error()`, `warn()`, `debug()`, `verbose()`, `success()`
- Context parameter for identifying log source (e.g., "Auth", "Database", "Bootstrap")
- Italian language messages throughout logger
- Colors: Cyan (INFO), Yellow (WARN), Red (ERROR), Magenta (DEBUG), Green (SUCCESS)

**Database Logging:**
- `logDatabaseConnection()` - Connection status
- `logDatabaseQuery()` - Query execution with duration
- `logHttpRequest()` - HTTP requests with method, path, status code, duration
- `logAuthentication()` - Login success/failure with username
- `logModuleInit()` - Module initialization tracking

**Usage Example:**
```typescript
this.logger.log("User created successfully", "UsersService");
this.logger.error("Database connection failed", error.stack, "Database");
this.logHttpRequest("POST", "/api/users", 201, 45);
```

## Comments

**When to Comment:**
- Complex business logic that isn't immediately obvious
- Non-obvious parameter meanings or return values
- Workarounds for third-party library issues
- Important assumptions or constraints
- JSDoc comments for public APIs and parameters

**JSDoc/TSDoc:**
- Used on service methods and exported functions
- Include `@param`, `@returns` for complex operations
- Used on DTOs with `@ApiProperty` for Swagger documentation

**Example:**
```typescript
/**
 * Log an activity
 */
async log(params: LogActivityParams): Promise<void> {
  // Implementation...
}
```

**Backend Controllers:**
- `@ApiTags()` for grouping in Swagger UI
- `@ApiOperation()` for endpoint description
- `@ApiBearerAuth()` for protected routes
- DTOs use `@ApiProperty()` for field documentation

## Function Design

**Size:**
- Aim for single responsibility: each function does one thing
- Services keep business logic, controllers handle HTTP concerns
- Break down complex operations into smaller, composable functions

**Parameters:**
- Prefer objects/interfaces over multiple scalar parameters
- DTOs for request bodies with validation
- Include context parameter for logging

**Return Values:**
- Use interfaces/types for complex return objects
- Async functions return Promises: `Promise<T>`
- Service methods return DTOs or data models
- Controllers return structured JSON responses

**Example (Service):**
```typescript
async login(user: any) {
  const payload = {
    sub: user.id,
    username: user.userName,
  };

  return {
    access_token: this.jwtService.sign(payload),
    user: { /* ... */ },
  };
}
```

## Module Design

**Exports:**
- NestJS modules use `@Module()` decorator with imports/exports
- Services are provided in module (dependency injection)
- Controllers define routes
- Services are private to module unless exported

**Barrel Files:**
- Minimal use of barrel files; direct imports preferred
- When used: group related exports in `index.ts`

**Dependency Injection:**
- Constructor-based injection in NestJS: `constructor(private service: Service) {}`
- Services injected as private readonly properties
- PrismaService injected for database access

**Example Module:**
```typescript
@Module({
  imports: [PrismaModule, JwtModule],
  controllers: [AuthController],
  providers: [AuthService],
  exports: [AuthService],
})
export class AuthModule {}
```

## Frontend State Management

**Framework:** Zustand with persistence middleware

**Patterns:**
- Store files in `src/store/`
- Create store with `create<StateInterface>()`
- Use `persist()` middleware for localStorage persistence
- Methods return void or Promise
- Use `get()` and `set()` for state updates

**Example (auth.ts):**
```typescript
export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      setAuth: (user, token) => {
        set({ user, token, isAuthenticated: true });
      },
    }),
    {
      name: 'coregre-auth',
      storage: createJSONStorage(() => localStorage),
    }
  )
);
```

## Backend Decorators

**Permission System:**
- `@RequirePermissions(...permissions)` - Require module access
- `@RequirePermLevel(level)` - Require permission level (READ, CREATE, UPDATE, DELETE)
- `@LogActivity(metadata)` - Log method execution
- `@Public()` - Skip authentication for endpoint

**Permission Levels (Bitmask):**
- NONE: 0
- READ: 1 (bit 0)
- CREATE: 3 (bits 0-1)
- UPDATE: 7 (bits 0-2)
- DELETE: 15 (bits 0-3)

**Guards:**
- `JwtAuthGuard` - Validates JWT tokens
- `LocalAuthGuard` - Basic username/password authentication
- `PermissionsGuard` - Checks module and level permissions
- Guards applied with `@UseGuards()` decorator

## API Response Format

**Success Response:**
```json
{
  "access_token": "jwt.token.here",
  "user": {
    "id": 1,
    "userName": "admin",
    "nome": "Administrator",
    "mail": "admin@example.com",
    "permissions": { "riparazioni": 15, "quality": 7 }
  }
}
```

**Error Response:**
```json
{
  "statusCode": 400,
  "error": "Errore Validazione",
  "message": ["Il campo 'username' è obbligatorio"],
  "timestamp": "2026-03-03T10:00:00.000Z",
  "path": "/api/auth/login"
}
```

---

*Convention analysis: 2026-03-03*
