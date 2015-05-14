{* $Id: ratings_edit.tpl,v 1.15.2.3 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_edit_ratings}

{$lng.txt_customer_ratings_edit_top_text}

<br /><br />

{capture name=dialog}

{include file="customer/main/navigation.tpl"}

{if $ratings ne ""}

{if $productid ne ""}
<b>{$lng.txt_ratings_for_product_N|substitute:"product":$product.product}</b>
{elseif $ip ne ""}
<b>{$lng.txt_ratings_for_ip_n|substitute:"IP":$ip}</b>
{else}
<b>{$lng.txt_all_ratings}</b>
{/if}
{if $productid ne "" or $ip ne ""}
&nbsp;<a href="ratings_edit.php?sortby={$sortby}&amp;sortorder={$sortorder}&amp;page={$smarty.get.page|escape:"html"}">({$lng.lbl_show_all_ratings})</a>
{/if}

<br />
<hr size="1" noshade="noshade" />

{include file="main/check_all_row.tpl" style="line-height: 170%;" form="ratingsform" prefix="to_delete"}

<form action="ratings_edit.php?page={$smarty.get.page|escape:"html"}" method="post" name="ratingsform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="productid" value="{$productid}" />
<input type="hidden" name="ip" value="{$ip}" />
<input type="hidden" name="sortby" value="{$sortby}" />
<input type="hidden" name="sortorder" value="{$sortorder}" />

<table width="100%">

<tr class="TableHead">
	<td>&nbsp;</td>
	<td width="10%"><a href="ratings_edit.php?sortby=productcode&amp;sortorder={if $sortby eq "productcode"}{$invsortorder}{else}{$sortorder}{/if}&amp;productid={$productid}&amp;ip={$ip}">{$lng.lbl_sku}</a></td>
	<td width="60%"><a href="ratings_edit.php?sortby=product&amp;sortorder={if $sortby eq "product"}{$invsortorder}{else}{$sortorder}{/if}&amp;productid={$productid}&amp;ip={$ip}">{$lng.lbl_product}</a></td>
	<td width="20%"><a href="ratings_edit.php?sortby=ip&amp;sortorder={if $sortby eq "ip"}{$invsortorder}{else}{$sortorder}{/if}&amp;productid={$productid}&amp;ip={$ip}">{$lng.lbl_remote_IP}{if $sortby eq "ip"}&nbsp;{/if}</a></td>
	<td width="10%"><a href="ratings_edit.php?sortby=vote&amp;sortorder={if $sortby eq "vote"}{$invsortorder}{else}{$sortorder}{/if}&amp;productid={$productid}&amp;ip={$ip}">{$lng.lbl_vote}</a></td>
</tr>

{section name=ri loop=$ratings}

<tr{cycle values=", class='TableSubHead'"}>
	<td align="center"><input type="checkbox" name="to_delete[{$ratings[ri].vote_id}]" value="Y" /></td>
	<td><b>{$ratings[ri].productcode}</b></td>
	<td><a href="ratings_edit.php?sortby={$sortby}&amp;sortorder={$sortorder}&amp;productid={$ratings[ri].productid}">{$ratings[ri].product}</a></td>
	<td><a href="ratings_edit.php?sortby={$sortby}&amp;sortorder={$sortorder}&amp;ip={$ratings[ri].remote_ip|escape:"url"}">{$ratings[ri].remote_ip}</a></td>
	<td>
	<select name="update_votes[{$ratings[ri].vote_id}]">
		<option value=""{if $ratings[ri].vote_value eq ""} selected="selected"{/if}>{$lng.lbl_undefined}</option>
		<option value="5"{if $ratings[ri].vote_value eq "5"} selected="selected"{/if}>{$lng.lbl_excellent}</option>
		<option value="4"{if $ratings[ri].vote_value eq "4"} selected="selected"{/if}>{$lng.lbl_very_good}</option>
		<option value="3"{if $ratings[ri].vote_value eq "3"} selected="selected"{/if}>{$lng.lbl_good}</option>
		<option value="2"{if $ratings[ri].vote_value eq "2"} selected="selected"{/if}>{$lng.lbl_fair}</option>
		<option value="1"{if $ratings[ri].vote_value eq "1"} selected="selected"{/if}>{$lng.lbl_poor}</option>
	</select>
	</td>
</tr>

{/section}

<tr>
	<td colspan="5" class="SubmitBox">
	<input type="submit" value="{$lng.lbl_update|strip_tags:false|escape}" />
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'delete');" />
	</td>
</tr>

</table>
</form>

{else}

<br />
<div align="center">{$lng.txt_no_ratings}</div>

{/if}

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_ratings extra='width="100%"'}
