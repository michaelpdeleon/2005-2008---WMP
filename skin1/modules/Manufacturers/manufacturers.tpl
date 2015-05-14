{* $Id: manufacturers.tpl,v 1.32.2.3 2006/07/19 06:38:47 max Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_manufacturers}
{if $active_modules.HTML_Editor}
{include file="modules/HTML_Editor/editor.tpl"}
{/if}

{if $usertype eq "A" or ($active_modules.Simple_Mode ne "" and $usertype eq "P")}
{assign var="administrate" value="Y"}
{/if}

{$lng.txt_manufacturers_top_text}

{if $active_modules.Simple_Mode eq ""}
<br /><br />

{$lng.txt_manufacturers_note_pro}
{/if}

{if $single_mode eq ""}
<br /><br />

{$lng.txt_manufacturers_notes}

{if $active_modules.Simple_Mode eq "" and $usertype eq "P"}
{$lng.txt_manufacturers_note_pro_provider}
{/if}

{/if}

<br /><br />

{if $mode ne "manufacturer_info"}

{capture name=dialog}

{include file="customer/main/navigation.tpl"}

{if $manufacturers ne ""}

<script type="text/javascript" language="JavaScript 1.2">
<!--
checkboxes_form = 'manufform';
checkboxes = new Array({foreach from=$manufacturers item=v key=k}{if $k > 0},{/if}'{if !($administrate eq "" and ($v.provider ne $login or $v.used_by_others gt 0))}to_delete[{$v.manufacturerid}]{/if}'{/foreach});
 
-->
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}

<div style="line-height:170%"><a href="javascript:change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:change_all(false);">{$lng.lbl_uncheck_all}</a></div>

{/if}

<form action="manufacturers.php" method="post" name="manufform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="page" value="{$page}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	{if $manufacturers ne ""}<td width="10">&nbsp;</td>{/if}
	<td width="40%">{$lng.lbl_manufacturer}</td>
	<td width="30%">{$lng.lbl_provider}</td>
	<td width="20%" align="center">{$lng.lbl_products}</td>
	<td width="30" align="center">{$lng.lbl_orderby}</td>
	<td width="30" align="center">{$lng.lbl_active}</td>
</tr>

{if $manufacturers ne ""}

{foreach from=$manufacturers item=v}

<tr{cycle values=", class='TableSubHead'"}>
	<td align="center"><input type="checkbox" name="to_delete[{$v.manufacturerid}]"{if $administrate eq "" and ($v.provider ne $login or $v.used_by_others gt 0)} disabled="disabled"{/if} /></td>
	<td><b><a href="manufacturers.php?manufacturerid={$v.manufacturerid}{if $page}&amp;page={$page}{/if}">{$v.manufacturer}</a></b></td>
	<td>{if $v.is_provider eq 'Y'}{$v.provider_name}{else}{$lng.lbl_manuf_owner_lost}{/if}{if $administrate} ({$v.provider}){/if}</td>
	<td align="center">{$v.products_count|default:$lng.txt_not_available}{if $v.used_by_others gt 0}*{assign var="show_note" value="Y"}{/if}</td>
	<td align="center"><input type="text" name="records[{$v.manufacturerid}][orderby]" size="5" value="{$v.orderby}"{if $administrate eq ""} disabled="disabled"{/if} /></td>
	<td align="center"><input type="checkbox" name="records[{$v.manufacturerid}][avail]" value="Y"{if $v.avail eq "Y"} checked="checked"{/if}{if $administrate eq ""} disabled="disabled"{/if} /></td>
</tr>

{/foreach}

{if $show_note eq "Y"}
<tr>
	<td colspan="6"><br />{$lng.txt_manufacturers_special_note}</td>
</tr>
{/if}

<tr>
	<td colspan="6" class="SubmitBox">
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('^to_delete\\[.+\\]', 'gi'))) if (confirm('{$lng.txt_manufacturers_delete_msg|strip_tags}')) {ldelim} document.manufform.mode.value='delete'; document.manufform.submit(); {rdelim}" />
	<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	</td>
</tr>

{else}

<tr>
	<td colspan="6" align="center"><br />{$lng.txt_no_manufacturers}</td>
</tr>

{/if}

<tr>
<td colspan="6"><br /><input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location = 'manufacturers.php?mode=add';" /></td>
</tr>

</table>

</form>

{include file="customer/main/navigation.tpl"}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_manufacturers_list content=$smarty.capture.dialog extra='width="100%"'}

{else}

{include file="main/include_js.tpl" src="main/popup_image_selection.js"}

{capture name=dialog}

<div align="right">
<table cellspacing="0" cellpadding="0">
<tr>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_manufacturers_list href="manufacturers.php?page=`$page`"}</td>
{if $manufacturer.manufacturerid}
	<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_add_manufacturer href="manufacturers.php?mode=add&page=`$page`"}</td>
{/if}
</tr>
</table>
</div>

{if $administrate eq "" and $manufacturer.used_by_others gt 0}
<br />
<font class="ErrorMessage">{$lng.txt_manufacturers_warning}</font>
<br />
{/if}

<br />

{if $administrate eq "" and $login ne $manufacturer.provider and $smarty.get.mode ne "add"}
{assign var="disabled" value=" disabled"}
{/if}

{if $manufacturer.manufacturerid ne ''}
{include file="main/language_selector.tpl" script="manufacturers.php?manufacturerid=`$manufacturer.manufacturerid`&"}
{/if}
<form action="manufacturers.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="manufacturerid" value="{$manufacturer.manufacturerid}" />
<input type="hidden" name="page" value="{$page}" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td width="20%" class="FormButton">{$lng.lbl_manufacturer}:</td>
	<td><font class="Star">*</font></td>
	<td width="80%"><input type="text" name="manufacturer" size="50" value="{$manufacturer.manufacturer}" style="width:80%"{$disabled} /></td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_logo}:</td>
	<td>&nbsp;</td>
	{if $manufacturer.is_image eq 'Y'}{assign var="no_delete" value=""}{else}{assign var="no_delete" value="Y"}{/if}
	<td>{include file="main/edit_image.tpl" type="M" id=$manufacturer.manufacturerid delete_url="manufacturers.php?mode=delete_image&manufacturerid=`$manufacturer.manufacturerid`" button_name=$lng.lbl_save no_delete=$no_delete}</td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_description}:</td>
	<td>&nbsp;</td>
	<td>
{include file="main/textarea.tpl" name="descr" cols=55 rows=10 class="InputWidth" data=$manufacturer.descr width="80%" btn_rows=3}
	</td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_url}:</td>
	<td>&nbsp;</td>
	<td><input type="text" size="50" name="url" value="{$manufacturer.url}" style="width:80%"{$disabled} /></td>
</tr>

{if $administrate eq "Y"}
<tr>
	<td class="FormButton">{$lng.lbl_orderby}:</td>
	<td>&nbsp;</td>
	<td><input type="text" name="orderby" size="5" value="{$manufacturer.orderby|default:"0"}" /></td>
</tr>

<tr>
	<td class="FormButton">{$lng.lbl_availability}:</td>
	<td>&nbsp;</td>
	<td><input type="checkbox" name="avail" value="Y"{if $manufacturer.avail eq 'Y' || $manufacturer.manufacturerid eq ''} checked="checked"{/if} /></td>
</tr>
{/if}

<tr>
	<td colspan="2">&nbsp;</td>
	<td><input type="submit" value=" {$lng.lbl_save|strip_tags:false|escape} "{$disabled} /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_manufacturer_details content=$smarty.capture.dialog extra='width="100%"'}

{/if}

