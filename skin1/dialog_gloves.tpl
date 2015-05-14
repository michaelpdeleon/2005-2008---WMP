{* $Id: dialog.tpl,v 1.19 2004/06/24 09:53:29 max Exp $ *}
{if $printable ne ''}
{include file="dialog_printable.tpl"}
{else}
<TABLE border="0" cellpadding="0" cellspacing="0" {$extra}>
<TR> 
<TD height="15" class="DialogTitle_gloves" background="{$ImagesDir}/dialog_bg_n_gloves.gif" valign="bottom">&nbsp;&nbsp;{$title}</TD>
</TR>
<TR><TD class="DialogBorder_gloves"><TABLE border="0" cellpadding="10" cellspacing="1" width="100%">
<TR><TD class="DialogBox_gloves">{$content}
&nbsp;
</TD></TR>
</TABLE></TD></TR>
</TABLE>
<BR>
{/if}
