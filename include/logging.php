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
# $Id: logging.php,v 1.18.2.3 2006/08/05 07:42:54 max Exp $
#
# Logging subsystem
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

define ('X_LOG_SIGNATURE', '<'.'?php die(); ?'.">\n");

function x_log_add($label, $message, $add_backtrace=false, $stack_skip=0, $email_addresses=false, $email_only=false) {
	global $var_dirs;
	global $PHP_SELF;
	global $HTTP_SERVER_VARS;
	global $config;

	$filename = sprintf("%s/x-errors_%s-%s.php", $var_dirs["log"], strtolower($label), date('ymd'));

	if ($label == 'SQL')
		$type = 'error';
	elseif ($label == 'INI' || $label == 'SHIPPING')
		$type = 'warning';
	else
		$type = 'message';

	$uri = $PHP_SELF;
	if (!empty($HTTP_SERVER_VARS['QUERY_STRING'])) $uri .= '?'.$HTTP_SERVER_VARS['QUERY_STRING'];

	if ($add_backtrace) {
		$stack = func_get_backtrace(1+$stack_skip);
		$backtrace = "Backtrace:\n".implode("\n", $stack)."\n";
	}
	else
		$backtrace = '';

	if (is_array($message) || is_object($message)) {
		ob_start();
		print_r($message);
		$message = ob_get_contents();
		ob_end_clean();
	} else {
		$message = trim($message);
	}

	$local_time = "";
	if (!empty($config)) {
		$local_time = "(shop: ".date('d-M-Y H:i:s', time() + $config['Appearance']['timezone_offset']).") ";
	}

	$message = str_replace("\n", "\n    ", "\n".$message);
	$message = str_replace("\t", "    ", $message);

	$data = sprintf("[%s] %s%s %s:%s\nRequest URI: %s\n%s-------------------------------------------------\n",
		date('d-M-Y H:i:s'),
		$local_time,
		$label, $type,
		$message,
		$uri,
		$backtrace
	);

	if (!$email_only && x_log_check_file($filename) !== false) {
		$fp = @fopen($filename, "a+");
		if ($fp !== false) {
			fwrite($fp, $data);
			fclose($fp);
		}
	}

	if (!empty($email_addresses) && is_array($email_addresses)) {
		x_load('mail');

		foreach ($email_addresses as $k=>$email) {
			func_send_simple_mail(
				$email,
				$config["Company"]["company_name"].": $label $type notification",
				$data, $config["Company"]["site_administrator"]);
		}
	}
}

function x_log_flag($flag_key, $label, $message, $add_backtrace=false, $stack_skip=0) {
	static $email_addresses = false;
	global $config;

	if ($email_addresses === false && isset($config['Logging']['email_addresses'])) {
		$email_addresses = array_unique(split('[ ,]+', $config['Logging']['email_addresses']));
	}

	$do_log =  empty($config);
	$addresses = false;
	$do_email = false;

	if (isset($config['Logging'][$flag_key])) {
		$value = $config['Logging'][$flag_key];
		$do_log = (strpos($value,'L') !== false);
		$do_email = (strpos($value,'E') !== false);
	}

	if ($do_email)
		$addresses = $email_addresses;

	if ($do_log || $do_email)
		x_log_add($label, $message, $add_backtrace, $stack_skip+1, $addresses, ($do_email && !$do_log));
}

#
# For testing purpose: set parameters of debugging functions
#
# Operations:
# 'P' - display/not display debug messages
#
function x_debug_ctl($operation, $arg=null) {
	static $print_status = true;

	switch ($operation) {
		case 'P':
			if (is_null($arg))
				return $print_status;
			$print_status = $arg;
			return true;
	}

	return false;
}

