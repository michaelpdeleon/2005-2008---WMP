{* $Id: interneka_tags.tpl,v 1.4 2005/11/18 12:01:10 max Exp $ *}
{if $active_modules.Interneka ne ""}
{if $config.Interneka.interneka_per_lead eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.Interneka.interneka_id}&amp;Payment=yes&amp;OrderID={$orders[oi].order.orderid}" width="1" height="1" alt="" />
<!--- end of the link --> 
{/if}
{if $config.Interneka.interneka_per_sale eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.Interneka.interneka_id}&amp;TotalCost={$orders[oi].order.subtotal}&amp;OrderID={$orders[oi].order.orderid}" width="1" height="1" alt="" />
<!--- end of the link -->
{/if}
{/if}
