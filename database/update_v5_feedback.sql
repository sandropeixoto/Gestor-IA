-- Gestor IA - Adicionando Feedback do Gestor e Status de Revisão

ALTER TABLE reports 
MODIFY COLUMN status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft';

ALTER TABLE reports 
ADD COLUMN manager_feedback TEXT NULL AFTER content_draft;
