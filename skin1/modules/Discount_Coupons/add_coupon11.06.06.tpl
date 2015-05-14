{* $Id: add_coupon.tpl,v 1.11.2.2 2006/07/11 08:39:29 svowl Exp $ *}
{$lng.txt_add_coupon_header}
<p />
{capture name=dialog}
<form action="cart.php" name="couponform">
<table>
<tr>
	<td class="FormButton">{$lng.lbl_coupon_code}</td>
	<td><input type="text" size="32" name="coupon" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
{if $js_enabled}
{include file="buttons/submit.tpl" href="javascript: document.couponform.submit();" js_to_href="Y"}
{else}
<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
{/if}
	</td>
</tr>
</table>
<input type="hidden" name="mode" value="add_coupon" />
</form>
{/capture}
{ include file="dialog.tpl" title=$lng.lbl_redeem_discount_coupon content=$smarty.capture.dialog extra='width="100%"' }
