{* $Id: debug_templates.tpl,v 1.21 2006/04/12 08:05:26 max Exp $ *}
{assign_debug_info}

<script type="text/javascript" language="JavaScript 1.2">
<!--
	if (window.opener==null || "{$opener}"!="console") {ldelim}
	_smarty_console = window.open("","console","width=360,height=500,resizable,scrollbars=yes");
	if(_smarty_console) {ldelim}
	_smarty_console.document.open();
	_smarty_console.document.write('<html><title>{$lng.lbl_xcart_debugging_console|strip_tags}</title><body bgcolor="#FFFBD3" nowrap="nowrap">');
	_smarty_console.document.write('<table width="360" cellpadding="0" cellspacing="0" nowrap="nowrap">');
	_smarty_console.document.write('<tr bgcolor="#cccccc"><td colspan="2" align="center"><b>{$lng.lbl_included_templates_config_files|strip_tags}:</b></td></tr></table>');
	_smarty_console.document.write('<table width="100%" cellpadding="0" cellspacing="0" nowrap="nowrap">');
	{section name=templates loop=$_debug_tpls}
		_smarty_console.document.write('<tr><td colspan="2" nowrap="nowrap"><tt>{section name=indent loop=$_debug_tpls[templates].depth}&nbsp;&nbsp;&nbsp;{/section}<img src="{$ImagesDir}/rarrow.gif" width="9" height="9" /> ');
		{if $_debug_tpls[templates].type eq "template" and $webmaster_mode eq "editor"}
		_smarty_console.document.write('<a style="color: brown; text-decoration: none;" hr'+'ef="{$catalogs.admin}/file_edit.php?{$XCARTSESSNAME}={$XCARTSESSID}&file=%2f{$_debug_tpls[templates].filename|escape:"url"}&opener=console" target="_blank" onmouseover="javascript: if (mainWnd && mainWnd.tmo) mainWnd.tmo(\'{$_debug_tpls[templates].filename|replace:"/":"0"}\')" onmouseout="javascript: if (mainWnd && mainWnd.tmu) mainWnd.tmu(\'{$_debug_tpls[templates].filename|replace:"/":"0"}\')">{$_debug_tpls[templates].filename}</a>');
		{else}
		_smarty_console.document.write('<font color="{if $_debug_tpls[templates].type eq "template"}brown{else}black{/if}">{$_debug_tpls[templates].filename}</font>')
		{/if}
{*		_smarty_console.document.write('<font size="-1"><i>({$_debug_tpls[templates].exec_time|string_format:"%.5f"}){if %templates.index% eq 0} (total){/if}</i></font>');*}
		_smarty_console.document.write('</tt></td></tr>');
	{sectionelse}
		_smarty_console.document.write('<tr bgcolor="#eeeeee"><td colspan="2"><tt><i>no templates included</i></tt></td></tr>');	
	{/section}
	_smarty_console.document.write('</table>');
	_smarty_console.document.write('</body></html>');
	_smarty_console.document.close();
	_smarty_console.mainWnd = window;
	{rdelim}
	{rdelim}
-->
</script>
