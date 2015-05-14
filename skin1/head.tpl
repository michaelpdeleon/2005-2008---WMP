{* $Id: head.tpl,v 1.58 2006/03/17 08:50:44 svowl Exp $ *}
<!--
{*php*}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{*/php*}
-->
<!-- Start of edit by Michael de Leon 09.14.06-->
<table cellpadding="0" cellspacing="0" width="100%" align="center">
<tr> 
	<td class="HeadLogo" align="center"><a href="javascript:"><img src="{$ImagesDir}/wwmp_header11.17.06.jpg" width="980" height="100" alt="" /></a></td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" align="center">
{if $main ne "fast_lane_checkout"}
<tr> 
	<td colspan="3" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<!-- End of edit by Michael de Leon 09.14.06-->
<tr> 
	<td class="HeadLine" height="22" width="20%">
{if $usertype eq "C"}
{ include file="customer/search.tpl" }
{/if}
	</td>
<!-- Deleted by Michael de Leon 10.25.06
	<td class="HeadLine" align="right">
{* if ($usertype eq "C" || $usertype eq "B") && $all_languages_cnt gt 1 *}
<form action="home.php" method="get" name="sl_form">
<input type="hidden" name="redirect" value="{* $smarty.server.PHP_SELF *}?{* $smarty.server.QUERY_STRING|amp *}" />
<table cellpadding="0" cellspacing="0">
<tr>
	<td style="padding-right: 5px;"><b>{* $lng.lbl_select_language *}:</b></td>
	<td><select name="sl" onchange="javascript: this.form.submit();">
{* section name=ai loop=$all_languages *}
<option value="{* $all_languages[ai].code *}"{* if $store_language eq $all_languages[ai].code *} selected="selected"{* /if *}>{* $all_languages[ai].language *}</option>
{* /section *}
	</select></td>
</tr>
</table>
</form>
{* else *}
&nbsp;
{* /if *}
	</td>
-->
	<!-- Begin addition by Michael de Leon 09.14.06-->
	<td class="HeadLine" align="center">
{section name=sb loop=$speed_bar}
	{if $speed_bar[sb].active eq "Y"}
		{if $smarty.section.sb.last}
			{include file="customer/tab.tpl" tab_title="<A href=\"`$speed_bar[sb].link`\" class=\"speedmenu\">`$speed_bar[sb].title`</A>"}
		{else}
			{include file="customer/tab.tpl" tab_title="<A href=\"`$speed_bar[sb].link`\" class=\"speedmenu\">`$speed_bar[sb].title`</A>"} <font class="speedmenu_bar">|</font>
		{/if}
	{/if}
{/section}</td>
	<td class="HeadLine" width="20%">&nbsp;</td>
	<!-- End addition by Michael de Leon 09.14.06-->
</tr>
{else}
{* Fast Lane Checkout page *}
<!-- Start addition by Michael de Leon 11.09.06 -->
<tr> 
	<td colspan="5" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr> 
	<td class="HeadLine" height="22" width="20%">
{if $usertype eq "C"}
{ include file="customer/search.tpl" }
{/if}
	</td>
	<td class="HeadLine" align="center">
{section name=sb loop=$speed_bar}
	{if $speed_bar[sb].active eq "Y"}
		{if $smarty.section.sb.last}
			{include file="customer/tab.tpl" tab_title="<A href=\"`$speed_bar[sb].link`\" class=\"speedmenu\">`$speed_bar[sb].title`</A>"}
		{else}
			{include file="customer/tab.tpl" tab_title="<A href=\"`$speed_bar[sb].link`\" class=\"speedmenu\">`$speed_bar[sb].title`</A>"} <font class="speedmenu_bar">|</font>
		{/if}
	{/if}
{/section}</td>
<!-- End addition by Michael de Leon 11.09.06 -->
	<td colspan="3" class="HeadLine" width="20%">
	<form action="{$xcart_web_dir}/include/login.php" method="post" name="toploginform">
	<input type="hidden" name="mode" value="logout" />
	<input type="hidden" name="redirect" value="{$redirect}" />
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="FLCAuthPreBox">
{if $active_modules.SnS_connector and $sns_collector_path_url ne '' && $config.SnS_connector.sns_display_button eq 'Y'}
	<img src="{$ImagesDir}/rarrow.gif" alt="" valign="middle" /><b>{include file="modules/SnS_connector/button.tpl" text_link="Y"}</b>
{else}
	<img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" />
{/if}</td>
{if $login ne ""}
<!-- Start addition by Michael de Leon 11.02.06 -->
			<td class="wwmp_loginnoticehead" nowrap="nowrap">Hello {$login}!</td>
			<td><a href="javascript: document.toploginform.submit();"><img class="wwmp_logoutbtn_head" src="{$ImagesDir}/wwmp_logoutbtn11.01.06.jpg" border="0"></a></td>
<!-- End addition by Michael de Leon 11.02.06 -->
{/if}
		</tr>
		</table>
	</form>
	</td>
</tr>
{/if}
<tr> 
	<td colspan="5" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{******** Remove this line to display how much products there are online ****
<tr>
{insert name="productsonline" assign="_productsonline"}
	<td colspan="2" class="NumberOfArticles" align="right">
{if $config.Appearance.show_in_stock eq "Y"}
{insert name="itemsonline" assign="_itemsonline"}
{$lng.lbl_products_and_items_online|substitute:"X":$_productsonline:"Y":$_itemsonline}
{else}
{$lng.lbl_products_online|substitute:"X":$_productsonline}
{/if}
&nbsp;
	</td>
</tr>
**** Remove this line to display how much products there are online ********}
{if $main ne "fast_lane_checkout"}
<tr>
<!-- Start edit by Michael de Leon 12.06.06 -->
	<td colspan="3" valign="middle" height="30" width="100%" align="center">
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
{if (($main eq 'catalog' && $cat ne '') || $main eq 'product' || ($main eq 'comparison' && $mode eq 'compare_table') || ($main eq 'choosing' && $smarty.get.mode eq 'choose')) && $config.Appearance.enabled_printable_version eq 'Y'}
	<td align="right">{include file="printable.tpl"}</td>
{else}
	<td align="left">&nbsp;</td>
{/if}
</tr>
</table>
	</td>
<!-- End edit by Michael de Leon 12.06.06 -->
</tr>
{else}
{* Fast Lane Checkout page *}
<tr>
	<td colspan="3" class="FLCTopPad"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>
{/if}
</table>
