{* $Id: help.tpl,v 1.20 2005/11/17 06:55:36 max Exp $ *}
{capture name=menu}
<a href="help.php?section=contactus&amp;mode=update" class="VertMenuItems">{$lng.lbl_contact_us}</a><br />
<a href="help.php?section=business" class="VertMenuItems">{$lng.lbl_privacy_statement}</a><br />
<a href="help.php?section=conditions" class="VertMenuItems">{$lng.lbl_terms_n_conditions}</a><br />
{include file="pages_menu.tpl"}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_help.gif" menu_title=$lng.lbl_help menu_content=$smarty.capture.menu link_href="help.php"}
