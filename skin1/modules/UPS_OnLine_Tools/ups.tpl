{* $Id: ups.tpl,v 1.5 2006/01/30 13:19:07 mclap Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_ups_online_tools}

{$lng.txt_ups_online_tools_top_text}

<br /><br />

{if $mode eq "rss"}
{include file="modules/UPS_OnLine_Tools/ups_rss.tpl"}
{elseif $ups_reg_step eq 0}
{include file="modules/UPS_OnLine_Tools/ups_main.tpl"}
{elseif $ups_reg_step eq 1}
{include file="modules/UPS_OnLine_Tools/ups_access_license_1.tpl"}
{elseif $ups_reg_step eq 2}
{include file="modules/UPS_OnLine_Tools/ups_access_license_2.tpl"}
{elseif $ups_reg_step eq 3}
{include file="modules/UPS_OnLine_Tools/ups_access_license_3.tpl"}
{elseif $ups_reg_step eq 4}
{include file="modules/UPS_OnLine_Tools/ups_access_license_4.tpl"}
{/if}
