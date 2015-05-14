{* $Id: paypal_enable.tpl,v 1.3 2006/03/31 05:51:43 svowl Exp $ *}
{config_load file="$skin_config"}

{$lng.eml_paypal_enable|substitute:"admin_url":$catalogs.admin:"paypal_enable_id":$paypal_enable_id}

{include file="mail/html/signature.tpl"}
