CREATE DATABASE IF NOT EXISTS gestion_commandes;
USE gestion_commandes;

CREATE TABLE IF NOT EXISTS users (
    usr VARCHAR(50) PRIMARY KEY,
    pw VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS clients (
    code_client VARCHAR(20) PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    tele VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    descr TEXT,
    prix DECIMAL(10,2) NOT NULL CHECK (prix > 0),
    stock INT NOT NULL CHECK (stock >= 0)
);

CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_client VARCHAR(20) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL CHECK (qty > 0),
    total DECIMAL(12,2) NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (code_client) REFERENCES clients(code_client)
);

--INSERT INTO users values("admin","$2y$10$QHxnTAcNGCKS.HGLFaUphefjAxu5vvW1aWH1558vfYiWVt4e1x77i");

/*
INSERT INTO clients (code_client, nom, prenom, email, tele) VALUES
('C001','El Fassi','Youssef','youssef.elfassi@example.com','0612345678'),
('C002','Benali','Sara','sara.benali@example.com','0623456789'),
('C003','Ouazzani','Amine','amine.ouazzani@example.com','0634567890'),
('C004','Chraibi','Salma','salma.chraibi@example.com','0645678901'),
('C005','Rifai','Imane','imane.rifai@example.com','0656789012');

INSERT INTO produits (nom, descr, prix, stock) VALUES
('Laptop Dell XPS 13','Ultrabook performant pour professionnels','1200.00',15),
('Smartphone Samsung Galaxy S23','Smartphone haut de gamme Android','950.00',30),
('Apple MacBook Pro 14','Portable Apple M2 Pro','2200.00',10),
('Raspberry Pi 5','Mini-ordinateur pour projets DIY','75.00',50),
('Corsair RAM 16GB DDR5','Mémoire vive pour PC','120.00',40),
('Logitech MX Master 3','Souris ergonomique haut de gamme','100.00',60),
('Sony WH-1000XM5','Casque audio à réduction de bruit','350.00',25),
('Samsung 970 EVO Plus 1TB','SSD NVMe ultra-rapide','150.00',35),
('NVIDIA RTX 4070 Ti','Carte graphique pour gaming','900.00',8);
*/