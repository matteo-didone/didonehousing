#!/bin/bash

echo "🎨 Installing shadcn-vue for Nuxt..."
echo ""

# Enter the Docker container and run installations
docker compose exec frontend sh -c "
  echo '📦 Step 1: Installing shadcn-nuxt module...'
  npx nuxi@latest module add shadcn-nuxt

  echo ''
  echo '📦 Step 2: Preparing Nuxt...'
  npx nuxi prepare

  echo ''
  echo '✅ Installation complete!'
  echo ''
  echo 'Next steps:'
  echo '1. Initialize shadcn-vue: npx shadcn-vue@latest init'
  echo '2. Add components: npx shadcn-vue@latest add button'
"

echo ""
echo "🎉 Ready to configure!"
