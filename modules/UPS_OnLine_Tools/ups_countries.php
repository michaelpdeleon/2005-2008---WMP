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
# $Id: ups_countries.php,v 1.4 2006/01/11 06:56:20 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

$ups_countries = array(
"AR" => "Argentina",
"AU" => "Australia",
"AT" => "Austria",
"BE" => "Belgium",
"BR" => "Brazil",
"CA" => "Canada",
"CL" => "Chile",
"CR" => "Costa Rica",
"DK" => "Denmark",
"DO" => "Dominican Republic",
"FI" => "Finland",
"FR" => "France",
"DE" => "Germany",
"GR" => "Greece",
"GT" => "Guatemala",
"HK" => "Hong Kong",
"IN" => "India",
"IE" => "Ireland",
"IL" => "Israel",
"IT" => "Italy",
"JP" => "Japan",
"LU" => "Luxembourg",
"MY" => "Malaysia",
"MX" => "Mexico",
"NL" => "Netherlands",
"NZ" => "New Zealand",
"NO" => "Norway",
"PA" => "Panama",
"PE" => "Peru",
"PH" => "Philippines",
"PT" => "Portugal",
"PR" => "Puerto Rico",
"RO" => "Romania",
#"RU" => "Russian Federation",
"SG" => "Singapore",
"ZA" => "South Africa",
"KR" => "South Korea",
"ES" => "Spain",
"SE" => "Sweden",
"CH" => "Switzerland",
"GB" => "United Kingdom (Great Britain)",
"US" => "United States",
"VI" => "United States Virgin Islands",
"VE" => "Venezuela"
);

$smarty->assign("ups_countries", $ups_countries);

?>
