{* $Id: counties.tpl,v 1.8.2.3 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_counties_management}

{$lng.txt_counties_management_top_text}

<br /><br />

{capture name=dialog}

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td class="HeadText">{$state.state} ({$state.country})</td>
<td align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_return_states_list href="states.php?country=`$state.country_code`"}</td>
</tr>
</table>

<br /><br />

{include file="customer/main/navigation.tpl"}

{if $counties ne ""}
<form action="counties.php" method="post" name="countiesform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="stateid" value="{$stateid}" />
<input type="hidden" name="page" value="{$smarty.get.page}" />
{/if}

<table cellpadding="3" cellspacing="1" width="80%">

{if $counties ne ""}
<tr>
	<td colspan="2">{include file="main/check_all_row.tpl" style="line-height: 170%; text-align: right;" form="countiesform" prefix="selected"}</td>
</tr>
{/if}

<tr class="TableHead">
	<td width="80%">{$lng.lbl_county}</td>
	<td width="20%" align="center">{$lng.lbl_delete}</td>
</tr>

{if $counties ne ""}

{section name=cnt loop=$counties}

<tr{cycle values=", class='TableSubHead'"}>
<td><input type="text" size="50" name="posted_data[{$counties[cnt].countyid}][county]" value="{$counties[cnt].county}" /></td>
<td align="center"><input type="checkbox" name="selected[{$counties[cnt].countyid}]" /></td>
</tr>

{/section}

<tr>
<td colspan="4"><br />
{include file="customer/main/navigation.tpl"}

<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: document.countiesform.mode.value = 'delete'; document.countiesform.submit();" />
</td>
</tr>

{else}

<tr>
<td colspan="4" align="center">{$lng.txt_no_counties}</td>
</tr>

{/if}

</table>

{if $counties ne ""}
</form>
{/if}

<br /><br />

<form action="counties.php" method="post">
<input type="hidden" name="stateid" value="{$stateid}" />
<input type="hidden" name="mode" value="add" />

<table cellpadding="2" cellspacing="1">

<tr>
<td colspan="3"><br />{include file="main/subheader.tpl" title=$lng.lbl_add_new_county}</td>
</tr>

<tr>
<td>{$lng.lbl_enter_county_name}:</td>
<td><input type="text" size="40" name="new_county_name" value="" /></td>
<td><input type="submit" value=" {$lng.lbl_add|strip_tags:false|escape} "></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_counties content=$smarty.capture.dialog extra='width="100%"'}
