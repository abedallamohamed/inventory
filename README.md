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

## Database Schema

The application includes a basic CRM structure with referential integrity constraints:

### Tables

- **customers**: Client information (name, email, address, phone)
- **orders**: Customer orders with foreign key relationship

### Key Constraints

- **Soft Deletes**: Both customers and orders use soft deletes (deleted_at column)
- **Referential Integrity**: Orders have a foreign key to customers with `RESTRICT ON DELETE`
- **Data Protection**: A customer cannot be deleted if they have existing orders
- **Order Status**: ENUM values (pending, processing, completed, cancelled)

This ensures data integrity and prevents orphaned records while maintaining audit trails through soft deletes.

## API Resources

The application uses **Laravel API Resources** to format and control JSON responses:

### Why API Resources?

- ✅ **Data Control**: Expose only necessary fields per endpoint
- ✅ **Format Consistency**: Automatic date formatting and data transformation
- ✅ **Performance**: Conditional loading prevents N+1 queries
- ✅ **Context Aware**: Different data based on route (list vs detail)

### Implementation

- **CustomerResource**: Shows basic info in lists, full details (including address) in show endpoints
- **OrderResource**: Formats amounts, dates, and includes human-readable status labels
- **Conditional Fields**: Uses `whenLoaded()` and `when()` to include data only when appropriate

### Example Response

```json
// GET /api/customers (list)
{
  "id": 1,
  "name": "Mario Rossi",
  "email": "mario@example.com",
  "phone": "+39 123 456 789",
  "created_at": "15/01/2026",
  "orders_count": 3
}

// GET /api/customers/1 (detail)
{
  "id": 1,
  "name": "Mario Rossi", 
  "email": "mario@example.com",
  "phone": "+39 123 456 789",
  "address": "Via Roma 123, Milan",
  "created_at": "15/01/2026",
  "orders_count": 3
}
```
