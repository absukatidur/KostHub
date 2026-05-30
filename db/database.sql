-- KostHub Seed Data
USE kosmanager;

-- Clear existing data
DELETE FROM logs;
DELETE FROM repairs;
DELETE FROM orders;
DELETE FROM customers;
DELETE FROM facilities;
DELETE FROM rooms;

DELETE FROM users WHERE role != 'owner'
