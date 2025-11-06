#!/bin/bash

# Frontend Docker Rebuild Script
# Fixes oxc-parser errors by rebuilding with clean volumes

set -e  # Exit on error

echo "🔧 Rebuilding Frontend Container with Clean Volumes..."
echo ""

echo "📦 Step 1: Stopping frontend container..."
docker compose stop frontend

echo "🗑️  Step 2: Removing container..."
docker compose rm -f frontend

echo "🧹 Step 3: Removing contaminated node_modules volume..."
docker volume rm didonehousing_frontend_node_modules || echo "Volume already removed"

echo "🧹 Step 4: Removing .nuxt cache volume..."
docker volume rm didonehousing_frontend_nuxt || echo "Volume already removed"

echo "🏗️  Step 5: Rebuilding frontend image..."
docker compose build --no-cache frontend

echo "🚀 Step 6: Starting frontend container..."
docker compose up -d frontend

echo ""
echo "✅ Frontend container rebuilt successfully!"
echo ""
echo "📊 Watching logs (Ctrl+C to stop)..."
echo "   You should see: ✔ Nuxt built successfully"
echo "   Then access: http://localhost:3000"
echo ""

# Follow logs
docker compose logs -f frontend
