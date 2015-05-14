{* $Id: cc_processing_main.tpl,v 1.8.2.1 2006/06/16 10:47:40 max Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_payment_gateways}
{include file=$processing_module }
{if $module_data ne "" and $module_data.status ne "1"}
<br /><br />
{capture name=dialog}
<table cellpadding="2" cellspacing="1" width="100%">
<tr><td>
<br />
{if $module_data.failed_func eq "httpsmod"}
<font class="AdminTitle">{$lng.txt_no_https_modules_detected}</font>
{elseif $module_data.failed_func eq "testexec"}
<font class="AdminTitle">{$lng.txt_file_none_exe_no_exists|substitute:"file":$module_data.failed_param}</font>
{else}
<font class="AdminTitle">{$lng.txt_some_requirements_failed}</font>
{/if}
&nbsp;&nbsp;&nbsp; <a href="{$catalogs.admin}/general.php#Environment" title="{$lng.lbl_environment_info|escape}">{$lng.lbl_check_environment_link} &gt;&gt;</a>
</td></tr>
</table>
{/capture}
{include file="dialog.tpl" title=$lng.lbl_warning content=$smarty.capture.dialog extra='width="100%"'}
{/if}
