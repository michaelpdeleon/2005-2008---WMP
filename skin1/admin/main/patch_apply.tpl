{* $Id: patch_apply.tpl,v 1.20.2.2 2006/07/11 08:39:26 svowl Exp $ *}

{include file="page_title.tpl" title=$lng.lbl_applying_patch}
{capture name=dialog}
{if ($patch_type eq "text" or $patch_type eq "upgrade") and $patch_phase ne "upgrade_final"}

<br /><br />

<b>{$lng.txt_applying_patch_step_1}</b>

<br /><br />

{$lng.txt_testing_phase_result}

<br /><br />

{include file="admin/main/patch_apply_tbl.tpl" files=$patch_files}

{if $ready_to_patch ne 1}
<br /><br />

{$lng.txt_patch_application_error}

<br /><br />

<form action="patch.php" method="post">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="patch_filename" value="{$patch_filename}" />
<input type="hidden" name="reverse" value="{$reverse}" />
<input type="submit" value="{$lng.lbl_go_back|strip_tags:false|escape}" />
</form>
{/if}
{/if}

{if $ready_to_patch eq "1" and $patch_phase ne "upgrade_final"}

{if $mode ne "sql"}
<br /><br />
{$lng.txt_some_files_will_be_ignored}
<br /><br />
{/if}

<form action="patch.php" method="post" name="step1form">
<input type="hidden" name="mode" value="{$mode}" />
{$lng.lbl_patch_text}:<br />
<textarea cols="60" rows="10">{$patch_text}</textarea>
<p>
<input type="hidden" name="patch_filename" value="{$patch_filename}" />
<input type="hidden" name="reverse" value="{$reverse}" />
<input type="hidden" name="confirmed" value="Y" />

{if $could_not_patch ne "0" and $mode ne "sql"}
{$lng.txt_have_could_not_patch}
<br />{$lng.lbl_tick_here_to_apply_patch}
<input type="checkbox" name="try_all" checked="checked" />
<br /><br />
{/if}

<input type="button" value="{$lng.lbl_go_back|strip_tags:false|escape}" onclick="document.step1form.confirmed.value='';document.step1form.submit();" />
&nbsp;
&nbsp;
<input type="submit" value="{$lng.lbl_apply_patch|strip_tags:false|escape}" />
</form>
{/if}

{if !empty($files_to_patch)}

{$lng.txt_patch_applying_note}

{elseif $confirmed ne ""}

{if $patch_type eq "text" or $patch_type eq "upgrade"}
<br /><br />
<b>{$lng.txt_applying_patch_step_2}</b>
<br /><br />
{/if}

{if $patch_phase eq "upgrade_final"}
{if $patched_files ne ""}
<p>
<b>{$lng.lbl_files_patch_status}:</b>
</p>
{include file="admin/main/patch_apply_tbl.tpl" files=$patched_files}
{/if}
{if $excluded_files ne ""}
<p>
<b>{$lng.lbl_files_excluded_from_patch}:</b>
</p>
{include file="admin/main/patch_apply_tbl.tpl" files=$excluded_files}
{/if}
<p>
<b>{$lng.lbl_patch_results}</b>
<p>
{section name=line loop=$patch_result}
{$patch_result[line]}<br />
{/section}
<p>
<b>{$lng.lbl_patch_log}</b>
<p>
{section name=line loop=$patch_log}
{$patch_log[line]}<br />
{/section}
{else}
<p>
{section name=line loop=$patch_result}
{$patch_result[line]}<br />
{/section}
{/if}

{if $patch_completed ne "1" and $mode eq "sql"}
<br /><br />

<font color="red">{$lng.txt_correct_errors}</font><br />
<form action="patch.php#patch_sql" method="get">
<input type=submit value="<< {$lng.lbl_go_back}" />
</form>

{elseif $patch_completed ne "1" and $mode ne "sql"}
<br /><br />

{$lng.txt_files_could_not_be_patched}

<br /><br />

{include file="admin/main/patch_apply_tbl.tpl" files=$failed_files}

<font color="red">{$lng.txt_correct_errors}</font><br />
<form action="patch.php" method="post">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="patch_filename" value="{$patch_filename}" />
<input type="hidden" name="reverse" value="{$reverse}" />
<input type="submit" value="&lt;&lt; {$lng.lbl_go_back|strip_tags:false}" />
</form>

{else}

<form action="patch.php" method="get">

{if $need_manual_patch}

{$lng.txt_files_could_not_be_patched}

{include file="admin/main/patch_apply_tbl.tpl" files=$failed_files}

{else}

<font color="green">{$lng.txt_patch_applied_successfuly}</font>

{/if}

<br /><br />

<input type="submit" value="{$lng.lbl_finish|strip_tags:false|escape}" />

{/if}

</form>

{/if}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_applying_patch content=$smarty.capture.dialog extra='width="100%"'}
