<?php
/*****************************************************************************\
+-----------------------------------------------------------------------------+
| X-Cart                                                                      |
| Copyright (c) 2001-2006 Ruslan R. Fazliev <rrf@rrf.ru>                      |
| All rights reserved.                                                        |
+-----------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION. THE AGREEMENT TEXT IS ALSO AVAILABLE  |
| AT THE FOLLOWING URL: http://www.x-cart.com/license.php                     |
|                                                                             |
| THIS  AGREEMENT  EXPRESSES  THE  TERMS  AND CONDITIONS ON WHICH YOU MAY USE |
| THIS SOFTWARE   PROGRAM   AND  ASSOCIATED  DOCUMENTATION   THAT  RUSLAN  R. |
| FAZLIEV (hereinafter  referred to as "THE AUTHOR") IS FURNISHING  OR MAKING |
| AVAILABLE TO YOU WITH  THIS  AGREEMENT  (COLLECTIVELY,  THE  "SOFTWARE").   |
| PLEASE   REVIEW   THE  TERMS  AND   CONDITIONS  OF  THIS  LICENSE AGREEMENT |
| CAREFULLY   BEFORE   INSTALLING   OR  USING  THE  SOFTWARE.  BY INSTALLING, |
| COPYING   OR   OTHERWISE   USING   THE   SOFTWARE,  YOU  AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE  ACCEPTING  AND AGREEING  TO  THE TERMS OF THIS |
| LICENSE   AGREEMENT.   IF  YOU    ARE  NOT  WILLING   TO  BE  BOUND BY THIS |
| AGREEMENT, DO  NOT INSTALL OR USE THE SOFTWARE.  VARIOUS   COPYRIGHTS   AND |
| OTHER   INTELLECTUAL   PROPERTY   RIGHTS    PROTECT   THE   SOFTWARE.  THIS |
| AGREEMENT IS A LICENSE AGREEMENT THAT GIVES  YOU  LIMITED  RIGHTS   TO  USE |
| THE  SOFTWARE   AND  NOT  AN  AGREEMENT  FOR SALE OR FOR  TRANSFER OF TITLE.|
| THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY GRANTED BY THIS AGREEMENT.      |
|                                                                             |
| The Initial Developer of the Original Code is Ruslan R. Fazliev             |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2001-2006           |
| Ruslan R. Fazliev. All Rights Reserved.                                     |
+-----------------------------------------------------------------------------+
\*****************************************************************************/

#
# $Id: install_lng_US.php,v 1.24.2.5 2006/08/09 07:39:59 max Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$install_lng_defs["US"] = array("name" => "English", "charset" => "iso-8859-1");

