{* $Id: printable.tpl,v 1.4 2005/11/17 06:55:36 max Exp $ *}
<table cellspacing="0" cellpadding="0">
<tr>
	<td align="right" valign="middle"><a href="{$php_url.url}?printable=Y{if $php_url.query_string ne ''}&amp;{$php_url.query_string|amp}{/if}" style="TEXT-DECORATION: underline;">{$lng.lbl_printable_version}&nbsp;</a></td>
	<td width="16" valign="middle"><a href="{$php_url.url}?printable=Y{if $php_url.query_string ne ''}&amp;{$php_url.query_string|amp}{/if}"><img src="{$ImagesDir}/printer.gif" alt="" /></a></td>
</tr>
</table>
