{* $Id: change_password.tpl,v 1.8 2005/11/17 06:55:37 max Exp $ *}
{capture name=dialog}
<table>
<form action="change_password.php" method="post">
<tr><td colspan="3">{$lng.txt_chpass_msg}</td></tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
<td>{$lng.lbl_username}:</td><td>&nbsp;</td><td><b>{$username}</b></td>
</tr>
<tr>
<td>{$lng.lbl_old_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="old_password" value="{$old_password}" /></td>
</tr>
<tr>
<td>{$lng.lbl_new_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="new_password" value="{$new_password}" /></td>
</tr>
<tr>
<td>{$lng.lbl_confirm_password}:</td><td><font class="Star">*</font></td><td><input type="password" size="30" name="confirm_password" value="{$confirm_password}" /></td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
<td colspan="3" align="center"><input type="submit" /></td>
</tr>
</form>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_chpass content=$smarty.capture.dialog extra='width="100%"'}
