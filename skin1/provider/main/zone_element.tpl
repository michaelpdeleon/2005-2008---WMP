{* $Id: zone_element.tpl,v 1.3 2005/12/05 15:00:45 max Exp $ *}
<textarea cols="40" rows="{$box_size|default:3}" style="width: 100%;" name="{$name}">
{section name=id loop=$zone_elements}
{if $zone_elements[id].field_type eq $field_type}
{$zone_elements[id].field|escape}
{/if}
{/section}
</textarea>


