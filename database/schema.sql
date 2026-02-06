-- Gestor IA MVP - Banco de dados MySQL

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'employee') DEFAULT 'employee',
    manager_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_manager FOREIGN KEY (manager_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    content_draft TEXT,
    status ENUM('draft', 'submitted', 'approved') DEFAULT 'draft',
    submission_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_report FOREIGN KEY (user_id)
        REFERENCES users(id)
);

CREATE TABLE chat_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    sender ENUM('user', 'ai') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_report_chat FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE CASCADE
);

CREATE TABLE evidences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    description VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_report_evidence FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE CASCADE
);

CREATE TABLE deadlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    deadline_date DATE NOT NULL,
    CONSTRAINT fk_manager_deadline FOREIGN KEY (manager_id)
        REFERENCES users(id)
);

CREATE UNIQUE INDEX uq_reports_user_month ON reports (user_id, month_year);
CREATE INDEX idx_chat_logs_report ON chat_logs (report_id, created_at);
CREATE INDEX idx_evidences_report ON evidences (report_id);
CREATE INDEX idx_deadlines_manager_month ON deadlines (manager_id, month_year);
