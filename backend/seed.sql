-- ============================================
-- CRL - Données de Test
-- ============================================
-- Importer ce fichier après schema.sql
-- Commande: mysql -u root crl_db < backend/seed.sql

-- Insérer les utilisateurs de test
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('admin', 'admin@crl.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/tlm', 'admin', TRUE),
('secretaire', 'secretaire@crl.local', '$2y$10$sOkZAL7m1PfH8nJuZcM0LusqB8yBvs7/Zqwrn9JH3A2v0y0gGvgJ2', 'secretaire', TRUE),
('teacher', 'teacher@crl.local', '$2y$10$Z9L5A3n2KpQrT8vN1mB4MejCqP9xL0uRsWwXyZ7cD3eF4gH5iJ6K.', 'comptable', TRUE),
('parent', 'parent@crl.local', '$2y$10$K2mL9pQ4rS5tU6vW7xY8zAb1cD2eF3gH4iJ5kL6mN7oP8qR9sT0', 'parent', TRUE),
('apprenant', 'apprenant@crl.local', '$2y$10$U1vW2xY3zAb4cD5eF6gH7iJ8kL9mN0oP1qR2sT3uV4wX5yZ6aB7c', 'apprenant', TRUE);

-- Insérer des classes de test
INSERT INTO classes (name, level, description, created_at) VALUES
('Classe A', '6e', 'Classe de 6ème année', NOW()),
('Classe B', 'CM2', 'Classe de CM2', NOW()),
('Maternelle', 'GS', 'Grande Section', NOW());

-- Insérer des étudiants de test
INSERT INTO students (first_name, last_name, gender, birth_date, email, phone, class_id, notes, created_at) VALUES
('Jean', 'Dupont', 'M', '2010-05-15', 'jean.dupont@example.com', '+33612345678', 1, 'Élève appliqué', NOW()),
('Marie', 'Martin', 'F', '2010-08-22', 'marie.martin@example.com', '+33687654321', 1, 'Très bonne élève', NOW()),
('Pierre', 'Bernard', 'M', '2011-03-10', 'pierre.bernard@example.com', NULL, 2, 'À suivre', NOW());
