{* $Id: ie_delimiter.tpl,v 1.11 2005/12/05 15:00:44 max Exp $ *}
{if $saved_delimiter eq ''}{assign var="saved_delimiter" value=$smarty.get.delimiter}{/if}
<select name="{$field_name|default:"delimiter"}">
	<option value=";"{if $saved_delimiter eq ";"} selected="selected"{/if}>{$lng.lbl_semicolon}</option>
	<option value=","{if $saved_delimiter eq ","} selected="selected"{/if}>{$lng.lbl_comma}</option>
	<option value="tab"{if $saved_delimiter eq "\t"} selected="selected"{/if}>{$lng.lbl_tab}</option>
</select>
