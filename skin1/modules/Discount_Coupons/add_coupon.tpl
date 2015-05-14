{* $Id: add_coupon.tpl,v 1.11.2.2 2006/07/11 08:39:29 svowl Exp $ *}
<!-- Deleted by Michael de Leon 11.06.06
{* $lng.txt_add_coupon_header *}
<p />
-->
<!-- Deleted by Michael de Leon 11.07.06
{* capture name=dialog *}
-->
<div align="center">
<form action="cart.php" name="couponform">
<table class="wwmp_coupon" width="400" cellspacing="0" cellpadding="10">
  <tr>
    <td>
	<table border="0" cellspacing="0" cellpadding="0" width="400">
	<tr>
		<td class="wwmp_coupondesc" align="left"><font class="wwmp_coupondesc">SAVE</font><br /><br />
		If you have a valid discount coupon code, please enter it below to redeem it.  The discount amount will be deducted from your total order.<br /><br />
		If you have any questions or comments, <a class="wwmp_vertmenulink" href="http://www.wwmponline.com/cart/help.php?section=contactus&mode=update" target="_self">contact us</a>.</td>
		<td valign="top"><img src="{$ImagesDir}/wwmp_logoyellowbg11.10.06.jpg" border="0"></td>
	</tr>
	</table>
	<br />
	<table cellpadding="4" cellspacing="0" border="0" width="300" align="left">
	<tr>
		<td align="left" class="wwmp_couponlabel">{$lng.lbl_coupon_code}</td>
		<td align="left"><input type="text" size="32" name="coupon" /></td>
	</tr>
	<tr>
		<td align="left">&nbsp;</td>
		<td align="left" colspan="2">{if $js_enabled}
		<a href="javascript: document.couponform.submit();"><img src="{$ImagesDir}/wwmp_redeembtn11.07.06.jpg" border="0"></a>
		<!-- Deleted by Michael de Leon 11.08.06
		{include file="buttons/submit.tpl" href="javascript: document.couponform.submit();" js_to_href="Y"}
		-->
		{else}
		<input type="submit" value="{$lng.lbl_submit|strip_tags:false|escape}" />
		{/if}
		</td>
	</tr>
	</table></td>
  </tr>
</table>
<input type="hidden" name="mode" value="add_coupon" />
</form>
<!-- Deleted by Michael de Leon 11.07.06
{* /capture *}
{* include file="dialog.tpl" title=$lng.lbl_redeem_discount_coupon content=$smarty.capture.dialog extra='width="634"' *}
-->
<!-- Deleted by Michael de Leon 11.06.06
{* include file="dialog.tpl" title=$lng.lbl_redeem_discount_coupon content=$smarty.capture.dialog extra='width="100%"' *}
-->
</div>
