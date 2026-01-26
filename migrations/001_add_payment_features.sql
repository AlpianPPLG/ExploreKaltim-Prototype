-- ==========================================
-- Migration: Add Payment Verification Features
-- Date: 26 January 2026
-- Description: Add rejection_reason to payments table and create payment_history table
-- ==========================================

USE explorekaltim;

-- Add rejection_reason column to payments table
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS rejection_reason TEXT AFTER payment_status;

-- Create payment_history table for tracking status changes
CREATE TABLE IF NOT EXISTS payment_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    booking_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by INT NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Update payment_status enum to include 'approved' and 'rejected'
ALTER TABLE payments 
MODIFY COLUMN payment_status ENUM('pending', 'approved', 'rejected', 'success', 'failed') DEFAULT 'pending';

SELECT 'Migration 001 completed successfully!' as status;
