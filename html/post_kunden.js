
/* Button onClick-Event (Kontakt "+") macht eine Prüfung der form-input Felder auf "required" und sendet dann nicht.
   Input onChange-Event (z.B. bei "Tafel") macht keine Prüfung.

   Das onClick-Event soll durch ein zusätzliches Change-Event die "required" Prüfung umgehen:
*/
actiongui = document.getElementById("action_gui"); 				// Hidden-field für "action_gui" Parameter.
actiongui.addEventListener("change", (e) => { 					// Zusätzliches onChange-Event auf "action_gui"
	console.log("Submit ausgelöst für: " + e.target.value); 
	document.getElementById("form_kunden").submit();
});

	// Auf "kontakt_hinzu" wird ein onClick-Event gelegt, dass das vorherige Event auslöst.
document.getElementById("kontakt_hinzu").addEventListener("click", () => {	
	console.log("Event senden.");
	actiongui.value = "Add_Contact";
    actiongui.dispatchEvent(new Event("change", { addContact: true })); // Event feuern
});
document.getElementById("kontakt_entfernen").addEventListener("click", () => {	
	console.log("Event senden.");
	actiongui.value = "Del_Contact";
    actiongui.dispatchEvent(new Event("change", { delContact: true })); // Event feuern
});

