#!/bin/bash

# Setup script for Laravel 12 with Docker

echo "🐳 Setting up Laravel 12 with Docker..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker before continuing."
    exit 1
fi

echo "� Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "📄 Created .env file from .env.example"
else
    echo "📄 Using existing .env file"
fi

echo "📦 Building and starting containers..."
docker compose up -d --build

echo "⏳ Waiting for services to be ready..."
sleep 30

echo "🔑 Generating application key..."
docker compose exec app php artisan key:generate

echo "📦 Installing Composer dependencies..."
docker compose exec app composer install

echo "🎨 Installing Node.js dependencies..."
docker compose exec app npm install

echo "🗃️ Setting up database..."
docker compose exec app php artisan migrate

echo "🔑 Setting permissions..."
docker compose exec app chown -R www:www /var/www
docker compose exec app chmod -R 755 /var/www/storage
docker compose exec app chmod -R 755 /var/www/bootstrap/cache

echo "✅ Setup completed!"
echo ""
echo "🌐 Available services:"
echo "   - Laravel: http://localhost:8080"
echo "   - phpMyAdmin: http://localhost:8081"
echo "   - MySQL Database: localhost:3306"
echo ""
echo "📋 Database credentials:"
echo "   - Database: laravel_db"
echo "   - Username: laravel_user"
echo "   - Password: laravel_password"
echo "   - Root password: root_password"
echo ""
echo "🚀 To stop services: docker compose down"
echo "🔄 To restart: docker compose up -d"