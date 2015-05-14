{* $Id: tools.tpl,v 1.21.2.3 2006/07/11 08:39:27 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_tools}

{include file="dialog_tools.tpl"}

<br />

{$lng.txt_tools_top_text}

<br /><br />

<script type="text/javascript">
<!--

var lbl_remove_test_data_confirm = "{$lng.lbl_remove_test_data_confirm|replace:'"':'\"'|replace:"\n":""}";
var lbl_remove_test_data_alert = "{$lng.lbl_remove_test_data_alert|replace:'"':'\"'|replace:"\n":""}";

{literal}
function clickMore(id) {
	if (!document.getElementById(id) || !document.getElementById(id+'_note'))
		return false;
	var disp = (document.getElementById(id).style.display == 'none');
	document.getElementById(id).style.display = disp ? "" : "none";
	document.getElementById(id+'_note').style.display = !disp ? "" : "none";
}

function changeRSD(sObj) {
	var obj = document.getElementById('tr_select_date');
	if (!obj)
		return false;

	obj.style.display = (sObj.options[sObj.selectedIndex].value == 's') ? '' : 'none';
}

function check_cc_assurance() {
	var obj = document.getElementById('assurance');
	if (!obj)
		return false;
	if (!obj.checked) {
		alert(lbl_remove_test_data_alert);
		return false;
	}

	return confirm(lbl_remove_test_data_confirm);
}

{/literal}
-->
</script>

{capture name=dialog}

{*** CLEARING CC INFO SECTION ***}

<a name="clearcc" />

{include file="main/subheader.tpl" title=$lng.txt_credit_card_information_removal}

<form action="tools.php" method="post" name="processform">
<table cellpadding="2" cellspacing="0">

{if $is_subscription}
<tr>
	<td colspan="2">
<table cellspacing="0" cellpadding="2">
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td>{$lng.txt_remove_cc_data_subscription_note}</td>
</tr>
</table>
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
{/if}

<tr>
	<td width="20"><input type="checkbox" id="remove_ccinfo_profiles" name="remove_ccinfo_profiles" value="Y" /></td>
	<td><label for="remove_ccinfo_profiles">{$lng.lbl_remove_from_customer_profiles}</label></td>
</tr>

<tr>
	<td><input type="checkbox" id="remove_ccinfo_orders" name="remove_ccinfo_orders" value="Y" /></td>
	<td><label for="remove_ccinfo_orders">{$lng.lbl_remove_from_completed_orders}</label></td>
</tr>

<tr>
	<td><input type="checkbox" id="save_4_numbers" name="save_4_numbers" value="Y" /></td>
	<td><label for="save_4_numbers">{$lng.lbl_save_last_4_digits_cc_number}</label></td>
</tr>

<tr>
	<td colspan="2"><input type="submit" name="mode_clear" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick='javascript: return confirm("{$lng.txt_cc_info_removal_warning|strip_tags}");' /><br /><br />
{$lng.txt_cc_info_removal_note}
	</td>
</tr>

</table>
</form>

<br /><br /><br />

{*** OPTIMIZE TABLE ***}

<a name="optimdb" />

{include file="main/subheader.tpl" title=$lng.lbl_optimize_tables}

<form action="tools.php" method="post" name="formmode_optimize">

<table cellpadding="2" cellspacing="0" width="100%">

