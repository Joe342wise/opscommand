#!/bin/bash
set -euo pipefail

APP_DIR="/opt/opscommand"
COMPOSE_FILE="docker-compose.prod.yml"
APP_IMAGE="${APP_IMAGE:-ghcr.io/nponthutech/opscommand:latest}"

echo "=== OpsCommand Deployment ==="
echo "Time: $(date)"
echo "Image: $APP_IMAGE"
echo ""

cd "$APP_DIR"

echo "[1/5] Pulling latest image..."
docker pull "$APP_IMAGE"

echo "[2/5] Pulling latest code..."
git pull origin master

echo "[3/5] Running migrations..."
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE run --rm app php artisan migrate --force

echo "[4/5] Caching config..."
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE run --rm app php artisan config:cache
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE run --rm app php artisan route:cache
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE run --rm app php artisan view:cache

echo "[5/5] Restarting services..."
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE up -d --remove-orphans

echo ""
echo "=== Deployment Complete ==="
echo "App URL: https://osei.app"
echo "Image: $APP_IMAGE"
echo "Containers:"
APP_IMAGE="$APP_IMAGE" docker compose -f $COMPOSE_FILE ps
