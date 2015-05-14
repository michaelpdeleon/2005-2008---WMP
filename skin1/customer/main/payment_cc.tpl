{* $Id: payment_cc.tpl,v 1.11 2005/11/28 14:19:29 max Exp $ *}
{if $payment_cc_data.disable_ccinfo ne "Y"}
<table cellspacing="0" cellpadding="2">
{if $payment_cc_data.c_template ne ""}
{include file=$payment_cc_data.c_template}
{else}
{include file="main/register_ccinfo.tpl"}
{/if}
</table>
{else}
{$lng.disable_ccinfo_msg}<br />
{/if}
