# Testing Patterns

**Analysis Date:** 2026-03-03

## Test Framework

**Runner:**
- Jest 29.7.0 (backend only)
- Config: `apps/backend/package.json` (inline jest config)

**Frontend:**
- No testing framework configured (testing gap - see concerns)

**Assertion Library:**
- Jest built-in matchers

**Run Commands:**
```bash
npm run test              # Run all tests
npm run test:watch       # Watch mode for development
npm run test:cov         # Generate coverage report
```

## Test File Organization

**Backend Location:**
- Pattern: co-located in same directory as source code
- Naming: `*.spec.ts` suffix (configured in `testRegex`)
- Root directory for test execution: `src/`

**Current Status:**
- No test files found in `apps/backend/` (see concerns)
- Test infrastructure is configured but unused

**Frontend:**
- No testing framework configured
- No test files present
- Testing gap for React components and Next.js pages

## Test Structure

**Configuration** (`apps/backend/package.json`):
```json
{
  "jest": {
    "moduleFileExtensions": ["js", "json", "ts"],
    "rootDir": "src",
    "testRegex": ".*\\.spec\\.ts$",
    "transform": {
      "^.+\\.(t|j)s$": "ts-jest"
    },
    "collectCoverageFrom": ["**/*.(t|j)s"],
    "coverageDirectory": "../coverage",
    "testEnvironment": "node"
  }
}
```

**Key Settings:**
- Test regex matches `*.spec.ts` files only
- TypeScript compiled with `ts-jest`
- Coverage directory at `../coverage/` (repo root level)
- Node environment for backend tests
- Source maps support via ts-jest

## Expected Test Suite Organization

**Recommended Pattern** (not yet implemented):
```typescript
describe('AuthService', () => {
  let service: AuthService;
  let prisma: PrismaService;

  beforeEach(async () => {
    // Setup
  });

  afterEach(async () => {
    // Cleanup
  });

  describe('login', () => {
    it('should return access token on valid credentials', async () => {
      // Arrange
      // Act
      // Assert
    });

    it('should throw UnauthorizedException on invalid credentials', async () => {
      // Arrange
      // Act
      // Assert
    });
  });
});
```

## Mocking

**Framework:** Jest mocking built-in

**Patterns (to be implemented):**
- `jest.mock()` for module mocking
- `@nestjs/testing` for NestJS Test Module
- Manual mocks for Prisma database calls
- Jest spies for method call verification

**What to Mock:**
- Database calls (PrismaService)
- External API calls (HTTP clients)
- JWT token generation/verification
- File system operations
- Environment variables

**What NOT to Mock:**
- Business logic implementations
- Internal service methods
- Utility functions
- Type definitions

## Fixtures and Factories

**Test Data:**
- No fixtures or factories currently implemented (gap)
- Should create factories for:
  - User objects with various permission levels
  - DTOs with different validation states
  - Database models for integration tests

**Recommended Location:**
- `test/fixtures/` - Static test data
- `test/factories/` - Factory functions for dynamic test data

**Example Pattern (not implemented):**
```typescript
export const createMockUser = (overrides?: Partial<User>): User => ({
  id: 1,
  userName: 'testuser',
  nome: 'Test User',
  mail: 'test@example.com',
  password: 'hashedpassword',
  permissions: { riparazioni: 15 },
  ...overrides,
});
```

## Coverage

**Requirements:** Not enforced

**View Coverage:**
```bash
npm run test:cov
# Generates coverage report in apps/backend/coverage/
```

**Coverage Metrics Available:**
- Line coverage
- Branch coverage
- Function coverage
- Statement coverage

**Current State:**
- Coverage infrastructure exists but no tests run it
- Coverage thresholds not configured

## Test Types

**Unit Tests (Not Yet Implemented):**
- Should test individual services and utilities
- Mock all external dependencies
- Test error handling and edge cases
- Scope: Single class/function isolation

**Integration Tests (Not Yet Implemented):**
- Should test service interactions with database
- Use test database or in-memory database
- Test decorator and guard behavior
- Scope: Multiple components working together

**E2E Tests (Not Implemented):**
- Framework: None configured
- Recommendation: Consider Cypress or Playwright
- Scope: Full user flows through the application

## Async Testing

**Recommended Pattern (for backend):**
```typescript
it('should retrieve user profile', async () => {
  // Async test using async/await
  const user = await authService.getProfile(1);
  expect(user).toBeDefined();
  expect(user.id).toBe(1);
});
```

**Promise-based Alternative:**
```typescript
it('should handle async operations', () => {
  return authService.login(user).then(result => {
    expect(result).toHaveProperty('access_token');
  });
});
```

## Error Testing

**Recommended Pattern (not implemented):**
```typescript
describe('error handling', () => {
  it('should throw UnauthorizedException for invalid password', async () => {
    // Use jest.toThrow or expect with async throws
    await expect(
      authService.changePassword(1, 'wrong', 'new')
    ).rejects.toThrow(UnauthorizedException);
  });

  it('should catch database errors', async () => {
    // Mock PrismaService to throw error
    jest.spyOn(prisma.user, 'findUnique').mockRejectedValueOnce(
      new Error('Database connection failed')
    );

    await expect(authService.getProfile(1)).rejects.toThrow();
  });
});
```

## Testing Best Practices (To Implement)

**Service Testing:**
- Test all public methods of services
- Mock PrismaService for database operations
- Test error conditions and edge cases
- Verify async operations complete correctly

**Controller Testing:**
- Mock underlying services
- Test route parameters and query strings
- Verify guard/decorator application
- Test response formats

**Guard/Interceptor Testing:**
- Test permission checking logic
- Mock request/response objects
- Verify exception throwing
- Test metadata reading

**DTO Testing:**
- Validate class-validator decorators
- Test validation success and failure
- Test transformation behavior

## Current Testing Gaps

**Backend:**
- Zero test coverage despite Jest configuration
- No service tests
- No controller tests
- No DTO validation tests
- No database integration tests
- No guard/decorator tests

**Frontend:**
- No testing framework configured
- No component tests
- No hook tests
- No page tests
- No integration tests with API

**Recommendations (Priority Order):**
1. Implement backend unit tests for core services (auth, users)
2. Add integration tests for database operations
3. Configure frontend testing (Jest + React Testing Library)
4. Add component snapshot tests
5. Implement E2E testing for critical user flows

## Testing Dependencies (Backend)

**Installed:**
- `jest` 29.7.0
- `ts-jest` 29.1.1 - TypeScript support for Jest
- `@nestjs/testing` 10.3.0 - NestJS testing utilities
- `@types/jest` - TypeScript definitions

**Missing (Recommended):**
- `jest-mock-extended` - Better mocking for TypeScript
- `supertest` - HTTP assertion library for controller tests
- Test database setup (test containers or in-memory DB)

## Testing Dependencies (Frontend)

**Not Configured:**
- `jest` not configured for frontend
- `@testing-library/react` not installed
- `@testing-library/jest-dom` not installed
- No TypeScript test types

**Recommended Setup:**
```json
{
  "devDependencies": {
    "jest": "^29.7.0",
    "@testing-library/react": "^14.0.0",
    "@testing-library/jest-dom": "^6.1.0",
    "@types/jest": "^29.5.0",
    "ts-jest": "^29.1.1"
  }
}
```

---

*Testing analysis: 2026-03-03*