function x_log_list_files($labels = false, $start=false, $end=false) {
	global $var_dirs;

	$regexp = '!^x-errors_([a-zA-Z_-]+)-(\d{6})\.php$!S';

	$dp = @opendir($var_dirs["log"]);
	if ($dp === false) return false;

	if ($start !== false)
		$start = (int)date('ymd', $start);
	else
		$start = 0;

	if ($end === false)
		$end = time() + 86400 * 30;

	$end = (int)date('ymd', $end);

	$return = array();

	if (!is_array($labels)) {
		if (!empty($labels))
			$labels = array (strtoupper($labels));
	}
	else {
		foreach ($labels as $k=>$v) {
			$labels[$k] = strtoupper($v);
		}
	}

	while ($file = readdir($dp)) {
		if (!preg_match($regexp, $file, $matches)) {
			continue;
		}

		$time_str = $matches[2];
		$ts = (int)$time_str;

		if ($ts < $start || $ts > $end) {
			continue;
		}

		$prefix = strtoupper($matches[1]);
		if ($labels !== false && is_array($labels) && !in_array($prefix, $labels)) {
			continue;
		}

		if (!isset($return[$prefix]))
			$return[$prefix] = array();

		$time_ts = mktime(0,0,0, substr($time_str,2,2), substr($time_str,4,2), substr($time_str,0,2));

		$return[$prefix][$time_ts] = $file;
	}

	foreach ($return as $prefix=>$data) {
		ksort($return[$prefix]);
	}

	return $return;
}

function x_log_get_contents($labels = false, $start=false, $end=false, $html_safe=false, $count=0) {
	global $var_dirs;
	static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

	$logs = x_log_list_files($labels, $start, $end);

	if (empty($logs)) return false;

	$logs_data = array();

	if ($count < 0) $count = 0;

	foreach ($logs as $label=>$data) {
		$contents = "";
		$records = array();
		foreach ($data as $ts=>$file) {
			$fp = @fopen($var_dirs["log"].'/'.$file, "rb");
			if ($fp !== false) {
				fseek($fp, strlen(X_LOG_SIGNATURE), SEEK_SET);
				$buffer = '';
				while (($line = fgets($fp, 8192)) !== false) {
					if (!$count) {
						$contents .= $line;
						continue;
					}

					if (preg_match($regexp, $line)) {
						if (!empty($buffer)) {
							$records[] = $buffer;
							if (count($records) > $count) array_splice($records, 0, -$count);
						}

						$buffer = $line;
					}
					else {
						$buffer .= $line;
					}
				}

				if (!empty($buffer)) {
					$records[] = $buffer;
					if (count($records) > $count) array_splice($records, 0, -$count);
				}

				fclose($fp);
			}
		}

		if (!empty($records)) {
			$contents .= implode('', $records);
			$records = false;
		}


		if ($html_safe) {
			$contents = htmlspecialchars($contents);
			$contents = str_replace('  ', '&nbsp ', $contents);
		}

		if (!empty($contents)) {
			$logs_data[$label] = $contents;
		}
	}

	return $logs_data;
}


function x_log_count_messages($labels=false, $start=false, $end=false) {
	global $var_dirs;
	static $regexp = '!^\[\d{2}-.{3}-\d{4} \d{2}:\d{2}:\d{2}\] !S';

	$logs = x_log_list_files($labels, $start, $end);

	if (!is_array($logs) || empty($logs))
		return false;

	$return = array();

	foreach ($logs as $label=>$list) {
		if (!is_array($list) || empty($list)) continue;

		foreach ($list as $timestamp=>$file) {
			# count records in single log file
			$fp = @fopen($var_dirs["log"].'/'.$file, 'r');
			if ($fp === false)
				continue;

			$count = 0;
			while (($line = fgets($fp, 8192)) !== false) {
				if (preg_match($regexp, $line)) $count++;
			}

			fclose($fp);

			$return[$label][$timestamp] = $count;
		}
	}

	return $return;
}

