-- Script de création de la base de données SORTIES (MySQL/MariaDB)
-- Conversion depuis SQL Server 2012
-- Auteur : Moran (adapté pour WAMP)

DROP DATABASE IF EXISTS sorties;
CREATE DATABASE sortir CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE sortir;

-- Table ETATS
CREATE TABLE etats (
    no_etat INT NOT NULL AUTO_INCREMENT,
    libelle VARCHAR(30) NOT NULL,
    PRIMARY KEY (no_etat)
);

-- Table SITES
CREATE TABLE sites (
    no_site INT NOT NULL AUTO_INCREMENT,
    nom_site VARCHAR(30) NOT NULL,
    PRIMARY KEY (no_site)
);

-- Table VILLES
CREATE TABLE villes (
    no_ville INT NOT NULL AUTO_INCREMENT,
    nom_ville VARCHAR(30) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    PRIMARY KEY (no_ville)
);

-- Table LIEUX
CREATE TABLE lieux (
    no_lieu INT NOT NULL AUTO_INCREMENT,
    nom_lieu VARCHAR(30) NOT NULL,
    rue VARCHAR(30),
    latitude FLOAT,
    longitude FLOAT,
    villes_no_ville INT NOT NULL,
    PRIMARY KEY (no_lieu),
    CONSTRAINT lieux_villes_fk FOREIGN KEY (villes_no_ville) REFERENCES villes(no_ville)
        ON DELETE NO ACTION ON UPDATE NO ACTION
);

-- Table PARTICIPANTS
CREATE TABLE participants (
    no_participant INT NOT NULL AUTO_INCREMENT,
    pseudo VARCHAR(30) NOT NULL UNIQUE,
    nom VARCHAR(30) NOT NULL,
    prenom VARCHAR(30) NOT NULL,
    telephone VARCHAR(15),
    mail VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    administrateur BOOLEAN NOT NULL DEFAULT 0,
    actif BOOLEAN NOT NULL DEFAULT 1,
    sites_no_site INT NOT NULL,
    PRIMARY KEY (no_participant),
    CONSTRAINT participants_sites_fk FOREIGN KEY (sites_no_site) REFERENCES sites(no_site)
        ON DELETE NO ACTION ON UPDATE NO ACTION
);

-- Table SORTIES
CREATE TABLE sorties (
    no_sortie INT NOT NULL AUTO_INCREMENT,
    nom VARCHAR(30) NOT NULL,
    datedebut DATETIME NOT NULL,
    duree INT,
    datecloture DATETIME NOT NULL,
    nbinscriptionsmax INT NOT NULL,
    descriptioninfos VARCHAR(500),
    etatsortie INT,
    urlPhoto VARCHAR(250),
    organisateur INT NOT NULL,
    lieux_no_lieu INT NOT NULL,
    etats_no_etat INT NOT NULL,
    PRIMARY KEY (no_sortie),
    CONSTRAINT sorties_etats_fk FOREIGN KEY (etats_no_etat) REFERENCES etats(no_etat)
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT sorties_lieux_fk FOREIGN KEY (lieux_no_lieu) REFERENCES lieux(no_lieu)
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT sorties_participants_fk FOREIGN KEY (organisateur) REFERENCES participants(no_participant)
        ON DELETE NO ACTION ON UPDATE NO ACTION
);

-- Table INSCRIPTIONS
CREATE TABLE inscriptions (
    date_inscription DATETIME NOT NULL,
    sorties_no_sortie INT NOT NULL,
    participants_no_participant INT NOT NULL,
    PRIMARY KEY (sorties_no_sortie, participants_no_participant),
    CONSTRAINT inscriptions_sorties_fk FOREIGN KEY (sorties_no_sortie) REFERENCES sorties(no_sortie)
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT inscriptions_participants_fk FOREIGN KEY (participants_no_participant) REFERENCES participants(no_participant)
        ON DELETE NO ACTION ON UPDATE NO ACTION
);
