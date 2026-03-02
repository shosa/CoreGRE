#!/bin/sh
set -e

# Fix permissions on mounted volumes (runs as root before su-exec)
chown -R nestjs:nodejs /app/storage

# Run migrations and start as nestjs user
exec su-exec nestjs sh -c "npx prisma migrate deploy || true && node dist/src/main"
