# Installation:
1. Clone repository 
2. run on terminal:

    cd Suziria

    cp .env.qxample .env

3. run on terminal:

    docker compose up -d

...after install

4. run on terminal:

   docker exec php83 composer install

   chmod +x migrate.sh  
   
   ./migrate.sh

# REQUESTS:

1. Create product (POST /api/products)

   curl -X POST http://localhost/api/products \
   -H "Content-Type: application/json" \
   -d '{
   "name": "iPhone 15 Pro",
   "price": 999.99,
   "category": "electronics",
   "attributes": {
   "brand": "Apple",
   "color": "black",
   "storage": "256GB"
   }
   }'

2. Get products (GET /api/products)
    
    - without filters
   
    curl -X GET http://localhost/api/products

    - with category filter
   
    curl -X GET "http://localhost/api/products?category=electronics"

    - with price filter
   
    curl -X GET "http://localhost/api/products?min_price=500&max_price=1000"

3. Get product (GET /api/products/{id}) 
    * Use real ID
   
    curl -X GET http://localhost/api/products/550e8400-e29b-41d4-a716-446655440000

4. Update product (PATCH /api/products/{id})

   curl -X PATCH http://localhost/api/products/550e8400-e29b-41d4-a716-446655440000 \
   -H "Content-Type: application/json" \
   -d '{
   "price": 90.99
   }'
5. Delete product (DELETE /api/products/{id})

   curl -X DELETE http://localhost/api/products/550e8400-e29b-41d4-a716-446655440000


# TESTS:
run on terminal:
	docker exec php83 ./vendor/bin/phpunit
