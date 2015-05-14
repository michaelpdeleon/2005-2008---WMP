{* $Id: payment_dd.tpl,v 1.1 2006/01/06 14:51:46 mclap Exp $ *}
{if $payment_cc_data.disable_ccinfo ne "Y"}
<table cellspacing="0" cellpadding="2">
{if $payment_cc_data.c_template ne ""}
{include file=$payment_cc_data.c_template}
{else}
{include file="main/register_ddinfo.tpl"}
{/if}
</table>
{else}
{$lng.disable_chinfo_msg}<br />
{/if}
