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
# $Id: vote.php,v 1.9.2.2 2006/07/20 09:59:20 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: home.php"); die("Access denied"); }

x_load('user');

if (empty($mode)) $mode = "";

if (($mode=='vote') && ($productid) && ($vote>=1) && ($vote<=5) && $config['Customer_Reviews']['customer_voting'] == 'Y') {
	$result = func_query_first("SELECT * FROM $sql_tbl[product_votes] WHERE remote_ip='$REMOTE_ADDR' AND productid='$productid'");
	if ($result) {
		func_header_location("error_message.php?error_already_voted");
	}
	else {
		db_query ("INSERT INTO $sql_tbl[product_votes] (remote_ip, vote_value, productid) VALUES ('$REMOTE_ADDR','$vote', '$productid')");
		func_header_location("product.php?productid=$productid");
	}
}
elseif (($mode=='review') && ($productid) && $config['Customer_Reviews']['customer_reviews'] == 'Y' && ($config['Customer_Reviews']['writing_reviews'] == 'A' || ($config['Customer_Reviews']['writing_reviews'] == 'R' && !empty($login)))) {
	$result = func_query_first("SELECT * FROM $sql_tbl[product_reviews] WHERE remote_ip='$REMOTE_ADDR' AND productid='$productid'");
	if ($result) {
		func_header_location("error_message.php?error_review_exists");
	}
	else {
		$review_author = htmlspecialchars($review_author);
		$review_message = htmlspecialchars($review_message);
		db_query ("INSERT INTO $sql_tbl[product_reviews] (remote_ip, email, message, productid) VALUES ('$REMOTE_ADDR', '$review_author', '$review_message', '$productid')");

		if (!empty($active_modules['SnS_connector'])) {
			func_generate_sns_action("WriteReview");
		}

		func_header_location("product.php?productid=$productid");
	}
}

$vote_result = func_query_first("SELECT COUNT(remote_ip) AS total, AVG(vote_value) AS rating FROM $sql_tbl[product_votes] WHERE productid='$productid'");
if ($vote_result["total"] == 0)
	$vote_result["rating"] = 0;

$smarty->assign("vote_result", $vote_result);

$vote_max_cows = floor ($vote_result["rating"]);
$vote_little_cow = round (($vote_result["rating"]-$vote_max_cows) * 4);
$vote_free_cows = 5 - $vote_max_cows - (($vote_little_cow==0) ? 0 : 1);
$smarty->assign("vote_max_cows", $vote_max_cows);
$smarty->assign("vote_little_cow", $vote_little_cow);
$smarty->assign("vote_free_cows", $vote_free_cows);

if (!empty($login)) {
	$customer_info = func_userinfo($login,$login_type);
	$smarty->assign("customer_info", $customer_info);
}

$reviews = func_query("SELECT * FROM $sql_tbl[product_reviews] WHERE productid='$productid'");

$smarty->assign ("reviews", $reviews);
?>
