{* $Id: common_templates.tpl,v 1.46.2.4 2006/07/19 10:19:34 max Exp $ *}
{if $main eq "last_admin"}
{include file="main/error_last_admin.tpl"}

{elseif $main eq "product_disabled"}
{include file="main/error_product_disabled.tpl"}

{elseif $main eq "wrong_merchant_password"}
{include file="main/error_wrong_merchant_password.tpl"}

{elseif $main eq "product_in_cart_expired"}
{include file="main/error_product_in_cart_expired.tpl"}

{elseif $main eq "cant_open_file"}
{include file="main/error_cant_open_file.tpl"}

{elseif $main eq "profile_delete"}
{include file="main/profile_delete_confirmation.tpl"}

{elseif $main eq "profile_notdelete"}
{include file="main/profile_notdelete_message.tpl"}

{elseif $main eq "classes"}
{include file="modules/Feature_Comparison/classes.tpl"}

{elseif $main eq "help"}
{include file="help/index.tpl" section=$help_section}

{elseif $main eq "login_incorrect"}
{assign var="is_remember" value="Y"}
{include file="main/error_login_incorrect.tpl"}

{elseif $main eq "need_login"}
{assign var="is_remember" value="Y"}
{include file="main/error_login.tpl"}

{elseif $main eq "access_denied"}
{include file="main/error_access_denied.tpl"}

{elseif $main eq "giftreg_is_private"}
{include file="main/error_giftreg_is_private.tpl"}

{elseif $main eq "page_not_found"}
{include file="main/error_page_not_found.tpl"}

{elseif $main eq "error_no_shipping"}
{include file="main/error_no_shipping.tpl"}

{elseif $main eq "permission_denied"}
{include file="main/error_permission_denied.tpl"}

{elseif $main eq "delivery_error"}
{include file="main/error_delivery.tpl"}

{elseif $main eq "subscribe_exist_email" or $main eq "subscribe_bad_email"}
{include file="main/error_subscribe.tpl"}

{elseif $main eq "error_ccprocessor_unavailable"}
{include file="main/error_ccprocessor_unavail.tpl"}

{elseif $main eq "error_cmpi_error"}
{include file="main/error_cmpi_error.tpl"}

{elseif $main eq "error_ccprocessor_error"}
{include file="main/error_ccprocessor_error.tpl"}

{elseif $main eq "error_ccprocessor_notfound"}
{include file="main/error_ccprocessor_notfound.tpl"}

{elseif $main eq "error_ccprocessor_baddata"}
{include file="main/error_ccprocessor_baddata.tpl"}

{elseif $main eq "error_giftcert_notfound"}
{include file="main/error_giftcert_notfound.tpl"}

{elseif $main eq "error_giftcert_notenough"}
{include file="main/error_giftcert_notenough.tpl"}

{elseif $main eq "import_3x_4x" && $import_pass ne ''}
{include file="modules/Import_3x_4x/import_results.tpl"}

{elseif $main eq "import_3x_4x"}
{include file="modules/Import_3x_4x/import.tpl"}

{elseif $main eq "import_error"}
{include file="main/error_import_error.tpl"}

{elseif $main eq "order_delete_confirmation"}
{include file="main/order_delete_confirmation.tpl"}

{elseif $main eq "product_delete_confirmation"}
{include file="main/product_delete_confirmation.tpl"}

{elseif $main eq "orders"}
{include file="main/orders.tpl"}

{elseif $main eq "history_order"}
{include file="main/history_order.tpl"}

{elseif $main eq "product_modify"}
{include file="main/product_modify.tpl"}

{elseif $main eq "error_min_order"}
{include file="main/error_min_order.tpl"}

{elseif $main eq "error_max_order"}
{include file="main/error_max_order.tpl"}

{elseif $main eq "error_max_items"}
{include file="main/error_max_items.tpl"}

{elseif $main eq "error_already_voted"}
{include file="customer/main/error_already_voted.tpl"}

{elseif $main eq "error_review_exists"}
{include file="customer/main/error_review_exists.tpl"}

{elseif $main eq "edit_file"}
{include file="admin/main/edit_file.tpl"}

{elseif $main eq "edit_dir"}
{include file="admin/main/edit_dir.tpl"}

{elseif $main eq "patch"}
{include file="admin/main/patch.tpl"}

{elseif $main eq "editor_mode"}
{include file="admin/main/editor_mode.tpl"}

{elseif $main eq "insecure_login_form"}
{include file="main/insecure_login_form.tpl"}

{elseif $main eq "shipping_disabled"}
{include file="main/error_shipping_disabled.tpl"}

{elseif $main eq "realtime_shipping_disabled"}
{include file="main/error_realtime_shipping_disabled.tpl"}

{elseif $main eq "pages"}
{include file="customer/main/pages.tpl"}

{elseif $main eq "news_archive"}
{include file="modules/News_Management/news_archive.tpl"}

{elseif $main eq "news_lists"}
{include file="modules/News_Management/news_lists.tpl"}

{elseif $main eq "disabled_cookies"}
{include file="main/error_disabled_cookies.tpl"}

{elseif $main eq "demo_login_with_form"}
{include file="modules/Demo/login.tpl"}

{elseif $main eq "surveys"}
{include file="modules/Survey/surveys.tpl"}

{elseif $main eq "survey"}
{include file="modules/Survey/survey_modify.tpl"}

{else}
{include file="main/error_page_not_found.tpl"}

{/if}
