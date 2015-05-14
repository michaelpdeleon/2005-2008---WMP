{* $Id: unsubscribe_confirmation.tpl,v 1.12 2006/03/16 15:28:19 svowl Exp $ *}
{capture name=dialog}
{$lng.txt_unsubscribed_msg}<br />
{$lng.lbl_email}: <b>{$smarty.get.email|replace:"\\":""}</b>
{/capture}
{ include file="dialog.tpl" title=$lng.txt_thankyou_for_unsubscription content=$smarty.capture.dialog extra='width="100%"'}
