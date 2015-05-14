{* $Id: import_export.tpl,v 1.2 2005/11/17 06:55:39 max Exp $ *}

{include file="page_title.tpl" title=$lng.lbl_import_export_data_header}

{$lng.txt_import_data_top_text}

<br /><br />

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

<br />

{if $mode eq "export"}
{include file="main/export.tpl"}

{else}
{include file="main/import.tpl"}
{/if}

