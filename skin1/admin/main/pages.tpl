{* $Id: pages.tpl,v 1.15.2.2 2006/07/11 08:39:26 svowl Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_static_pages}

{$lng.txt_static_pages_top_text}

<br /><br />

{capture name=dialog}

{include file="main/language_selector.tpl" script="pages.php?"}

<script type="text/javascript" language="JavaScript 1.2">
<!--
function mark_all_e(status) {ldelim}
    var fieldname;
{section name=pg loop=$pages}
{if $pages[pg].level eq "E"}
    fieldname = 'posted_data['+'{$pages[pg].pageid}'+'][to_delete]';
    document.embedpagesform.elements[fieldname].checked = status;
{/if}
{/section}
{rdelim}

function mark_all_r(status) {ldelim}
    var fieldname;
{section name=pg loop=$pages}
{if $pages[pg].level eq "R"}
    fieldname = 'posted_data['+'{$pages[pg].pageid}'+'][to_delete]';
    document.pagesform.elements[fieldname].checked = status;
{/if}
{/section}
{rdelim}

-->
</script>

<form action="pages.php" method="post" name="pagesform">
<input type="hidden" name="mode" value="update" />
<input type="hidden" name="sec" value="E" />

<table cellpadding="3" cellspacing="1" width="100%">

<tr>
	<td colspan="5">{include file="main/subheader.tpl" title=$lng.lbl_embedded_level class="grey"}</td>
</tr>

{if $pages}

{capture name=embedpages}

{section name=pg loop=$pages}

{if $pages[pg].level eq "E"}

{assign var="embedded" value="found"}

<tr{cycle name="embed" values=", class='TableSubHead'"}>
<td><input type="checkbox" name="posted_data[{$pages[pg].pageid}][to_delete]" value="{$pages[pg].pageid}" /></td>
<td><input type="text" name="posted_data[{$pages[pg].pageid}][orderby]" value="{$pages[pg].orderby}" size="5" /></td>
<td><b><a href="pages.php?pageid={$pages[pg].pageid}" title="{$pages[pg].filename|escape}">{$pages[pg].title|truncate:"30":"..."}</a></b></td>
<td><select name="posted_data[{$pages[pg].pageid}][active]">
<option value="Y"{if $pages[pg].active eq "Y"} selected="selected"{/if}>{$lng.lbl_enabled}</option>
<option value="N"{if $pages[pg].active eq "N"} selected="selected"{/if}>{$lng.lbl_disabled}</option>
</select>
</td>
<td align="right"><a href="{$catalogs.customer}/pages.php?pageid={$pages[pg].pageid}&amp;mode=preview" target="previewpage">{$lng.lbl_preview}</a></td>
</tr>

{/if}

{/section}

{/capture}

{/if}

{if $embedded}

<tr>
<td colspan="4"><div style="line-height: 170%;"><a href="javascript: mark_all_e(true);">{$lng.lbl_check_all}</a> / <a href="javascript: mark_all_e(false);">{$lng.lbl_uncheck_all}</a></div></td>
</tr>

{/if}

<tr class="TableHead">
	<td width="10">&nbsp;</td>
	<td width="10%">{$lng.lbl_pos}</td>
	<td width="70%">{$lng.lbl_page_title}</td>
	<td width="20%" colspan="2">{$lng.lbl_status}</td>
</tr>

{if $embedded}

{$smarty.capture.embedpages}

<tr>
<td>&nbsp;</td>
<td colspan="4"><hr />
<table cellpadding="0" cellspacing="0">
<tr>
<td width="5"><input type="checkbox" id="parse_smarty_tags" name="parse_smarty_tags" value="Y"{if $config.General.parse_smarty_tags eq "Y"} checked="checked"{/if} /></td>
<td><label for="parse_smarty_tags">{$lng.lbl_parse_smarty_tags_in_embedded_pages}</label></td>
</tr>
</table>
</td>
</tr>

<tr>
	<td colspan="5" class="SubmitBox">
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'delete');" />
	<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'update');" />
	</td>
</tr>

{else}

<tr>
<td align="center" colspan="5">{$lng.txt_no_embedded_pages}</td>
</tr>

{/if}

<tr>
	<td colspan="5" class="SubmitBox">
	<input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='pages.php?level=E&amp;pageid=';" />
	</td>
</tr>

{if $pages}

{capture name=rootpages}

{section name=pg loop=$pages}

{if $pages[pg].level eq "R"}

{assign var="root" value="found"}


<tr{cycle name="root" values=", class='TableSubHead'"}>
	<td>
	<input type="hidden" name="pages_array[{$pages[pg].pageid}][active]" value="Y" />
	<input type="checkbox" name="posted_data[{$pages[pg].pageid}][to_delete]" value="{$pages[pg].pageid}" />
	</td>
	<td><input type="text" name="posted_data[{$pages[pg].pageid}][orderby]" value="{$pages[pg].orderby}" size="5" /></td>
	<td colspan="2"><b><a href="pages.php?pageid={$pages[pg].pageid}" title="{$pages[pg].filename|escape}">{$pages[pg].title|truncate:"30":"..."}</a></b></td>
	<td align="right"><a href="{$xcart_web_dir}/{$pages[pg].filename}" target="previewpage">{$lng.lbl_preview}</a></td>
</tr>

{/if}

{/section}

{/capture}

{/if}

<tr>
<td colspan="5"><br /><br />{include file="main/subheader.tpl" title=$lng.lbl_root_level class="grey"}</td>
</tr>

{if $root}

<tr>
<td colspan="4"><div style="line-height: 170%;"><a href="javascript:mark_all_r(true);">{$lng.lbl_check_all}</a> / <a href="javascript:mark_all_r(false);">{$lng.lbl_uncheck_all}</a></div></td>
</tr>

{/if}

<tr class="TableHead">
<td>&nbsp;</td>
<td>{$lng.lbl_pos}</td>
<td colspan="3">{$lng.lbl_page_title}</td>
</tr>


{if $root}

{$smarty.capture.rootpages}

<tr>
	<td colspan="5" class="SubmitBox">
	<input type="button" value="{$lng.lbl_delete_selected|strip_tags:false|escape}" onclick="javascript: document.pagesform.sec.value='R'; submitForm(this, 'delete');" />
	<input type="button" value="{$lng.lbl_update|strip_tags:false|escape}" onclick="javascript: document.pagesform.sec.value='R'; submitForm(this, 'update');" />
	</td>
</tr>

{else}

<tr>
<td align="center" colspan="5">{$lng.txt_no_root_pages}</td>
</tr>

{/if}

<tr>
<td colspan="5" class="SubmitBox">
<input type="button" value="{$lng.lbl_add_new_|strip_tags:false|escape}" onclick="javascript: self.location='pages.php?level=R&amp;pageid=';" />

<br /><br />

<div align="right"><input type="button" value="{$lng.lbl_find_pages|strip_tags:false|escape}" onclick="javascript: submitForm(this, 'check');" /></div>
</td>
</tr>

</table>
</form>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_static_pages content=$smarty.capture.dialog extra='width="100%"'}

