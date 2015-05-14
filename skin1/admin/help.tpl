{* $Id: help.tpl,v 1.6 2006/04/11 12:28:25 svowl Exp $ *}
{capture name=menu}
<a href="http://www.x-cart.com/faq.html" class="VertMenuItems" target="_blank">{$lng.lbl_xcart_faqs}</a><br />
<a href="http://forum.x-cart.com/" class="VertMenuItems" target="_blank">{$lng.lbl_community_forums}</a><br />
<a href="http://secure.qualiteam.biz/" class="VertMenuItems" target="_blank">{$lng.lbl_support_helpdesk}</a><br />
<a href="http://www.x-cart.com/software_license_agreement.html" class="VertMenuItems" target="_blank">{$lng.lbl_license_agreement}</a><br />
{/capture}
{ include file="menu.tpl" dingbats="dingbats_help.gif" menu_title=$lng.lbl_help menu_content=$smarty.capture.menu link_href="help.php"}
