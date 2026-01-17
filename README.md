# Laravel 12 Docker Setup

Dockerized environment for Laravel 12 with nginx, MySQL, and phpMyAdmin.

## Included Services

- **Laravel App**: PHP 8.2-fpm container for Laravel application
- **Nginx**: Web server to serve the application
- **MySQL 8.0**: Database
- **phpMyAdmin**: Web interface for database management

## Requirements

- Docker
- Docker Compose (plugin)

## Quick Setup

1. **Clone your Laravel repository into this directory**
2. **Run setup script**:
   ```bash
   ./setup.sh
   ```

## Manual Setup

1. **Start containers**:
   ```bash
   docker compose up -d --build
   ```

2. **Configure Laravel**:
   ```bash
   # Copy .env file
   docker compose exec app cp .env.example .env
   
   # Install dependencies
   docker compose exec app composer install
   docker compose exec app npm install
   
   # Generate application key
   docker compose exec app php artisan key:generate
   
   # Run migrations
   docker compose exec app php artisan migrate
   ```

3. **Set permissions**:
   ```bash
   docker compose exec app chown -R www:www /var/www
   docker compose exec app chmod -R 755 /var/www/storage
   docker compose exec app chmod -R 755 /var/www/bootstrap/cache
   ```

## Access Points

- **Laravel Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Database**: localhost:3306

## Database Credentials

- **Database**: `laravel_db`
- **Username**: `laravel_user`
- **Password**: `laravel_password`
- **Root password**: `root_password`

## Useful Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs

# Access app container
docker compose exec app bash
```

## File Structure

```
.
├── docker-compose.yml      # Docker services configuration
├── Dockerfile             # PHP container for Laravel
├── nginx/
│   └── conf.d/
│       └── app.conf       # Nginx configuration
├── php/
│   └── local.ini          # PHP configurations
├── .env.example           # Environment variables template
├── setup.sh              # Automatic setup script
└── README.md             # This documentation
```

## Notes

- Application files are mounted in `/var/www` in the container
- Database data is persisted in the `mysql_data` volume
- For development, edit files locally and they will be synced automatically
- PHP is configured with necessary Laravel extensions: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip

## API Authentication with Laravel Sanctum

### Why Sanctum?

We chose **Laravel Sanctum** for API authentication because:

- ✅ **Native Integration**: Included in Laravel 12, no external dependencies
- ✅ **Simple Setup**: Quick configuration with minimal overhead
- ✅ **Security**: SHA-256 hashed tokens stored in database, instantly revocable
- ✅ **Flexibility**: Supports both SPA session authentication and API token authentication
- ✅ **Production Ready**: Used by Laravel ecosystem, battle-tested in enterprise applications
- ✅ **Rate Limiting**: Built-in protection against brute force attacks

For **simple projects**, Sanctum is more than sufficient and provides enterprise-level security without complexity.
