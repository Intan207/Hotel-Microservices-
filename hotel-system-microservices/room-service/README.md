# Room Service

## Setup

1. Import database:  
   `mysql -h localhost -P 3308 -u user -ppassword < sql/roomdb.sql`

2. Jalankan service:  
   `php -S localhost:8002 -t public`

## API

- GET /rooms  
  Mengambil daftar semua kamar

- POST /rooms  
  Body JSON: { "room_number": "101", "type": "Deluxe", "price": 200.00 }

- DELETE /rooms/{id}  
  Menghapus kamar berdasarkan ID
