{* $Id: FAQ_HTML.tpl,v 1.8 2005/11/17 06:55:38 max Exp $ *}
{capture name=dialog}
{$lng.txt_faq}
{/capture}
{include file="dialog.tpl" title=$lng.lbl_faq content=$smarty.capture.dialog extra='width="100%"'}
