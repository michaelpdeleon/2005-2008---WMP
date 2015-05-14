{* $Id: gc_admin.tpl,v 1.30.2.3 2006/07/11 08:39:30 svowl Exp $ *}
{if $smarty.get.mode eq "add_gc" or ($smarty.get.mode eq "modify_gc" and $gc_readonly ne "Y")}
{include file="modules/Gift_Certificates/giftcert.tpl"}
{elseif $smarty.get.mode eq "modify_gc"}
{include file="modules/Gift_Certificates/giftcert_static.tpl"}
{else}

{include file="page_title.tpl" title=$lng.lbl_gift_certificates}

{$lng.txt_gc_admin_top_text}

<br /><br />

{capture name=dialog}

<script type="text/javascript" language="JavaScript 1.2">
<!--

{literal}
function gc_change_all(flag) {
	checkAll(flag, document.gcform, 'gcids');
	gc_selected();
}
{/literal}

var gc_print_warning = "{$lng.lbl_gc_print_warning|escape:javascript}";
var lbl_no_giftcert_selected = "{$lng.lbl_no_giftcert_selected|escape:javascript}";
var gc_templates = [];
{foreach from=$giftcerts item=v}{if $v.send_via ne "E"}gc_templates['{$v.gcid}'] = '{$v.tpl_file}';
{/if}
{/foreach}

-->
</script>
{include file="main/include_js.tpl" src="change_all_checkboxes.js"}

<div style="line-height:170%"><a href="javascript:gc_change_all(true);">{$lng.lbl_check_all}</a> / <a href="javascript:gc_change_all(false);">{$lng.lbl_uncheck_all}</a></div>

<form name="gcform" action="giftcerts.php" method="post">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="tpl_file" value="" />

{if $giftcerts ne ""}

{literal}
<script type="text/javascript" language="JavaScript 1.2">
<!--
function gc2tpl() {
	var r = [];
	for (var gcid in gc_templates) {
		if (!document.gcform.elements['gcids['+gcid+']'].checked)
			continue;

		var tpl = gc_templates[gcid];
		if (!r[tpl])
			r[tpl] = [];
		r[tpl].push(gcid);
	}
	return r;
}

function gc_selected() {
	var gc_tpl = gc2tpl();
	var tpl_count = 0;

	for (var i in gc_tpl) {
		tpl_count++;
	}

	if (!document.getElementById('print_warn_box'))
		return;

	if (tpl_count > 1) {
		document.getElementById('print_warn_box').style.display = '';
		document.getElementById('print_warn_msg').innerHTML = substitute(gc_print_warning, "num", tpl_count);
	} else {
		document.getElementById('print_warn_box').style.display = 'none';
	}
}

function gc_do_print() {
	var gc_tpl = gc2tpl();
	var found = false;
	
	for (var tpl in gc_tpl) {
		found = true;
		var url_arr = [];
		for (var i in gc_tpl[tpl]) {
			url_arr.push('gcids['+gc_tpl[tpl][i]+']=on');
		}
		window.open('giftcerts.php?mode=print&tpl_file=' + tpl + '&' + url_arr.join('&'));
	}

	if (!found)
		alert(lbl_no_giftcert_selected);
}
-->
</script>
{/literal}
{/if}

<table cellpadding="3" cellspacing="1" width="100%">

<tr class="TableHead">
	<td width="5">&nbsp;</td>
	<td width="10%">{$lng.lbl_order}</td>
	<td width="25%">{$lng.lbl_giftcert_ID}</td>
	<td width="10%">{$lng.lbl_gc_type}</td>
	<td width="10%">{$lng.lbl_status}</td>
	<td width="25%" align="center">{$lng.lbl_rem_amount}</td>
	<td width="20%" align="center">{$lng.lbl_added}</td>
</tr>

{if $giftcerts ne ""}

