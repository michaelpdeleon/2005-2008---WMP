{* $Id: location.tpl,v 1.14 2005/11/17 06:55:36 max Exp $ *}
<!--
{*php*}
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
{*/php*}
-->
{if $location}
<font class="NavigationPath">
<!-- Start addition by Michael de Leon 09.14.06 -->
{if $cat ne "0" || $cat ne ""}
	<div align="center">
	{assign var="subcat_rootparent" value=$current_category.categoryid_path|truncate:3:"":true}
	{if $subcat_rootparent ne ""}
		<img src="{$xcart_web_dir}/image.php?type=C&amp;id={$subcat_rootparent}" border="0">
<!-- Deleted by Michael de Leon 11.02.06
<img src="{* $xcart_web_dir *}/image.php?categoryid={* $subcat_rootparent *}" border="0">
-->
	{else}
		<IMG src="{$xcart_web_dir}/image.php?categoryid={$cat}&rand={$rand}{if $file_upload_data.file_path}&tmp=y{/if}" border="0">
	{/if}
	</div>
{/if}
<!-- End addition by Michael de Leon 09.14.06 -->
<!-- Deleted by Michael de Leon 09.14.06
{* strip *}
{* section name=position loop=$location *}
{* if $location[position].1 ne "" *}<a href="{$location[position].1|amp}" class="NavigationPath">{*/if*}
{* $location[position].0 *}
{* if $location[position].1 ne "" *}</a>{*/if*}
{* if not %position.last% *}&nbsp;::&nbsp;{*/if*}
{* /section *}
{* /strip *}
-->
</font>
<!-- Deleted by Michael de Leon 09.14.06
<br /><br />
-->
{/if}
