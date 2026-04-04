-- ============================================================
-- Migration: Add remarks column to applications table
-- Run this in phpMyAdmin on the u153070096_vakmedia database
-- ============================================================

ALTER TABLE applications
ADD COLUMN remarks TEXT DEFAULT NULL
AFTER other_info;
