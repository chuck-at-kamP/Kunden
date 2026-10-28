
-- Zuerst Kunde einrichten ?

INSERT INTO `mk_kunde` (`kundennummer`,`vorname`,`nachname`,`tafel`,`kaufhaus`,`nachweis`,`gueltigkeit`,`bemerkung`) 
	VALUES ('003/26','Hinnerk','Jacobsen',false,true,'ALG','2026-12-31','Testkunde');

SET @kunde_id_1 = LAST_INSERT_ID();

INSERT INTO `mk_adresse` (`kunde_id`, `strasse`, `hausnummer`, `plz`, `ort`) 
	VALUES (LAST_INSERT_ID(), 'Eugenstrasse', '44', '22044', 'Hamburg');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (@kunde_id_1, 'Festnetz', '040444444');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (@kunde_id_1, 'Email', 'jacobsen@localhost');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (@kunde_id_1, 'Mobil', '0171444444');

INSERT INTO `mk_person` (`kunde_id`, `nachname`, `vorname`) 
	VALUES (@kunde_id_1, 'Pflegedienst 1', 'Hilfsperson 1');

SET @person_id_1 = LAST_INSERT_ID();

INSERT INTO `mk_kontakt` (`person_id`, `k_type`, `kontakt`) 
	VALUES (@person_id_1, 'Festnetz', '05012345');

INSERT INTO `mk_kontakt` (`person_id`, `k_type`, `kontakt`) 
	VALUES (@person_id_1, 'Mobil', '017212345');

INSERT INTO `mk_person` (`kunde_id`, `nachname`, `vorname`) 
	VALUES (@kunde_id_1, 'Pflegedienst 2', 'Hilfsperson 2');

SET @person_id_2 = LAST_INSERT_ID();

INSERT INTO `mk_kontakt` (`person_id`, `k_type`, `kontakt`) 
	VALUES (@person_id_2, 'Festnetz', '06012345');

INSERT INTO `mk_kontakt` (`person_id`, `k_type`, `kontakt`) 
	VALUES (@person_id_2, 'Mobil', '017312345');


