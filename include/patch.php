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
# $Id: patch.php,v 1.13 2006/03/30 11:33:04 mclap Exp $
#
# Part of Patch/Upgrade center
#
# Note: function func_patch_apply() currently able to handle only unified diffs
#

if ( !defined('XCART_START') ) { header("Location: ../"); die("Access denied"); }

x_load('files');

define('PATCH_UNIFIED',1);
define('PATCH_CONTEXT',2);

define('PATCH_MASK_UNIFIED', '!^(\s*)(\@\@ -(\d+)(?:,(\d+))? \+(\d+)(?:,(\d+))? \@\@)!S');

#
# Function to apply patch against file
# Pass empty $outfile to check patch applicability
#
function func_patch_apply($origfile, $patchfile, $rejfile, $backupfile, &$log, &$rejects, $check=false, $reverse=false) {
	static $masks = array (
		PATCH_UNIFIED => PATCH_MASK_UNIFIED
	);
	$type = PATCH_UNIFIED;

	if (empty($origfile) || empty($patchfile) || !file_exists($patchfile))
		return false;

	$orig = array();
	if (file_exists($origfile))
		$orig = file($origfile);

	$empty_orig = empty($orig);

	$diff = file($patchfile);

	$outdata = $orig;
	$log[] = "Patching file $origfile ...";

	$idx = 0;
	$hunk_number = 1;
	$i_offset = 0;
	$rejected = array();
	$rejects = false;
	$changed = false;
	$is_new_file = false;

	while ($idx < count($diff)) {
		$line = $diff[$idx];
		if (preg_match($masks[PATCH_UNIFIED], $line, $m)) {
			if ($reverse) {
				list(,$space, $range, $o_start, $o_lines, $i_start, $i_lines) = $m;
			}
			else {
				list(,$space, $range, $i_start, $i_lines, $o_start, $o_lines) = $m;
			}

			$is_new_file = $is_new_file
				|| ($m[3] === '0' && $m[4] === '0');

			if (empty($i_lines)) $i_lines = 1;
			if (empty($o_lines)) $o_lines = 1;
			$hunk = array();
			$idx ++;

			$can_apply = true;
			$additions = false;

			for (;$idx < count($diff) && !preg_match($masks[PATCH_UNIFIED], $diff[$idx]); $idx++) {
				$diff_line = $diff[$idx];

				if (!preg_match('!^(.)(.*)$!sS', $diff_line, $parsed_line)) continue;

				if ($parsed_line[1] == '\\') continue;

				if ($reverse) {
					switch ($parsed_line[1]) {
					case '+':
						$parsed_line[1] = '-'; break;
					case '-':
						$parsed_line[1] = '+'; break;
					}

					$diff_line = $parsed_line[1].$parsed_line[2];
				}

				$hunk[] = $diff_line;

				# Cannot apply 'new file' hunk(s) against non empty files
				$additions = $additions
					|| $parsed_line[1] == '+';
			}

			$can_apply = !$is_new_file
				|| $empty_orig
				|| ($is_new_file && !$empty_orig && !$additions);

			if ($can_apply) {
				$data = func_pch_apply($hunk_number, $outdata, $hunk, $space, $i_start+$i_offset, $i_lines, $o_start, $o_lines);
			}
			else {
				$data = array (
					'pos' => false,
					'success' => false
				);
			}

			if (is_array($data)) {
				if ($data['pos'] === false) {
					$hunk_pos = $i_start + $i_offset;
				} else {
					$hunk_pos = $i_start + $i_offset + $data['pos'];
				}
				if (!$data['success']) {
					$log[] = sprintf("Hunk #%d failed at %d.", $hunk_number, $hunk_pos);
					$rejected[] = array (
						'start' => $hunk_pos,
						'hunk' => $hunk
					);
					$i_offset += $o_lines - $i_lines;
				} else {
					$log[] = sprintf("Hunk #%d succeeded at %d.", $hunk_number, $hunk_pos);
					array_splice($outdata, $hunk_pos-1, $i_lines, $data['replace']);
					$changed = true;
					# correct offset for next hunks
					$i_offset += $o_lines - $i_lines + $data['pos'];
				}
			}
			$hunk_number++;
		}
		else $idx++;
	}

	if (!empty($rejected)) {
		if (empty($rejfile)) {
			$log[] = sprintf("%d out of %d hunks ignored", count($rejected), $hunk_number-1);
		}
		else {
			$log[] = sprintf("%d out of %d hunks ignored--saving rejects to %s", count($rejected), $hunk_number-1, $rejfile);

			$rejects = true;
			if (!$check) {
				if (is_writeable(dirname($rejfile))) {
					$r = func_pch_write_rejfile($rejfile, $rejected);
					if (!$r) {
						$log[] = "Write to $rejfile is failed!";
					}
				} else {
					$log[] = "No permissions to write $rejfile!";
				}
			}
		}
	}

	if (!$check) {
		if (!empty($backupfile) && $changed) {
			if (file_exists($backupfile))
				@unlink($backupfile);

			if (!file_exists($origfile))
				$r = touch($backupfile);
			else
				$r = copy($origfile, $backupfile);

			if (!$r) {
				$log[] = "Cannot backup file ``$origfile'' to ``$backupfile''!";
			}
		}
		$r = func_pch_write($origfile, $outdata);
		if (!$r) {
			$log[] = "Write to $origfile is failed!";
		}
	}
	$log[] = "done";

	return empty($rejected);
}

