{* $Id: product_modify.tpl,v 1.12 2006/04/07 06:00:35 max Exp $ *}
{foreach from=$extra_fields item=ef}
<!-- Start addition by Michael de Leon 09.19.06 -->
{if $ef.fieldid eq '1'}
	<tr> 
{if $productids ne ''}<td width="15" class="TableSubHead"><INPUT type="checkbox" value="Y" name="fields[efields][{$ef.fieldid}]"></td>{/if}
	<td class="FormButton" nowrap>{$ef.field}</td>
	<td><select name="efields[{$ef.fieldid}]" size="1">
			<option value="1-2 days" {if $ef.field_value eq '1-2 days'}selected{/if}>1-2 days</option>
			<option value="3-5 days" {if $ef.field_value eq '3-5 days'}selected{/if}>3-5 days</option>
		</select></td>
</tr>
{else}
<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[efields][{$ef.fieldid}]" /></td>{/if}
	<td class="FormButton" nowrap="nowrap">{$ef.field}</td>
	<td><input type="text" name="efields[{$ef.fieldid}]" size="24" value="{if $ef.is_value eq 'Y'}{$ef.field_value|escape:"html"}{else}{$ef.value|escape:"html"}{/if}" /></td>
</tr>
{/if}
{/foreach}
<!-- End addition by Michael de Leon 09.19.06 -->
<!-- Deleted by Michael de Leon 09.19.06
<tr> 
{* if $geid ne '' *}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[efields][{$ef.fieldid}]" /></td>{* /if *}
	<td class="FormButton" nowrap="nowrap">{* $ef.field *}</td>
	<td><input type="text" name="efields[{* $ef.fieldid *}]" size="24" value="{* if $ef.is_value eq 'Y' *}{* $ef.field_value|escape:"html" *}{* else *}{* $ef.value|escape:"html" *}{* /if *}" /></td>
</tr>
{* /foreach *}
-->
