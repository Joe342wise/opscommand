#!/bin/bash
set -euo pipefail

echo "=== OpsCommand Server Setup ==="
echo "This script prepares a fresh Ubuntu 24.04 VPS for OpsCommand deployment."
echo ""

# Update system
echo "[1/6] Updating system packages..."
sudo apt-get update -y
sudo apt-get upgrade -y

# Install Docker
echo "[2/6] Installing Docker..."
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
fi

# Install Docker Compose plugin
echo "[3/6] Installing Docker Compose..."
sudo apt-get install -y docker-compose-plugin

# Create app directory
echo "[4/6] Creating app directory..."
APP_DIR="/opt/opscommand"
sudo mkdir -p "$APP_DIR"
sudo chown $USER:$USER "$APP_DIR"

# Install Git
echo "[5/6] Installing Git..."
sudo apt-get install -y git

# Install Certbot (for SSL)
echo "[6/6] Installing Certbot..."
sudo apt-get install -y certbot

echo ""
echo "=== Setup Complete ==="
echo ""
echo "Next steps:"
echo "1. Clone your repo: cd $APP_DIR && git clone <your-repo-url> ."
echo "2. Copy .env.production to .env and edit it"
echo "3. Run: docker compose -f docker-compose.prod.yml up -d"
echo "4. Point your domain DNS to this server's IP"
echo "5. Run: sudo certbot certonly --standalone -d osei.app -d www.osei.app"
echo ""
echo "Server IP: $(curl -s ifconfig.me)"