{section name=gc_num loop=$giftcerts}
<tr{cycle values=", class='TableSubHead'"}>
	<td width="5"><input type="checkbox"{if $giftcerts[gc_num].send_via eq "E"} disabled="disabled"{else} name="gcids[{$giftcerts[gc_num].gcid}]" onclick="javascript:gc_selected();"{/if} /></td>
{if $giftcerts[gc_num].orderid}
	<td><a href="order.php?orderid={$giftcerts[gc_num].orderid}">#{$giftcerts[gc_num].orderid}</a></td>
{elseif $giftcerts[gc_num].return ne ''}
	<td>{$lng.lbl_return} <a href="returns.php?mode=modify&amp;returnid={$giftcerts[gc_num].return.returnid}">RMA#{$giftcerts[gc_num].return.returnid}</a></td>
{else}
	<td>{$lng.txt_not_available}</td>
{/if}
	<td><a href="giftcerts.php?mode=modify_gc&amp;gcid={$giftcerts[gc_num].gcid}">{$giftcerts[gc_num].gcid}</a></td>
	<td>
{if $giftcerts[gc_num].send_via eq "E"}{$lng.lbl_email}{else}{$lng.lbl_mail}{/if}
	</td>
	<td>
	<select name="status-{$giftcerts[gc_num].gcid}">
		<option value="P"{if $giftcerts[gc_num].status eq "P"} selected="selected"{/if}>{$lng.lbl_pending}</option>
		<option value="A"{if $giftcerts[gc_num].status eq "A"} selected="selected"{/if}>{$lng.lbl_active}</option>
		<option value="B"{if $giftcerts[gc_num].status eq "B"} selected="selected"{/if}>{$lng.lbl_blocked}</option>
		<option value="D"{if $giftcerts[gc_num].status eq "D"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
		<option value="E"{if $giftcerts[gc_num].status eq "E"} selected="selected"{/if}>{$lng.lbl_expired}</option>
		<option value="U"{if $giftcerts[gc_num].status eq "U"} selected="selected"{/if}>{$lng.lbl_used}</option>
	</select>
	</td>
	<td align="center" nowrap="nowrap">
{include file="currency.tpl" value=$giftcerts[gc_num].debit}/{include file="currency.tpl" value=$giftcerts[gc_num].amount}  
	</td>
	<td align="center" nowrap="nowrap">
{if $giftcerts[gc_num].orderid}<a href="user_modify.php?user={$giftcerts[gc_num].login|escape:"url"}&amp;usertype={$giftcerts[gc_num].usertype}">{$giftcerts[gc_num].purchaser|truncate:"30":"...":true}</a>{else}{$giftcerts[gc_num].purchaser|truncate:"30":"...":true}{/if}<br />
<font class="SmallText">{$giftcerts[gc_num].add_date|date_format:$config.Appearance.datetime_format}</font>
	</td>
{if $giftcerts[gc_num].orderid eq 0}
	<td><input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: self.location='giftcerts.php?mode=delete&amp;gcid={$giftcerts[gc_num].gcid}';" /></td>
{/if}
</tr>
{/section}

<tr>
	<td colspan="7" class="SubmitBox">
<input type="submit" value="{$lng.lbl_update_all|strip_tags:false|escape}" />
<br /><br />
{$lng.txt_gc_update_warning}
	</td>
</tr>

<tr>
	<td colspan="7"><br />
<div style="display: none" id="print_warn_box">
<table cellspacing="0" cellpadding="2">
<tr>
	<td><img src="{$ImagesDir}/log_type_Warning.gif" alt="" /></td>
	<td id="print_warn_msg"></td>
</tr>
</table>
<br />
</div>
<input type="button" value="{$lng.lbl_print_selected|strip_tags:false|escape}" onclick="javascript: gc_do_print();" />
	</td>
</tr>

{else}

<tr>
	<td colspan="7" align="center">{$lng.txt_no_gc}</td>
</tr>

{/if}

<tr>
	<td colspan="7" class="SubmitBox">
	<input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='giftcerts.php?mode=add_gc'" />
	</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_gift_certificates content=$smarty.capture.dialog extra='width="100%"'}
{/if}
