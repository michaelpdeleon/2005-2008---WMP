{* $Id: payment_chk.tpl,v 1.9 2005/12/26 07:47:50 mclap Exp $ *}
{if $payment_cc_data.disable_ccinfo ne "Y"}
<table cellspacing="0" cellpadding="2">
{if $payment_cc_data.c_template ne ""}
{include file=$payment_cc_data.c_template}
{else}
{include file="main/register_chinfo.tpl"}
{/if}
</table>
{else}
{$lng.disable_chinfo_msg}<br />
{/if}
