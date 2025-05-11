-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reference_id INT NOT NULL,
    reference_type ENUM('complaint', 'lost_item') NOT NULL,
    type ENUM('scheduled', 'resolved', 'found_item', 'new_complaint') NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    date_created DATE NOT NULL,
    time_created TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_reference_id (reference_id),
    INDEX idx_date_created (date_created),
    INDEX idx_is_read (is_read),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;