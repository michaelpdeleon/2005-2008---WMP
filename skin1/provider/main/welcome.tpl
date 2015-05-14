{* $Id: welcome.tpl,v 1.17 2005/11/28 08:15:05 max Exp $ *}
<h3>{$lng.lbl_welcome_to_the_providers_zone}</h3>
<p />
{capture name=dialog}
{$lng.txt_provider_zone_welcome_note}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_become_our_partner content=$smarty.capture.dialog extra='width="100%"'}
