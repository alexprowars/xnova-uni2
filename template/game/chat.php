<style>
.date1	{ font-size: 11px; text-decoration:none; font-weight:normal; color: #007000; }
.date2	{ font-size: 11px; text-decoration:none; font-weight:normal; color: #007000; background-color: #00FFAA }
.to 	{ cursor:pointer; color: #FFFFFF; font-weight: bold; }
.player	{ color: #0046D5; font-weight:bold; cursor:pointer; }
.private 	{ COLOR: Red; font-weight:bold; cursor:pointer; }
</style>
<input type="hidden" name="message_id" id="message_id" value="1">

<table align="center" width='95%'><tbody>

<tr><td class="c"><b>Межгалактический чат</b></td></tr>

<tr><th>
	<div id="shoutbox" style="margin: 5px; vertical-align: text-top; height: 380px; overflow:auto;"></div>
	<div id="smiles" style="margin: 5px; height: 380px; display:none;"></div>
</th>
</tr>

<tr><th nowrap>
<input name="msg" type="text" id="msg" style="width:95%" maxlength="750"><br>
<input type="button" name="send" value="Отправить" id="send" onClick="addMessage()">
<input type="button" name="smils" value="Смайлы" id="smils" onClick="ShowSmiles()">
<input type="button" name="clear" value="Очистить" id="clear" onClick="ClearChat()">
<br><div id="new_msg"></div>
</th>
</tr>
</table>
<script type="text/javascript" src="scripts/smiles_v2.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/chat.js"></script>
<script>
function doSomething(e){
 	if (!e) var e = window.event;
	if (e.keyCode == 13)
		addMessage();
  	return true;
}
window.document.onkeydown = doSomething;
</script>