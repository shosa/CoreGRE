# Codebase Concerns

**Analysis Date:** 2026-03-03

## Tech Debt

**Overly Permissive CORS Configuration:**
- Issue: CORS is configured with `origin: true` and `credentials: true` without domain restrictions
- Files: `apps/backend/src/main.ts` (line 32-35)
- Impact: Any origin can make authenticated requests to the API, increasing attack surface
- Fix approach: Restrict CORS to specific frontend domains using `origin: ['https://yourdomain.com']` and maintain a whitelist in environment variables

**Deprecated and Stubbed API Methods:**
- Issue: `findAllLinee()` in riparazioni service returns empty array with DEPRECATED comment, table was removed but method remains
- Files: `apps/backend/src/modules/riparazioni/riparazioni.service.ts` (line 372-377)
- Impact: Dead code paths that may be called by mobile or legacy integrations, potential for confusion
- Fix approach: Remove deprecated method entirely, audit all callers, add deprecation warnings before removal

**PermissionsGuard Creates Unmanaged PrismaClient:**
- Issue: Guard instantiates new `PrismaClient()` instead of injecting shared service
- Files: `apps/backend/src/common/guards/permissions.guard.ts` (line 18)
- Impact: Multiple database connections created per request, resource leaks, no connection pooling benefit, memory overhead
- Fix approach: Inject PrismaService via constructor and use it instead of standalone client

**Generic Exception Handling with `any` Type:**
- Issue: Multiple catch blocks use `error: any` without proper type narrowing
- Files: `apps/backend/src/main.ts` (line 103), `apps/frontend/src/lib/api.ts` (line 19, 44), and 158+ occurrences across backend
- Impact: Loses error type information, harder to debug, potential null reference errors, type safety compromised
- Fix approach: Use `error: unknown` and narrow types with type guards (`instanceof Error`, property checks)

**Try-Catch Without Proper Error Context:**
- Issue: Error handlers catch errors but lose stack trace context, some errors silently ignored
- Files: `apps/frontend/src/lib/api.ts` (line 44-46) - JSON parsing errors silently ignored
- Impact: Silent failures hard to diagnose, malformed auth tokens could go unnoticed
- Fix approach: Log all caught errors even when handled gracefully, add structured error tracking

## Security Considerations

**Password Reset/Change Lacks Email Verification:**
- Issue: Password change endpoint doesn't verify user identity via email confirmation
- Files: `apps/backend/src/modules/auth/auth.service.ts` (line 80-96)
- Impact: Account takeover if session/token is compromised, no secondary confirmation
- Fix approach: Implement email verification flow before password change, add rate limiting on attempts

**Credentials Stored in localStorage Without Encryption:**
- Issue: Auth token stored directly in localStorage as part of Zustand state
- Files: `apps/frontend/src/lib/api.ts` (line 36), `apps/frontend/src/lib/api.ts` (line 106)
- Impact: XSS vulnerabilities can steal tokens; tokens remain in localStorage after logout
- Fix approach: Use httpOnly cookies for tokens, clear all sensitive data on logout, consider sessionStorage for sensitive data

**Database Connection Status Not Enforced:**
- Issue: PrismaService allows application bootstrap even if database connection fails
- Files: `apps/backend/src/prisma/prisma.service.ts` (line 14-19)
- Impact: API starts in partially degraded state, no health checks before accepting requests
- Fix approach: Fail fast on startup if database unavailable, add mandatory health checks before API handlers

**Module-Level Settings Cache Without Invalidation:**
- Issue: PermissionsGuard caches module enabled/disabled status for 60 seconds, no cache invalidation on settings update
- Files: `apps/backend/src/common/guards/permissions.guard.ts` (line 19-20, 87-102)
- Impact: Changes to module permissions take up to 60 seconds to apply, potential privilege escalation during cache window
- Fix approach: Implement cache invalidation on settings updates, use shorter TTL or event-driven invalidation

**Hardcoded Health Check Endpoint Without Auth:**
- Issue: Health check accessible without authentication
- Files: `apps/backend/src/modules/mobile/discovery.controller.ts` (line 37-40)
- Impact: Allows anyone to probe system status and availability, aids reconnaissance
- Fix approach: Require auth for detailed health checks, return minimal info for public health endpoint

## Performance Bottlenecks

**Large Service Files with Complex Logic:**
- Problem: Multiple service files exceed 1000 lines with intertwined business logic
- Files:
  - `apps/backend/src/modules/settings/settings.service.ts` (1415 lines)
  - `apps/backend/src/modules/produzione/produzione.service.ts` (1193 lines)
  - `apps/backend/src/modules/tracking/tracking.service.ts` (945 lines)
  - `apps/backend/src/modules/export/export.service.ts` (934 lines)
