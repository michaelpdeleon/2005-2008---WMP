{* $Id: ups_access_license_1.tpl,v 1.7 2006/02/01 14:26:44 max Exp $ *}
{capture name=dialog}
<!-- ACCESS LICENSE AGREEMENT SCREEN START -->
<br />
<form action="ups.php" method="post" name="upsstep1form">
<input type="hidden" name="current_step" value="{$ups_reg_step}" />

<table cellpadding="2" cellspacing="2" width="100%">

<tr>
	<td width="95" align="center" valign="top">{include file="modules/UPS_OnLine_Tools/ups_logo.tpl"}</td>
	<td>&nbsp;</td>
	<td width="100%">
<font class="ProductTitle">{$lng.lbl_ups_wizard}</font>

<br /><br />

{$lng.txt_ups_wizard_text}

<br /><br /><br />

<div align="right">
<table border="0">
<tr>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_next style="button" href="javascript: document.upsstep1form.submit()"}</td>
	<td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_cancel style="button" href="ups.php?mode=cancel"}</td>
	<td><img src="{$ImagesDir}/spacer.gif" width="50" height="1" alt="" /></td>
</tr>
</table>
</div>

	</td>
</tr>

</table>
</form>

<br />

<hr />

<div align="center">
{$lng.txt_ups_trademark_text}
</div>
<!-- ACCESS LICENSE AGREEMENT SCREEN END -->
{/capture}
{include file="modules/UPS_OnLine_Tools/dialog.tpl" title=$title content=$smarty.capture.dialog extra='width="100%"'}

