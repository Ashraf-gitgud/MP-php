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