function func_pch_apply($num, &$outdata, &$hunk, $space, $i_start, $i_lines, $o_start, $o_lines) {
	$offset = func_pch_locate($outdata, $hunk, $i_start, $i_lines);

	$result = array (
		'pos' => $offset,
		'success' => false
	);

	if ($offset === false) {
		return $result;
	}

	$work_copy = array_slice($outdata,$i_start-1+$offset,$i_lines);
	$pos = 0;
	foreach ($hunk as $line) {
		if (strlen($line)>0) {
			$cmd = $line[0];
			$line = substr($line,1);
		}
		else $cmd = '';

		switch ($cmd) {
			case '-':
				if (trim($line) != trim($work_copy[$pos])) {
					# FAILED
					return $result;
				}
				array_splice($work_copy,$pos,1);
				break;
			case '+':
				func_pch_array_insert($work_copy,$line,$pos);
				$pos++;
				break;
			default :
				# skip ...
				$pos++;
		}
	}

	$result['success'] = true;
	$result['replace'] = $work_copy;

	return $result;
}

function func_pch_array_insert(&$array, $value, $pos) {
	if (!is_array($array)) return FALSE;

	$last = array_splice($array, $pos);
	array_push($array, $value);
	$array = array_merge($array, $last);
	return $pos;
}

function func_pch_locate(&$data, &$hunk, $start, $lines) {
	$data_len = count($data);

	$max_after = $data_len - $start - $lines;
	for ($offset = 0; ; $offset++) {
		$check_after = ($offset <= $max_after);
		$check_before = ($offset <= $start);

		if ($check_after && func_pch_match($data, $hunk, $start+$offset)) {
			return $offset;
		}
		else
		if ($check_before && func_pch_match($data, $hunk, $start-$offset)) {
			return -$offset;
		}
		else
		if (!$check_after && !$check_before) {
			return false;
		}
	}

	return false;
}

function func_pch_match(&$data, &$hunk, $pos) {
	$len = count($hunk);
	$data_len = count($data);

	for ($i=0, $hunk_pos=0; $hunk_pos<$len && $pos+$i < $data_len; ) {
		if (!preg_match('!^(.)(.*)$!sS', $hunk[$hunk_pos], $matched)) {
			return false;
		}

		if ($matched[1] == '+') {
			$hunk_pos++;
			continue;
		}

		if (trim($data[$pos+$i-1]) != trim($matched[2])) {
			return false;
		}

		$i++; $hunk_pos++;
	}

	return true;
}

function func_pch_write($filename, $data) {
	func_mkdir(dirname($filename));
	$fp = fopen($filename, "wb");
	if (!$fp) return false;

	fwrite($fp,implode("",$data));
	fclose($fp);

	return true;
}

