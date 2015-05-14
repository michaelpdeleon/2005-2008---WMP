{* $Id: customer_manufacturers_list.tpl,v 1.6 2005/11/21 12:42:10 max Exp $ *}
{capture name=dialog}
{ include file="customer/main/navigation.tpl" }
<br />
<table cellspacing="5">
{foreach from=$manufacturers item=v}
<tr>
	<td class="ManufacturersItem"><a href="manufacturers.php?manufacturerid={$v.manufacturerid}"><font class="ItemsList">{$v.manufacturer|escape}</font></a></td>
</tr>
{/foreach}
</table>
<br /><br />
{ include file="customer/main/navigation.tpl" }
{/capture}
{include file="dialog.tpl" title=$lng.lbl_manufacturers content=$smarty.capture.dialog extra='width="100%"'}
