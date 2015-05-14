{* $Id: register_ddinfo.tpl,v 1.2 2006/01/16 06:47:37 mclap Exp $ *}
{if $hide_header ne "Y"}
<tr valign="middle">
<td height="20" colspan="3"><font class="RegSectionTitle">{$lng.lbl_check_information}</font><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr valign="middle">
<td align="right">
<script type="text/javascript">
<!--
requiredFields.push(['debit_name','{$lng.lbl_ch_name|escape}']);
requiredFields.push(['debit_bank_account','{$lng.lbl_ch_bank_account|escape}']);
requiredFields.push(['debit_bank_number','{$lng.lbl_ch_bank_routing|escape}']);
-->
</script>
{$lng.lbl_ch_name}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" id="debit_name" name="debit_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_account}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_account" name="debit_bank_account" autocomplete="off" size="32" maxlength="20" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_routing}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_number" name="debit_bank_number" autocomplete="off" size="32" maxlength="20" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_name}</td>
<td>&nbsp;</td>
<td nowrap="nowrap">
<input type="text" id="debit_bank_name" name="debit_bank_name" autocomplete="off" size="32" maxlength="20" value="" />
</td>
</tr>
