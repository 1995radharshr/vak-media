-- ============================================================
-- Vāk Media — Database Setup
-- Run this ONCE on your Hostinger MySQL database via phpMyAdmin
-- ============================================================

-- 1. Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Applications table
CREATE TABLE IF NOT EXISTS applications (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(200)  NOT NULL,
    email        VARCHAR(254)  NOT NULL,
    phone        VARCHAR(30)   DEFAULT '',
    location     VARCHAR(200)  DEFAULT '',
    role         VARCHAR(100)  NOT NULL,
    experience   VARCHAR(100)  DEFAULT '',
    availability VARCHAR(100)  DEFAULT '',
    portfolio    VARCHAR(500)  DEFAULT '',
    tools        TEXT,
    niches       TEXT,
    why_vak      TEXT,
    other_info   TEXT,
    remarks      TEXT,
    status       ENUM('new','reviewed','shortlisted','rejected') DEFAULT 'new',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_submitted (submitted_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Audit log table
CREATE TABLE IF NOT EXISTS audit_log (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    action     VARCHAR(50)  NOT NULL,
    detail     TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Rate limits table
CREATE TABLE IF NOT EXISTS rate_limits (
    ip_address   VARCHAR(45)  NOT NULL,
    endpoint     VARCHAR(50)  NOT NULL,
    attempts     INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address, endpoint)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- NOTE: Do NOT insert admin user here.
-- Run /api/setup.php after importing this SQL to create the
-- admin user with a proper bcrypt hash.
-- See SETUP.md for full instructions.
-- ============================================================
