# Reservation Service

## Setup

1. Import database:  
   `mysql -h localhost -P 3309 -u user -ppassword < sql/reservationdb.sql`

2. Jalankan service:  
   `php -S localhost:8003 -t public`

## API

- GET /reservations  
  Mengambil daftar semua reservasi

- POST /reservations  
  Body JSON: { "user_id": 1, "room_id": 2, "check_in": "2025-05-25", "check_out": "2025-05-30" }
