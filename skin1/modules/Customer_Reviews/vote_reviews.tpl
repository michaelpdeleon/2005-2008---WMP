{* $Id: vote_reviews.tpl,v 1.7 2005/12/02 08:20:54 svowl Exp $ *}
{if $config.Customer_Reviews.customer_voting eq "Y" || ($config.Customer_Reviews.customer_reviews eq "Y" && ($reviews ne "" || $config.Customer_Reviews.writing_reviews eq "A" || ($login ne "" && $config.Customer_Reviews.writing_reviews eq "R")))}
{capture name=dialog}
<table width="100%" cellpadding="0" cellspacing="0">
{if $config.Customer_Reviews.customer_voting eq "Y"}
{include file="modules/Customer_Reviews/vote.tpl"}
<tr><td colspan="2"><br /><br /></td></tr>
{/if}
{if $config.Customer_Reviews.customer_reviews eq "Y"}
{include file="modules/Customer_Reviews/reviews.tpl"}
{/if}
</table>
{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_customers_feedback extra='width="100%"'}
{/if}
