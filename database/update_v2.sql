-- Add work_area to users table
ALTER TABLE users ADD COLUMN work_area VARCHAR(50) DEFAULT NULL;

-- Create user_insights table for long-term memory
CREATE TABLE IF NOT EXISTS user_insights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    insight_type VARCHAR(50) NOT NULL COMMENT 'preference, project, vocabulary',
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Insert some initial insights for demonstration (if needed)
-- INSERT INTO user_insights (user_id, insight_type, content) VALUES (2, 'preference', 'Prefere relatórios em tópicos curtos');
