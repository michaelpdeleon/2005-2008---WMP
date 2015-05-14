{* $Id: menu_admin.tpl,v 1.34 2005/11/17 06:55:36 max Exp $ *}
{capture name=menu}
<a href="{$catalogs.admin}/general.php" class="VertMenuItems">{$lng.lbl_summary}</a><br />
<a href="{$catalogs.admin}/db_backup.php" class="VertMenuItems">{$lng.lbl_db_backup_restore}</a><br />
<a href="{$catalogs.admin}/import.php" class="VertMenuItems">{$lng.lbl_import_export}</a><br />
<a href="{$catalogs.admin}/memberships.php" class="VertMenuItems">{$lng.lbl_membership_levels}</a><br />
<a href="{$catalogs.admin}/card_types.php" class="VertMenuItems">{$lng.lbl_credit_card_types}</a><br />
<a href="{$catalogs.admin}/titles.php" class="VertMenuItems">{$lng.lbl_titles}</a><br />
<a href="{$catalogs.admin}/file_edit.php" class="VertMenuItems">{$lng.lbl_edit_templates}</a><br />
<a href="{$catalogs.admin}/file_manage.php" class="VertMenuItems">{$lng.lbl_files}</a><br />
<a href="{$catalogs.admin}/configuration.php" class="VertMenuItems">{$lng.lbl_general_settings}</a><br />
<a href="{$catalogs.admin}/images_location.php" class="VertMenuItems">{$lng.lbl_images_location}</a><br />
<a href="{$catalogs.admin}/languages.php" class="VertMenuItems">{$lng.lbl_languages}</a><br />
<a href="{$catalogs.admin}/editor_mode.php" class="VertMenuItems">{$lng.lbl_webmaster_mode}</a><br />
<a href="{$catalogs.admin}/modules.php" class="VertMenuItems">{$lng.lbl_modules}</a><br />
<a href="{$catalogs.admin}/payment_methods.php" class="VertMenuItems">{$lng.lbl_payment_methods}</a><br />
<a href="{$catalogs.admin}/patch.php" class="VertMenuItems">{$lng.lbl_patch_upgrade}</a><br />
<a href="{$catalogs.admin}/html_catalog.php" class="VertMenuItems">{$lng.lbl_html_catalog}</a><br />
<a href="{$catalogs.admin}/pages.php" class="VertMenuItems">{$lng.lbl_static_pages}</a><br />
<a href="{$catalogs.admin}/speed_bar.php" class="VertMenuItems">{$lng.lbl_speed_bar}</a><br />
{/capture}
{ include file="menu.tpl" dingbats="dingbats_categorie.gif" menu_title=$lng.lbl_administration menu_content=$smarty.capture.menu }
