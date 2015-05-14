{* $Id: profile_delete_confirmation.tpl,v 1.17 2005/11/30 13:29:35 max Exp $ *}
{capture name=dialog}

{if $smarty.get.confirmed eq "Y"}

{$lng.txt_profile_deleted}

{else}

<form action="register.php" method="get" name="processform">
<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="mode" value="delete" />

<table cellpadding="0" cellspacing="0" width="100%">

<tr>
	<td>
<div class="Text">
{$lng.txt_profile_delete_confirmation}
</div>
<br /><br />
<table cellspacing="0" cellpadding="2">
<tr>
	<td>{include file="buttons/yes.tpl" href="javascript:document.processform.mode.value='delete'; document.processform.submit()" js_to_href="Y"}</td>
	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/no.tpl" href="register.php?mode=notdelete"}</td>
</tr>
</table>

	</td>
</tr>

</table>
</form>

{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
