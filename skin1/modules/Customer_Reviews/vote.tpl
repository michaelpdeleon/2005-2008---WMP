{* $Id: vote.tpl,v 1.11 2005/12/15 12:59:13 max Exp $ *}
<tr>
	<td colspan="2">{include file="main/subheader.tpl" title=$lng.lbl_customers_rating}</td>
</tr>
{if $vote_max_cows ne ""}
<tr>
	<td><br />{$lng.lbl_customers_rating}</td>
	<td><br />
{section name=full_cows loop=$vote_max_cows}
<img src="{$ImagesDir}/star_4.gif" class="StarImg" alt="" />
{/section}
{if $vote_little_cow ne "0"}
<img src="{$ImagesDir}/star_{$vote_little_cow}.gif" class="StarImg" alt="" />&nbsp;
{/if}
{section name=free_cows loop=$vote_free_cows}
<img src="{$ImagesDir}/star_0.gif" class="StarImg" alt="" />
{/section}
	</td>
</tr>
{/if}
<tr>
	<td><br />{$lng.lbl_customer_voting}</td>
	<td>

<form method="get" action="product.php" name="voteform">
<input type="hidden" name="mode" value="vote" />
<input type="hidden" name="productid" value="{$product.productid}" />
<br />
<table cellspacing="1" cellpadding="2">
<tr>
	<td>
	<select name="vote">
		<option value="" selected="selected">{$lng.lbl_select_rating}</option>
		<option value="5">{$lng.lbl_excellent}</option>
		<option value="4">{$lng.lbl_very_good}</option>
		<option value="3">{$lng.lbl_good}</option>
		<option value="2">{$lng.lbl_fair}</option>
		<option value="1">{$lng.lbl_poor}</option>
	</select>
	</td>
	<td>{include file="buttons/button.tpl" button_title=$lng.lbl_rate_it style="button" href="javascript: document.voteform.submit();" type="input"}</td>
</tr>
</table>
</form>

	</td>
</tr>
