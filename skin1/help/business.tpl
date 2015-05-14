{* $Id: business.tpl,v 1.9 2005/11/21 12:41:58 max Exp $ *}
<p />
{capture name=dialog}
{$lng.txt_privacy_statement}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_privacy_statement content=$smarty.capture.dialog extra='width="100%"'}
