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
# $Id: ups_func.php,v 1.11 2006/01/11 06:56:20 mclap Exp $
#

if ( !defined('XCART_SESSION_START') ) { header("Location: ../"); die("Access denied"); }

#
# Generate the unoque string
#
function u_generate_unique_string($pos, $length) {
	$str = md5(uniqid(rand()));

	$result = substr($str,$pos,$length);

	return $result;
}

#
# Validate string for using in XML node
#
function func_ups_xml_quote($arg) {
	if (is_array($arg)) {
		foreach ($arg as $k=>$v) {
			if ($k == "phone") {
				$arg[$k] = preg_replace("/[^0-9]/", "", $v);
			}
			elseif (is_string($v)) {
				$arg[$k] = htmlspecialchars($v);
			}
		}

		return $arg;
	}
	elseif (is_string($arg)) {
		return htmlspecialchars($arg);
	}
}

#
# Send XML-request and process XML-response
#
function u_process($request, $func, $tool) {
	global $UPS_url;
	global $ps;
	global $show_XML;

	x_load('http');

	if ($show_XML) {
		$out = $request;
		$out = preg_replace("|<AccessLicenseNumber>.*</AccessLicenseNumber>|i","<AccessLicenseNumber>xxx</AccessLicenseNumber>",$out);
		$out = preg_replace("|<UserId>.*</UserId>|i","<UserId>xxx</UserId>",$out);
		$out = preg_replace("|<Password>.*</Password>|i","<Password>xxx</Password>",$out);
		$out = preg_replace("|<DeveloperLicenseNumber>.*</DeveloperLicenseNumber>|i","<DeveloperLicenseNumber>xxx</DeveloperLicenseNumber>",$out);
		print("<pre>"); print(htmlspecialchars($out)); print("</pre>");
	}
	$ps = ""; $ps["tags"] = array();

	$post=explode("\n",$request);

	list ($a,$result)=func_https_request("POST",$UPS_url.$tool,$post,"","","text/xml");

	if ($show_XML) {
		$out = $result;
		$out = preg_replace("|<AccessLicenseNumber>.*</AccessLicenseNumber>|i","<AccessLicenseNumber>xxx</AccessLicenseNumber>",$out);
		$out = preg_replace("|<UserId>.*</UserId>|i","<UserId>xxx</UserId>",$out);
		$out = preg_replace("|<Password>.*</Password>|i","<Password>xxx</Password>",$out);
		$out = preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $out);
		$out = preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $out);
		print("<pre>"); print(htmlspecialchars($out)); print("</pre>");
	}

	$xml_parser = xml_parser_create("ISO-8859-1");
	xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
	xml_set_element_handler($xml_parser, "u_elem_start", "u_elem_end");
	xml_set_character_data_handler($xml_parser, $func);
	xml_parse($xml_parser, $result);
	xml_parser_free($xml_parser);

}

function u_elem_start($parser, $name, $attrs) {
	global $ps;
	$ps["tags"][] = $name;
}

function u_elem_end($parser, $name) {
	global $ps;
	array_pop($ps["tags"]);
}

#
# Common code for UPS XML tools
#
function u_elem_data_base($parser, $data) {
	global $ps;

	if (count($ps["tags"]) == 3) {
		if ($ps["tags"][2] == "RESPONSESTATUSCODE")
			$ps["statuscode"] = $data;
		elseif ($ps["tags"][2] == "RESPONSESTATUSDESCRIPTION")
			$ps["statusdesc"] = @$ps["statusdesc"].$data;
	}
	elseif (count($ps["tags"]) == 4) {
		if ($ps["tags"][3] == "ERRORCODE")
			$ps["errorcode"] = $data;
		elseif ($ps["tags"][3] == "ERRORDESCRIPTION")
			$ps["errordesc"] = @$ps["errordesc"].$data;
	}
}

#
# XML data handler for Register Request
#
function u_elem_data_reg($parser, $data) {
	global $ps;

	if (count($ps["tags"]) == 2 && $ps["tags"][1] == "USERID") {
		$ps["UserId"] = $data;
	}
	else
		u_elem_data_base($parser, $data);
}

#
# XML data handler for AccessLicenseAgreement (License tool)
#
function u_elem_data_agree($parser, $data) {
	global $ps;

	if (count($ps["tags"]) == 2 && $ps["tags"][1] == "ACCESSLICENSETEXT") {
		if (!isset($ps["licensetext"]))
			$ps["licensetext"] = $data;
		else
			$ps["licensetext"] .= $data;
	}
	else
		u_elem_data_base($parser, $data);
}

#
# XML data handler for AccessLicense (License tool)
#
function u_elem_data_accept($parser, $data) {
	global $ps;

	if (count($ps["tags"]) == 2 && $ps["tags"][1] == "ACCESSLICENSENUMBER") {
		$ps["licensenum"] = $data;
	}
	else
		u_elem_data_base($parser, $data);
}

#
# XML data handler for Address Validation
#
function u_elem_data_av($parser, $data) {
	global $ps;
	global $rank;

	if ($ps["tags"][1] == "ADDRESSVALIDATIONRESULT") {
		if ($ps["tags"][2] == "RANK") {
			$rank = $data;
		}
		if (!empty($rank) && !empty($ps["tags"][2])) {
			if ($ps["tags"][2] == "ADDRESS")
				$ps[$rank]["ADDRESS"][$ps["tags"][3]] = $data;
			else
				$ps[$rank][$ps["tags"][2]] = $data;
		}
	}
	else
		u_elem_data_base($parser, $data);
}

?>
