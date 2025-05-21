# Payment Service

## Setup

1. Import database:  
   `mysql -h localhost -P 3310 -u user -ppassword < sql/paymentdb.sql`

2. Jalankan service:  
   `php -S localhost:8004 -t public`

## API

- POST /payments  
  Body JSON: { "reservation_id": 1, "amount": 500000.00, "payment_method": "Credit Card" }

- GET /payments/{id}  
  Mendapatkan data pembayaran berdasarkan ID
