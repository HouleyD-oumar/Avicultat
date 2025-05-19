-- Script de création de la base de données Aviculture App
-- Conforme au MCD fourni dans le cahier des charges

-- Création de la base de données
DROP DATABASE IF EXISTS aviculture_db;
CREATE DATABASE aviculture_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE aviculture_db;

-- Table des utilisateurs
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('eleveur', 'veterinaire', 'admin') NOT NULL DEFAULT 'eleveur',
    photo_profil VARCHAR(255) DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des fermes
CREATE TABLE farms (
    id_ferme INT AUTO_INCREMENT PRIMARY KEY,
    nom_ferme VARCHAR(100) NOT NULL,
    localisation VARCHAR(255) NOT NULL,
    date_creation DATE NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des lots de volailles
CREATE TABLE poultry_batches (
    id_lot INT AUTO_INCREMENT PRIMARY KEY,
    race VARCHAR(100) NOT NULL,
    effectif_initial INT NOT NULL,
    date_arrivee DATE NOT NULL,
    statut ENUM('actif', 'vendu', 'perte totale') NOT NULL DEFAULT 'actif',
    id_ferme INT NOT NULL,
    FOREIGN KEY (id_ferme) REFERENCES farms(id_ferme) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les recherches sur les lots de volailles
CREATE INDEX idx_poultry_batches_ferme ON poultry_batches (id_ferme);
CREATE INDEX idx_poultry_batches_statut ON poultry_batches (statut);
CREATE INDEX idx_poultry_batches_date ON poultry_batches (date_arrivee);

-- Table des traitements
CREATE TABLE treatments (
    id_traitement INT AUTO_INCREMENT PRIMARY KEY,
    produit VARCHAR(100) NOT NULL,
    posologie VARCHAR(255) NOT NULL,
    date_application DATE NOT NULL,
    observations TEXT,
    id_lot INT NOT NULL,
    FOREIGN KEY (id_lot) REFERENCES poultry_batches(id_lot) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des alimentations
CREATE TABLE feeds (
    id_feed INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(100) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_distribution DATE NOT NULL,
    id_lot INT NOT NULL,
    FOREIGN KEY (id_lot) REFERENCES poultry_batches(id_lot) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des sujets du forum
CREATE TABLE forum_posts (
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    date_post TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réponses du forum
CREATE TABLE forum_replies (
    id_reply INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    date_reply TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_post) REFERENCES forum_posts(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des signalements du forum
CREATE TABLE forum_reports (
    id_report INT AUTO_INCREMENT PRIMARY KEY,
    motif TEXT NOT NULL,
    date_report TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en attente', 'traité', 'rejeté') NOT NULL DEFAULT 'en attente',
    id_post INT DEFAULT NULL,
    id_reply INT DEFAULT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_post) REFERENCES forum_posts(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_reply) REFERENCES forum_replies(id_reply) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des alertes
CREATE TABLE alerts (
    id_alert INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('mortalité', 'traitement', 'alimentation') NOT NULL,
    message TEXT NOT NULL,
    date_alert TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('active', 'résolue') NOT NULL DEFAULT 'active',
    id_lot INT NOT NULL,
    FOREIGN KEY (id_lot) REFERENCES poultry_batches(id_lot) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion d'un utilisateur administrateur par défaut
-- Mot de passe: admin123 (à changer en production)
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Système', 'admin@aviculture.app', '$2y$10$8tGIx5g5xLB1Dy.HpMVZm.CVyb6xw0iFRjyKbpFg73co3TJDZnk0G', 'admin');

-- Insertion de quelques données de test
-- Utilisateurs
INSERT INTO users (nom, prenom, email, mot_de_passe, role) VALUES
('Diallo', 'Mamadou', 'mamadou@example.com', '$2y$10$8tGIx5g5xLB1Dy.HpMVZm.CVyb6xw0iFRjyKbpFg73co3TJDZnk0G', 'eleveur'),
('Camara', 'Fatou', 'fatou@example.com', '$2y$10$8tGIx5g5xLB1Dy.HpMVZm.CVyb6xw0iFRjyKbpFg73co3TJDZnk0G', 'eleveur'),
('Bah', 'Ibrahim', 'ibrahim@example.com', '$2y$10$8tGIx5g5xLB1Dy.HpMVZm.CVyb6xw0iFRjyKbpFg73co3TJDZnk0G', 'veterinaire');

-- Fermes
INSERT INTO farms (nom_ferme, localisation, date_creation, id_user) VALUES
('Ferme Conakry', 'Conakry, Guinée', '2023-01-15', 2),
('Ferme Kindia', 'Kindia, Guinée', '2023-02-20', 2),
('Ferme Mamou', 'Mamou, Guinée', '2023-03-10', 3);

-- Lots de volailles
INSERT INTO poultry_batches (race, effectif_initial, date_arrivee, statut, id_ferme) VALUES
('Poulet de chair', 500, '2023-04-01', 'actif', 1),
('Poule pondeuse', 300, '2023-04-15', 'actif', 1),
('Poulet local', 200, '2023-05-01', 'actif', 2),
('Canard', 100, '2023-05-15', 'actif', 3);

-- Traitements
INSERT INTO treatments (produit, posologie, date_application, observations, id_lot) VALUES
('Vaccin Newcastle', '1 goutte/oiseau', '2023-04-10', 'Traitement préventif', 1),
('Antibiotique Tylosine', '1g/litre d\'eau', '2023-04-20', 'Traitement respiratoire', 2),
('Vitamines', '2ml/litre d\'eau', '2023-05-05', 'Complément alimentaire', 3);

-- Alimentations
INSERT INTO feeds (type, quantite, date_distribution, id_lot) VALUES
('Aliment démarrage', 100.00, '2023-04-05', 1),
('Aliment croissance', 150.00, '2023-04-25', 1),
('Aliment pondeuse', 80.00, '2023-04-20', 2),
('Maïs concassé', 50.00, '2023-05-10', 3);

-- Forum posts
INSERT INTO forum_posts (titre, contenu, id_user) VALUES
('Conseils pour démarrage d\'élevage', 'Bonjour, je débute dans l\'élevage de poulets de chair. Quels conseils pourriez-vous me donner pour bien démarrer ?', 2),
('Problème respiratoire chez mes poules', 'Mes poules présentent des symptômes respiratoires depuis quelques jours. Que faire ?', 3),
('Meilleur aliment pour poules pondeuses', 'Quel est selon vous le meilleur aliment pour maximiser la production d\'œufs ?', 2);

-- Forum replies
INSERT INTO forum_replies (contenu, id_post, id_user) VALUES
('Pour bien démarrer, assurez-vous d\'avoir une bonne litière, une température adéquate et une alimentation adaptée à l\'âge des poussins.', 1, 3),
('Je vous conseille de consulter un vétérinaire rapidement. En attendant, isolez les poules malades et désinfectez le poulailler.', 2, 4),
('L\'aliment pondeuse doit contenir au moins 16% de protéines et un bon apport en calcium pour la formation des coquilles.', 3, 4);