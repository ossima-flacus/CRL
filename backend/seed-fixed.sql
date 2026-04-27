-- ============================================
-- CRL - Données de Test (Mots de passe corrects)
-- ============================================
-- Importer ce fichier après schema.sql
-- Commande: mysql -u root crl_db < backend/seed-fixed.sql

-- Insérer des classes de test
INSERT INTO classes (name, level, description, created_at) VALUES
('Classe A', '6e', 'Classe de 6ème année', NOW()),
('Classe B', 'CM2', 'Classe de CM2', NOW()),
('Maternelle', 'GS', 'Grande Section', NOW());

-- Insérer les utilisateurs de test
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('admin', 'admin@crl.local', '$2y$10$kuYfB5FgE816evj3wsytTO8t2I7bKFRq0W48nEXgfF67JpZbxPPhi', 'admin', TRUE),
('secretaire', 'secretaire@crl.local', '$2y$10$Ei.OdvMhYvRwcun3UBnZ1uVyt8sJtwYqw8UzKjETZfvI/l31Z6xre', 'secretaire', TRUE),
('teacher', 'teacher@crl.local', '$2y$10$u2Y6KEzuNdfXImwxsV.yI.j3.Bpf4DyRzrh74X5a75F9HyVqnGWh.', 'comptable', TRUE),
('parent', 'parent@crl.local', '$2y$10$v4RPMRWJonTMagww8qS4UugANNs4rBVxMsOiFSQc7br0BhgiTcCHe', 'parent', TRUE),
('apprenant', 'apprenant@crl.local', '$2y$10$wZ/1RxuXjW4UWxRQ5J0iDe7FRYAxmKFALqFS0aLoLkfHstrUntwsK', 'apprenant', TRUE);

-- Insérer des étudiants de test
INSERT INTO students (first_name, last_name, gender, birth_date, email, phone, class_id, notes, created_at) VALUES
('Jean', 'Dupont', 'M', '2010-05-15', 'jean.dupont@example.com', '+33612345678', 1, 'Élève appliqué', NOW()),
('Marie', 'Martin', 'F', '2010-08-22', 'marie.martin@example.com', '+33687654321', 1, 'Très bonne élève', NOW()),
('Pierre', 'Bernard', 'M', '2011-03-10', 'pierre.bernard@example.com', NULL, 2, 'À suivre', NOW());
