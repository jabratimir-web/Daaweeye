# DAAWEYE TELEMEDICINE SYSTEM

Professional telemedicine platform built with Core PHP + MySQL.

## Key modules
- Public home page with doctors listing
- Doctor profile and schedule
- Appointment booking flow
- Contact + About pages
- Secure authentication (password hashing, prepared statements, CSRF/session protection)
- Finance model ($6 split into doctor $4 and system $2)
- Doctor wallet + withdraw requests
- Admin finance dashboard
- Invoice generation and PDF download endpoint
- Medical records
- Notifications
- Chart.js admin stats

## Setup
1. Create MySQL database `daaweeye`.
2. Import `sql/schema.sql`.
3. Update `config/database.php` if needed.
4. Serve project in Apache/XAMPP root.

## Default routes
- `/index.php` home
- `/login.php` login
- `/register.php` register
- `/dashboard.php` role-aware dashboard
