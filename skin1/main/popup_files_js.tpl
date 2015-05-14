{* $Id: popup_files_js.tpl,v 1.8 2005/11/18 12:01:08 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
function popup_files (filename, path) {ldelim}
	window.open ("popup_files.php?{if $usertype eq "A"}product_provider={$product.provider}&{/if}field_filename="+filename+"&field_path="+path, "selectfile", "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
{rdelim}

function popup_images (filename, path) {ldelim}
	window.open ("popup_files.php?tp=images&field_filename="+filename+"&field_path="+path, "selectfile", "width=600,height=450,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
{rdelim}
-->
</script>
