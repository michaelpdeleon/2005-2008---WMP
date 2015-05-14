{* $Id: customer_options.tpl,v 1.24 2006/04/07 05:19:21 svowl Exp $ *}
{if $product_options ne ''}
{if $nojs ne 'Y'}
<tr style="display: none;"><td>
<script type="text/javascript" language="JavaScript 1.2">
<!--
var alert_msg = '{$alert_msg}';
-->
</script>
{include file="modules/Product_Options/check_options.tpl"}
</td></tr>
{/if}

{foreach from=$product_options item=v}
{if $v.options ne '' || $v.is_modifier eq 'T'}
<tr>
	<!-- Deleted by Michael de Leon 11.17.06
	<td valign="middle" height="25">{if $usertype eq "A"}{$v.class}{else}{$v.classtext|default:$v.class}{/if}</td>
	-->
	<td class="wwmp_product_labels" valign="middle" height="25">{if $usertype eq "A"}{$v.class}{else}{$v.classtext|default:$v.class}:{/if}</td>
	<td valign="middle">
{if $cname ne ""}
{assign var="poname" value="$cname[`$v.classid`]"}
{else}
{assign var="poname" value="product_options[`$v.classid`]"}
{/if}
{if $v.is_modifier eq 'T'}
<input id="po{$v.classid}" type="text" name="{$poname}" value="{$v.default|escape}" />
{else}
<select id="po{$v.classid}" name="{$poname}"{if $disable} disabled="disabled"{/if}{if $nojs ne 'Y'} onchange="javascript: check_options();"{/if}>
{foreach from=$v.options item=o}
	<option value="{$o.optionid}"{if $o.selected eq 'Y'} selected="selected"{/if}>{$o.option_name}{if $v.is_modifier eq 'Y' && $o.price_modifier ne 0} ({if $o.modifier_type ne '%'}{include file="currency.tpl" value=$o.price_modifier display_sign=1 plain_text_message=1}{else}{$o.price_modifier}%{/if}){/if}</option>
{/foreach}
</select>
{/if}
	</td>
</tr>
{/if}
{/foreach}
{/if}


{if $product_options_ex ne ""}
<tr>
    <td colspan="2"><font id="exception_msg" color="red"></font></td>
</tr>
{if $err ne ''}
<tr>
	<td colspan="2"><font class="CustomerMessage">{$lng.txt_product_options_combinations_warn}:</font></td>
</tr>
{foreach from=$product_options_ex item=v}
<tr>
	<td>{foreach from=$v item=o}{if $usertype eq "A"}{$o.class}{else}{$o.classtext}{/if}: {$o.option_name}<br />{/foreach}<br /></td>
</tr>
{/foreach}
{/if}
{/if}

