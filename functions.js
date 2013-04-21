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

function moveimages(cnt, mv, imagestring) {
	var j;
	var images = imagestring.split(",");
	var current = document.getElementById("shift").innerHTML;
	document.getElementById("shift").innerHTML=(current-mv);
	mv -= current;
	for(var i=0; i<cnt; i++) {
		j = (mv-i)%cnt;
		if(j<0) j += cnt;
		document.getElementById('ipos' + i).innerHTML="<a href=\"?c=photography&amp;pi=" + (cnt-j-1) + "&amp;shift=" + (mv*-1) + "\"><img src=\"img/photography/grid/" + images[j] + "\" alt=\"" + images[j] + "\" /></a>";
		}
	if(document.getElementById('pr') != null) {
		var pr = document.getElementById('pr');
		var pnum = pr.innerHTML.match(/\d+/);
		pr.innerHTML="<a href=\"?c=photography&amp;pi=" + pnum + "&amp;shift=" + (mv*-1) + "\">&laquo;</a>";
		}
	if(document.getElementById('nx') != null) {
		var nx = document.getElementById('nx');
		var nnum = nx.innerHTML.match(/\d+/);
		nx.innerHTML="<a href=\"?c=photography&amp;pi=" + nnum + "&amp;shift=" + (mv*-1) + "\">&raquo;</a>";
		}
	}