#
# Declaration of $install_languages for English language
#
$install_languages["US"] = array (
	"status_on" => "On",
	"status_off" => "Off",
	"status_ok" => "OK",
	"status_failed" => "FAILED",
	"fatal_error" => "Fatal error: {{error}}.<br />Please correct the error(s) before proceeding to the next step.",
	"warning" => "Warning: {{warning}}",
	"customer_area" => "CUSTOMER FRONT-END",
	"admin_area" => "ADMINISTRATOR BACKOFFICE",
	"provider_area" => "BACKOFFICE FOR PRODUCT PROVIDERS",
	"partner_area" => "PARTNERS BACKOFFICE",
	"username" => "Username",
	"password" => "Password",
	"install_complete" => "Installation complete.",
	"new_install" => "New installation",
	"reinstall_skins" => "Re-install skin files",
	"uninstall_module" => "Uninstall the module",
	"auth_code" => "Auth code",
	"auth_code_note" => "This is a protection from unauthorized<br /> use of installation script",
	"wrong_auth_code" => "Wrong auth code! You can not proceed.",
	"i_accept_license" => "I accept the License Agreement",
	"thank_you" => "Thank you for choosing {{product}}.<br />This wizard will provide you with the installation instructions and will handle most of the installation tasks for you.",
	"push_next_button" => "Push the \"Next\" button below to continue",
	"push_next_button_to_install" => "Push the \"Next\" button below to begin the installation",
	"button_back" => "&lt; Back",
	"button_next" => "Next &gt;",
	"lbl_yes" => "Yes",
	"lbl_no" => "No",
	"no_license_file" => "License agreement file is not found. Installation aborted",
	"select_language_prompt" => "Please select language for installation wizard",
	# install.php modules
	"install_wiz" => "{{product}} Installation Wizard",
	"install_step" => "Step {{num}}: {{comment}}",
	"mod_language" => "Selecting installation language",
	"mod_license" => "License agreement",
	"mod_license_alert" => "It is necessary to agree to the terms of X-Cart License Agreement to be able to continue the installation. If you do not wish to be bound by this agreement, do not install the sofware.",
	"mod_check_cfg" => "Checking PHP configuration",
	"mod_cfg_install_db" => "Preparing to install X-Cart database",
	"mod_install_db" => "Installing X-Cart database",
	"mod_cfg_install_dirs" => "Color and layout settings",
	"mod_install_dirs" => "Setting up templates",
	"mod_cfg_enable_paypal" => "PayPal payment processing",
	"mod_enable_paypal" => "Enabling PayPal payment processing",
	"mod_install_done" => "Installation complete",
	"mod_generate_snapshot" => "Generating a system fingerprint",
	# module_check_cfg
	"cheking_results" => "Checking results",
	"critical_dependencies" => "Critical dependencies",
	"non_critical_dependencies" => "Non critical dependencies",
	"status" => "Status",
	"php_ver_min" => "PHP Version (min {{version}} required)",
	"php_safe_mode_is" => "PHP Safe mode is",
	"pcre_extension_is" => "PCRE extension is",
	"php_disabled_funcs" => "Disabled functions list",
	"php_disabled_funcs_none" => "none",
	"php_fileuploads_is" => "File uploads is",
	"php_mysql_support_is" => "MySQL support is",
	"php_upload_maxsize_is" => "Maximum file size for upload is",
	"php_register_long_arrays_is" => "Register long arrays is",
	"access_perm_note" => "Before the installation starts, please ensure that you have properly configured file access permissions (UNIX only):",

	# cfg_install_db
	"install_web_mysql" => "The Installation Wizard needs to know your web server details and MySQL database information",
	"install_http_name" => "<b>Server host name</b><br />Host name of your server (e.g. www.mywebstore.com)",
	"install_https_name" => "<b>Secure server host name</b><br />Host name of your secure (HTTPS) server (e.g. secure.mywebstore.com)",
	"install_webdir" => "<b>X-Cart web directory</b><br />Web directory where X-Cart files are located (e.g. /xcart)",
	"install_mysqlhost" => "<b>MySQL host name</b><br />Host name of MySQL server. It can be host name or IP address",
	"install_mysqlhost_alert" => "You must enter MySQL host name",

	"install_mysqluser" => "<b>MySQL user name</b><br />The name of the MySQL user",
	"install_mysqluser_alert" => "You must enter MySQL user name",

	"install_mysqldb" => "<b>MySQL database name</b><br />The name of the database you connect to",
	"install_mysqldb_alert" => "You must enter MySQL database name",
	#
	"install_mysqlpass" => "<b>MySQL password</b><br />Which password to use for MySQL",
	"install_email" => "<b>Your e-mail address</b><br />This address will be used as default for company options",
	"install_languages" => "<b>Languages</b><br />Languages you want to install (use Ctrl key to select multiple options)",
	"install_states" => "<b>States table</b><br />States of the country where the shop is located (use Ctrl key to select multiple options)",
	"install_demodata" => "<b>Sample categories/products</b><br />Would you like to setup sample categories and products?",
	"install_configuration" => "<b>Configuration settings</b><br />Apply pre-configured settings to selected country",
	"install_update_config" => "<b>Update config.php only</b><br />Tick this if you want to skip database setup (no data will be installed in the database)",
	"install_store_images_in" => "<b>Store images in</b><br />Select where you want to store your images",
	"install_store_images_db" => "Database",
	"install_store_images_fs" => "File system",
	"moving_images_to_fs" => "Moving images to the file system",
	#
	"error_connect" => "Can't connect to the MySQL server. Press 'BACK' button and check database info again",
	"error_select_db" => "Installer couldn't find database \"{{db}}\". You should ask your system administrator to create one or choose another name",
	"error_check_write_config" => "Cannot open file \"config.php\" for writing. You should set UNIX permissions for file \"config.php\" to 0666",
	"warning_db_tables_exists" => "Installation Wizard has found existing X-Cart tables in your database. If you continue, they will be deleted.",

	"updating_config_file" => "Updating config.php file... ",
	"error_cannot_open_config" => "Can't open file \"config.php\" for reading\\writing",
	"upload_cannot_open" => "Uploading file '{{file}} : {{status}} Cannot open file<br />'",

	"fatal_error_install_db" => "Fatal error occured while installing database.<br />Please, check all again and restart installation. Maybe you have not enought rights to that database",

	"error_unexp_connect" => "Cannot connect to MySQL server. This is unexpected error, so please start installation again.",

	"error_unexp_select_db" => "Couldn't find database \"{{db}}\". This is unexpected error, so please start installation again.",

	"error_unexp_check_write_config" => "Cannot open file \"config.php\" for writing. This is unexpected error, so please start installation again.",

	"creating_tables" => "Creating tables...",
	"creating_table" => "Creating table: [{{table}}] ... ",

	"importing_data" => "Importing data...",
	"importing_languages" => "Importing languages...",
	"importing_states" => "Importing states...",

	"importing_demodata" => "Setting up sample categories and products...",

	"fatal_error_config_update" => "Fatal error occured while updating config.php file<br />Please, check permissions and restart installation.",

	"please_wait" => "Please wait ...",

	# cfg_install_dirs
	"select_color_n_layout" => "Select color/layout",
	"select_layout" => "<b>Layout</b><br />Select shop layout",
	"select_color" => "<b>Color scheme</b><br />Select color scheme",
	"select_dingbats" => "<b>Dingbats</b><br />Select dingbats set",
	"color_scheme" => "Color scheme",
	"recommended_dingbats" => "Recommended dingbat sets",

	# install_dirs
	"creating_directories" => "Creating directories...",
	"creating_directory" => "Creating directory: [{{dir}}] ... ",
	"dir_already_exists" => "Already exists",
	"warn_file_create_failed" => "Creating of file '{{file}}' is failed",
	"copying_file_from_to" => "Copying {{src}} to {{dst}}",
	"copying_to_file" => "Copying to file {{dst}}",
	"copying_directory" => "Copying directory: {{dir}} - {{status}}",
	"copying_templates" => "Copying templates...",
	"removing_directory" => "Removing directory: {{dir}}",
	"removing_file" => "Removing file {{file}}",

	"creating_layout" => "Creating layout...",
	"copying_color_scheme" => "Copying color scheme files...",
	"copying_dingbats" => "Copying color scheme files...",
	"error_creating_directories" => "Fatal error occured while creating directories. Please check permissions and try again",

	"color_layout_preview" => "Color/layout preview",
	"click_to_refresh" => "click to refresh",

	"default" => "default",

	# module_generate_snapshot
	"txt_begin_generating_snapshot" => "Generating the system fingerprint.<br />This may take several minutes, please be patient...<br />",
	"msg_snapshot_generated" => "System fingerprint is successfully generated",
	"txt_N_unprocessed_files_in_snapshot" => "<br /><font color=\"red\">Warning! {{unproc}} files out of {{total}} are ignored (cannot be read)</font><br />",
	"installation_snapshot" => "Installation system fingerprint",
	"err_snpst_write_file" => "System fingerprint file cannot be created: permission is denied",
	"err_snpst_no_files" => "System fingerprint file cannot be created: no files found",

	# mod_cfg_enable_paypal
	"paypal_question" => "Do you wish to enable PayPal payment processing now?",

	# mod_enable_paypal
	"install_web_paypal" => "The Installation Wizard needs to know the email address you wish to use for PayPal registration or the email address your current PayPal account is registered for",
	"install_paypal_account" => "<b>E-Mail address for PayPal</b><br />leave blank, if you do not wish to configure PayPal at this time",
	"install_web_paypal_comment" => "<p>After you click on 'Next' a verification message will be sent to the email address specified here. Please follow the web link in this message to enable PayPal payments in your store.</p><p><b>Notes:</b><ol><li>Please make sure the spam filter on this mail box is configured to accept notifications from PayPal &amp; X-Cart.</li><li>PayPal and other payment gateways can be configured on the \"Payment methods\" page in X-Cart administrator backoffice at any time later.</li></p>",

	# final page
	"install_paypal_mail_note" => "A verification message with the necessary instructions was sent to the email address you specified for PayPal account.",
	"auth_code_for_future" => "Auth code (for accessing install.php in future): <b>{{code}}</b>",
	"blowfish_key" => "Blowfish key: <b>{{key}}</b>",
	"distribution_warning" => "<b>Warning!</b> We strongly recommend you to remove {{product}} distribution package from your web directory to prevent unauthorized access to {{product}} source code.",
	"xcart_final_note" => "<br /><br />You can use install.php to change skin set or to completely reinstall X-Cart. If you do not need this you can delete install.php installation script.<br /><br />Before you proceed to using X-Cart, please make sure that you setup secure file permissions:<br />chmod 644 config.php<br />chmod 755 .<br /><br />X-Cart has been successfully installed at the following URLs:<br />",
	"final_email_message" => '
<center>
<h3>Installation complete.</h3>
</center>
<p>
Congratulations! {{product}} e-commerce software has been successfully installed.<br />
<p>
You can access your {{product}}-based store at the following URLs:<br />
{{interfaces}}
<p>
Before you proceed to using {{product}}, please restore secure permissions for the file config.php and your {{product}} installation directory. Issue the commands:<br />
&nbsp;&nbsp;&nbsp;chmod 644 config.php<br />
&nbsp;&nbsp;&nbsp;chmod 755 .<br />
<p>
<b>IMPORTANT!</b> We strongly recommend you remove {{product}} distribution package from your web directory to prevent unauthorized access to {{product}} source code.<br />
<p>
You can use install.php to change the skin set or to completely re-install {{product}}. If you think you are not going to need this, you can delete the install.php installation script.<br />
<p>
In the process of installation the following security keys have been generated:<br /><br />
<ul>
<li>Auth code: <b>{{installation_auth_code}}</b><br />
<li>Blowfish key: <b>{{blowfish_key}}</b><br />
</ul>
<p>
<b>Note:</b><br />
Auth code will be used to prevent unauthorized access to {{product}} installation script install.php. If, in the future, you decide to completely re-install {{product}}, change your store\'s skin set or install some {{product}} add-ons, you will be required to enter this code at the time of installation. Please beaware that this code is stored for you in include/install.php.<br />
<p>
Blowfish key will be used to encrypt all sensitive data in your store including user passwords, order details, etc. This code is supposed to be stored permanently in config.php (the variable $blowfish_key). Please DO NOT change this key manually.<br />
',
	# some modules messages
	"removing_skin_files" => "Removing skin files ...",
	"deactivating_module" => "Deactivating the module ...",
	"copying_skin_files" => "Copying skin files ...",
	"activating_module" => "Activating the module ...",
	"module_installed" => "{{name}} module has been successfully installed",
	"module_final_msg" => "Before you proceed to use {{name}} module, please remove the {{script}} installation script.",
	"module_uninstalled" => "{{name}} module has been successfully uninstalled",
	"mod_modinstall" => "Installing and configuring the module",
	"mod_moduninstall" => "Uninstalling the module",
	"mod_moduninstall_done" => "Uninstallation complete",
	# install-x* labels
	"xaff_admin_note" => "New menu item called 'Affiliates' will appear in your admin interface. Use it to set up commision plans, manage affiliates etc.",
	"xaff_partner_note" => "Using this URL your partners can register, upload new banners, view stats etc.",
	"xaom_admin_note" => "( \"Orders\" :: \"Order details\" =&gt; \"Modify\" )",
	"xfancycat_admin_note" => "( \"Administration\" :: \"Modules\" )<br />( \"Administration\" :: \"General settings\" )",
	"xgiftreg_customer_note" => "( \"Gift Registry\" menu)",
	"xpconf_provider_note" => "( \"Products\" :: \"Product Configurator\" )",
	"modules_admin_note" => "( \"Administration\" :: \"Modules\" )"
);

?>
