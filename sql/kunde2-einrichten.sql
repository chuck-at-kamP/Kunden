
-- Zuerst Kunde einrichten ?

INSERT INTO `mk_kunde` (`kundennummer`,`vorname`,`nachname`,`tafel`,`kaufhaus`,`nachweis`,`gueltigkeit`,`bemerkung`) 
	VALUES ('002/26','Peter','Petersen',true,true,'Gehalt','2026-12-31','Testkunde');

INSERT INTO `mk_adresse` (`kunde_id`, `strasse`, `hausnummer`, `plz`, `ort`) 
	VALUES (2, 'Fritzstrasse', '33', '22033', 'Hamburg');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (2, 'Festnetz', '040333333');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (2, 'Email', 'petersen@localhost');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (2, 'Mobil', '0171333333');

INSERT INTO `mk_tafel` (`kunde_id`, `personen`, `preis`, `gruppe`) 
	VALUES (2, 1, 2, 'gelb');

