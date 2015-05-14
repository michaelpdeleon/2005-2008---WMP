{* $Id: head.tpl,v 1.11 2006/03/21 07:45:09 svowl Exp $ *}
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td class="HeadLogo"><a href="{$http_location}"><img src="{$ImagesDir}/xlogo.gif" width="244" height="67" alt="" /></a></td>
	<td class="HeadRightBox">
{if $usertype eq "C"}
{include file="customer/top_menu.tpl"}
{/if}
	</td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%">
<tr> 
	<td colspan="2" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
</tr>
{if $main ne "fast_lane_checkout"}
<tr> 
	<td class="HeadLine" height="22">&nbsp;</td>
	<td class="HeadLine" align="right">
{if ($usertype eq "C" || $usertype eq "B") && $all_languages_cnt gt 1}
<form action="home.php" method="get" name="sl_form">
<input type="hidden" name="redirect" value="{$smarty.server.PHP_SELF}?{$smarty.server.QUERY_STRING|amp}" />
<table cellpadding="0" cellspacing="0">
<tr>
	<td style="padding-right: 5px;"><b>{$lng.lbl_select_language}:</b></td>
	<td><select name="sl" onchange="javascript: this.form.submit();">
{section name=ai loop=$all_languages}
<option value="{$all_languages[ai].code}"{if $store_language eq $all_languages[ai].code} selected="selected"{/if}>{$all_languages[ai].language}</option>
{/section}
	</select></td>
</tr>
</table>
</form>
{else}
&nbsp;
{/if}
	</td>
</tr>
{else}
{* Fast Lane Checkout page *}
<tr> 
	<td colspan="2" class="HeadLine">
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
{/if}
	</td>
{if $login ne ""}
	<td align="right" nowrap="nowrap"><b>{$userinfo.firstname} {$userinfo.lastname}</b> {$lng.txt_logged_in}</td>
	<td class="FLCAuthBox">
{if $js_enabled}
{include file="buttons/button.tpl" button_title=$lng.lbl_logoff href="javascript: document.toploginform.submit();" js_to_href="Y"}
{else}
{include file="buttons/logout_menu.tpl"}
{/if}
	</td>
{/if}
</tr>
</table>
</form>
	</td>
</tr>
{/if}
<tr> 
	<td colspan="2" class="HeadThinLine"><img src="{$ImagesDir}/spacer.gif" class="Spc" alt="" /></td>
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
	<td colspan="2" valign="middle" height="32">
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td class="HeadTopPad"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
{if (($main eq 'catalog' && $cat ne '') || $main eq 'product' || ($main eq 'comparison' && $mode eq 'compare_table') || ($main eq 'choosing' && $smarty.get.mode eq 'choose')) && $config.Appearance.enabled_printable_version eq 'Y'}
	<td class="PrintableRow" align="right">{include file="printable.tpl"}</td>
{/if}
</tr>
</table>
	</td>
</tr>
{else}
{* Fast Lane Checkout page *}
<tr>
	<td colspan="2" class="FLCTopPad"><img src="{$ImagesDir}/spacer.gif" alt="" /></td>
</tr>
{/if}
</table>
