
CREATE TABLE `mk_kunde` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(20),
  `kundennummer` varchar(10) NOT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `tafel` boolean DEFAULT NULL,
  `kaufhaus` boolean DEFAULT NULL,
  `nachweis` ENUM('ALG','Bafög','Bürgergeld','Rente','Grundsicherung','Gehalt','Wohngeld') NOT NULL,
  `gueltigkeit` date,
  `bemerkung` text DEFAULT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY `kundennummer` (`kundennummer`)
);
CREATE TABLE `mk_tafel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunde_id` int(11) NOT NULL UNIQUE,
  `personen` int(2),
  `preis` int(2),
  `gruppe` ENUM('blau','lila','grün','rot','gelb') NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_tafel_kunde
  FOREIGN KEY (kunde_id) references mk_kunde(id)
  ON DELETE CASCADE
);
CREATE TABLE `mk_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kunde_id` int(11) NOT NULL,			-- Person ist immer einem Kunden zugewiesen.
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_person_kunde
  FOREIGN KEY (kunde_id) references mk_kunde(id)
  ON DELETE CASCADE
);
--  `p_type` varchar(15) NOT NULL,			-- Ist "Kontaktperson" (Kunde hat jetzt vor- und nachname.
CREATE TABLE `mk_kontakt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11),			-- Kontakt ist immer einer Person zugewiesen.
  `kunde_id` int(11),
  `k_type` ENUM('Festnetz','Mobil','Email') NOT NULL,			-- "Festnetz", "Mobil", "Email"
  `kontakt` varchar(50) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT ID_NOT_NULL CHECK(person_id > 0 or kunde_id > 0),
  CONSTRAINT fk_kontakt_person
  FOREIGN KEY (person_id) references mk_person(id)
  ON DELETE CASCADE,
  CONSTRAINT fk_kontakt_kunde
  FOREIGN KEY (kunde_id) references mk_kunde(id)
  ON DELETE CASCADE
);
CREATE TABLE `mk_adresse` (			
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11), 			-- Adresse ist einer Person zugewiesen.
  `kunde_id` int(11), 			-- Adresse ist einem Kunden zugewiesen.
  `strasse` varchar(50) NOT NULL,
  `hausnummer` varchar(10) NOT NULL,
  `plz` varchar(5) NOT NULL,
  `ort` varchar(50) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT ID_NOT_NULL CHECK(person_id > 0 or kunde_id > 0),
  CONSTRAINT fk_adresse_person
  FOREIGN KEY (person_id) references mk_person(id)
  ON DELETE CASCADE,
  CONSTRAINT fk_adresse_kunde
  FOREIGN KEY (kunde_id) references mk_kunde(id)
  ON DELETE CASCADE
);

