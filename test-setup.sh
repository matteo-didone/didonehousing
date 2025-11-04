#!/bin/bash

# ============================================================================
# Aviano Housing Platform - Quick Test Script
# ============================================================================

set -e

echo "=========================================="
echo "Aviano Housing - Quick Setup Test"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Error: Docker is not running${NC}"
    echo "Please start Docker Desktop first"
    exit 1
fi

echo -e "${GREEN}✓ Docker is running${NC}"
echo ""

# Build and start services
echo "Building and starting services..."
echo "(This may take a few minutes on first run)"
docker-compose up -d --build

echo ""
echo "Waiting for services to be ready..."
sleep 15

# Check service health
echo ""
echo "Checking service health..."

# Check Postgres
if docker-compose exec -T postgres pg_isready -U aviano > /dev/null 2>&1; then
    echo -e "${GREEN}✓ PostgreSQL is ready${NC}"
else
    echo -e "${RED}✗ PostgreSQL is not responding${NC}"
fi

# Check Redis
if docker-compose exec -T redis redis-cli --raw incr ping > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Redis is ready${NC}"
else
    echo -e "${RED}✗ Redis is not responding${NC}"
fi

# Check Backend
if curl -s http://localhost:8000/up > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Backend is ready${NC}"
else
    echo -e "${YELLOW}⚠ Backend may still be starting...${NC}"
fi

# Check Frontend
if curl -s http://localhost:3000 > /dev/null 2>&1; then
    echo -e "${GREEN}✓ Frontend is ready${NC}"
else
    echo -e "${YELLOW}⚠ Frontend may still be starting...${NC}"
fi

echo ""
echo "Running migrations and seeders..."
docker-compose exec -T backend php artisan migrate:fresh --seed --force

echo ""
echo "=========================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "=========================================="
echo ""
echo "Services are running at:"
echo "  Frontend:  http://localhost:3000"
echo "  Backend:   http://localhost:8000"
echo "  API:       http://localhost:8000/api"
echo "  MailHog:   http://localhost:8025"
echo "  MinIO:     http://localhost:9001"
echo ""
echo "Test Users (all password: 'password'):"
echo "  Admin:    admin@avianohousing.local"
echo "  Tenant:   tenant@avianohousing.local"
echo "  Landlord: landlord@avianohousing.local"
echo "  HO:       ho@avianohousing.local"
echo "  Vendor:   vendor@avianohousing.local"
echo ""
echo "Quick API Test:"
echo "  curl -X POST http://localhost:8000/api/auth/login \\"
echo "    -H 'Content-Type: application/json' \\"
echo "    -d '{\"email\":\"tenant@avianohousing.local\",\"password\":\"password\"}'"
echo ""
echo "View logs:"
echo "  docker-compose logs -f backend"
echo ""
echo "Stop services:"
echo "  docker-compose down"
echo ""
