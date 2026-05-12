# KostHub

## Description
KostHub is a modern web-based boarding house (kost) room management system focused on providing a platform where administrators can easily manage rooms, tenants, rental transactions, and repairs. This application is also equipped with a Tenant Portal, designed to make it easier for boarding house tenants to check bills, report facility damages, and independently submit room transfer requests.

## Address
[http://localhost]

## Main Menu
```text
- Admin
    - Dashboard
    - Room Types (Master Data)
    - Customers (Master Data)
    - Room Management
    - Orders / Rentals
    - Room Transfer
    - Repairs
    - Public Facilities
    - Activity Logs
    - User Requests
- User (Tenant)
    - Dashboard
    - Bills & Payments
    - Repairs
    - Boarding House Facilities
    - My Profile
    - Submission Services (Transfer / Checkout)

```
## TechStack 
* **Frontend**: HTML5, CSS3 (Custom styling, CSS Variables), Vanilla JavaScript (ES6+ API fetching, DOM manipulation).
* **Icons**: Lucide Icons.
* **Backend**: Vanilla PHP (Custom API Router handling RESTful endpoints).
* **Database**: MySQL.
* **Architecture**: Client-Server architecture using AJAX/Fetch API for asynchronous data loading.

## DBMS: Configuration, Table Specification

### Configuration
* **DBMS**: MySQL
* **Host**: `localhost`
* **Username**: `root`
* **Database Name**: `kosmanager`

### Table Specification

* **`users`** (Authentication Credentials)
  * `id` (INT, PK, Auto Increment)
  * `username` (VARCHAR 50, Unique)
  * `password` (VARCHAR 255)
  * `role` (ENUM: 'admin', 'user')
  * `customer_id` (VARCHAR 10, Nullable, FK)

* **`rooms`** (Room Master Data)
  * `id` (VARCHAR 10, PK)
  * `floor` (INT)
  * `type` (VARCHAR 50)
  * `rent` (VARCHAR 50)
  * `price` (INT)
  * `status` (VARCHAR 20)
  * `tenant` (VARCHAR 100)
  * `until` (VARCHAR 20)
  * `facilities` (TEXT)

* **`customers`** (Tenant Master Data)
  * `id` (VARCHAR 10, PK)
  * `name` (VARCHAR 100)
  * `email` (VARCHAR 100)
  * `wa` (VARCHAR 20)
  * `ktp` (VARCHAR 50)
  * `room` (VARCHAR 10)
  * `emergency1` (VARCHAR 100)
  * `emergency2` (VARCHAR 100)

* **`orders`** (Rental Transactions)
  * `id` (VARCHAR 20, PK)
  * `customer` (VARCHAR 100)
  * `room` (VARCHAR 10)
  * `type` (VARCHAR 50)
  * `start` (DATE)
  * `end` (DATE)
  * `total` (INT)
  * `status` (VARCHAR 20)

* **`repairs`** (Maintenance Logs)
  * `id` (VARCHAR 20, PK)
  * `target` (VARCHAR 100)
  * `type` (VARCHAR 20)
  * `issue` (TEXT)
  * `reported` (DATE)
  * `status` (VARCHAR 20)
  * `tech` (VARCHAR 100)

* **`facilities`** (Public Amenities)
  * `id` (VARCHAR 10, PK)
  * `name` (VARCHAR 100)
  * `floor` (VARCHAR 10)
  * `desc` (TEXT)
  * `status` (VARCHAR 20)

* **`requests`** (User Applications/Services)
  * `id` (VARCHAR 10, PK)
  * `customer_id` (VARCHAR 10)
  * `type` (ENUM: 'pindah', 'checkout')
  * `detail` (TEXT)
  * `status` (ENUM: 'pending', 'approved', 'rejected')
  * `created_at` (DATETIME)
  * `resolved_at` (DATETIME, Nullable)
  * `admin_note` (TEXT)

* **`logs`** (Admin Activity History)
  * `id` (INT, PK, Auto Increment)
  * `time` (DATETIME)
  * `action` (VARCHAR 100)
  * `detail` (TEXT)
  * `type` (VARCHAR 50)