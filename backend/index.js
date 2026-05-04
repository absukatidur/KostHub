// KosManager Backend Entry
import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import mysql from 'mysql2/promise';
dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

const db = await mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// Example: Get all rooms
app.get('/api/rooms', async (req, res) => {
  const [rows] = await db.query('SELECT * FROM rooms');
  res.json(rows);
});

// TODO: Add endpoints for customers, orders, repairs, facilities, logs

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Backend running on port ${PORT}`);
});
