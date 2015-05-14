{* $Id: publicity.tpl,v 1.7 2005/11/21 12:41:58 max Exp $ *}
<p />
{capture name=dialog}
{$lng.txt_publicity_msg}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_publicity content=$smarty.capture.dialog extra='width="100%"'}
