-- Add manager_id to users to support hierarchy
ALTER TABLE users ADD COLUMN manager_id INT NULL AFTER role;
ALTER TABLE users ADD CONSTRAINT fk_users_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL;

-- Update existing users for testing (optional, can be done via UI later)
-- For MVP, let's assume 'admin' might be a manager of someone if needed, 
-- but we will leave it null for now and let the Admin UI handle assignments.
