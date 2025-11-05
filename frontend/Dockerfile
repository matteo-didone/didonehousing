# ============================================================================
# Nuxt 3 Frontend - Multi-stage Dockerfile
# ============================================================================

# Base image with Node.js 20 LTS
FROM node:20-alpine AS base

# Install system dependencies
RUN apk add --no-cache git curl

# Set working directory
WORKDIR /app

# ============================================================================
# Development stage
# ============================================================================
FROM base AS development

# Copy package files
COPY package*.json ./

# Install all dependencies (including devDependencies)
RUN npm install --legacy-peer-deps

# Copy application files
COPY . .

# Expose ports
EXPOSE 3000 24678

# Start development server
CMD ["npm", "run", "dev"]

# ============================================================================
# Builder stage
# ============================================================================
FROM base AS builder

# Copy package files
COPY package*.json ./

# Install all dependencies
RUN npm install --legacy-peer-deps

# Copy application files
COPY . .

# Build application
RUN npm run build

# Prune dev dependencies
RUN npm prune --production

# ============================================================================
# Production stage
# ============================================================================
FROM base AS production

# Set NODE_ENV to production
ENV NODE_ENV=production

# Copy built application from builder
COPY --from=builder /app/.output /app/.output
COPY --from=builder /app/node_modules /app/node_modules

# Create non-root user
RUN addgroup -g 1001 -S nodejs && adduser -S nuxt -u 1001
RUN chown -R nuxt:nodejs /app

USER nuxt

# Expose port
EXPOSE 3000

# Start production server
CMD ["node", ".output/server/index.mjs"]