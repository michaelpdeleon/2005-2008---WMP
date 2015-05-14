{* $Id: form_validation_js.tpl,v 1.9 2006/04/10 07:36:17 max Exp $ *}
<script type="text/javascript" language="JavaScript 1.2">
<!--
function FormValidation() {ldelim}

    {if $active_modules.Product_Options ne '' && $product_options ne ''}
    if(!check_exceptions()) {ldelim}
        alert(exception_msg);
        return false;
    {rdelim}
	{if $product_options_js ne ''}
	{$product_options_js}
	{/if}
    {/if}

	if(document.getElementById('product_avail'))
	    if(document.getElementById('product_avail').value == 0) {ldelim}
    	    alert("{$lng.txt_out_of_stock|replace:"\n":"<br />"|replace:"\r":" "|replace:'"':'\"'}");
        	return false;
	    {rdelim}

    return true;
{rdelim}
-->
</script>

