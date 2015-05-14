{* $Id: menu_profile.tpl,v 1.17 2006/02/15 06:37:43 max Exp $ *}
{capture name=menu}
<a href="{$catalogs.provider}/register.php?mode=update" class=VertMenuItems>{$lng.lbl_modify}</a><br />
<a href="{$catalogs.provider}/register.php?mode=delete" class=VertMenuItems>{$lng.lbl_delete}</a><br />
{if ($usertype eq 'A' || ($usertype eq 'P' && $active_modules.Simple_Mode)) && $is_merchant_password eq 'Y'}
<a class="VertMenuItems" href="{$catalogs.admin}/change_mpassword.php">{$lng.lbl_change_mpassword}</a><br />
{/if}
{/capture}
{ include file="menu.tpl" dingbats="dingbats_profil.gif" menu_title=$lng.lbl_your_profile menu_content=$smarty.capture.menu }
