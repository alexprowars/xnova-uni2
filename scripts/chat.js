function to(login) {
	msg.focus();
	msg.value = 'для ['+login+'] ' + msg.value;
	msg.focus();
}

function pp(login) {
	msg.focus();
	msg.value = 'приватно ['+login+'] ' + msg.value;
	msg.focus();
}

var ChatTimer;
function StopChatTimer() {
  	if (ChatTimer)
    		clearTimeout(ChatTimer);
  	return 1;
}

function RefreshChat() {
  	StopChatTimer();
	showMessage();
  	ChatTimer = setTimeout(RefreshChat, 10000);
}

function MsgSent(msg_id) {

	document.all("message_id").value = msg_id;

	if (ChatTimer) clearTimeout(ChatTimer);
	ChatTimer = setTimeout(showMessage, 5000);
}

function ChatMsg (Time, Player, Msg, Me, My) {
	var str = "";

	for (var i = 0; i < sm_repl.length; i++) {
		Msg = Msg.replace(sm_find[i], '<img border="0" src="/images/smile/' + sm_repl[i] + '.gif" onclick="AddSmile(\''+sm_repl[i]+'\')" style="cursor:pointer">');
	}

	if (!Time) return;
	if (Me>0) str += "<font class=date2>";
	else str += "<font class=date1>";
	if (!Player) str += print_date(Time,1)+"</FONT> ";
	else {
		str += print_date(Time,1)+"</FONT> [";
		if (My==1) str += "<b style='COLOR: Red;'>";
		else str += "<b class=to onclick='to(\""+Player+"\");' style='cursor:pointer;cursor:hand;'>";
		str += Player+"</B>] ";
	}
	str += Msg+"<br>";

	document.getElementById('shoutbox').innerHTML += '<div align="left">'+str+'</div>';
	descendreTchat();

}

function descendreTchat(){
 	var elDiv =document.getElementById('shoutbox');
 	elDiv.scrollTop = elDiv.scrollHeight-elDiv.offsetHeight;
}

function addMessage() {

	var data = $("#msg").val();

	data = data.replace('%', '%25');
	while (data.indexOf('+')>=0) data = data.replace('+', '%2B');
	while (data.indexOf('#')>=0) data = data.replace('#', '%23');
	while (data.indexOf('&')>=0) data = data.replace('&', '%26');
	while (data.indexOf('?')>=0) data = data.replace('?', '%3F');
	while (data.indexOf('\'')>=0)data = data.replace('\'', '`');

	$("#msg").val('');

	$.ajax({
		type: "POST",
		url: "chat_daemon.php",
		data: "msg="+data+""
	});

	setTimeout(RefreshChat, 1000);
}

function showMessage()
{
	$.ajax({
		type: "GET",
		url: "chat_daemon.php",
		data: "message_id="+$("#message_id").val()+"",
		success: function(msg)
		{
			eval(msg)
		}
	});
}

function S(name){
	msg.value  += ':'+name+':';
	msg.focus();
}

var sml = 0;

function ShowSmiles () {
	var str = "";

	if (sml == 1) {
		HideSmiles();
		return;
	}
	
	sml = 1;
	var i = 0;
	for (i = 0; i < sm_repl.length; i++) {
        str += '<img src=images/smile/'+sm_repl[i]+'.gif BORDER=0 ALT="'+sm_repl[i]+'" onclick="S(\''+sm_repl[i]+'\')" style="cursor:pointer"> ';
	}
	document.getElementById('smiles').innerHTML = str;
	document.getElementById('smiles').style.display = "block";
	document.getElementById('shoutbox').style.display = "none";
}
function HideSmiles () {
	document.getElementById('smiles').innerHTML = "";
	document.getElementById('smiles').style.display = "none";
	document.getElementById('shoutbox').style.display = "block";
	sml = 0;
}

function ClearChat(){
	document.getElementById("shoutbox").innerHTML = '';
}

setTimeout(RefreshChat, 2000);