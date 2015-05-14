{* $Id: logs.tpl,v 1.7.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_shop_logs}

{include file="dialog_tools.tpl"}

<br />

{$lng.txt_shop_logs_top_text}

<br /><br />

<script type="text/javascript" language="JavaScript 1.2"><!--

function managedate(type, status) {ldelim}

	var fields = new Array('StartDay','StartMonth','StartYear','EndDay','EndMonth','EndYear');

	for (var i in fields)
		document.searchform.elements[fields[i]].disabled = status;

{rdelim}

--></script>

{capture name=dialog}

<form name="searchform" action="logs.php" method="post">
<input type="hidden" name="mode" value="" />

<table cellpadding="1" cellspacing="5" width="100%">

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_date_period}:</td>
<td width="10">&nbsp;</td>
<td>
<table cellpadding="0" cellspacing="0">
<tr>
	<td width="5"><input id="date_period_all" type="radio" name="posted_data[date_period]" value=""{if $search_prefilled eq "" or $search_prefilled.date_period eq ""} checked="checked"{/if} onclick="javascript: managedate('date',true)" /></td>
	<td nowrap="nowrap"><label for="date_period_all">{$lng.lbl_all_dates}&nbsp;&nbsp;</label></td>

	<td width="5"><input id="date_period_M" type="radio" name="posted_data[date_period]" value="M"{if $search_prefilled.date_period eq "M"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td nowrap="nowrap"><label for="date_period_M">{$lng.lbl_this_month}&nbsp;&nbsp;</label></td>

	<td width="5"><input id="date_period_W" type="radio" name="posted_data[date_period]" value="W"{if $search_prefilled.date_period eq "W"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td nowrap="nowrap"><label for="date_period_W">{$lng.lbl_this_week}&nbsp;&nbsp;</label></td>

	<td width="5"><input id="date_period_D" type="radio" name="posted_data[date_period]" value="D"{if $search_prefilled.date_period eq "D"} checked="checked"{/if} onclick="javascript:managedate('date',true)" /></td>
	<td nowrap="nowrap"><label for="date_period_D">{$lng.lbl_today}</label></td>
</tr>
<tr>
	<td width="5"><input id="date_period_C" type="radio" name="posted_data[date_period]" value="C"{if $search_prefilled.date_period eq "C"} checked="checked"{/if} onclick="javascript:managedate('date',false)" /></td>
	<td colspan="7"><label for="date_period_C">{$lng.lbl_specify_period_below}</label></td>
</tr>
</table>
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_date_from}:</td>
<td width="10">&nbsp;</td>
<td>
{html_select_date prefix="Start" time=$search_prefilled.start_date start_year=$config.Company.start_year end_year=$config.Company.end_year}
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_date_through}:</td>
<td width="10">&nbsp;</td>
<td>
{html_select_date prefix="End" time=$search_prefilled.end_date start_year=$config.Company.start_year end_year=$config.Company.end_year display_days=yes}
</td>
</tr>

<tr valign="top">
<td class="FormButton" nowrap="nowrap">{$lng.lbl_log_include_logs}:</td>
<td width="10">&nbsp;</td>
<td>
  <table>
{foreach key=log_label item=txt_label from=$log_labels}
   <tr>
     <td><input id="ll_{$log_label}" type="checkbox" name="posted_data[logs][]" value="{$log_label}"{if $search_prefilled.logs.$log_label ne ""} checked="checked"{/if} /></td>
     <td><label for="ll_{$log_label}">{$txt_label}</label></td>
{/foreach}
  </table>
</td>
</tr>

<tr valign="top">
<td class="FormButton" wrap>{$lng.lbl_log_records_count}:</td>
<td width="10">&nbsp;</td>
<td>
<input type="text" name="posted_data[count]" value="{$search_prefilled.count}" size="5" />
<br />
<font class="SmallText">{$lng.lbl_log_records_count_note}</font>
</td>
</tr>

<tr>
	<td colspan="2">&nbsp;</td>
	<td class="SubmitBox">
	<input type="submit" value="{$lng.lbl_search|strip_tags:false|escape}" onclick="javascript: document.searchform.mode.value = ''; document.searchform.submit();" />
	<input type="submit" value="{$lng.lbl_log_clean_selected|strip_tags:false|escape}" onclick="javascript: document.searchform.mode.value = 'clean'; document.searchform.submit();" />
	</td>
</tr>

{if $search_prefilled.date_period ne "C"}
<script type="text/javascript" language="JavaScript 1.2"><!--
managedate('date',true);
--></script>
{/if}

</form>
</table>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"' title=$lng.lbl_view_shop_logs}

{if $show_results ne ""}
<br /><br />
{capture name=dialog}
{if $logs ne ""}
{foreach key=label item=data from=$logs}
<a name="result_{$label}" />
{include file="main/subheader.tpl" title=$log_labels.$label|default:$label}
<tt>
{$data|replace:"-------------------------------------------------\n":'<hr size="1" noshade="noshade" />'|replace:"\n":"<br />"|replace:"``":"&ldquo;"|replace:"''":"&rdquo;"}
</tt>
<br />
{/foreach}
{else}{* $logs ne "" *}
{$lng.lbl_log_no_entries_found}
{/if}{* $logs ne "" *}
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"' title=$lng.lbl_search_results}
{/if}{* $show_results ne "" *}
