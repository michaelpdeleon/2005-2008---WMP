{* $id: reviews.tpl,v 1.9 2005/05/16 12:06:36 svowl Exp $ *}
<tr>
	<td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_customer_reviews}</td>
</tr>
{if $reviews ne ""}
{foreach from=$reviews item=r}
<tr>
	<td colspan="2"><br /><b>{$lng.lbl_author}: {$r.email|default:$lng.lbl_unknown}</b><br />{$r.message|replace:"\n":"<br />"}<br /><br /></td>
</tr>
{/foreach}
{else}
<tr>
	<td colspan="2" align="center"><br/>{$lng.txt_no_customer_reviews}</td>
</tr>
{/if}
{if ($config.Customer_Reviews.writing_reviews eq "A") or ($login ne "" and $config.Customer_Reviews.writing_reviews eq "R")}
<tr>
	<td colspan="2"><br /><b><font class="ProductDetailsTitle">{$lng.lbl_add_your_review}</font></b></td>
</tr>
<tr>
	<td colspan="2">

<form method="post" action="product.php?mode=review&amp;productid={$product.productid}" name="reviewform">
<input type="hidden" name="productid" value='{$product.productid}' />
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="30%"><br />{$lng.lbl_your_name}:</td><td><br /><input type="text" size="24" name="review_author"{if $login ne ""} value="{$customer_info.firstname|escape} {$customer_info.lastname|escape} ({$customer_info.email|escape})"{/if} /></td>
</tr>
<tr>
	<td width="30%">{$lng.lbl_your_message}:</td><td><textarea cols="40" rows="4" name="review_message"></textarea></td></tr>
<tr>
	<td colspan="2">{include file="buttons/button.tpl" button_title=$lng.lbl_add_review style="button" href="javascript: document.reviewform.submit();" type="input"}</td>
</tr>
</table>
</form>

	</td>
</tr>
{/if}

