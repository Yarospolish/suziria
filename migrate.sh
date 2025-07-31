#!/bin/bash

if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
else
  echo ".env file not found!"
  exit 1
fi

echo "Checking database connection..."

until docker exec postgres pg_isready -U "$DB_USER" -h "$DB_HOST"; do
  echo "Waiting for PostgreSQL to be ready..."
  sleep 2
done

echo "Creating database if not exists..."

docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d postgres -c "CREATE DATABASE suziria;"

echo "Running migrations..."
docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d suziria -c "
CREATE TABLE IF NOT EXISTS products (
  id UUID PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  category VARCHAR(50) NOT NULL,
  attributes JSONB NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_category ON products(category);
CREATE INDEX IF NOT EXISTS idx_price ON products(price);
"

docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d postgres -c "CREATE DATABASE suziria_tests;"

echo "Running migrations..."
docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d suziria_tests -c "
CREATE TABLE IF NOT EXISTS products (
  id UUID PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  category VARCHAR(50) NOT NULL,
  attributes JSONB NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_category ON products(category);
CREATE INDEX IF NOT EXISTS idx_price ON products(price);
"

echo "Verifying migration..."
docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d postgres -c "\dt"
docker exec -e PGPASSWORD=$DB_PASSWORD postgres \
  psql -U user -h postgres -d postgres -c "\di"

echo "Migration completed successfully!"