-- Gestor IA - Persistência de Sugestões da IA (Snippets)

ALTER TABLE chat_logs 
ADD COLUMN suggested_snippet TEXT NULL AFTER message;