function func_pch_write_rejfile($filename, $rejected) {
	$fp = fopen($filename, "w");
	if (!$fp) return false;

	foreach ($rejected as $saved) {
		$removed = array();
		$added = array();
		foreach($saved['hunk'] as $line) {
			if (!preg_match('!^((.).*)$!S', $line, $matched)) {
				continue; # garbage ???
			}

			switch ($matched[2]) {
				case '-':
					$removed[] = $matched[1];
					break;
				case '+':
					$added[] = $matched[1];
					break;
				default:
					$removed[] = $matched[1];
					$added[] = $matched[1];
			}
		}

		$data = "***************\n";

		$first = $saved['start'];
		$removed_last = $first + (!empty($removed) ? count($removed) - 1 : 0);
		$added_last = $first + (!empty($added) ? count($added) - 1 : 0);

		if ($removed_last != $first)
			$data .= "*** $first,$removed_last ****\n";
		else
			$data .= "*** $first ****\n";

		if (!empty($removed)) $data .= implode("\n", $removed)."\n";

		if ($added_last != $first)
			$data .= "--- $first,$added_last ----\n";
		else
			$data .= "--- $first ----\n";

		if (!empty($added)) $data .= implode("\n", $added)."\n";

		fwrite($fp, $data);
	}

	fclose($fp);
	return true;
}

function func_prepare_list ($patch_lines) {
	$list = "";

	$diff_data = "";
	$orig_file = "";
	$index_found = false;

	foreach($patch_lines as $patch_line) {
		if(preg_match('/(^Index: (.+))|(^diff)|(^((---)|(\+\+\+)|(\*\*\*)) ([^\t:]+))/S',$patch_line, $m)) {
			if (!empty($m[2]) || !empty($m[3]) && !$index_found) {
				if (!empty($orig_file)) {
					$diff_file = func_store_in_tmp(join("",$diff_data),false);
					$list[] = $orig_file.",".$diff_file.",";
					$orig_file = "";
					$index_found = false;
					$diff_data = "";
				}
			}
			# from Index field
			if (!empty($m[2])) {
				$index_found = true;
				if (empty($orig_file) || strlen($orig_file) > strlen($m[2]))
					$orig_file = $m[2];
			}
			# from ---/***/+++ field
			elseif (!empty($m[9])) {
				if (empty($orig_file) || strlen($orig_file) > strlen($m[9]))
					$orig_file = $m[9];
			}
		}
		$diff_data[] = $patch_line;
	}
	if (!empty($orig_file) && !empty($diff_data)) {
		$diff_file = func_store_in_tmp(join("",$diff_data),false);
		$list[] = $orig_file.",".$diff_file.",";
	}

	return $list;
}

function func_read_skin_descr($descr_path) {
	if (!file_exists($descr_path))
		return false;

	$data = file($descr_path);
	$result = array();
	foreach ($data as $line) {
		$line = trim($line);
		list($key,$value) = explode('=',$line,2);
		$result[$key] = $value;
	}

	return $result;
}

function func_read_lst($file, $split=false, $with_sections=true) {
	$result = array();
	$fp = @fopen($file, "r");
	if (!$fp) return array();

	$section = false;
	while ($line = fgets($fp, 4096)) {
		$line = trim($line);
		if (empty($line)) continue;

		if (!$with_sections) {
			$result[$line] = true;
			continue;
		}

		if (substr($line, 0, 1) == '=') {
			$section = substr($line, 1);
			if (empty($section)) $section = "";

			if (!isset($result[$section]))
				$result[$section] = array();
		}
		elseif ($section !== false) {
			if ($split) {
				$tmp = explode(',',$line);
				$result[$section][$tmp[0]] = $tmp;
			}
			else {
				$result[$section][] = $line;
			}
		}
	}

	fclose($fp);

	return $result;
}

