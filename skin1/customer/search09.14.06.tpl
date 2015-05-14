{* $Id: search.tpl,v 1.9 2005/11/17 06:55:37 max Exp $ *}
<form method="post" action="search.php" name="productsearchform">
<input type="hidden" name="simple_search" value="Y" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="posted_data[by_title]" value="Y" />
<input type="hidden" name="posted_data[by_shortdescr]" value="Y" />
<input type="hidden" name="posted_data[by_fulldescr]" value="Y" />
<table cellpadding="0" cellspacing="0">  
<tr> 
	<td class="TopLabel" style="padding-left: 20px; padding-right: 5px;">{$lng.lbl_search}:</td>
	<td valign="middle"><input type="text" name="posted_data[substring]" size="16" value="{$search_prefilled.substring|escape}" /></td>
	<td valign="middle" style="padding-left: 5px; padding-right: 20px;"><a href="javascript: document.productsearchform.submit();">{include file="buttons/search_head.tpl"}</a></td>
	<td><a href="search.php"><u>{$lng.lbl_advanced_search}</u></a></td>
</tr>
</table>
</form>