<tr id="optimize_tables_small">
	<td>{$lng.txt_optimize_tables_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('optimize_tables_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="optimize_tables_small_note" style="display: none;">
	<td>{$lng.txt_optimize_tables_note}</td>
</tr>
<tr>
	<td class="SubmitBox"><input type="submit" name="mode_optimize" value="{$lng.lbl_optimize_tables|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<br /><br /><br />

{*** Check database integrity ***}

<a name="integrdb" />

{include file="main/subheader.tpl" title=$lng.lbl_check_database_integrity}

<form action="tools.php" method="post" name="formmode_check_integrity">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="check_database_integrity_small">
	<td>{$lng.txt_check_database_integrity_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('check_database_integrity_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="check_database_integrity_small_note" style="display: none;">
	<td>{$lng.txt_check_database_integrity_note}</td>
</tr>
<tr>
	<td class="SubmitBox"><input type="submit" name="mode_check_integrity" value="{$lng.lbl_check_database_integrity|strip_tags:false|escape}" /></td>
</tr>

{if $err_store}
<tr>
	<td><br />
{$lng.lbl_unrelated_data_found}
<br />

<table width="100%" cellspacing="1" cellpadding="2">
{foreach from=$err_store item=keys key=tbl}
{foreach from=$keys item=rows key=tbl2}
<tr>
	<td colspan="2">{include file="main/visiblebox_link.tpl" mark=$tbl|cat:$tbl2 title=$tbl|cat:" -> "|cat:$tbl2}</td>
</tr>
<tr id="box{$tbl|cat:$tbl2}" style="display: none;">
	<td width="11"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
	<td>
{$lng.lbl_relationships_missing}:
	<table cellspacing="1" cellpadding="2" width="100%">
	<tr class="TableHead">
		<td width="50%">{$lng.lbl_records_in_table|substitute:"table":$tbl}</td>
		<td>{$lng.lbl_fields_in_table|substitute:"table":$tbl2}</td>
	</tr>
{foreach from=$rows item=row}
	<tr{cycle name=$tbl|cat:$tbl2 values=', class="TableSubHead"'}>
		<td>
{foreach from=$row.row item=v key=k}
{$k}: {$v}<br />
{/foreach}
		</td>
		<td valign="top">
{foreach from=$row.keys item=v key=k}
{$k}: {$v}<br />
{/foreach}
		</td>
	</tr>
{/foreach}
	</table></td>
</tr>
{/foreach}
{/foreach}
</table>

</td>
</tr>
{/if}

</table>
</form>

<br /><br /><br />

{*** Force cache generation ***}

<a name="gencache" />

{include file="main/subheader.tpl" title=$lng.lbl_force_cache_generation}

<form action="tools.php" method="post" name="formmode_clear_cache">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="force_cache_generation_small">
	<td>{$lng.txt_force_cache_generation_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('force_cache_generation_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="force_cache_generation_small_note" style="display: none;">
	<td>{$lng.txt_force_cache_generation_note}</td>
</tr>
<tr>
	<td class="SubmitBox"><input type="submit" name="mode_clear_cache" value="{$lng.lbl_force_cache_generation|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

<br /><br /><br />

{*** CLEARING SECTION ***}

<a name="clearstat" />

{include file="main/subheader.tpl" title=$lng.lbl_statistics_clearing}

<form action="tools.php" method="post" name="processform" onsubmit="javascript: return checkMarks(this, new RegExp('[a-z]+_stat', 'gi'));">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="clearing_section_small">
	<td>{$lng.txt_clearing_section_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('clearing_section_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="clearing_section_small_note" style="display: none;">
	<td>{$lng.txt_clearing_section_note}</td>
</tr>

<tr>
<td>
<table cellpadding="2" cellspacing="0">

<tr>
	<td><input type="checkbox" id="track_stat" name="track_stat" value="Y" /></td>
	<td><label for="track_stat">{$lng.lbl_clear_all_tracking_statistics}</label></td>
</tr>
<tr>
	<td><input type="checkbox" id="shop_stat" name="shop_stat" value="Y" /></td>
	<td><label for="shop_stat">{$lng.lbl_clear_all_store_statistics}</label></td>
</tr>
<tr>
	<td><input type="checkbox" id="referer_stat" name="referer_stat" value="Y" /></td>
	<td><label for="referer_stat">{$lng.lbl_clear_all_referrals_statistics}</label></td>
</tr>
<tr>
	<td><input type="checkbox" id="adaptive_stat" name="adaptive_stat" value="Y" /></td>
	<td><label for="adaptive_stat">{$lng.lbl_clear_all_visitors_enviroment_statistics}</label></td>
</tr>
<tr>
	<td><input type="checkbox" id="search_stat" name="search_stat" value="Y" /></td>
	<td><label for="search_stat">{$lng.lbl_clear_all_search_statistics}</label></td>
</tr>
<tr>
	<td><input type="checkbox" id="bench_stat" name="bench_stat" value="Y" /></td>
	<td><label for="bench_stat">{$lng.lbl_clear_all_bench_statistics}</label></td>
</tr>

<tr>
	<td colspan="2">
	<table cellspacing="1" cellpadding="2">
	<tr>
		<td>{$lng.lbl_remove_stats_date_note}</td>
		<td>
		<select name="rsd_date" onchange="javascript: changeRSD(this);">
			<option value="">{$lng.lbl_remove_stats_date_none}</option>
			<option value="s">{$lng.lbl_remove_stats_date_select}</option>
		</select>
		</td>
	</tr>
	<tr id="tr_select_date" style="display: none;">
		<td>&nbsp;</td>
		<td>{html_select_date prefix="RSD_" start_year=$rsd_start_year}</td>
	</tr>
	</table>
	</td>
</tr>

</table>
</td>
</tr>

<tr>
	<td><input type="submit" name="mode_clear" value="{$lng.lbl_apply|strip_tags:false|escape}" /><br /><br />
	{$lng.txt_clearing_statistics_note}
	</td>
</tr>

</table>
</form>

<br /><br /><br />

{*** CLEARING PRECOMPILED TEMPLATES SECTION ***}

<a name="cleartmp" />

{include file="main/subheader.tpl" title=$lng.lbl_clear_templates_cache}

<table cellpadding="2" cellspacing="0" width="100%">

<tr>
	<td>
	<input type="button" value="{$lng.lbl_clear|strip_tags:false|escape}" onclick="javascript: self.location='tools.php?mode=templates'" /><br /><br />
{$lng.txt_clear_templates_cache_text|substitute:"dir":$templates_cache.dir:"files":$templates_cache.files:"size":$templates_cache.size}
	</td>
</tr>

</table>

<br /><br /><br />

{*** Regenerating blowfish key ***}

<a name="regenbk" />

{include file="main/subheader.tpl" title=$lng.lbl_regenerating_blowfish_key}

<form action="tools.php" method="post" name="formmode_regen_bk" onsubmit="javascript: return confirm('{$lng.txt_regen_blowfish_key_confirm|replace:"\n":""|replace:"'":"\'"}');">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="regen_bk_small">
	<td colspan="2">{$lng.txt_regen_blowfish_key_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('regen_bk_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="regen_bk_small_note" style="display: none;">
	<td colspan="2">{$lng.txt_regen_blowfish_key_note}</td>
</tr>
{if $config_non_writable}
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" />&nbsp;{$lng.txt_regen_blowfish_key_alert}</td>
</tr>
{/if}
<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" name="regenerate_blowfish" value="{$lng.lbl_regenerate|strip_tags:false|escape}" />
<br />
<br />
{$lng.txt_regen_blowfish_key_warning}
	</td>
</tr>

</table>
</form>

<br /><br /><br />

{*** Remove test/demo data ***}

<a name="cleardb" />

{include file="main/subheader.tpl" title=$lng.lbl_remove_test_data}

<form action="tools.php" method="post" name="formmode_clear_db" onsubmit="javascript: return check_cc_assurance();">
<table cellpadding="2" cellspacing="0" width="100%">

<tr id="clear_db_small">
	<td colspan="2">{$lng.txt_remove_test_data_small_note}&nbsp;<a href="javascript: void(0);" onclick="javascript: clickMore('clear_db_small');">{$lng.lbl_more}</a></td>
</tr>
<tr id="clear_db_small_note" style="display: none;">
	<td colspan="2">{$lng.txt_remove_test_data_note}</td>
</tr>
<tr>
	<td><input type="checkbox" id="assurance" name="assurance" value="Y" /></td>
	<td><label for="assurance">{$lng.lbl_remove_test_data_cb_note}</label></td>
</tr>
<tr>
	<td colspan="2" class="SubmitBox"><input type="submit" name="mode_clear_db" value="{$lng.lbl_remove_test_data|strip_tags:false|escape}" /></td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_tools content=$smarty.capture.dialog extra='width="100%"'}
