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
# $Id: config.php,v 1.409.2.1 2006/04/12 13:26:06 max Exp $
#
# Configuration settings
#

if (!defined('XCART_START')) { header("Location: index.php"); die("Access denied"); }

#
# SQL database details
#
# This section sets up a connection between X-Cart shopping cart software
# and your MySQL database. If X-Cart is installed using Web installation, the
# variables of this section are configured via the Installation Wizard. If you
# install X-Cart manually, or if, after X-Cart has been installed, your MySQL
# server information changes, use this section to provide database access
# information manually.
#
# $sql_host - DNS name or IP of your MySQL server;
# $sql_user - MySQL user name;
# $sql_db - MySQL database name;
# $sql_password - MySQL password.
#
$sql_host ='localhost';
$sql_user ='root';
$sql_db ='wwmpon2_xcart3';
$sql_password ='root';

#
# X-Cart HTTP & HTTPS host and web directory
#
# This section defines the location of your X-Cart installation. If X-Cart is 
# installed using Web installation, the variables of this section are 
# configured via the Installation Wizard. If you install X-Cart manually, use 
# this section to provide your web server details manually.
#
# $xcart_http_host - Host name of the server on which your X-Cart software is 
# to be installed;
# $xcart_https_host - Host name of the secure server that will provide access
# to your X-Cart-based store via the HTTPS protocol;
# $xcart_web_dir - X-Cart web directory.
#
# NOTE:
# The variables $xcart_http_host and $xcart_https_host must contain hostnames 
# ONLY (no http:// or https:// prefixes, no trailing slashes).
#
# Web dir is the directory where your X-Cart is installed as seen from the Web,
# not the file system.
#
# Web dir must start with a slash and have no slash at the end. An exception to
# this rule is when you install X-Cart in the site root, in which case you need
# to leave the variable empty.
#
# EXAMPLE 1:
# $xcart_http_host ="www.yourhost.com";
# $xcart_https_host ="www.securedirectories.com/yourhost.com";
# $xcart_web_dir ="/xcart";
# will result in the following URLs:
# http://www.yourhost.com/xcart
# https://www.securedirectories.com/yourhost.com/xcart
#
# EXAMPLE 2:
# $xcart_http_host ="www.yourhost.com";
# $xcart_https_host ="www.yourhost.com";
# $xcart_web_dir ="";
# will result in the following URLs:
# http://www.yourhost.com/
# https://www.yourhost.com/
#
$xcart_http_host ="localhost:8888/MDL/WWMPonline.com1";
$xcart_https_host ="localhost:8888/MDL/WWMPonline.com1";
$xcart_web_dir ="";

# Storing Customers' Credit Card Info
#
# The variable $store_cc defines whether you want the credit card info provided
# by your customers at checkout to be stored in the database or not. 
# The credit card info that can be stored includes:
# - Cardholder's name;
# - Card type;
# - Card number;
# - Valid from (for certain card types);
# - Exp. date;
# - Issue No (for certain card types).
#
# Admissible values for $store_cc are 'true' and 'false':
# 'true' - X-Cart will store your customers' credit card info in the order
# details and user profiles;
# 'false' - X-Cart will not store your customers' credit card info anywhere.
#
# NOTE:
# If you are going to use 'Subscription' module, set $store_cc to 'true'.
#
$store_cc = true;

# Storing CVV2 codes
#
# The variable $store_cvv2 defines whether you want the CVV2 codes of your
# customers' credit cards to be stored in the database or not.
#
# Admissible values for $store_cvv2 are 'true' and 'false':
# 'true' - X-Cart will store the CVV2 codes of your customers' credit cards
# in the order details and user profiles;
# 'false' - X-Cart will not store the CVV2 codes of your customers' credit
# cards anywhere.
#
# NOTE:
# VISA International does not recommend storing CVV2 codes along with credit
# card numbers.
# If you are going to use 'Subscription' module, set $store_cvv2 to 'true'.
#
$store_cvv2 = false;

# Storing Customers' Checking Account Details 
#
# The variable $store_ch defines whether you want your customers checking
# account details to be stored in the database or not.
# The checking account details that can be stored include: 
# - Bank account number;
# - Bank routing number;
# - Fraction number.
#
# If Direct Debit is used then Account owner name is stored instead of Fraction number.
#
# Admissible values for $store_ch are 'true' and 'false':
# 'true' - X-Cart will store your customers' checking account details in the
# order details;
# 'false' - X-Cart will not store your customers' checking account details 
# anywhere.
#
$store_ch = true;

#
# Default images
#
# The variable $default_image defines which image file should be used as the
# default "No image available" picture (a picture that will appear in the
# place of any missing image in your X-Cart-based store if no other "No image
# available"-type picture is defined for that case).
#
$default_image = "default_image.gif";

#
# The variable $shop_closed_file defines which HTML page should be displayed
# to anyone trying to access the Customer zone of your store when the store is
# closed for maintenance.
#
$shop_closed_file = "shop_closed.html";

#
# Single Store mode (X-Cart PRO only)
#
# The variable $single_mode allows you to enable/disable Single Store mode if
# your store is based on X-Cart PRO. Single Store mode is an operation mode in
# which your store represents a unified environment shared by multiple
# providers in such a way that any provider can edit the products of the other
# providers, and shipping rates, discounts, taxes, discount coupons, etc are
# the same for all the providers.
#
# Admissible values for $single_mode are 'true' and 'false':
# 'true' - enables Single Store mode;
# 'false' - puts your store into normal mode where each of your providers can
# control his own products only and can have shipping rates, discounts, taxes,
# etc different from those of the other providers.
#
# NOTE:
# If your store is based on X-Cart GOLD, $single_mode must be set to 'true' at
# all times.
$single_mode = true;