- Cause: Multiple responsibilities per service (export, import, cron jobs, settings all in settings.service.ts)
- Improvement path: Break into smaller focused services (ImportService, CronService, SettingsService), separate concerns by domain

**PDF/Excel Report Handlers with Massive Inline Code:**
- Problem: Report handlers are 1000+ lines of procedural code with limited reusability
- Files:
  - `apps/backend/src/modules/jobs/handlers/export.ddt-completo-pdf.ts` (1612 lines)
  - `apps/backend/src/modules/jobs/handlers/analitiche.report-pdf.ts` (736 lines)
- Cause: No abstraction layer for PDF generation, formatting logic tightly coupled to data queries
- Improvement path: Extract PDF building into composable helper functions, create template system for reports, share column definition logic

**N+1 Query Risk in Export Operations:**
- Problem: Export service likely makes sequential queries per row without batch optimization
- Files: `apps/backend/src/modules/export/export.service.ts` (934 lines)
- Cause: No evidence of batch loading or query aggregation in large export flows
- Improvement path: Use Prisma batch queries, aggregate related data in single query, paginate large exports

**Frontend API Interceptor Makes Network Check on Every 4xx Error:**
- Problem: On network errors, frontend makes extra HTTP call to check database status
- Files: `apps/frontend/src/lib/api.ts` (line 91-101)
- Cause: Synchronous network check in response interceptor blocks error handling
- Improvement path: Implement exponential backoff, cache server status for 5-10 seconds, use separate health check endpoint

**In-Memory Permission Cache Not Distributed:**
- Problem: Each application instance maintains separate permission cache
- Files: `apps/backend/src/common/guards/permissions.guard.ts` (line 19-20)
- Cause: Guard creates unmanaged Prisma client with local cache, no Redis synchronization
- Improvement path: Move to Redis cache for distributed instances, use pub/sub for invalidation events

## Known Issues

**Production Startup When Database Unavailable:**
- Symptoms: API responds with 500 errors on all requests if database is offline at boot; no graceful degradation
- Files: `apps/backend/src/prisma/prisma.service.ts` (line 14-19), `apps/backend/src/main.ts` (line 14-22)
- Trigger: Start backend without MySQL available; application bootstraps successfully; any API call fails
- Workaround: Implement retry logic at startup, ensure MySQL is running before starting backend

**Linee API Still Called by Unknown Clients:**
- Symptoms: Calls to `/riparazioni/linee` endpoint fail silently, clients receive empty response
- Files: `apps/backend/src/modules/riparazioni/riparazioni.service.ts` (line 375-377)
- Trigger: Mobile app or legacy integration calls riparazioni/linee which returns empty array
- Workaround: None - data is gone; clients need to be updated to use alternative approach

**Auth Token Persists After Browser Logout:**
- Symptoms: localStorage still contains old token after logout in some browser scenarios
- Files: `apps/frontend/src/lib/api.ts` (line 106)
- Trigger: User logs out; closes browser tab; token remains in localStorage if clear failed
- Workaround: Manually clear localStorage; don't rely on logout for security

## Fragile Areas

**PermissionsGuard Database Access Pattern:**
- Files: `apps/backend/src/common/guards/permissions.guard.ts`
- Why fragile:
  - Unmanaged PrismaClient in guard creates independent connection
  - Direct database query on every protected request
  - No caching at route handler level, only in local guard instance
  - If permission table structure changes, all endpoints break simultaneously
- Safe modification: Don't modify without adding comprehensive permission tests; coordinate with permission schema changes
- Test coverage: No unit tests visible for permission checking logic

**Settings Service State Management:**
- Files: `apps/backend/src/modules/settings/settings.service.ts` (line 42-49)
- Why fragile:
  - Multiple in-memory state objects (`importProgress`, `pendingImportData`, `pendingAnalysis`)
  - State persists in memory across requests; no cleanup mechanism
  - Manual state management prone to race conditions in concurrent imports
  - No locking mechanism for concurrent import operations
- Safe modification: Add request ID tracking, implement queue-based import locking, move to external queue system
- Test coverage: No visible test coverage for concurrent import scenarios

**PDF/Excel Report Generation:**
- Files: `apps/backend/src/modules/jobs/handlers/export.ddt-completo-pdf.ts`, `export.excel-processor.service.ts`
- Why fragile:
  - Tightly coupled to specific data shapes and database schema
  - Manual string concatenation for file operations
  - No validation of intermediate states during generation
  - Column mapping is hardcoded in multiple places
