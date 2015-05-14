{* $Id: advanced_search.tpl,v 1.10 2005/11/17 06:55:37 max Exp $ *}
{capture name=adv_search}
<table border="0">
<form action="search.php" name="productsearchbyprice_form">
<tr>
<td>{$lng.lbl_product_title}</td>
<td> 
<input type="text" name="substring" size="30" value="{$smarty.get.substring|escape:"html"}" />
</td>
</tr>
<tr><td>{$lng.lbl_price}, {$config.General.currency_symbol}</td>
<td><input type="text" name="price_search_1" size="6" value="{$smarty.get.price_search_1|escape}" /> - <input type="text" name="price_search_2" size="6" value="{$smarty.get.price_search_2|escape}" /></td></tr>
<tr><td>{$lng.lbl_category}</td>
<td>
<select name="in_category">
<option value="">All</option>
{foreach from=$categories item=c}
<option value="{$c.categoryid}" {if $smarty.get.in_category eq $c.categoryid or $cat eq $c.categoryid}selected{/if}>{$c.category|escape}</option>
{/foreach}
</select>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td>&nbsp;</td>
<td>{include file="buttons/search.tpl" style="button" type="input" href="javascript:document.productsearchbyprice_form.submit()"}</td></tr>
</form>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_advanced_search content=$smarty.capture.adv_search extra='width="100%"'}
