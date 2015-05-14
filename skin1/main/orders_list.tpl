{* $Id: orders_list.tpl,v 1.18.2.5 2006/07/21 07:15:12 max Exp $ *}

{assign var="total" value=0.00}
{assign var="total_paid" value=0.00}

{if $orders ne ""}

{capture name=dialog}

<div align="right">{include file="buttons/button.tpl" button_title=$lng.lbl_search_again href="orders.php"}</div>

{include file="customer/main/navigation.tpl"}

{include file="main/check_all_row.tpl" form="processorderform" prefix="orderids"}

<form action="process_order.php" method="post" name="processorderform">
<input type="hidden" name="mode" value="" />

<table cellpadding="2" cellspacing="1" width="100%">

{assign var="colspan" value=6}

<tr class="TableHead">
	<td width="5">&nbsp;</td>
	<td width="5%" nowrap="nowrap">{if $search_prefilled.sort_field eq "orderid"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=orderid">#</a></td>
	<td nowrap="nowrap">{if $search_prefilled.sort_field eq "status"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=status">{$lng.lbl_status}</a></td>
	<td width="30%" nowrap="nowrap">{if $search_prefilled.sort_field eq "customer"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=customer">{$lng.lbl_customer}</a></td>
{if $usertype eq "A" and $single_mode eq ""}
{assign var="colspan" value=7}
	<td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "provider"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=provider">{$lng.lbl_provider}</a></td>
{/if}
	<td width="20%" nowrap="nowrap">{if $search_prefilled.sort_field eq "date"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=date">{$lng.lbl_date}</a></td>
	<td width="20%" align="right" nowrap="nowrap">{if $search_prefilled.sort_field eq "total"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="orders.php?mode=search&amp;sort=total">{$lng.lbl_total}</a></td>
</tr>

{section name=oid loop=$orders}

{math equation="x + ordertotal" x=$total ordertotal=$orders[oid].total assign="total"}
{if $orders[oid].status eq "P" or $orders[oid].status eq "C"}
{math equation="x + ordertotal" x=$total_paid ordertotal=$orders[oid].total assign="total_paid"}
{/if}

<tr{cycle values=", class='TableSubHead'"}>
	<td width="5"><input type="checkbox" name="orderids[{$orders[oid].orderid}]" /></td>
	<td><a href="order.php?orderid={$orders[oid].orderid}">#{$orders[oid].orderid}</a></td>
	<td nowrap="nowrap">
{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode ne "")}
<input type="hidden" name="order_status_old[{$orders[oid].orderid}]" value="{$orders[oid].status}" />
{include file="main/order_status.tpl" status=$orders[oid].status mode="select" name="order_status[`$orders[oid].orderid`]"}
{else}
<a href="order.php?orderid={$orders[oid].orderid}"><b>{include file="main/order_status.tpl" status=$orders[oid].status mode="static"}</b></a>
{/if}
{if $active_modules.Stop_List ne '' && $orders[oid].blocked  eq 'Y'}
<img src="{$ImagesDir}/no_ip.gif" style="vertical-align: middle;" alt="" />
{/if}
	</td>
	<td>{$orders[oid].firstname} {$orders[oid].lastname} ({$orders[oid].login})</td>
{if $usertype eq "A" and $single_mode eq ""}
	<td>{$orders[oid].provider}</td>
{/if}
	<td nowrap="nowrap"><a href="order.php?orderid={$orders[oid].orderid}">{$orders[oid].date|date_format:$config.Appearance.datetime_format}</a></td>
	<td nowrap="nowrap" align="right">
	<a href="order.php?orderid={$orders[oid].orderid}">{include file="currency.tpl" value=$orders[oid].total}</a>
	</td>
</tr>

{/section}

<tr>
	<td colspan="{$colspan}"><img src="{$ImagesDir}/spacer.gif" width="100%" height="1" alt="" /></td>
</tr>

<tr>
	<td colspan="{$colspan}" align="right">{$lng.lbl_gross_total}: <b>{include file="currency.tpl" value=$total}</b></td>
</tr>

<tr>
	<td colspan="{$colspan}" align="right">{$lng.lbl_total_paid}: <b>{include file="currency.tpl" value=$total_paid}</b></td>
</tr>

<tr>
	<td colspan="{$colspan}"><br />

{include file="customer/main/navigation.tpl"}

{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode)}
<input type="button" value="{$lng.lbl_update_status|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
&nbsp;&nbsp;&nbsp;&nbsp;
<br /><br />
{/if}

<input type="button" value="{$lng.lbl_invoices_for_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.target='invoices'; submitForm(this, 'invoice'); document.processorderform.target=''; {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
{if $usertype ne "C"}
<input type="button" value="{$lng.lbl_labels_for_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.target='labels'; submitForm(this, 'label'); document.processorderform.target=''; {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $usertype eq "A" or ($usertype eq "P" and $active_modules.Simple_Mode)}
<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) if (confirm('{$lng.txt_delete_selected_orders_warning|strip_tags}')) submitForm(this, 'delete');" />
&nbsp;&nbsp;&nbsp;&nbsp;
{/if}
{if $active_modules.Shipping_Label_Generator ne '' && ($usertype eq 'A' || ($usertype eq 'P' && $active_modules.Simple_Mode))}
<input type="button" value="{$lng.lbl_get_shipping_labels|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) {ldelim} document.processorderform.action='generator.php'; submitForm(this, ''); {rdelim}" />
&nbsp;&nbsp;&nbsp;&nbsp;
{/if}

{if $usertype ne "C" && $current_membership_flag ne 'FS'}
<br />
<br />
<br />
{include file="main/subheader.tpl" title=$lng.lbl_export_orders}
{$lng.txt_export_all_found_orders_text}
<br /><br />
{$lng.lbl_export_file_format}:<br />
<select id="export_fmt" name="export_fmt">
	<option value="std">{$lng.lbl_standart}</option>
	<option value="csv_tab">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_tab_delimiter}</option>
	<option value="csv_semi">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_semicolon_delimiter}</option>
	<option value="csv_comma">{$lng.lbl_40x_compatible}: CSV {$lng.lbl_with_comma_delimiter}</option>
{if $active_modules.QuickBooks eq "Y"}
{include file="modules/QuickBooks/orders.tpl"}
{/if}
</select>
<br />
<br />
<input type="button" value="{$lng.lbl_export|strip_tags:false|escape}" onclick="javascript: if (checkMarks(this.form, new RegExp('orderids\[[0-9]+\]', 'gi'))) submitForm(this, 'export');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="{$lng.lbl_export_all_found|strip_tags:false|escape}" onclick="javascript: self.location='orders.php?mode=search&amp;export=export_found&amp;export_fmt='+document.getElementById('export_fmt').value;" />
{/if}
</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_search_results content=$smarty.capture.dialog extra='width="100%"'}
{/if}
