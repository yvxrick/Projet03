USE projet_03;

-- Drop tables in correct order (children first)
DROP TABLE IF EXISTS connexions;
DROP TABLE IF EXISTS annonces;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS categories;


CREATE TABLE categories (
    NoCategorie INT(6) PRIMARY KEY,
    Description VARCHAR(20)
) ENGINE=InnoDB;


CREATE TABLE utilisateurs (
    NoUtilisateur INT(3) AUTO_INCREMENT PRIMARY KEY ,
    Courriel VARCHAR(50),
    MotDePasse VARCHAR(255),
    Creation DATETIME,
    NbConnexions INT(4),
    Statut INT,
    NoEmpl INT(4),
    Nom VARCHAR(25),
    Prenom VARCHAR(20),
    NoTelMaison VARCHAR(15),
    NoTelTravail VARCHAR(21),
    NoTelCellulaire VARCHAR(15),
    Modification DATETIME,
    AutresInfos VARCHAR(50)
) ENGINE=InnoDB;


CREATE TABLE connexions (
    NoConnexion INT(4) PRIMARY KEY,
    NoUtilisateur INT(3),
    Connexion DATETIME,
    Deconnexion DATETIME,
    FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;


CREATE TABLE annonces (
    NoAnnonce INT(4) PRIMARY KEY,
    NoUtilisateur INT(3),
    Parution DATETIME,
    Categorie INT(6),
    DescriptionAbregee VARCHAR(50),
    DescriptionComplete VARCHAR(250),
    Prix DECIMAL(8,2),
    Photo VARCHAR(50),
    MiseAJour DATETIME,
    Etat INT,
    FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (Categorie) REFERENCES categories(NoCategorie)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;