function x_log_get_names($labels=false, $force_output=false) {
	static $all_labels = false;

	if ($all_labels === false) {
		$all_labels = array (
			'DATABASE' => 'lbl_log_database_operations',
			'FILES' => 'lbl_log_file_operations',
			'ORDERS' => 'lbl_log_orders_operations',
			'PRODUCTS' => 'lbl_log_products_operations',
			'SHIPPING' => 'lbl_log_shipping_errors',
			'PAYMENTS' => 'lbl_log_payment_errors',
			'PHP' => 'lbl_log_php_errors',
			'SQL' => 'lbl_log_sql_errors',
			'ENV' => 'lbl_log_env_changes',
			'DEBUG' => 'lbl_log_debug_messages',
			'DECRYPT' => 'lbl_decrypt_errors',
			'BENCH' => 'lbl_log_bench_reports'
		);
	}

	if ($force_output !== false && $fource_output !== true)
		$force_output = false;

	$keys = array_keys($all_labels);
	if (empty($labels) || !is_array($labels))
		$labels = $keys;
	else {
		$labels = array_intersect($labels, $keys);
		if (empty($labels))
			$labels = $keys;
	}

	$result = array ();
	foreach ($labels as $label) {
		$result[$label] = func_get_langvar_by_name($all_labels[$label], NULL, false, $force_output);
	}

	return $result;
}

function x_log_check_file($filename) {
	$fp = @fopen($filename, "a+");
	if ($fp === false) return false;

	if (filesize($filename) ==0) {
		@fwrite($fp, X_LOG_SIGNATURE);
		@fclose($fp);
		return $filename;
	}

	if (@fseek($fp, 0, SEEK_SET) < 0) {
		@fclose($fp);
		return false;
	}

	$tmp = @fread($fp, strlen(X_LOG_SIGNATURE));
	if (strcmp($tmp, X_LOG_SIGNATURE)) {
		@fseek($fp, 0, SEEK_SET);
		@ftruncate($fp, 0);
		@fwrite($fp, X_LOG_SIGNATURE);
	}
	@fclose($fp);

	return $filename;
}

function func_array_compare($orig, $new) {
	$result = array (
		'removed' => false,
		'added' => false,
		'delta' => false,
		'changed' => false
	);

	$keys = array();
	if (is_array($orig)) $keys = array_keys($orig);
	if (is_array($new)) $keys = array_merge($keys, array_keys($new));
	$keys = array_unique($keys);

	foreach ($keys as $key) {
		$in_orig = isset($orig[$key]);
		$in_new = isset($new[$key]);

		if ($in_orig && !$in_new) {
			$result['removed'][$key] = $orig[$key];
		}
		elseif (!$in_orig && $in_new) {
			$result['added'][$key] = $new[$key];
		}
		else {
			# check for changed values
			if (!is_array($new[$key])) {
				if (!strcmp((string)$orig[$key], (string)$new[$key])) {
					continue;
				}

				$is_numeric = preg_match('!^((\d+)|(\d+\.\d+))$!S', $new[$key]);

				if ($is_numeric) {
					$result['delta'][$key] = $new[$key] - $orig[$key];
				}

				$result['changed'][$key] = $new[$key];
			}
			else {
				$tmp = func_array_compare($orig[$key],$new[$key]);

				foreach ($tmp as $tmp_key=>$tmp_value) {
					if ($tmp_value === false) continue;

					$result[$tmp_key][$key] = $tmp_value;
				}
			}
		}
	}

	# remove not used arrays
	foreach ($result as $k=>$v) {
		if ($v === false)
			unset($result[$k]);
	}

	return $result;
}

#
# Function to get backtrace for debugging
#
function func_get_backtrace($skip=0) {
	$result = array();
	if (!function_exists('debug_backtrace')) {
		$result[] = '[func_get_backtrace() is supported only for PHP version 4.3.0 or better]';
		return $result;
	}
	$trace = debug_backtrace();

	if (is_array($trace) && !empty($trace)) {
		if ($skip>0) {
			if ($skip < count($trace))
				$trace = array_splice($trace, $skip);
			else
				$trace = array();
		}

		foreach ($trace as $item) {
			$result[] = $item['file'].':'.$item['line'];
		}
	}

	if (empty($result)) {
		$result[] = '[empty backtrace]';
	}

	return $result;
}

