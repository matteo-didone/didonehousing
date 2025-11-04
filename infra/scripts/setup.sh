#!/bin/bash

# ============================================================================
# Aviano Housing Platform - Initial Setup Script
# ============================================================================

set -e

echo "================================"
echo "Aviano Housing Platform Setup"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed${NC}"
    echo "Please install Docker first: https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Error: Docker Compose is not installed${NC}"
    echo "Please install Docker Compose first: https://docs.docker.com/compose/install/"
    exit 1
fi

echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"
echo ""

# Create environment files
echo "Setting up environment files..."

if [ ! -f backend/.env ]; then
    cp backend/.env.example backend/.env || cp .env.example backend/.env
    echo -e "${GREEN}✓ Created backend/.env${NC}"
else
    echo -e "${YELLOW}⚠ backend/.env already exists, skipping${NC}"
fi

if [ ! -f frontend/.env ]; then
    cp frontend/.env.example frontend/.env
    echo -e "${GREEN}✓ Created frontend/.env${NC}"
else
    echo -e "${YELLOW}⚠ frontend/.env already exists, skipping${NC}"
fi

echo ""

# Start Docker services
echo "Starting Docker services..."
docker-compose up -d postgres redis minio typesense mailhog

echo "Waiting for services to be ready..."
sleep 10

echo -e "${GREEN}✓ Docker services started${NC}"
echo ""

# Install backend dependencies
echo "Installing backend dependencies..."
cd backend
if [ -d "vendor" ]; then
    echo -e "${YELLOW}⚠ Vendor directory exists, running composer install${NC}"
else
    echo "Running composer install (this may take a few minutes)..."
fi
composer install --no-interaction --prefer-dist
echo -e "${GREEN}✓ Backend dependencies installed${NC}"
cd ..
echo ""

# Generate application key
echo "Generating Laravel application key..."
cd backend
if grep -q "APP_KEY=base64:" .env; then
    echo -e "${YELLOW}⚠ APP_KEY already exists, skipping${NC}"
else
    php artisan key:generate --ansi
    echo -e "${GREEN}✓ Application key generated${NC}"
fi
cd ..
echo ""

# Run migrations
echo "Running database migrations..."
cd backend
php artisan migrate --force
echo -e "${GREEN}✓ Database migrations completed${NC}"
cd ..
echo ""

# Install frontend dependencies
echo "Installing frontend dependencies..."
cd frontend
if [ -d "node_modules" ]; then
    echo -e "${YELLOW}⚠ node_modules exists, running npm install${NC}"
else
    echo "Running npm install (this may take a few minutes)..."
fi
npm install
echo -e "${GREEN}✓ Frontend dependencies installed${NC}"
cd ..
echo ""

# Create MinIO bucket
echo "Setting up MinIO bucket..."
docker-compose exec -T minio mc alias set local http://localhost:9000 aviano avianoSecret123 || true
docker-compose exec -T minio mc mb local/aviano-housing || echo "Bucket may already exist"
docker-compose exec -T minio mc anonymous set download local/aviano-housing || true
echo -e "${GREEN}✓ MinIO bucket configured${NC}"
echo ""

# Summary
echo ""
echo "================================"
echo -e "${GREEN}Setup Complete!${NC}"
echo "================================"
echo ""
echo "You can now start the application with:"
echo "  docker-compose up -d"
echo ""
echo "Access the services at:"
echo "  Frontend:    http://localhost:3000"
echo "  Backend API: http://localhost:8000"
echo "  API Docs:    http://localhost:8000/docs"
echo "  Horizon:     http://localhost:8000/horizon"
echo "  MailHog:     http://localhost:8025"
echo "  MinIO:       http://localhost:9001"
echo ""
echo "For more commands, run: make help"
echo ""
