{* $Id: send_to_friend.tpl,v 1.11 2006/03/17 12:30:32 svowl Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var requiredFields = new Array();
requiredFields[0] = new Array('send_name', "{$lng.lbl_send_your_name|strip_tags|replace:'"':'\"'}", false);
requiredFields[1] = new Array('send_from', "{$lng.lbl_send_your_email|strip_tags|replace:'"':'\"'}", false);
requiredFields[2] = new Array('send_to', "{$lng.lbl_recipient_email|strip_tags|replace:'"':'\"'}", false);
-->
</script>
{include file="check_required_fields_js.tpl"}
{include file="check_email_script.tpl"}
{capture name=dialog}
<form action="product.php" method="post" name="send">
<input type="hidden" name="mode" value="send" />
<input type="hidden" name="productid" value="{$product.productid}" />
<table>

<tr>
    <td class="FormButton">{$lng.lbl_send_your_name}:</td>
	<td><font class="Star">*</font></td>
    <td><input id="send_name" type="text" size="45" name="name" /></td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_send_your_email}:</td>
	<td><font class="Star">*</font></td>
	<td><input id="send_from" type="text" size="45" name="from" onchange="javascript: checkEmailAddress(this);" /></td>
</tr>

<tr>
    <td class="FormButton">{$lng.lbl_recipient_email}:</td>
	<td><font class="Star">*</font></td>
    <td><input id="send_to" type="text" size="45" name="email" onchange="javascript: checkEmailAddress(this);" /></td>
</tr> 

<tr>
	<td colspan="3"><br />{include file="buttons/button.tpl" style="button" button_title=$lng.lbl_send_to_friend href="javascript: if(checkRequired(requiredFields)) document.send.submit();"}</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_send_to_friend content=$smarty.capture.dialog extra='width="100%"'}
