{* $Id: popup_product.tpl,v 1.24.2.6 2006/07/11 08:39:27 svowl Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
{config_load file="$skin_config"}
<html>
<head>
	<title>{$lng.lbl_select_product|strip_tags}</title>
	<link rel="stylesheet" href="{$SkinDir}/skin1_admin.css" />
<script type="text/javascript" language="JavaScript 1.2">
<!--
var err_choose_product = "{$lng.err_choose_product|strip_tags|replace:"\n":" "|replace:"\r":" "|replace:'"':'\"'}";
var err_choose_category = "{$lng.err_choose_category|strip_tags|replace:"\n":" "|replace:"\r":" "|replace:'"':'\"'}";
var id_obj = window.opener.document.{$smarty.get.field_productid|stripslashes} ? window.opener.document.{$smarty.get.field_productid|stripslashes} : window.opener.document.getElementById('{$smarty.get.field_productid}');
var name_obj = window.opener.document.{$smarty.get.field_product|stripslashes} ? window.opener.document.{$smarty.get.field_product|stripslashes} : window.opener.document.getElementById('{$smarty.get.field_product}');

{literal}
function setProduct (productid, product) {
	if (id_obj)
		id_obj.value = productid;
	if (name_obj)
		name_obj.value=product;

	window.close();
}
function setProductInfo () {
	if (document.products_form.productid.value != "") {
		setProduct (document.products_form.productid.options[document.products_form.productid.selectedIndex].value, document.products_form.productid.options[document.products_form.productid.selectedIndex].text);
	} else {
		alert (err_choose_product);
	}
}

function checkCategory () {
	if (document.cat_form.cat.selectedIndex == -1) {
		alert (err_choose_category);
		return false;
	}
	return true;
}

{/literal}
-->
</script>
</head>
<body>
<br />
{capture name=dialog}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td colspan="2">
<table cellpadding="2" cellspacing="0">
<tr>
	<td valign="top"><font class="TopLabel">{$lng.lbl_bookmarks}:</font></td>
	<td valign="top">
{if $bookmarks ne ""}
<ul>
{section name=book_idx loop=$bookmarks}
	<li>
	<a href="javascript: setProduct('{$bookmarks[book_idx].productid}','{$bookmarks[book_idx].product|replace:"'":"\'"|replace:'"':"\'\'"}')">{$bookmarks[book_idx].product|truncate:30:"...":true}</a>
	&nbsp;&nbsp;
	<a href="popup_product.php?mode=delete_bookmark&amp;cat={$smarty.get.cat|escape:"html"}&amp;productid={$bookmarks[book_idx].productid}&amp;field_product={$smarty.get.field_product}&amp;field_productid={$smarty.get.field_productid|stripslashes}"><b>[{$lng.lbl_delete}]</b></a>
	</li>
{/section}
</ul>
{else}
&nbsp;
{/if}
	</td>
</tr>
</table>
<hr />
	</td>
</tr>
<tr>
	<td width="50%" valign="top">

<form method="get" onsubmit="javascript: return checkCategory ()" name="cat_form">
<input type="hidden" name="top_cat" value="{$smarty.get.top_cat}" />
<input type="hidden" name="field_product" value="{$smarty.get.field_product|stripslashes}" />
<input type="hidden" name="field_productid" value="{$smarty.get.field_productid|stripslashes}" />

<b>{$lng.lbl_categories}:</b><br />
{include file="main/category_selector.tpl" field="cat" extra=' size="20" style="width: 100%" ondblclick="javascript: if (checkCategory()) document.cat_form.submit();"' categoryid=$smarty.get.cat}<br /><br />
<center><input type="submit" value="{$lng.lbl_show_products|strip_tags:false|escape}" /></center>
</form>

	</td>
	<td width="50%" valign="top">
{if $products eq ""}
{$lng.txt_no_products_in_cat}
{else}

<form method="get" name="products_form">
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />
<input type="hidden" name="mode" value="bookmark" />
<input type="hidden" name="field_productid" value="{$smarty.get.field_productid|escape:"html"}" />
<input type="hidden" name="field_product" value="{$smarty.get.field_product|escape:"html"}" />

<b>{$lng.lbl_products}:</b><br />
<select name="productid" size="20" style="width: 100%" ondblclick="javascript: setProductInfo();">
{section name=prod_idx loop=$products}
	<option value="{$products[prod_idx].productid}">{$products[prod_idx].product}</option>
{/section}
</select><br /><br />
<center>
	<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setProductInfo();" />
	&nbsp;&nbsp;
	<input type="submit" value="{$lng.lbl_bookmark|strip_tags:false|escape}" />
</center>
</form>
{/if}

	</td>
</tr>
</table>
{/capture}

<div align="center">
{include file="dialog.tpl" content=$smarty.capture.dialog title=$lng.lbl_choose_product extra="width=90%"}
</div>

</body>
</html>
