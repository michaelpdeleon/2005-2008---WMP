// $Id: popup_product.js,v 1.1 2005/11/30 13:29:35 max Exp $
function popup_product (field_productid, field_product, query) {
	window.open ("popup_product.php?field_productid="+field_productid+"&field_product="+field_product+"&query="+query, "selectproduct", "width=600,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
