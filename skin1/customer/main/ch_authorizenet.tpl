{* $Id: ch_authorizenet.tpl,v 1.4 2005/12/26 08:07:14 mclap Exp $ *}
<tr valign="middle">
<td height="20" colspan="3"><b>{$lng.lbl_check_information}</b><hr size="1" noshade="noshade" /></td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_name}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" name="check_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_name}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" name="check_bname" size="32" maxlength="50" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_account}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" name="check_ban" size="32" maxlength="20" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_bank_routing}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" name="check_brn" size="32" maxlength="20" value="" />
</td>
</tr>

{if $payment_cc_data.param07 eq 'W'}
<tr valign="middle">
<td align="right">{$lng.lbl_ch_org_type}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap"><select name="check_wf_org_type">
<option value='I'>{$lng.lbl_ch_individual}</option>
<option value='B'>{$lng.lbl_ch_business}</option>
</select></td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ssn}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" name="check_wf_ssn" size="32" maxlength="9" value="{$userinfo.ssn}" />
</td>
</tr>

<tr valign="middle">
<td align="right" colspan="3">{$lng.txt_if_unknown_ssn}:</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_driver_license_num}</td>
<td></td>
<td nowrap="nowrap">
<input type="text" name="check_wf_dln" size="32" maxlength="50" value="" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_ch_driver_license_state}</td>
<td></td>
<td nowrap="nowrap"> 
<input type="text" name="check_wf_dls" size="3" maxlength="2" value="" />
</td> 
</tr> 

<tr valign="middle">
<td align="right">{$lng.lbl_ch_driver_license_dob}</td>
<td></td>
<td nowrap="nowrap"> 
<input type="text" name="check_wf_dldob" size="32" maxlength="15" value="" />
</td> 
</tr> 
{/if}
