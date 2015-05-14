{* $Id: bestsellers.tpl,v 1.8 2005/11/21 12:42:00 max Exp $ *}
{if $bestsellers}
{capture name=bestsellers}
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
<!-- Start addition by Michael de Leon 09.15.06 -->
{section name=bestsellers_num loop=$bestsellers}
{ if %bestsellers_num.first% }
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
{/if}
{ if %bestsellers_num.index% is div by 5}
	<tr>
{/if}
		<td align="left" valign="top" width="100">
{if $location[1].1|replace:"home.php?cat=":"" eq  436}
{* Food Science *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Food_IndustryLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br/>
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  342}
{* General Practice *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="General_PracticeLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  259}
{* Gloves *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="GlovesLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  378}
{* Infection Control *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Infection_ControlLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  384}
{* Laboratory Consumables *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Lab_ConsumablesLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  273}
{* Shipping Materials *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Shipping_MaterialsLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  478}
{* Janitorial Supplies *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Janitorial_SuppliesLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  505}
{* BioExcell *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="BioExcellLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{else}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="BestsellerLink">{include file="product_thumbnail.tpl" productid=$bestsellers[bestsellers_num].productid image_x=70 image_y=70 product=$bestsellers[bestsellers_num].product}<br /><br />
<b>{$bestsellers[bestsellers_num].product}</b></A>
{/if}
		</td>
{ if %bestsellers_num.last% }
	</tr>
</table>
{/if}
{/section}

{section name=bestsellers_num loop=$bestsellers}
{ if %bestsellers_num.first% }
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
{/if}
{ if %bestsellers_num.index% is div by 5}
	<tr>
{/if}
		<td align="left" valign="top" width="100">
{if $location[1].1|replace:"home.php?cat=":"" eq  436}
{* Food Science *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Food_IndustryLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  342}
{* General Practice *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="General_PracticeLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  259}
{* Gloves *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="GlovesLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  378}
{* Infection Control *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Infection_ControlLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  384}
{* Laboratory Consumables *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Lab_ConsumablesLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  273}
{* Shipping Materials *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Shipping_MaterialsLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{elseif $location[1].1|replace:"home.php?cat=":"" eq  478}
{* Janitorial Supplies *}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="Janitorial_SuppliesLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{else}
<A href="product.php?productid={$bestsellers[bestsellers_num].productid}&cat={$cat}&bestseller" class="BestsellerLinkPrice"><b>{$lng.lbl_our_price}:<br />{include file="currency.tpl" value=$bestsellers[bestsellers_num].price}</b>
</A>
{/if}
		</td>
{ if %bestsellers_num.last% }
	</tr>
</table>
{/if}
{/section}
		</td>
		<td valign="top" align="left"><img src="skin1/images/bestsellers.jpg"></td>
	</tr>
</table>
{/capture}
{if $location[1].1|replace:"home.php?cat=":"" eq  436}
{* Food Science *}
{include file="dialog_food_science.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  342}
{* General Practice *}
{include file="dialog_generalpractice.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  259}
{* Gloves *}
{include file="dialog_gloves.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  378}
{* Infection Control *}
{include file="dialog_infection_control.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  384}
{* Laboratory Consumables *}
{include file="dialog_clinical.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  273}
{* Shipping Materials *}
{include file="dialog_shipping_materials.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{elseif $location[1].1|replace:"home.php?cat=":"" eq  478}
{* Janitorial Supplies *}
{include file="dialog_janitorial_supplies.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{else}
{include file="dialog.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra="width=100%"}
{/if}
{/if}
<!-- End addition by Michael de Leon 09.15.06 -->
<!-- Deleted by Michael de Leon 09.15.06
{* if $bestsellers *}
{* capture name=bestsellers *}
<table cellpadding="0" cellspacing="2">
{* foreach from=$bestsellers item=bestseller *}
<tr>
{* if $config.Bestsellers.bestsellers_thumbnails eq "Y" *}
	<td width="30">
	<a href="product.php?productid={$bestseller.productid}&cat={$cat}&bestseller">{include file="product_thumbnail.tpl" productid=$bestseller.productid image_x=25 product=$bestseller.product}</a>
	</td>
{*/if*}
	<td>
	<b><a href="product.php?productid={$bestseller.productid}&amp;cat={$cat}&amp;bestseller">{$bestseller.product}</a></b><br />
{$lng.lbl_our_price}: {include file="currency.tpl" value=$bestseller.price}<br />
	</td>
</tr>
{*/foreach*}
</table>
{*/capture*}
{*include file="dialog.tpl" title=$lng.lbl_bestsellers content=$smarty.capture.bestsellers extra='width="100%"'*}
{*/if*}
-->