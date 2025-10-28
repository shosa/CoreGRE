#!/bin/bash
# ============================================================================
# WEBGRE3 - Docker Pre-flight Check
# Verifica prerequisiti prima di avviare i container
# ============================================================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

echo -e "${BLUE}============================================${NC}"
echo -e "${BLUE}WEBGRE3 - Docker Pre-flight Check${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""

# ============================================================================
# 1. Check Docker
# ============================================================================
echo -n "Checking Docker installation... "
if command -v docker &> /dev/null; then
    DOCKER_VERSION=$(docker --version | cut -d ' ' -f3 | cut -d ',' -f1)
    echo -e "${GREEN}✓${NC} Docker ${DOCKER_VERSION}"
else
    echo -e "${RED}✗ Docker not found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# ============================================================================
# 2. Check Docker Compose
# ============================================================================
echo -n "Checking Docker Compose... "
if command -v docker-compose &> /dev/null; then
    COMPOSE_VERSION=$(docker-compose --version | cut -d ' ' -f3 | cut -d ',' -f1)
    echo -e "${GREEN}✓${NC} Docker Compose ${COMPOSE_VERSION}"
else
    echo -e "${RED}✗ Docker Compose not found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# ============================================================================
# 3. Check Docker is running
# ============================================================================
echo -n "Checking Docker daemon... "
if docker info &> /dev/null; then
    echo -e "${GREEN}✓${NC} Running"
else
    echo -e "${RED}✗ Docker daemon not running!${NC}"
    echo "   Start Docker Desktop and try again."
    ERRORS=$((ERRORS + 1))
fi

# ============================================================================
# 4. Check .env file
# ============================================================================
echo -n "Checking .env file... "
if [ -f .env ]; then
    echo -e "${GREEN}✓${NC} Found"
else
    echo -e "${YELLOW}⚠${NC} Not found"
    echo "   Copy .env.docker to .env and configure it"
    WARNINGS=$((WARNINGS + 1))
fi

# ============================================================================
# 5. Check core-services MySQL
# ============================================================================
echo -n "Checking core-services MySQL container... "
if docker ps | grep -q mysql; then
    MYSQL_CONTAINER=$(docker ps | grep mysql | awk '{print $NF}' | head -1)
    echo -e "${GREEN}✓${NC} Found: ${MYSQL_CONTAINER}"
else
    echo -e "${YELLOW}⚠${NC} MySQL container not found or not running"
    echo "   Make sure core-services stack is running"
    WARNINGS=$((WARNINGS + 1))
fi

# ============================================================================
# 6. Check core-services network
# ============================================================================
echo -n "Checking core-services network... "
if docker network ls | grep -q "core-services"; then
    NETWORK_NAME=$(docker network ls | grep "core-services" | awk '{print $2}' | head -1)
    echo -e "${GREEN}✓${NC} Found: ${NETWORK_NAME}"
else
    echo -e "${YELLOW}⚠${NC} core-services network not found"
    echo "   Check network name with: docker network ls"
    WARNINGS=$((WARNINGS + 1))
fi

# ============================================================================
# 7. Check port availability
# ============================================================================
if [ -f .env ]; then
    PORT=$(grep "^WEBGRE3_PORT=" .env | cut -d '=' -f2 | tr -d ' ')
    if [ -n "$PORT" ]; then
        echo -n "Checking port ${PORT} availability... "
        if lsof -Pi :${PORT} -sTCP:LISTEN -t >/dev/null 2>&1 || netstat -an | grep -q ":${PORT}.*LISTEN"; then
            echo -e "${YELLOW}⚠${NC} Port ${PORT} already in use"
            echo "   Change WEBGRE3_PORT in .env or stop the service using port ${PORT}"
            WARNINGS=$((WARNINGS + 1))
        else
            echo -e "${GREEN}✓${NC} Available"
        fi
    fi
fi

# ============================================================================
# 8. Check required directories
# ============================================================================
echo -n "Checking project structure... "
REQUIRED_DIRS=(
    "docker/php"
    "docker/nginx"
    "docker/supervisor"
    "app"
    "core"
    "public"
)

MISSING_DIRS=()
for dir in "${REQUIRED_DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        MISSING_DIRS+=("$dir")
    fi
done

if [ ${#MISSING_DIRS[@]} -eq 0 ]; then
    echo -e "${GREEN}✓${NC} All directories present"
else
    echo -e "${YELLOW}⚠${NC} Missing directories:"
    for dir in "${MISSING_DIRS[@]}"; do
        echo "     - $dir"
    done
    WARNINGS=$((WARNINGS + 1))
fi

# ============================================================================
# 9. Check Dockerfile
# ============================================================================
echo -n "Checking Dockerfile... "
if [ -f Dockerfile ]; then
    echo -e "${GREEN}✓${NC} Found"
else
    echo -e "${RED}✗${NC} Dockerfile not found"
    ERRORS=$((ERRORS + 1))
fi

# ============================================================================
# 10. Check docker-compose.yml
# ============================================================================
echo -n "Checking docker-compose.yml... "
if [ -f docker-compose.yml ]; then
    echo -e "${GREEN}✓${NC} Found"

    # Validate syntax
    if docker-compose config &> /dev/null; then
        echo -e "   ${GREEN}✓${NC} Valid syntax"
    else
        echo -e "   ${RED}✗${NC} Invalid syntax"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo -e "${RED}✗${NC} docker-compose.yml not found"
    ERRORS=$((ERRORS + 1))
fi

# ============================================================================
# 11. Check disk space
# ============================================================================
echo -n "Checking disk space... "
AVAILABLE_GB=$(df -h . | tail -1 | awk '{print $4}' | sed 's/G.*//')
if [ -n "$AVAILABLE_GB" ] && [ "$AVAILABLE_GB" -ge 5 ]; then
    echo -e "${GREEN}✓${NC} ${AVAILABLE_GB}GB available"
else
    echo -e "${YELLOW}⚠${NC} Low disk space (${AVAILABLE_GB}GB)"
    echo "   At least 5GB recommended for Docker images"
    WARNINGS=$((WARNINGS + 1))
fi

# ============================================================================
# Summary
# ============================================================================
echo ""
echo -e "${BLUE}============================================${NC}"
echo "Pre-flight Check Summary"
echo -e "${BLUE}============================================${NC}"

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "You can now run:"
    echo "  docker-compose build"
    echo "  docker-compose up -d"
    echo ""
    echo "Or use the Makefile:"
    echo "  make build"
    echo "  make up"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠ ${WARNINGS} warning(s) found${NC}"
    echo ""
    echo "You can proceed, but review the warnings above."
    echo ""
    echo "To continue:"
    echo "  docker-compose build"
    echo "  docker-compose up -d"
    exit 0
else
    echo -e "${RED}✗ ${ERRORS} error(s) found${NC}"
    if [ $WARNINGS -gt 0 ]; then
        echo -e "${YELLOW}⚠ ${WARNINGS} warning(s) found${NC}"
    fi
    echo ""
    echo "Please fix the errors above before proceeding."
    exit 1
fi
