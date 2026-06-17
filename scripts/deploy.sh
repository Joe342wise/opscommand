#!/bin/bash
set -euo pipefail

APP_DIR="/opt/opscommand"
COMPOSE_FILE="docker-compose.prod.yml"

echo "=== OpsCommand Deployment ==="
echo "Time: $(date)"
echo ""

cd "$APP_DIR"

echo "[1/5] Pulling latest code..."
git pull origin master

echo "[2/5] Building containers..."
docker compose -f $COMPOSE_FILE build --no-cache

echo "[3/5] Running migrations..."
docker compose -f $COMPOSE_FILE run --rm app php artisan migrate --force

echo "[4/5] Caching config..."
docker compose -f $COMPOSE_FILE run --rm app php artisan config:cache
docker compose -f $COMPOSE_FILE run --rm app php artisan route:cache
docker compose -f $COMPOSE_FILE run --rm app php artisan view:cache

echo "[5/5] Restarting services..."
docker compose -f $COMPOSE_FILE up -d --remove-orphans

echo ""
echo "=== Deployment Complete ==="
echo "App URL: https://osei.app"
echo "Containers:"
docker compose -f $COMPOSE_FILE ps
