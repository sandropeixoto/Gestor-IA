-- Seed inicial para o MVP Gestor IA
-- Senhas padrão:
-- admin@gestoria.local -> Admin@123
-- maria.gestora@gestoria.local -> Manager@123
-- joao.colaborador@gestoria.local -> Employee@123
-- ana.colaboradora@gestoria.local -> Employee@123

INSERT INTO users (name, email, password_hash, role, manager_id)
VALUES
    ('Admin Sistema', 'admin@gestoria.local', '$2y$12$oOOTcOWbPHISvrGuWytsC.Y1XC/yJPifblult4r0qxUeqmAX7wmXm', 'admin', NULL),
    ('Maria Gestora', 'maria.gestora@gestoria.local', '$2y$12$10K7SAIaC3.RYeP/6lbdMer5bb1X.Q0CHzwnuf0MVPktrBNKjKOSq', 'manager', 1),
    ('João Colaborador', 'joao.colaborador@gestoria.local', '$2y$12$pCQzf4rLE5VVY6kV1pTskOfHagR/uuTEf3EB3TK.t/GNRcGWMAf2G', 'employee', 2),
    ('Ana Colaboradora', 'ana.colaboradora@gestoria.local', '$2y$12$pCQzf4rLE5VVY6kV1pTskOfHagR/uuTEf3EB3TK.t/GNRcGWMAf2G', 'employee', 2);
