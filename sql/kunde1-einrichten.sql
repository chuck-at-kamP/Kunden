
-- Zuerst Kunde einrichten ?

INSERT INTO `mk_kunde` (`kundennummer`,`vorname`,`nachname`,`tafel`,`kaufhaus`,`nachweis`,`gueltigkeit`,`bemerkung`) 
	VALUES ('001/26','Hans','Hansen',true,true,'Gehalt','2026-12-31','Testkunde');

INSERT INTO `mk_adresse` (`kunde_id`, `strasse`, `hausnummer`, `plz`, `ort`) 
	VALUES (LAST_INSERT_ID(), 'Am der Wiese', '22', '22022', 'Hamburg');

INSERT INTO `mk_kontakt` (`kunde_id`, `k_type`, `kontakt`) 
	VALUES (1, 'Festnetz', '04023232323');

INSERT INTO `mk_tafel` (`kunde_id`, `personen`, `preis`, `gruppe`) 
	VALUES (1, 2, 4, 'blau');

