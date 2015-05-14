{* $Id: welcome.tpl,v 1.28.2.1 2006/07/12 04:51:17 svowl Exp $ *}
{if ($active_modules.Greet_Visitor ne "") and ($smarty.cookies.GreetingCookie ne "") and $logout_user eq ''}
{assign var="_name" value=$smarty.cookies.GreetingCookie|replace:"\'":"'"}
<h3>{$lng.lbl_welcome_back|substitute:"name":$_name} </h3> 
{elseif $lng.lbl_site_title}
<h3>{$lng.lbl_welcome_to|substitute:"company":$lng.lbl_site_title}</h3>
{else}
<h3>{$lng.lbl_welcome_to|substitute:"company":$config.Company.company_name}</h3>
{/if}
{$lng.txt_welcome}
<br />
{if $active_modules.Bestsellers ne "" and $config.Bestsellers.bestsellers_menu ne "Y"}
{include file="modules/Bestsellers/bestsellers.tpl"}
{/if}
<br />
{include file="customer/main/featured.tpl" f_products=$f_products}