#
# Set internal php values
#
if ($debug_mode==2 || $debug_mode==0) {
	ini_set("display_errors",0);
	ini_set("display_startup_errors",0);
}
if ($debug_mode==2 || $debug_mode==3) {
	ini_set("log_errors", 1);
	ini_set("error_log", x_log_check_file($var_dirs["log"]."/x-errors_php-".date('ymd').".php"));
	ini_set("ignore_repeated_errors", 1);
}

# Remove empty log for previous day. Purging/checking all empty logs from
# previuos days can reduce performance
$_prev_logfile = $var_dirs["log"]."/x-errors_php-".date('ymd', time()-SECONDS_PER_DAY).".php";
if (@filesize($_f) <= strlen(X_LOG_SIGNATURE))
	@unlink($_f);

#
# Log changes of PHP.ini settings
#

$old_settings = false;
if (file_exists($var_dirs["log"]."/data.phpini.php")) {
	ob_start();
	readfile($var_dirs["log"]."/data.phpini.php");
	$_tmp = ob_get_contents();
	ob_end_clean();
	$_tmp = substr($_tmp, strlen(X_LOG_SIGNATURE));
	$old_settings = unserialize($_tmp);
}

$current_settings = ini_settings_storage();

# these optionas are set in config.php
func_unset($current_settings, 'error_log');
func_unset($current_settings, 'ignore_repeated_errors');
func_unset($current_settings, 'log_errors');
func_unset($current_settings, 'log_errors_max_len');
func_unset($current_settings, 'magic_quotes_runtime');
func_unset($current_settings, 'session.bug_compat_warn');
func_unset($current_settings, 'max_execution_time');

$_tmp_changed = false;
if (is_array($old_settings) && !empty($old_settings) && is_array($current_settings)) {
	$changed_settings = func_array_compare($old_settings, $current_settings);
	$_msg = array();

	if (!empty($changed_settings['removed'])) {
		$_lines = array();
		foreach ($changed_settings['removed'] as $_k=>$_v) {
			$_lines[] = "\t$_k = ``$_v''";
		}
		$_msg[] = "Removed options:\n".implode("\n", $_lines);
		unset($_lines);
	}

	if (!empty($changed_settings['added'])) {
		$_lines = array();
		foreach ($changed_settings['added'] as $_k=>$_v) {
			$_lines[] = "\t$_k = ``$_v''";
		}
		$_msg[] = "Added options:\n".implode("\n", $_lines);
		unset($_lines);
	}

	if (!empty($changed_settings['changed'])) {
		$_lines = array();
		foreach ($changed_settings['changed'] as $_k=>$_v) {
			$_lines[] = "\t$_k = ``$_v'' (was: ``".$old_settings[$_k]."'')";
		}
		$_msg[] = "Changed options:\n".implode("\n", $_lines);
		unset($_lines);
	}

	if (!empty($_msg)) {
		x_log_add('ENV', implode("\n",$_msg));
		$_tmp_changed = true;
	}

	unset($changed_settings);
	unset($_msg);
}

if (empty($old_settings) || $_tmp_changed) {
	$_tmp_fp = @fopen($var_dirs["log"]."/data.phpini.php", "wb");
	if ($_tmp_fp !== false) {
		@fwrite($_tmp_fp, X_LOG_SIGNATURE.serialize($current_settings));
		@fclose($_tmp_fp);
	}
}

unset($_tmp_changed);
unset($_tmp_fp);
unset($current_settings);
unset($old_settings);

#
# Log uploaded files
#

if (!empty($HTTP_POST_FILES)) {
	$_lines = array();
	foreach ($HTTP_POST_FILES as $_k=>$_v) {
		if (empty($_v['name'])) continue;

		$_lines[] = $_v['name'].' (size: '.$_v['size'].' byte(s), type: '.$_v['type'].')';
	}

	if (!empty($_lines)) {
		x_log_add('FILES', "Uploaded files:\n".implode($_lines));
	}
}

?>
