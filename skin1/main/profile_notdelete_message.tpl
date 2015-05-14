{* $Id: profile_notdelete_message.tpl,v 1.13 2005/11/17 06:55:39 max Exp $ *}
{capture name=dialog}
{$lng.txt_profile_not_deleted}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_confirmation content=$smarty.capture.dialog extra='width="100%"'}
