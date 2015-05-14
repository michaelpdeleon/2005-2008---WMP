{* $Id: Password_Recovery_message.tpl,v 1.9 2005/11/21 12:41:58 max Exp $ *}
{capture name=dialog}
<p />
{$lng.txt_password_recover_message1} {$smarty.get.email|escape:"html"}.
{$lng.txt_password_recover_message2}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
