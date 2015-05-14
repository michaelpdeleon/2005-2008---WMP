{* $Id: multirow.tpl,v 1.1.2.3 2006/07/04 09:58:41 svowl Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
var lbl_remove_row = '{$lng.lbl_remove_row|escape:"javascript"}';
var lbl_add_row = '{$lng.lbl_add_row|escape:"javascript"}';
var inputset_plus_img = "{$ImagesDir}/plus.gif";
var inputset_minus_img = "{$ImagesDir}/minus.gif";
-->
</script>
<img src="{$ImagesDir}/plus.gif" width="0" height="0" alt="" style="display: none" />
<img src="{$ImagesDir}/minus.gif" width="0" height="0" alt="" style="display: none" />
{include file="main/include_js.tpl" src="main/multirow.js"}
