{* $Id: dialog.tpl,v 1.19 2004/06/24 09:53:29 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<TABLE border="0" cellpadding="0" cellspacing="0" {$extra}>
<TR> 
<TD class="DialogTitle_bioexcell">&nbsp;&nbsp;{$title}</TD>
</TR>
<TR><TD class="DialogBorder_bioexcell"><table cellspacing="1" class="DialogBox">
<TR><TD class="DialogBox_bioexcell" valign="{$valign|default:"top"}">{$content}
&nbsp;
</TD></TR>
</TABLE></TD></TR>
</TABLE>
<BR>
{/if}
