.PHONY: help setup up down restart logs shell-backend shell-frontend install migrate seed fresh test clean

# Default target
help:
	@echo "Aviano Housing Platform - Development Commands"
	@echo ""
	@echo "Setup & Installation:"
	@echo "  make setup          - Initial project setup (first time only)"
	@echo "  make install        - Install dependencies for backend and frontend"
	@echo ""
	@echo "Docker Management:"
	@echo "  make up             - Start all services"
	@echo "  make down           - Stop all services"
	@echo "  make restart        - Restart all services"
	@echo "  make logs           - Show logs from all services"
	@echo "  make ps             - Show running containers"
	@echo ""
	@echo "Database:"
	@echo "  make migrate        - Run database migrations"
	@echo "  make seed           - Seed database with test data"
	@echo "  make fresh          - Fresh migration with seed (WARNING: destroys data)"
	@echo ""
	@echo "Development:"
	@echo "  make shell-backend  - Enter backend container shell"
	@echo "  make shell-frontend - Enter frontend container shell"
	@echo "  make test           - Run all tests"
	@echo "  make lint           - Run linters"
	@echo "  make format         - Format code"
	@echo ""
	@echo "Cleanup:"
	@echo "  make clean          - Remove containers, volumes, and build artifacts"

# Initial setup
setup:
	@echo "Setting up Aviano Housing Platform..."
	@cp -n .env.example backend/.env || true
	@cp -n frontend/.env.example frontend/.env || true
	@echo "Environment files created. Please review and update them."
	@echo "Run 'make install' to install dependencies."

# Install dependencies
install:
	@echo "Installing backend dependencies..."
	@cd backend && composer install
	@echo "Installing frontend dependencies..."
	@cd frontend && npm install
	@echo "Dependencies installed successfully!"

# Docker commands
up:
	docker-compose up -d
	@echo "Services started! Access:"
	@echo "  Frontend: http://localhost:3000"
	@echo "  Backend:  http://localhost:8000"
	@echo "  MailHog:  http://localhost:8025"
	@echo "  MinIO:    http://localhost:9001"

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

ps:
	docker-compose ps

# Database commands
migrate:
	docker-compose exec backend php artisan migrate

seed:
	docker-compose exec backend php artisan db:seed

fresh:
	@echo "WARNING: This will destroy all data in the database!"
	@read -p "Are you sure? (y/N): " confirm && [ "$$confirm" = "y" ] || exit 1
	docker-compose exec backend php artisan migrate:fresh --seed

# Development shells
shell-backend:
	docker-compose exec backend bash

shell-frontend:
	docker-compose exec frontend sh

# Testing
test:
	@echo "Running backend tests..."
	docker-compose exec backend php artisan test
	@echo "Running frontend tests..."
	docker-compose exec frontend npm run test

# Linting
lint:
	@echo "Linting backend..."
	docker-compose exec backend ./vendor/bin/pint --test
	@echo "Linting frontend..."
	docker-compose exec frontend npm run lint

# Code formatting
format:
	@echo "Formatting backend code..."
	docker-compose exec backend ./vendor/bin/pint
	@echo "Formatting frontend code..."
	docker-compose exec frontend npm run lint --fix

# Cleanup
clean:
	docker-compose down -v
	rm -rf backend/vendor
	rm -rf frontend/node_modules
	rm -rf backend/.env
	rm -rf frontend/.env
	@echo "Cleanup complete!"
