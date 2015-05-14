{* $Id: xcart_news.tpl,v 1.3 2005/11/17 06:55:37 max Exp $ *}
{include file="page_title.tpl" title=$lng.lbl_new_features_in_xcart}

<!-- IN THIS SECTION -->

{include file="dialog_tools.tpl"}

<!-- IN THIS SECTION -->

<br />

{capture name=dialog}

<div align="justify">{$lng.txt_xcart_new_features}</div>

{/capture}
{include file="dialog.tpl" title=$lng.lbl_new_features_in_xcart content=$smarty.capture.dialog extra='width="100%"'}

