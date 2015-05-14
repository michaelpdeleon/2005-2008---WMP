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
# $Id: func.payment.php,v 1.4.2.1 2006/05/06 08:21:55 max Exp $
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

#
# This function joins order_id's and urlencodes 'em
#
function func_get_urlencoded_orderids ($orderids) {
	if (!is_array($orderids))
		return '';

	return urlencode(join (",", $orderids));
}

function func_check_webinput($check_php = 1) {
	global $config, $sql_tbl, $HTTP_SERVER_VARS;

	static $pfiles = array (
		'cc_epdq.php' => 'cc_epdq_result.php',
		'cc_smartpag.php' => 'cc_smartpag_final.php',
		'cc_payzip.php' => 'cc_payzip_result.php',
		'cc_verisignl.php' => 'cc_verisignl_result.php',
		'cc_hsbc.php' => 'cc_hsbc_result.php',
		'cc_ogoneweb.php' => 'cc_ogoneweb_result.php',
		'cc_pswbill.php' => 'cc_pswbill_result.php',
		'cc_triple.php' => 'cc_triple_result.php',
		'cc_paybox.php' => 'cc_paybox_result.php',
		'cc_pp3.php' => array (
			'ebank_ok.php',
			'ebank_nok.php'
		)
	);

	$allow_php = array();
	$list = func_query("SELECT c.processor FROM $sql_tbl[ccprocessors] c, $sql_tbl[payment_methods] m WHERE m.active='Y' AND m.paymentid=c.paymentid AND c.background<>'Y'");

	if ($list) {
		foreach($list as $v) {
			$file = $v['processor'];
			if (!empty($pfiles[$file])) {
				if (is_array($pfiles[$file]))
					$allow_php = func_array_merge($allow_php, $pfiles[$file]);
				else
					$allow_php[] = $pfiles[$file];
			}
			else {
				$allow_php[] = $file;
			}
		}
	}

	$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
	$allow_ip = $config["Security"]["allow_ips"];

	$not_found = true;
	if ($check_php && !empty($allow_php)) {
		for ($i = 0; $i < count($allow_php); $i++)
			$allow_php[$i] = preg_quote($allow_php[$i]);
		$script = $HTTP_SERVER_VARS["PHP_SELF"];
		$re_allow = "!(".implode("|",$allow_php).")$!S";
		$not_found = !preg_match($re_allow, $script);
	}

	if ($not_found) {
		header("Location: ../");
		die("Access denied");
	}

	if ($allow_ip) {
		$not_found = true;
		$a = split(",",$allow_ip);
		foreach ($a as $v) {
			list($aip, $amsk) = split("/",trim($v));

			# Cannot use 0x100000000 instead 4294967296
			$amsk = 4294967296 - ($amsk ? pow(2,(32-$amsk)) : 1);

			if ((ip2long($ip) & $amsk) == ip2long($aip)) {
				$not_found = false;
				break;
			}
		}

		return ($not_found ? "err" : "pass");
	}

	return "pass";
}

#
# Display payment page footer
#
function func_payment_footer() {
	global $smarty;

	if (defined("DISP_PAYMENT_FOOTER"))
		return false;

	$fn = $smarty->template_dir."/customer/main/payment_wait_end.tpl";
	$fp = @fopen($fn, "r");
	if ($fp) {
		$data = fread($fp, filesize($fn));
		fclose($fp);

		$data = preg_replace("/\{\*.*\*\}/Us", "", $data);
		$data = preg_replace("/\{\/?literal\}/Us", "", $data);

		echo $data;
	}
    
	define("DISP_PAYMENT_FOOTER", true);
}

#
# Generated auto-submit form
#
function func_create_payment_form($url, $fields, $name, $method = "POST") {
	global $smarty;

	$charset = "";
	if (!empty($smarty))
		$charset = $smarty->get_template_vars("default_charset");
	if (empty($charset))
		$charset = "iso-8859-1";

	$method = strtoupper($method);
	if (in_array($method, array("POST", "GET")))
		$method = "POST";

	$button_title = func_get_langvar_by_name("lbl_submit", array(), false, true);
	$script_note = func_get_langvar_by_name("txt_script_payment_note", array("payment" => $name), false, true);
	$noscript_note = func_get_langvar_by_name("txt_noscript_payment_note", array("payment" => $name, "button" => $button_title), false, true);
	?>
<form action="<?php echo $url; ?>" method="<?php echo $method; ?>" name="process">
<?php
	foreach($fields as $fn => $fv) {
?>	<input type="hidden" name="<?php echo $fn; ?>" value="<?php echo htmlspecialchars($fv); ?>" />
<?php
	}
?>
<table class="WebBasedPayment" cellspacing="0">
<tr>
	<td id="text_box">
<noscript>
<?php echo $noscript_note; ?><br />
<input type="submit" value="<?php echo $button_title; ?>">
</noscript>
	</td>
</tr>
</table>
</form>
<script type="text/javascript">
<!--
if (document.getElementById('text_box'))
	document.getElementById('text_box').innerHTML = "<?php echo strtr($script_note, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/')); ?>";
document.process.submit();
-->
</script>
	<?php
}

?>
