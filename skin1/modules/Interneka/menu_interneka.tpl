{* $Id: menu_interneka.tpl,v 1.6 2005/11/17 06:55:47 max Exp $ *}
{capture name=menu}
<!-- begin cut here -->
<a href="http://interneka.com/affiliate/AffiliateSignup.php?WID={$interneka_id6}">{$lng.lbl_interneka_click_to_register}</a>
<!--- end cut here -->
<br />
{/capture}
{include file="menu.tpl" dingbats="dingbats_affiliates.gif" menu_title=$lng.lbl_interneka_affiliates menu_content=$smarty.capture.menu}