function func_correct_files_lst(&$_patch_files, $upgrade_prefix, $installed_modules=false) {
	global $sql_tbl, $xcart_dir;
	global $smarty;

	$error = array();

	# PARTS:
	# 1. read addons.lst & check $sql_tbl[modules]
	# 2. read skin1/.skin_descr & templates.lst (don't forget about *_full.lst)

	# normalize main list
	$files_list = array();
	$override_list = array();
	foreach ($_patch_files as $line) {
		$line = trim($line);
		$triple = explode(',',$line);
		$files_list[$triple[0]] = $triple;
		if (empty($triple[1]))
			$override_list[$triple[0]] = false; # mark entry for removal
	}

	#
	# PART1: correct addons
	#
	$addons_list = func_read_lst($upgrade_prefix.'/addons.lst',true);
	if (empty($installed_modules))
		$installed_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules]");
	$installed_addons = array_intersect(array_keys($addons_list), $installed_modules);

	$missing_addons = array();

	foreach ($addons_list as $addon_name=>$addon_files) {
		if (!in_array($addon_name, $installed_addons))
			continue;

		if (empty($addon_files)) {
			# ERROR, Addon installed, but not included in upgrade pack
			$missing_addons[] = str_replace('_', ' ', $addon_name);
			continue;
		}

		# add/replace diffs for this addon
		foreach ($addon_files as $file=>$triple) {
			$override_list[$file] = $triple;
		}
	}

	if (!empty($missing_addons)) {
		$error[] = "Your shop has installed following module(s): <b>".implode('</b>, <b>',$missing_addons)."</b>, but necessary patches are not included in this upgrade pack";
	}

	#
	# PART2: correct skins
	#

	$skin_descr_path = $smarty->template_dir.DIRECTORY_SEPARATOR.'.skin_descr';

	$skin_descr = func_read_skin_descr($skin_descr_path);

	$skins_ok = true;

	if ($skins_ok && (empty($skin_descr) || !isset($skin_descr['color']) || !isset($skin_descr['dingbats']) || !isset($skin_descr['layout']))) {
		$error[] = "Directory <b>".$skin_descr_path."</b> was corrupted or missing.";
		$skin_ok = false;
	}

	if ($skins_ok && !empty($skin_descr['layout'])) {
		$layout_path = $xcart_dir.'/schemes/templates/'.$skin_descr['layout'];

		if (!file_exists($layout_path) || !is_dir($layout_path)) {
			$error[] = "Unable to find templates repository for current layout: <b>$layout_path</b>.";
			$skins_ok = false;
		}
	}

	while ($skins_ok) {
		# $data = sprintf("layout=%s\ncolor=%s\ndingbats=%s", $params["layout"], $params["color"], $params["dingbats"]);
		$skin_fix = array (
			'colors' => array (
				'key' => $skin_descr['color'],
				'path' => 'schemes/colors/'.$skin_descr['color'],
				'lst' => 'colors'
				),
			'dingbats' => array (
				'key' => $skin_descr['dingbats'],
				'path' => 'schemes/dingbats/'.$skin_descr['dingbats'],
				'lst' => 'dingbats'
				),
			'layout' => array (
				'key' => $skin_descr['layout'],
				'path' => 'schemes/templates/'.$skin_descr['layout'],
				'lst' => 'templates'
				)
			);

		foreach ($skin_fix as $fix_index=>$fix) {
			$lst_file = $upgrade_prefix.'/'.$fix['lst'].'.lst';
			$lst_added_files = $upgrade_prefix.'/'.$fix['lst'].'_added.lst';
			$lst_scheme_files = $xcart_dir.'/'.$fix['path'].'/files.lst';

			$fix['lst_data'] = func_read_lst($lst_file, true);
			$fix['lst_added'] = func_read_lst($lst_added_files);
			$fix['files'] = func_read_lst($lst_scheme_files, false, false);

			if (!empty($skin_descr[$fix_index]) && empty($fix['files'])) {
				$error[] = "Unable to read file: <b>$lst_scheme_files</b>. Please restore it from X-Cart or skin distributive";
				$skins_ok = false;
				break;
			}

			$skin_fix[$fix_index] = $fix;
		}

		if (!$skins_ok) break;

		if (isset($skin_fix['layout']['lst_data'][$skin_descr['layout']])
		&&  empty($skin_fix['layout']['lst_data'][$skin_descr['layout']])) {
			$error[] = "Your shop has installed skin layout: <b>".$skin_descr['layout']."</b>, but necessary patches were not included in this upgrade pack";
			$skins_ok = false;
			break;
		}

		# replace skin related diffs...
		foreach ($files_list as $file=>$file_data) {
			if (!preg_match('!^skin1/(.+$)!S', $file, $matches)) {
				continue;
			}

			$short_name = $matches[1];

			foreach ($skin_fix as $fix_index=>$fix) {
				# fix_index ::= colors | dingbats | layout
				$fix_key = $fix['key'];
				if (empty($fix_key)) {
					continue; # from default skin
				}

				# fix_key ::= crystal_blue_crystal | ...


				if (!empty($fix['lst_data'][$fix_key])) {
					$_test_name = preg_replace(
						'!^skin1/!',
						$fix['path'].'/',
						$file);

					if (is_array($fix['lst_added'][$fix_key])
					&& in_array($_test_name, $fix['lst_added'][$fix_key])) {
						# new files
						$override_list[$file] = array (
							$file, $_test_name,
							'', # empty md5
							'copy' # copy flag
						);
					}
					elseif (isset($fix['lst_data'][$fix_key][$file])) {
						# file diff present in upgrade
						$section_data = $fix['lst_data'][$fix_key];
						$override_list[$file] = $section_data[$file];
					}
				}
				elseif (!empty($fix['files'][$short_name])) {
					# changes for this file are not necessary
					# because it doesn't changed in
					# installed color/dingbats/layout
					$override_list[$file] = false;
				}
			}
		}
		break;
	}

	#
	# PART3: finalize corrections
	#

	# correct $files_list
	foreach ($override_list as $k=>$triple) {
		if (empty($triple))
			func_unset($files_list,$k);
		else
			$files_list[$k] = $triple;
	}
	ksort($files_list);

	# write changes to $_patch_files
	$_patch_files = array();
	foreach ($files_list as $triple) {
		$_patch_files[] = implode(',',$triple);
	}

	if (!empty($error)) return $error;

	return true; # success
}

