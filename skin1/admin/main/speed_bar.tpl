{include file="page_title.tpl" title=$lng.lbl_speed_bar_management}

{$lng.txt_speed_bar_management_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="speed_bar.php?"}

<br />

<form action="speed_bar.php" method="post" name="speedbarform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="id" value="" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	<td>{$lng.lbl_pos}</td>
	<td>{$lng.lbl_link_title}</td>
	<td>{$lng.lbl_url}</td>
	<td>{$lng.lbl_active}</td>
	<td class="SectionBox">&nbsp;</td>
</tr>

{if $speed_bar}

{section name=sb loop=$speed_bar}
<tr>
	<td>
	<input type="hidden" name="posted_data[{%sb.index%}][id]" value="{$speed_bar[sb].id}" />
	<input type="text" size="3" maxlength="5" name="posted_data[{%sb.index%}][orderby]" value="{$speed_bar[sb].orderby}" />
	</td>
	<td><input type="text" size="25" name="posted_data[{%sb.index%}][title]" value="{$speed_bar[sb].title|escape}" /></td>
	<td><input type="text" size="45" name="posted_data[{%sb.index%}][link]" value="{$speed_bar[sb].link|escape}" /></td>
	<td align="center"><input type="checkbox" name="posted_data[{%sb.index%}][active]" value="Y"{if $speed_bar[sb].active eq "Y"} checked="checked"{/if} /></td>
	<td><input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick='javascript: document.speedbarform.id.value="{$speed_bar[sb].id}"; submitForm(this, "delete");' /></td>
</tr>
{/section}

{else}

<tr>
	<td colspan="5" align="center">{$lng.lbl_no_links_defined}</td>
</tr>

{/if}

<tr>
	<td colspan="5">&nbsp;</td>
</tr>

<tr>
	<td colspan="5">{include file="main/subheader.tpl" title=$lng.lbl_add_link}</td>
</tr>

<tr>
	<td><input type="text" size="3" maxlength="5" name="new_orderby" /></td>
	<td><input type="text" size="25" name="new_title" /></td>
	<td><input type="text" size="45" name="new_link" value="{$http_location}/" /></td>
	<td align="center"><input type="checkbox" name="new_active" value="Y" checked="checked" /></td>
</tr>

<tr>
	<td colspan="5" class="SubmitBox"><input type="submit" value="{$lng.lbl_add_update|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture} 
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_speed_bar_management extra='width="100%"'}
