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
# $Id: cc_fuse.php,v 1.17.2.2 2006/07/19 05:27:35 max Exp $
#

if (!defined('XCART_START')) { header("Location: ../"); die("Access denied"); }

@set_time_limit(100);

x_load('http');

$pp_login = $module_params["param01"];
$pp_pass = $module_params["param02"];
$pp_client = $module_params["param03"];
$domen = ($module_params["testmode"] == "N" ? $module_params["param06"] : $module_params["param07"]);
$curr = $module_params["param08"];

switch ($module_params["testmode"]) {
	case "N":
		$pp_mode = "P";
		break;

	case "A":
		$pp_mode = "Y";
		break;

	default:
		$pp_mode = "N";
}

$pfuse_cc_expire = substr($userinfo["card_expire"], 0, 2)."/".substr($userinfo["card_expire"], 2, 2);
$pfuse_cvv2_ind = (empty($userinfo["card_cvv2"]) ? 2 : 1);
$pfuse_total= (100*$cart["total_cost"]);

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<EngineDocList>
	<DocVersion DataType="String">1.0</DocVersion>
	<EngineDoc>
		<ContentType DataType="String">OrderFormDoc</ContentType>
		<User>
			<Name DataType="String">$pp_login</Name>
			<Password DataType="String">$pp_pass</Password>
			<ClientId DataType="S32">$pp_client</ClientId>
		</User>
		<Instructions>
			<Pipeline DataType="String">Payment</Pipeline>
		</Instructions>
		<IPAddress DataType="String">$REMOTE_ADDR</IPAddress>
		<OrderFormDoc>
			<Mode DataType="String">$pp_mode</Mode>
			<Consumer>
				<Email DataType="String">$userinfo[email]</Email>
				<BillTo>
					<Location>
						<TelVoice DataType="String">$userinfo[phone]</TelVoice>
						<TelFax DataType="String">$userinfo[fax]</TelFax>
						<Address>
							<Name DataType="String">$bill_name</Name>
							<City DataType="String">$userinfo[b_city]</City>
							<Street1 DataType="String">$userinfo[b_address]</Street1>
							<StateProv DataType="String">$userinfo[b_state]</StateProv>
							<PostalCode DataType="String">$userinfo[b_zipcode]</PostalCode>
							<Country DataType="String">$userinfo[b_country]</Country>
						</Address>
					</Location>
				</BillTo>
				<ShipTo>
					<Location>
						<TelVoice DataType="String">$userinfo[phone]</TelVoice>
						<TelFax DataType="String">$userinfo[fax]</TelFax>
						<Address>
							<Name DataType="String">$bill_name</Name>
							<City DataType="String">$userinfo[b_city]</City>
							<Street1 DataType="String">$userinfo[b_address]</Street1>
							<StateProv DataType="String">$userinfo[b_state]</StateProv>
							<PostalCode DataType="String">$userinfo[b_zipcode]</PostalCode>
							<Country DataType="String">$userinfo[b_country]</Country>
						</Address>
					</Location>
				</ShipTo>
				<PaymentMech>
					<CreditCard>
						<Number DataType="String">$userinfo[card_number]</Number>
						<Expires DataType="ExpirationDate" Locale="840">$pfuse_cc_expire</Expires>
						<Cvv2Val DataType="String">$userinfo[card_cvv2]</Cvv2Val>
						<Cvv2Indicator DataType="String">$pfuse_cvv2_ind</Cvv2Indicator>
					</CreditCard>
				</PaymentMech>
			</Consumer>
			<Transaction>
				<Type DataType="String">Auth</Type>
				<CurrentTotals>
					<Totals>
						<Total DataType="Money" Currency="$curr">$pfuse_total</Total>
					</Totals>
				</CurrentTotals>
			</Transaction>
		</OrderFormDoc>
	</EngineDoc>
</EngineDocList>
XML;

$xml = str_replace("\n", "", $xml);
$xml = preg_replace("/>[\t ]+</S", "><", $xml);
list($a, $return) = func_https_request("POST", "https://".$domen.":11500/", array("CLRCMRC_XML=".$xml));

$return = preg_replace("/\n/", "", $return);

$bill_output["code"] = 2;
$bill_output["billmes"] = '';
if (preg_match("/<TransactionStatus>(\w)<\/TransactionStatus>/s", $return, $out)) {
	$bill_output["code"] = ($out[1] == 'A' ? 1 : 2);

} else {
	$bill_output["billmes"] = 'Request error. ';
}

if (preg_match("/<Notice>(.*)<\/Notice>/", $return, $out))
	$bill_output["billmes"] .= $out[1];

if ($bill_output["code"] == 1) {
	if (preg_match("/<AuthCode>(.*)<\/AuthCode>/", $return, $out))
		$bill_output["billmes"] .= " (AuthCode: ".$out[2].")";

	if (preg_match("/<CardholderPresentCode(.*)>(.*)<\/CardholderPresentCode>/",$return,$out))
		$bill_output["billmes"] .= " (CardholderPresentCode: ".$out[2].")";

	if (preg_match("/<InputEnvironment(.*)>(.*)<\/InputEnvironment>/",$return,$out))
		$bill_output["billmes"] .= " (InputEnvironment: ".$out[2].")";

	if (preg_match("/<TerminalInputCapability(.*)>(.*)<\/TerminalInputCapability>/",$return,$out))
		$bill_output["billmes"] .= " (TerminalInputCapability: ".$out[2].")";

	if (preg_match("/<SecurityIndicator(.*)>(.*)<\/SecurityIndicator>/",$return,$out))
		$bill_output["cvvmes"] = "SecurityIndicator: ".$out[2];

} elseif ($bill_output["code"] == 2) {
	if (preg_match("/<CcErrCode>(.*)<\/CcErrCode>/", $return, $out))   
    	$bill_output["billmes"] .= " (Error code: $out[1])";
}

if (preg_match("/<DocumentId(.*)>(.*)<\/DocumentId>/",$return,$out))
	$bill_output["billmes"].= " (DocumentId: ".$out[2].")";

if (preg_match("/<FraudResultCode(.*)>(.*)<\/FraudResultCode>/",$return,$out))
	$bill_output["avsmes"] = "FraudResultCode: ".$out[2];


?>
