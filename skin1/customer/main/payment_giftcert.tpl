{* $Id: payment_giftcert.tpl,v 1.12 2006/04/10 07:36:17 max Exp $ *}
<script type="text/javascript">
<!--
requiredFields[0] = ["gcid", "{$lng.lbl_gift_certificate}"];
-->
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
{if $hide_header ne "Y"}
<tr valign="middle">
<td height="20" colspan="3"><b>{$lng.lbl_gc_information}</b><hr size="1" noshade="noshade" /></td>
</tr>
{/if}
{if $smarty.get.err eq "gc_used"}
<tr>
<td colspan="3">
<font class="ErrorMessage">{$lng.err_gc_used}</font>
</td>
</tr>
{/if}
<tr valign="middle">
<td align="right">{$lng.lbl_gift_certificate}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" size="32" id="gcid" name="gcid" />
</td>
</tr>
</table>
