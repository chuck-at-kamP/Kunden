
/* Zum Ändern von Daten:
   Es sollen nur die geänderten Werte per POST übertragen werden.
	- add(name) - Das Feld wird einem Array von geänderten Werten hinzugefügt.
	- addSelect(name) - Das Feld wird einem Array von geänderten Werten hinzugefügt.
		add() hat bei "Select" nicht funktioniert, höchstwahrscheinlich gibt es ein Add-Event,
		deswegen der geänderte Funktionsname für "Select".
	- is_changed() - sucht, ob das Feld im tosubmit[] Array vorhanden ist.
	- before_submit() - Setzt für das input-Feld das disabled-Attribut auf "true", heißt
		wird im POST nicht übertragen.
*/

var tosubmit = []
function add(name) {
    if(tosubmit.indexOf(name) == -1)
        tosubmit.push(name);
}
function addSelect(name) {
    if(tosubmit.indexOf(name) == -1)
        tosubmit.push(name);
}

function is_changed(name) {
    for(var k = 0; k < tosubmit.length; k++)
        if(name == tosubmit[k])
            return name && true;
    return false;
}

function before_submit() {
	// console.log("Before Submit ...");
    var allInputs = document.getElementsByTagName("input");
    var allSelects = document.getElementsByTagName("select");
    for(var k = 0; k < allInputs.length; k++) {
        var name   = allInputs[k].name;
		var hidden = allInputs[k].getAttribute('hidden');
		var type   = allInputs[k].type;
		// console.log("Before Submit INPUT: i=" + k + ", name=" + name + ", hidden=" + hidden);
        if(!is_changed(name) & hidden==null & type!="submit") {
			// console.log("Before Submit INPUT: i=" + k + ", name=" + name + ", NOT changed");
            allInputs[k].disabled = true;
		}
		// else { console.log("Before Submit INPUT: i=" + k + ", name=" + name + ", SEND"); }
    }
    for(var k = 0; k < allSelects.length; k++) {
        var name = allSelects[k].name;
		// console.log("Before Submit SELECT: i=" + k + ", name=" + name);
        if(!is_changed(name)) {
			// console.log("Before Submit SELECT: i=" + k + ", name=" + name + ", NOT changed");
            allSelects[k].disabled = true;
		} // else { console.log("Before Submit SELECT: i=" + k + ", name=" + name + ", SEND"); }
    }
}

/* Beim onChange Event sollen die action-Parameter gesendet werden für:
	- Selct_Tafel
	- Add_Contact
	- Add_Contactperson
*/

function send(action_type, action_sub_type, action_gui = "None") {
	document.getElementById("action_type").value = action_type;
	document.getElementById("action_sub_type").value = action_sub_type;
	document.getElementById("action_gui").value = action_gui;
	console.log("send() Action Type: " + action_type + ", Sub Type: " + action_sub_type + ", Gui: " + action_gui);
	/* alert("Action Type: " + action_type); */
	if(action_type == "Button_Change") {
		// console.log("send() call before_submit()");
		before_submit();
	}
	document.getElementById("form_kunden").submit();
}

