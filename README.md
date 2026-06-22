# KostHub

## Description
KostHub is a modern web-based boarding house (kost) room management system focused on providing a platform where administrators can easily manage rooms, tenants, rental transactions, and repairs. This application is also equipped with a Tenant Portal, designed to make it easier for boarding house tenants to check bills, report facility damages, and independently submit room transfer requests.

## Team
* **Made Agastya Devanantha Dharmawan (F1D02410071)**  - Project Leader: Backend & Frontend Developer
* **Muhammad Andika Azkiya (F1D02410076)**             - Team Member   : Frontend Developer
* **Faiz Ahmad Tsaqib Wirawan (F1D02410043)**          - Team Member   : Frontend Developer

## Main Menu & File Mapping

### Owner Panel
* **Dashboard** - Dynamic dashboard showing key operational metrics: vacant/occupied rooms count, pending bookings, active repairs, monthly earnings, and recent logs.
* **Tipe Kamar (Room Types)** - Manage categories of rooms, rents, prices, and amenities.
* **Customer (Tenants)** - Manage registered boarding house residents, WhatsApp contacts, and active room assignments.
* **Order / Sewa (Orders/Rentals)** - Add, approve, and track rental agreements, bills, and payment statuses.
* **Pindah Kamar (Room Transfer)**  - Dedicated admin panel for coordinating room swap logistics.
* **Perbaikan (Repairs)** - Track facility reports, assign technicians, monitor maintenance stages, and review tenant upvotes for public facilities.
* **Fasilitas Umum (Public Facilities)**  - Manage the operational status of shared building areas (kitchens, parking, laundry, etc.).
* **Log Aktivitas (Activity Logs)** - Audit logs detailing system transactions and administrator actions.
* **Permintaan User (User Requests)** - Accept or reject tenant-initiated room transfers or checkouts, with customizable administrator notes.
* **Kelola Admin (Manage Admin)** - Add, edit, and remove admin accounts.
* **Laporan Keuangan (Revenue Repport)** - Dedicated owner panel for analysing finnancial repport and transactions.
* **Reset Data**  - Administrative tool to quickly restore the database to seed conditions for debugging.

### Admin Panel
* **Dashboard** - Dynamic dashboard showing key operational metrics: vacant/occupied rooms count, pending bookings, active repairs, and recent logs.
* **Tipe Kamar (Room Types)** - Manage categories of rooms, rents, prices, and amenities.
* **Customer (Tenants)** - Manage registered boarding house residents, WhatsApp contacts, and active room assignments.
* **Order / Sewa (Orders/Rentals)** - Add, approve, and track rental agreements, bills, and payment statuses.
* **Pindah Kamar (Room Transfer)**  - Dedicated admin panel for coordinating room swap logistics.
* **Perbaikan (Repairs)** - Track facility reports, assign technicians, monitor maintenance stages, and review tenant upvotes for public facilities.
* **Fasilitas Umum (Public Facilities)**  - Manage the operational status of shared building areas (kitchens, parking, laundry, etc.).
* **Log Aktivitas (Activity Logs)** - Audit logs detailing system transactions and administrator actions.
* **Permintaan User (User Requests)** - Accept or reject tenant-initiated room transfers or checkouts, with customizable administrator notes.


### User (Tenant) Portal
* **Dashboard** - Overview of tenant's current room, active lease date, payment invoices, and active maintenance reports.
* **Tagihan (Bills & Payments)** - Monitor lease payments and process rent bills.
* **Perbaikan (Repairs)** - Report new damages inside rooms, and view/upvote open reports for shared public facilities.
* **Fasilitas (Boarding House Facilities)** - Check live statuses of common area amenities.
* **Cari Kamar (Browse & Book)** - Browse vacant rooms and submit booking requests.
* **Profil (My Profile)** - View and modify resident contact details.
* **Layanan (Submission Services)** - File official requests to transfer rooms (`pindah`) or schedule a room check-out (`checkout`).

---

## Tech Stack & Architecture

* **Frontend**: HTML5, Vanilla CSS3 (curated theme featuring modern dark slate aesthetics, glassmorphism, responsive grid layouts, and custom CSS Variables), and minimal client-side Vanilla JavaScript (for sidebar responsive toggle, automatic alert fades, and live payment calculation).
* **Icons**: Bootstrap Icons (v1.11.3, loaded via CDN).
* **Backend**: Traditional Server-Rendered PHP 8.x.
* **Database**: MySQL (Host: `localhost`, User: `root`, Database: `kosmanager`).
* **Architecture**: Modern, secure Multi-Page Application (MPA) using:
  * **Session-Based Authentication Guards**: Server-side role-checks redirecting unauthorized requests before HTML rendering.
  * **Layout Composition Modules**: Shared, reusable UI components for headers, top navigation bars, and sidebar controls.
  * **Direct DB Operations**: Secure direct database connection handling using native PHP MySQLi with prepared statements.
  * **State Management**: Session-based flash alerts and direct, validated PHP POST form submissions.

---

## DBMS Configuration & Table Specification

### Configuration
* **DBMS**: MySQL
* **Host**: `localhost`
* **Username**: `root`
* **Password**: `""` (empty string by default)
* **Database Name**: `kosmanager`

### Table Specification

* **`users`** (Authentication Credentials)
  * `id` (INT, PK, Auto Increment)
  * `username` (VARCHAR 50, Unique)
  * `password` (VARCHAR 255)
  * `role` (ENUM: 'owner', 'admin', 'user')
  * `customer_id` (VARCHAR 10, Nullable, FK to `customers.id`)

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
  * `room` (VARCHAR 10)

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
  * `votes` (INT) - Number of supporting votes/reports for general public facility repairs
  * `voted_by` (TEXT, Nullable) - JSON array of customer IDs who upvoted the repair report

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
  * `detail` (TEXT) - JSON string containing specific request details (e.g., target room, check-out date, and descriptions)
  * `from_room` (VARCHAR 10)
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

---
