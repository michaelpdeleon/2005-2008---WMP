{* $Id: popup_link.tpl,v 1.3 2006/03/24 12:42:27 max Exp $ *}
<div class="AELinkBox"{if $width} style="width: {$width};"{/if}>
<a href="javascript: void(0);" onclick="javascript: if (isHTML_Editor) window.open('{$xcart_web_dir}/wysiwyg.php?id={$id|escape}','WYSIWYG','width=600,height=400,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no'); else if (window.txt_advanced_editor_warning) alert(txt_advanced_editor_warning);">{$lng.lbl_advanced_editor|escape}</a>
</div>