- Safe modification: Don't modify without end-to-end export tests; changes to schema require report updates
- Test coverage: No visible test coverage for export handlers

**Frontend Auth Store Integration:**
- Files: `apps/frontend/src/lib/api.ts`, localStorage-based auth
- Why fragile:
  - Manual JSON parsing of auth state with silent error handling
  - No schema validation of stored auth object
  - Token expiration not validated before use
  - No automatic token refresh mechanism visible
- Safe modification: Add auth state validation before parsing, implement refresh token flow, add unit tests
- Test coverage: No visible tests for auth interceptor logic

## Scaling Limits

**Unmanaged Database Connections per Guard:**
- Current capacity: One PrismaClient per guard instance (shared across all requests)
- Limit: Database connection pool exhaustion under high permission-check load (50+ concurrent requests)
- Scaling path: Share single PrismaService via dependency injection, use connection pooling middleware, monitor connection count

**In-Memory Export State:**
- Current capacity: Single import/export operation at a time due to in-memory state in SettingsService
- Limit: Only one concurrent import allowed; second import overwrites first import state
- Scaling path: Implement distributed queue (BullMQ already available), move state to Redis, enable concurrent imports per tenant

**Single Instance Permission Cache:**
- Current capacity: Each server instance has local 60-second cache per module
- Limit: In multi-instance deployment, permissions changes take 60+ seconds to propagate; instances have stale data
- Scaling path: Move cache to Redis with pub/sub invalidation, reduce TTL, implement cache coherency protocol

**Token Storage in Browser Memory:**
- Current capacity: Tokens persist until browser closed or cleared manually
- Limit: XSS attack can steal all tokens; no logout invalidation on server
- Scaling path: Implement server-side token blacklist, use httpOnly cookies, add token rotation

## Test Coverage Gaps

**No visible unit/integration tests:**
- What's not tested: Core business logic in service files, permission guard validation, auth flows
- Files: No `.test.ts` or `.spec.ts` files found in `apps/backend/src/modules/`
- Risk: Refactoring breaks untested paths; regressions go unnoticed in production
- Priority: High - recommend test-first approach for all new features

**No tests for concurrent operations:**
- What's not tested: Multiple simultaneous imports, concurrent permission updates, race conditions
- Files: `apps/backend/src/modules/settings/settings.service.ts`
- Risk: Intermittent bugs in production under load; hard to reproduce and debug
- Priority: High for features using shared state

**No tests for API error handling:**
- What's not tested: Exception filter behavior, error response format, permission denied scenarios
- Files: `apps/backend/src/common/filters/all-exceptions.filter.ts`
- Risk: Error responses may not be consistent; clients may not parse errors correctly
- Priority: Medium - add contract tests for error responses

**No tests for PDF/Excel generation:**
- What's not tested: Output format validation, column ordering, data completeness in exports
- Files: `apps/backend/src/modules/jobs/handlers/*.ts`
- Risk: Export files may be corrupted or incomplete; client-side failures on production exports
- Priority: High - add end-to-end export validation tests

**No frontend integration tests:**
- What's not tested: API interceptor behavior, auth flow, error handling, loading states
- Files: `apps/frontend/src/lib/api.ts` (73KB with complex logic)
- Risk: Frontend breaks on API changes; auth flows fail silently
- Priority: High for production stability

## Missing Critical Features

**No Request/Response Logging for Audit Trail:**
- Problem: API calls not logged for compliance/debugging; no audit trail of data access
- Blocks: Regulatory compliance (GDPR audit requirements), debugging production issues
- Workaround: Add middleware to log all requests/responses, implement to `ActivityLog` module

**No Rate Limiting on Authentication:**
- Problem: No protection against brute force attacks on login endpoint
- Blocks: Security hardening, account lockout after failed attempts
- Workaround: Implement express-rate-limit or custom guard with attempt tracking

**No API Versioning Strategy:**
- Problem: No v1/v2 versioning; breaking changes directly affect all clients
- Blocks: Rolling deployments, gradual migrations, backwards compatibility
- Workaround: Add API version prefix to routes, implement deprecation warnings

**No Request Timeout Enforcement:**
- Problem: Long-running exports or reports can hang indefinitely
- Blocks: Resource cleanup, preventing connection exhaustion
- Workaround: Add request timeouts in main.ts or at route level using NestJS timeout decorators

**No Circuit Breaker for Database Failures:**
- Problem: Cascading failures when database is slow/intermittent
- Blocks: Graceful degradation, meaningful error messages to users
- Workaround: Implement circuit breaker pattern for Prisma calls, implement fallback responses

---

*Concerns audit: 2026-03-03*