#
# FedEx Rates directory
#
#The variable $fedex_default_rates_dir defines the location of the directory
# where files for the calculation of FedEx shipping rates are stored.
#
$fedex_default_rates_dir = $xcart_dir."/shipping/FedEx/";

#
# Temporary directories
#
$var_dirs = array (
	"tmp" => $xcart_dir."/var/tmp",
	"templates_c" => $xcart_dir."/var/templates_c",
	"upgrade" => $xcart_dir."/var/upgrade"
);

$var_dirs_web = array (
);

#
# Log directory
#
# The variable $var_dirs["log"] defines the location of the directory where X-Cart log
# files are stored.
#
$var_dirs["log"] = $xcart_dir."/var/log";

#
# Cache directory
#
# The variable $var_dirs["cache"] defines the location of the directory where
# X-Cart cache files are stored.
#
$var_dirs["cache"] = $xcart_dir."/var/cache";
$var_dirs_web["cache"] = "/var/cache";

#
# Export directory
#
# The variable $export_dir defines the location of X-Cart export directory
# (a directory on X-Cart server to which the CSV files of export packs are
# stored).
#
$export_dir = $var_dirs["tmp"];

#
#
# DO NOT CHANGE ANYTHING BELOW THIS LINE UNLESS
# YOU REALLY KNOW WHAT ARE YOU DOING
#
#

#
# Comma separated list of IP for access to admin area
# Leave empty for unrestricted access.
# E.g.:
#   1) access is unrestricted:
#      $admin_allowed_ip = "";
#   2) access allowed only from IP 192.168.0.1 and 127.0.0.1:
#      $admin_allowed_ip = "192.168.0.1, 127.0.0.1";
#
$admin_allowed_ip = "";

#
# Automatic repair of the broken indexes in mySQL tables
#
$mysql_autorepair = true;

#
# Caching
#
# The constant USE_DATA_CACHE defines whether you want to use data caching in 
# your store.
# Admissible values for USE_DATA_CACHE are 'true' and 'false'.
# By default, the value of this constant is set to 'true'. You can set it to 
# 'false' if you experience problems using the store with caching enabled 
# (for example, if you get some kind of error regarding a file in the /var/cache 
# directory of your X-Cart installation).
#
define("USE_DATA_CACHE", true);

############################################################
# THE ERRORS TRACKING CODE
############################################################
#
# Turning on/off the debug mode
# 0 - no debug info;
# 1 - display error (and exit script - for SQL errors);
# 2 - write errors to the log file (templates_c/xerrors.log)
# 3 - display error and write it to the log file.
#
$debug_mode = 3;

#
# Error reporting level:
#
if ($debug_mode)
	$x_error_reporting = E_ALL ^ E_NOTICE;
else
	$x_error_reporting = 0;

############################################################
# / THE ERRORS TRACKING CODE
############################################################

#
# Demo mode - protects the pages essential for the functioning of X-Cart
# from potentially harmful modifications
#
$admin_safe_mode = false;

#
# HTTP & HTTPS locations
#
$http_location = "http://$xcart_http_host".$xcart_web_dir;
$https_location = "https://$xcart_https_host".$xcart_web_dir;

#
# Files directory
#
$files_dir = "/files";
$files_webdir = "/files";

#
# Templates repository
# where original templates are located for "restore" facility
#
$templates_repository_dir = "/skin1_original";

#
# Store sessions data in database
#
#
# Select the sessions mechanism:
# 1 - PHP sessions data is stored on the file system
# 2 - PHP sessions data is stored on the MySQL database
# 3 - X-Cart internal sessions mechanism is used (highly recommended)
$use_sessions_type = 3;

#
# Set the session name here
#
$XCART_SESSION_NAME = "xid";

#
# Session duration (in seconds)
#
$use_session_length = 3600;

#
# Search by separate words
#
# Maximum number of words that can be searched for when search by separate
# words is enabled
# (Expressions enclosed in double-quote marks are treated as single words)
#
$search_word_limit = 10;

#
# Minimum word length (minimum number of significant characters a word must
# have to be considered a word) when search by separate words is enabled
#
$search_word_length_limit = 2;

#
# Skin configuration file
#
$skin_config_file = "skin1.conf";

#
# Anonimous user name
#
$anonymous_username_prefix="anonymous";

#
# Anonymous user password
#
$anonymous_password="";

#
# License
#
$license ='';


################################################################################
# NEVER CHANGE THE SETTINGS BELOW THIS LINE MANUALLY
################################################################################

#
# The variable $blowfish_key contains your Blowfish encryption key automatically
# generated by X-Cart during installation. This key is used to encrypt all the
# sensitive data in your store including user passwords, credit card data, etc. 
#
# NEVER try to change your Blowfish encryption key by editing the value  of the
# $blowfish_key variable in this file: your data is already encrypted with this
# key and X-Cart needs exactly the same key to be able to decrypt it. Changing
# $blowfish_key manually will corrupt all the user passwords (including the
# administrator's password), so you will not be able to use the store. 
#
# Please be aware that a lost Blowfish key cannot be restored, so X-Cart team
# will not be able to help you regain access to your store if you remove or
# change the value of $blowfish_key.
#
# It is quite safe to use X-Cart with the Blowfish key generated during
# installation; however, if you still want to change it, please refer to
# X-Cart Reference Manual or contact X-Cart Tech Support for details.
#
#$blowfish_key ='';
$blowfish_key ='';

#
# WARNING :
# Please ensure that you have no whitespaces or empty lines below this message.
# Adding a whitespace or an empty line below this line will cause a PHP error.
#
?>
