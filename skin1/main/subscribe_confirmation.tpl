{* $Id: subscribe_confirmation.tpl,v 1.18 2006/03/16 15:28:19 svowl Exp $ *}
{capture name=dialog}
{$lng.txt_newsletter_subscription_msg}:<br />
<b>{$smarty.get.email|replace:"\\":""}</b>
<p />
{$lng.txt_unsubscribe_information} <a href="{$http_location}/mail/unsubscribe.php?email={$smarty.get.email|replace:"\\":""}"><font class="FormButton">{$lng.lbl_this_url}</font></a>.
{/capture}
{ include file="dialog.tpl" title=$lng.txt_thankyou_for_subscription content=$smarty.capture.dialog extra='width="100%"'}
