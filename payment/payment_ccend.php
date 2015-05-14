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
# $Id: payment_ccend.php,v 1.26 2006/01/11 06:56:23 mclap Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

x_load('crypt');

require($xcart_dir."/payment/payment_ccmid.php");

x_session_register("cart");

if ($bill_error) {
	$request = $current_location.DIR_CUSTOMER."/error_message.php?".$sessurl."error=".$bill_error.$reason;
}
else {
	# If successful - Store CC number in database
	if ($store_cc && !empty($card_type) && !empty($card_number) && !empty($card_expire) && !empty($login)) {
		$query_data = array(
			"card_name" => $card_name,
			"card_type" => $card_type,
			"card_number" => addslashes(text_crypt($card_number)),
			"card_expire" => $card_expire
		);
		if ($store_cvv2) {
			$query_data['card_cvv2'] = addslashes(text_crypt($card_cvv2));
		}

		func_array2update("customers", $query_data, "login = '$login' AND usertype = '$login_type'");
	}

	$request = $xcart_catalogs['customer']."/cart.php?".$sessurl."mode=order_message&orderids=".$_orderids;
	$cart="";
	x_session_save();

	if (!empty($active_modules['SnS_connector'])) {
		func_generate_sns_action("CartChanged");
	}
}

require($xcart_dir."/payment/payment_ccredirect.php");

?>
