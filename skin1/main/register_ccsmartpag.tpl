{* $Id: register_ccsmartpag.tpl,v 1.6 2005/11/17 06:55:39 max Exp $ *}
{include file="check_cc_number_script.tpl"}
<table cellspacing="0" cellpadding="2">

<tr valign="middle">
<td align="right">{$lng.lbl_smartpag_payment_method}:</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap" colspan="3">
<select name="pagamento">
<option value="PAYMENT ORDER">PAYMENT ORDER</option>
<option value="CREDIT CARD">CREDIT CARD</option>
<option value="CHECK">CHECK</option>
<option value="DOC">DOC (CREDIT TRANSFER)</option>
<option value="FINANCING">FINANCING</option>
<option value="ELECTRONIC CHECK">ELECTRONIC CHECK</option>
<option value="PANAMERICANO">PANAMERICANO</option>
<option value="FINASA">FINASA</option>
<option value="PAGAMENTO FACIL BRADESCO">PAGAMENTO FACIL BRADESCO</option>
<option value="ITAU PAYMENT ORDER">ITAU PAYMENT ORDER</option>
<option value="FREEDOM2BUY">FREEDOM2BUY</option>
<option value="ABN FINANCING">ABN FINANCING</option>
<option value="BIAMEX SIMULATOR">BIAMEX SIMULATOR</option>
</select>
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_credit_card_type}:</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap" colspan="3">
<select name="bandeira">
<option value="1">American Express</option>
<option value="2">Mastercard</option>
<option value="3">Solo</option>
<option value="4">Visa</option>
<option value="5">Diners</option>
<option value="23">Visanet / Moset</option>
<option value="41">Mastercard Safenet</option>
<option value="44">Diners Safenet</option>
<option value="50">Amex Online / Webpos</option>
</select>
</td>
</tr>

</table>
