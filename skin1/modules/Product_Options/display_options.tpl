{* $Id: display_options.tpl,v 1.10 2005/11/17 06:55:55 max Exp $ *}
{if $options && $force_product_options_txt eq ''}
{if $is_plain eq 'Y'}
{if $options ne $options_txt}
{foreach from=$options item=v}
   {$v.class}: {$v.option_name}
{/foreach}
{else}
{$options_txt}
{/if}
{else}
{if $options ne $options_txt}
<table cellspacing="1" cellpadding="0">
{foreach from=$options item=v}
<tr>
	<td>{$v.class}:&nbsp;</td>
	<td>{$v.option_name}</td>
</tr>
{/foreach}
</table>
{else}
{$options_txt|replace:"\n":"<br />"}
{/if}
{/if}
{elseif $force_product_options_txt}
{if $is_plain eq 'Y'}
{$options_txt|escape:"html"}
{else}
{$options_txt|replace:"\n":"<br />"}
{/if}
{/if}
