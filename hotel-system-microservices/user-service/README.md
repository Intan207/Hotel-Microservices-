# User Service

## Setup

1. Import database:  
   `mysql -h localhost -P 3307 -u user -ppassword < sql/userdb.sql`

2. Jalankan service:  
   `php -S localhost:8001 -t public`

## API

- POST /register  
  Body JSON: { "username": "user1", "password": "pass123" }

- POST /login  
  Body JSON: { "username": "user1", "password": "pass123" }