function func_test_patch( $_patch_files, $is_upgrade=true ) {
	global $xcart_dir, $upgrade_repository, $target_version;
	global $ready_to_patch, $could_not_patch, $patch_cmd, $patch_rcmd;
	global $patch_lines;
	global $customer_files;
	global $patch_reverse;

	$could_not_patch	= 0;

	#
	# Parse patch info
	#

	echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>";
	echo func_get_langvar_by_name("txt_testing_patch_applicability",false,false,true)."<hr />\n";
	flush();

	if (!$is_upgrade)
		$_patch_files = func_prepare_list($_patch_files);

	foreach($_patch_files as $patch_file_info) {
		$patch_file_info = trim($patch_file_info);
		if ($patch_file_info == "" || $patch_file_info[0] == "#") continue;

		$parsed_info = preg_split( "/[ ,\t]/S", $patch_file_info);

		list($orig_file, $diff_file, $md5_sum) = $parsed_info;
		$is_copy = !empty($parsed_info[3]); # new file from schemes/*

		echo $orig_file." ... "; flush();

		$real_file = $xcart_dir.preg_replace(
			array(
				'!^(/(?:'.implode('|',$customer_files).'))$!S',
				'!^/admin!S',
				'!^/provider!S',
				'!^/partner!S' ),
			array(
				DIR_CUSTOMER.'\1',
				DIR_ADMIN,
				DIR_PROVIDER,
				DIR_PARTNER ),
			"/".$orig_file);

		if ($is_upgrade && $is_copy) {
			# new file. comes from schemes/*
			$real_diff = $xcart_dir."/".$diff_file;
		}
		elseif ($is_upgrade) {
			$real_diff = $upgrade_repository."/".$target_version."/".$diff_file;
		}
		else {
			$real_diff = $diff_file;
		}

		$patch_file = array(
			"orig_file" => $orig_file,
			"diff_file" => $diff_file,
			"real_file" => $real_file,
			"real_diff" => $real_diff,
			"md5_sum"   => $md5_sum,
			"is_copy"   => $is_copy,
			"status"    => "OK");

		if ($is_upgrade && !$is_copy) {
			# check checksums
			if ($md5_file = @file($real_diff)) {
				if (count($patch_lines) < 150)
					$patch_lines = func_array_merge($patch_lines, $md5_file);

				if ($md5_sum != md5(implode("", $md5_file))) {
					$patch_file["status"] = "checksum error";
				}
			}
			else {
				$patch_file["status"] = "not found";
			}
		}

		if (!file_exists($real_file) && ($is_copy || func_pch_is_create_new($real_diff, $patch_reverse))) {
			# assume diff will create new file
			$dir = dirname($real_file);

			if ($patch_file["status"] == "OK" && (!file_exists($dir) || !is_dir($dir))) {
				$patch_file["status"] = "directory not found";
			}

			if ($patch_file["status"] == "OK" && !is_writable($dir)) {
				$patch_file["status"] = "directory non-writable";
			}
		}
		else {
			# check write permissions
			if ($patch_file["status"] == "OK" && !file_exists($real_file)) {
				$patch_file["status"] = "not found";
			}

			if ($patch_file["status"] == "OK" && !is_file($real_file)) {
				$patch_file["status"] = "not a file";
			}

			if ($patch_file["status"] == "OK" && !is_writable($real_file)) {
				$patch_file["status"] = "non-writable";
			}
		}

		if ($patch_file["status"] != "OK")
			$ready_to_patch = false;

		# check patch applicability
		if ($patch_file["status"] == "OK") {
			$patch_result_ = array();
			$rejects_ = false;
			$patch_errorcode_ = !func_patch_apply($real_file, $real_diff, false, false, $patch_result_, $rejects_, true, $patch_reverse);
			if ($patch_errorcode_ != 0) {
				# check for applied patches:
				$patch_result_ = array();
				$rejects_ = false;
				$patch_errorcode_ = !func_patch_apply($real_file, $real_diff, false, false, $patch_result_, $rejects_, true, !$patch_reverse);
				if ($patch_errorcode_ == 0)
					$patch_file["status"] = "<font color=blue>already patched</font>";
				else {
					$patch_file["status"] = "<font color=red>could not patch</font>";
					$could_not_patch ++;
					$patch_file["testapply_failed"] = 1;
				}
			}
		}

		$patch_files[] = $patch_file;
		echo $patch_file["status"]."<br />\n"; flush();
	}

	return $patch_files;
}

