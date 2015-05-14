{* $Id: main.tpl,v 1.5 2004/05/06 11:40:19 svowl Exp $ *}
{section name=product loop=$products}
{sectionelse}
{$lng.txt_no_products_in_cat}
{/section}
