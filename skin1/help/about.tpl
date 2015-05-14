{* $Id: about.tpl,v 1.8 2005/11/21 12:41:58 max Exp $ *}
<p />
{capture name=dialog}
{$lng.txt_about}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_about_our_site content=$smarty.capture.dialog extra='width="100%"'}
