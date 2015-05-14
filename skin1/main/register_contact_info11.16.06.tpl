{* $Id: register_contact_info.tpl,v 1.10 2005/11/17 06:55:39 max Exp $ *}
{if $is_areas.C eq 'Y'}
{if $hide_header eq ""}
<tr>
<td height="20" colspan="3"><font class="RegSectionTitle">{$lng.lbl_contact_information}</font><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

{if $default_fields.phone.avail eq 'Y'}
<tr>
<td align="right">{$lng.lbl_phone}</td>
<td>{if $default_fields.phone.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="phone" name="phone" size="32" maxlength="32" value="{$userinfo.phone}" />
{if $reg_error ne "" and $userinfo.phone eq "" and $default_fields.phone.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
{if $default_fields.email.avail eq 'Y'}
<tr>
<td align="right">{$lng.lbl_email}</td>
<td>{if $default_fields.email.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="email" name="email" size="32" maxlength="128" value="{$userinfo.email}" />
{if $emailerror ne "" or ($reg_error ne "" and $userinfo.email eq "" and $default_fields.email.required eq 'Y')}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
{if $default_fields.fax.avail eq 'Y'}
<tr>
<td align="right">{$lng.lbl_fax}</td>
<td>{if $default_fields.fax.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="fax" name="fax" size="32" maxlength="128" value="{$userinfo.fax}" />
{if $reg_error ne "" and $userinfo.fax eq "" and $default_fields.fax.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
{if $default_fields.url.avail eq 'Y'}
<tr>
<td align="right">{$lng.lbl_web_site}</td>
<td>{if $default_fields.url.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<input type="text" id="url" name="url" size="32" maxlength="128" value="{$userinfo.url}" />
{if $reg_error ne "" and $userinfo.url eq "" and $default_fields.url.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
{include file="main/register_additional_info.tpl" section="C"}
{/if}