#
# Function to store data in temporaly file
# Return value: filename on success, FALSE overwise.
#
function func_store_in_tmp($data, $serialize = true) {
	global $file_temp_dir;

	$file = tempnam($file_temp_dir,"xctmp");
	if (!$file) return false;

	$fp = @fopen($file,"w");
	if (!$fp) {
		@unlink($file);
		return false;
	}

	if ($serialize) $data = serialize($data);

	if (@fwrite($fp, $data) != strlen($data)) {
		@fclose($fp);
		@unlink($file);
		return false;
	}

	@fclose($fp);
	return $file;
}

#
# This function is used to make window scrolled to the bottom edge
#
function func_auto_scroll($title) {
	global $admin_safe_mode;

	if (!$admin_safe_mode) {
		echo "<script language='javascript'> loaded = false; function refresh() { window.scroll(0, 100000); if (loaded == false) setTimeout('refresh()', 1000); } setTimeout('refresh()', 1000); </script>".$title;
		flush();
	}
}

function func_store_phase_result() {
	global $patch_phase_results_file, $phase_result;

	x_session_register("patch_phase_results_file");
	$patch_phase_results_file = func_store_in_tmp($phase_result);

	if ($patch_phase_results_file !== false) {
		x_session_save();
		func_html_location("patch.php?mode=result",0);
	}
	else {
		#
		# Error saving phase results in temporaly storage
		#
		x_session_unregister("patch_phase_results_file");
		die("Upgrade/patch process cannot continue:<br />There is a problem saving temporaly data at your server. Please check permissions and/or amount of free space in your TEMP directory.<br /><br /><a href=\"patch.php\">Click here to return to X-Cart</a>");
	}
}

function func_restore_phase_result($remove_files = false) {
	global $phase_result, $patch_phase_results_file, $patch_files;

	x_session_register("patch_phase_results_file");
	$phase_result = false;

	if ($patch_phase_results_file !== false) {
		ob_start();
		@readfile($patch_phase_results_file);
		$phase_result = unserialize(ob_get_contents());
		ob_end_clean();
		if ($remove_files)
			@unlink($patch_phase_results_file);
	}

	if ($remove_files) {
		x_session_unregister("patch_phase_results_file");
	}
}

function remove_tmp_files($patch_files) {
	#
	# Removing temporaly files
	#
	if (isset($patch_files[0])) {
		foreach ($patch_files as $f)
			@unlink($f["real_diff"]);
	}
}

function func_pch_is_create_new($patchfile, $reverse) {
	if (!file_exists($patchfile))
		return false;

	$patch = file($patchfile);

	$started = false;
	$regexp = '!^'.($reverse?'-':'\+').'!S';
	foreach ($patch as $line) {
		if (!$started) {
			if (!strncmp($line, '@@', 2))
				$started = true;

			continue;
		}

		if (!preg_match($regexp, $line))
			return false;
	}

	return true;
}

?>
