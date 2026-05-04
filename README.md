# KosManager Fullstack Project

This project is a fullstack room management system using Vue 3 (Vite) for the frontend and Node.js/Express with MySQL for the backend.

## Structure
- `frontend/` — Vue 3 (Vite) app
- `backend/` — Node.js/Express REST API

## Setup Instructions

### 1. Frontend
```
cd frontend
npm install
npm run dev
```

### 2. Backend
```
cd backend
npm install
cp .env.example .env
# Edit .env with your MySQL credentials
npm run dev
```

### 3. Database
- Create a MySQL database named `kosmanager` (or as set in `.env`).
- Import the provided SQL schema (to be added in `backend/db/schema.sql`).

### 4. API URL
- The frontend expects the backend at `http://localhost:3000` (set in `.env`).

---

## Migration Note
The original static HTML/CSS/JS files are kept for reference in the root folder.
