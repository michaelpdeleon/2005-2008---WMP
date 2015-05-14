{* $Id: error_last_admin.tpl,v 1.7 2005/11/17 06:55:39 max Exp $ *}
{capture name=dialog}

{$lng.txt_last_admin_warning}

<br /><br />

{include file="buttons/button.tpl" button_title=$lng.lbl_continue href="home.php"}

<br />

{/capture}
{include file="dialog.tpl" title=$lng.lbl_warning content=$smarty.capture.dialog extra='width="100%"'}
