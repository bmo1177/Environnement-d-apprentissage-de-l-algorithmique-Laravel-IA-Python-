#!/bin/bash

# Quick start script for the Learner Environment

echo "Starting Learner Environment..."

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "Created .env file from template"
fi

# Start Docker containers
docker-compose up -d

echo "Waiting for services to be ready..."
sleep 10

# Run Laravel migrations and seeders
docker-compose exec php-fpm php artisan key:generate
docker-compose exec php-fpm php artisan migrate --force
docker-compose exec php-fpm php artisan db:seed --force

# Install Laravel dependencies if needed
docker-compose exec php-fpm composer install --no-interaction

# Set proper permissions
docker-compose exec php-fpm chown -R www-data:www-data storage bootstrap/cache

echo "Learner Environment is ready!"
echo ""
echo "Access points:"
echo "   - Laravel App: http://localhost:8080"
echo "   - Python API: http://localhost:8000"
echo "   - Adminer DB: http://localhost:8081"
echo ""
echo " Default credentials:"
echo "   - Admin: admin@learner.com / password"
echo "   - Teacher: teacher@learner.com / password"
echo "   - Student: student1@learner.com / password"