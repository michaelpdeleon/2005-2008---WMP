{if $smarty.get.mode eq "subscribed"}
{include file="main/subscribe_confirmation.tpl"}

{elseif $smarty.get.mode eq "unsubscribed"}
{include file="main/unsubscribe_confirmation.tpl"}

{elseif $main eq "returns"}
{include file="modules/RMA/returns.tpl"}

{elseif $main eq "register"}
{include file="customer/main/register.tpl"}

{elseif $main eq "download"}
{include file="modules/Egoods/main.tpl"}

{elseif $main eq "send_to_friend"}
{include file="customer/main/send_to_friend.tpl"}

{elseif $main eq "pages"}
{include file="customer/main/pages.tpl"}

{elseif $main eq "manufacturers_list"}
{include file="modules/Manufacturers/customer_manufacturers_list.tpl"}

{elseif $main eq "manufacturer_products"}
{include file="modules/Manufacturers/customer_manufacturer_products.tpl"}

{elseif $main eq "search"}
{include file="customer/main/search_result.tpl"}

{elseif $main eq "advanced_search"}
{include file="customer/main/search_result.tpl"}

{elseif $main eq "cart"}
{include file="customer/main/cart.tpl"}

{elseif $main eq "comparison" && $active_modules.Feature_Comparison ne ''}
{include file="modules/Feature_Comparison/comparison.tpl"}

{elseif $main eq "choosing" && $active_modules.Feature_Comparison ne ''}
{include file="modules/Feature_Comparison/choosing.tpl"}

{elseif $main eq "wishlist"}
{if $active_modules.Wishlist ne ""}
{include file="modules/Wishlist/wishlist.tpl"}
{/if}

{elseif $main eq "anonymous_checkout"}
{include file="customer/main/anonymous_checkout.tpl"}

{elseif $main eq "order_message"}
{include file="customer/main/order_message.tpl"}

{elseif $main eq "checkout"}
{include file="customer/main/checkout.tpl"}

{elseif $main eq "product"}
{include file="customer/main/product.tpl" product=$product}

{elseif $main eq "giftcert"}
{include file="modules/Gift_Certificates/giftcert.tpl"}

{elseif $main eq "subscriptions"}
{include file="modules/Subscriptions/subscriptions.tpl"}

{elseif $main eq "catalog" and $current_category.category eq ""}
{include file="customer/main/aboutus.tpl" f_products=$f_products}

{elseif $main eq "catalog"}
{include file="customer/main/subcategories.tpl" cat=$cat}

{elseif $active_modules.Gift_Registry ne "" and $main eq "giftreg"}
{include file="modules/Gift_Registry/giftreg_common.tpl"}

{elseif $main eq "product_configurator"}
{include file="modules/Product_Configurator/pconf_common.tpl"}

{elseif $main eq "secure_login_form"}
{include file="customer/main/secure_login_form.tpl"}

{elseif $main eq "change_password"}
{include file="customer/main/change_password.tpl"}

{elseif $main eq "customer_offers"}
{include file="modules/Special_Offers/customer/offers.tpl"}

{elseif $main eq "customer_bonuses"}
{include file="modules/Special_Offers/customer/bonuses.tpl"}

{elseif $main eq "survey"}
{include file="modules/Survey/customer_survey.tpl"}

{elseif $main eq "surveys"}
{include file="modules/Survey/customer_surveys.tpl"}

{elseif $main eq "view_message"}
{include file="modules/Survey/customer_view_message.tpl"}

{elseif $main eq "view_results"}
{include file="modules/Survey/customer_view_results.tpl"}

{else}
{include file="common_templates.tpl"}
{/if}
