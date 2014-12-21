function uml2char(str) {
    return str.replace(/\u00E4/g, "&auml;").replace(/\u00C4/g, '&Auml;').replace(/\u00F6/g, '&ouml;').replace(/\u00D6/g, '&Ouml;').replace(/\u00FC/g, '&uuml;').replace(/\u00DC/g, '&Uuml;').replace(/\u00DF/g, '&szlig;');
    }

function dropdown(id, content, action, label) {

    if(action == 'open') {
        document.getElementById('cmnt_' + id).innerHTML=content;

        trigger = "<span class=\"a\" onClick=\"dropdown(\'" + id + "\', \'" + uml2char(content.replace(/'/g, "\\"+"\'")) + "\', \'close\', \'" + label + "\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">" + label + "</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }
    if(action == 'close') {
        document.getElementById('cmnt_' + id).innerHTML="";

        trigger = "<span class=\"a\" onClick=\"dropdown(\'" + id + "\', \'" + uml2char(content.replace(/'/g, "\\"+"\'")) + "\', \'open\', \'" + label + "\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">" + label + "</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }

    }

function dropdown2(id, action) {

    if(action == 'open') {
        document.getElementById('cntnt_' + id).style.display='block';

        trigger = "<span class=\"a\" onClick=\"dropdown2(\'" + id + "\', \'close\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">collapse</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }
    if(action == 'close') {
        document.getElementById('cntnt_' + id).style.display='none';

        trigger = "<span class=\"a\" onClick=\"dropdown2(\'" + id + "\', \'open\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">expand</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }

    }

function loadfile(file, id, action) {
    if(action == 'open') {
        var oRequest = new XMLHttpRequest();
        var sURL = window.location.href.toString().replace(/\/[^\/]+$/, "") + "/" + file;

        oRequest.open("GET", sURL, false);
        oRequest.setRequestHeader("User-Agent", navigator.userAgent);
        oRequest.send(null);

        if (oRequest.status==200) document.getElementById('cntnt_' + id).innerHTML=oRequest.responseText;
        else document.getElementById('cntnt' + id).innerHTML="an error occured loading the specified content ... sorry for that.";

        trigger = "<span class=\"a\" onClick=\"loadfile(\'" + file + "\', \'" + id + "\', \'close\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">collapse</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }
    if(action == 'close') {
        document.getElementById('cntnt_' + id).innerHTML="";

        trigger = "<span class=\"a\" onClick=\"loadfile(\'" + file + "\', \'" + id + "\', \'open\')\" onMouseOver=\"this.style.cursor=\'pointer\'\">expand</span>";
        document.getElementById('trigger_' + id).innerHTML = trigger;
        }
}
