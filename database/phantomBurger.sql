
-- Création des tables
CREATE TABLE boissons(
   boisson_id INT NOT NULL AUTO_INCREMENT,
   picture VARCHAR(255),
   name VARCHAR(255),
   description TEXT,
   prix DECIMAL(15,2),
   PRIMARY KEY(boisson_id)
);

CREATE TABLE burgers(
   burger_id INT NOT NULL AUTO_INCREMENT,
   picture VARCHAR(255),
   name VARCHAR(255),
   prix DECIMAL(15,2),
   description TEXT,
   PRIMARY KEY(burger_id)
);

CREATE TABLE comments(
   comment_id INT NOT NULL AUTO_INCREMENT,
   username VARCHAR(255),
   comment_text TEXT,
   comment_date DATETIME,
   rating INT,
   burger_id INT NOT NULL,
   PRIMARY KEY(comment_id),
   FOREIGN KEY(burger_id) REFERENCES burgers(burger_id)
);

CREATE TABLE menus(
   menu_id INT NOT NULL AUTO_INCREMENT,
   name VARCHAR(255),
   picture VARCHAR(255),
   description TEXT,
   prix DECIMAL(15,2),
   PRIMARY KEY(menu_id)
);

CREATE TABLE roles(
   role_id INT NOT NULL AUTO_INCREMENT,
   nom VARCHAR(255),
   PRIMARY KEY(role_id)
);

CREATE TABLE utilisateurs(
   utilisateur_id_ INT NOT NULL AUTO_INCREMENT,
   nom VARCHAR(255),
   prenom VARCHAR(255),
   email VARCHAR(255),
   mot_de_passe VARCHAR(255),
   date_inscription DATETIME,
   role_id INT NOT NULL,
   PRIMARY KEY(utilisateur_id_),
   FOREIGN KEY(role_id) REFERENCES roles(role_id)
);

CREATE TABLE contact(
   contact_id INT NOT NULL AUTO_INCREMENT,
   nom VARCHAR(255),
   prenom VARCHAR(255),
   email VARCHAR(255),
   telephone VARCHAR(255),
   message VARCHAR(255),
   datemsg DATETIME,
   PRIMARY KEY(contact_id)
);

CREATE TABLE commandes(
   commande_id INT NOT NULL AUTO_INCREMENT,
   quantité INT,
   date_commande DATETIME,
   utilisateur_id_ INT NOT NULL,
   PRIMARY KEY(commande_id),
   FOREIGN KEY(utilisateur_id_) REFERENCES utilisateurs(utilisateur_id_)
);

CREATE TABLE boissons_appartient(
   boisson_id INT NOT NULL,
   menu_id INT NOT NULL,
   PRIMARY KEY(boisson_id, menu_id),
   FOREIGN KEY(boisson_id) REFERENCES boissons(boisson_id),
   FOREIGN KEY(menu_id) REFERENCES menus(menu_id)
);

CREATE TABLE burgers_appartient(
   burger_id INT NOT NULL,
   menu_id INT NOT NULL,
   PRIMARY KEY(burger_id, menu_id),
   FOREIGN KEY(burger_id) REFERENCES burgers(burger_id),
   FOREIGN KEY(menu_id) REFERENCES menus(menu_id)
);

CREATE TABLE est_commander(
   burger_id INT NOT NULL,
   commande_id INT NOT NULL,
   PRIMARY KEY(burger_id, commande_id),
   FOREIGN KEY(burger_id) REFERENCES burgers(burger_id),
   FOREIGN KEY(commande_id) REFERENCES commandes(commande_id)
);

-- Ajout des données initiales (si nécessaire)
INSERT INTO roles (nom) VALUES ('Admin'), ('Modérateur'), ('Utilisateur');

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, date_inscription, role_id) 
VALUES 
('Admin', 'Admin', 'admin@phantomburger.com', 'admin', NOW(), 1),
('Modérateur', 'Modérateur', 'moderateur@phantomburger.com', 'moderateur', NOW(), 2);

INSERT INTO boissons (picture, name, description, prix) 
VALUES 
('coca.jpg', 'Coca-Cola', 'Boisson gazeuse classique', 2.50),
('sprite.jpg', 'Sprite', 'Boisson gazeuse au citron', 2.50),
('fanta.jpg', 'Fanta', 'Boisson gazeuse à l\'orange', 2.50);

INSERT INTO burgers (picture, name, description) 
VALUES 
('burger_classic.jpg', 'Classic Burger', 'Un burger intemporel avec steak juteux, fromage fondant, laitue croquante, tomates fraîches et sauce spéciale.'),
('burger_cheese.jpg', 'Cheeseburger', 'Délicieux cheeseburger avec viande grillée, fromage fondant et légumes frais.'),
('burger_bbq.jpg', 'BBQ Burger', 'Explosion de saveurs avec sauce barbecue, oignons caramélisés et bacon croustillant.');

-- Ajout de 3 menus avec les relations aux burgers et boissons
INSERT INTO menus (name, picture, description, prix) 
VALUES 
('Menu Classic', 'menu_classic.jpg', 'Un menu comprenant le Classic Burger et une boisson Coca-Cola.', 11.99),
('Menu Cheeseburger', 'menu_cheese.jpg', 'Un menu comprenant le Cheeseburger et une boisson Sprite.', 12.99),
('Menu BBQ', 'menu_bbq.jpg', 'Un menu comprenant le BBQ Burger et une boisson Fanta.', 13.99);

-- Ajout des relations entre menus et burgers
INSERT INTO burgers_appartient (burger_id, menu_id) 
VALUES 
(1, 1), -- Classic Burger pour Menu Classic
(2, 2), -- Cheeseburger pour Menu Cheeseburger
(3, 3); -- BBQ Burger pour Menu BBQ

-- Ajout des relations entre menus et boissons
INSERT INTO boissons_appartient (boisson_id, menu_id) 
VALUES 
(1, 1), -- Coca-Cola pour Menu Classic
(2, 2), -- Sprite pour Menu Cheeseburger
(3, 3); -- Fanta pour Menu BBQ
