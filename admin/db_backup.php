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
# $Id: db_backup.php,v 1.51.2.3 2006/08/11 07:52:28 max Exp $
#

@set_time_limit(2700);

require "./auth.php";
require $xcart_dir."/include/security.php";

x_load('files');

$location[] = array(func_get_langvar_by_name("lbl_db_backup_restore"), "");

$sqldump_file = $var_dirs["tmp"]."/xcartdump.sql";
$file_exists = file_exists($sqldump_file);
#
# While restoring database re-establish connection with mysql server before every Nth table row
#
$sql_reconnect_count = 100;
#
# Make tables backup by selecting N rows per query
#
$sql_backup_rows = 200;

$smarty->assign("sqldump_file", $sqldump_file);
$smarty->assign("file_exists", $file_exists);

$log_prefix = "Login: $login\nIP: $REMOTE_ADDR\nOperation: $mode\n----\n";

#
# Dump database code
#
if($REQUEST_METHOD=="POST" && $mode=="backup") {

	require $xcart_dir."/include/safe_mode.php";

	#
	# This function returns dump of the table
	#
	function dumpTableContent($table,$schema,$fd) {
		global $sql_backup_rows;

		if ($fd===false) {
			print "$schema\n\n";
		}
		else {
			fwrite($fd, "$schema\n\n");
			echo func_get_langvar_by_name("lbl_dumping_table_n", array("table" => $table),false,true)."<br />\n";
		}
		$rows_cnt=0;
		$current_row=0;

		$count = func_query_first_cell("SELECT count(*) FROM ".$table);
		if ($count < 1)
			return;

		for ($pos=0; $count > 0; $count -= $sql_backup_rows, $pos += $sql_backup_rows) {
			$local_query = "SELECT * FROM $table LIMIT $pos,$sql_backup_rows";
			$result = db_query($local_query);
			if ($result != FALSE) {
				$fields_cnt = mysql_num_fields($result);
				$rows_cnt   = mysql_num_rows($result);
				
				// Checks whether the field is an integer or not
				for ($j = 0; $j < $fields_cnt; $j++) {
					$field_set[$j] = mysql_field_name($result, $j);
					$type          = mysql_field_type($result, $j);
					if ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' || $type == 'bigint'  ||$type == 'timestamp') {
						$field_num[$j] = TRUE;
					}
					else {
						$field_num[$j] = FALSE;
					}
				} // end for
				
				// Sets the scheme
				$schema_insert = "INSERT INTO $table VALUES (";

				$search       = array("\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
				$replace      = array('0', '\n', '\r', '\Z');
				$current_row  = 0;

				while ($row = mysql_fetch_row($result)) {
					$current_row++;
					for ($j = 0; $j < $fields_cnt; $j++) {
						if (!isset($row[$j])) {
							$values[]     = 'NULL';
						}
						else if ($row[$j] == '0' || $row[$j] != '') {
							// a number
							if ($field_num[$j]) {
								$values[] = $row[$j];
							}
							// a string
							else {
								$values[] = "'" . str_replace($search, $replace, addslashes($row[$j])) . "'";
							}
						}
						else {
							$values[]     = "''";
						} // end if
					} // end for

					// Extended inserts case
					$insert_line  = $schema_insert . implode(', ', $values) . ')';
					unset($values);

					// Send the line
					if ($fd===false) {
						print $insert_line.";\n";
						flush();
					}
					else {
						fwrite($fd, $insert_line.";\n");
						fflush($fd);
					}

					// loic1: send a fake header to bypass browser timeout if data
					//        are bufferized
				} // end while
			} // end if ($result != FALSE)

			db_free_result($result);
			if ($fd!==false) {
				echo "."; func_flush();
			}
		} // for

		if ($fd===false) {
			print "\n";
		}
		else {
			fwrite($fd,"\n");
			echo "<br />";
		}
	}

	$destination = "browser";
	if ($HTTP_POST_VARS["write_to_file"]) {
		if ($fd = func_fopen($sqldump_file, "w", true))
			$destination = "file";
	}

	if ($destination == "browser") {
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=db_backup.sql");
	}
	else {
		echo func_get_langvar_by_name("lbl_db_backup_in_progress",false,false,true)."<hr /><br />\n";
?>
<script type="text/javascript">
<!--
loaded = false;
function refresh() {
	window.scroll(0, 100000);
	if (loaded == false)
		setTimeout('refresh()', 1000);
}
setTimeout('refresh()', 1000);
-->
</script>
<?php
		func_flush();
	}

	# Include disabled modules
	$disabled_modules = func_query_column("SELECT module_name FROM $sql_tbl[modules] WHERE active != 'Y'");
	if (!empty($disabled_modules)) {
		foreach($disabled_modules as $mn) {
			if (file_exists($xcart_dir."/modules/".$mn."/config.php"))
				include_once $xcart_dir."/modules/".$mn."/config.php";

		}
	}

	$tables = db_query('SHOW TABLES');
	while ($table = db_fetch_row($tables)) {
		$table = $table[0];
		if (constant("X_DEF_OS_WINDOWS") && !in_array($table, $sql_tbl)) {
			foreach ($sql_tbl as $t) {
				if (strtoupper($t) == strtoupper($table)) {
					$table = $t;
				}
			}
		}

		if (!in_array($table, $sql_tbl))
			continue;

		# Uncomment the line below if you experience problems with data restoring
		# from fedex's tables (you can import it again later)
		#	  if (in_array($table, array($sql_tbl[fedex_rates], $sql_tbl[fedex_zips]))) continue;
		$schema = "CREATE TABLE $table (\n";
		$fields = db_query("SHOW FIELDS FROM ".$table);
		$sflag = false;
		while ($field = db_fetch_array($fields)) {
			if ($sflag == true) {
				$schema .= ",\n";
			}

			$schema .= '  '.$field['Field'].' '.$field['Type'];
			if ($field['Null'] != 'YES') {
				$schema .= ' NOT NULL';
			}

			if ($field['Default'] !== NULL) {
				$schema .= " default '".$field['Default']."'";
			}

			if (isset($field['Extra'])) {
				$schema .= ' '.$field['Extra'];
			}

			$sflag = true;
		}

		# Add the keys
		$index = array();
		$keys = db_query("SHOW KEYS FROM ".$table);
		if ($keys != FALSE) {
			while ($key = db_fetch_array($keys)) {
				$kname = $key['Key_name'];
				if ($kname == "PRIMARY") {
					$kname = "PRIMARY KEY";
				} elseif ($key['Non_unique'] == 0) {
					$kname = "UNIQUE ".$kname;
				} elseif ($key['Comment'] == "FULLTEXT") {
					$kname = "FULLTEXT ".$kname;
				} else {
					$kname = "KEY ".$kname;
				}

				if (!isset($index[$kname])) {
					$index[$kname] = array();
				}
				
				$index[$kname][] = $key['Column_name'];
			}
		}

		foreach ($index as $kname => $columns) {
			$schema .= ",\n  ".$kname." (".implode(",",$columns).")";
		}

		$schema .= "\n) TYPE=MyISAM;";

		dumpTableContent($table,$schema,$destination=="file"?$fd:false);
	}
	db_free_result($tables);
	db_query("UPDATE $sql_tbl[config] SET value='".time()."' WHERE name='db_backup_date' AND category=''");

	if ($destination == "file") {
		fclose($fd);
		chmod($sqldump_file, 0666);
		$top_message["content"] = func_get_langvar_by_name("msg_adm_db_backup_success"). " '$sqldump_file'";
		echo "<hr />".func_get_langvar_by_name("lbl_done",false,false,true).".";
?>
<script type="text/javascript">
<!--
loaded = true;
-->
</script>
<?php
		func_flush();
		x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("msg_adm_db_backup_success",false,false,true));
		func_html_location("db_backup.php",10);
	}
	else {
		x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("lbl_done",false,false,true));
	}

	exit;
}

#
# Restore database code
#
if ($REQUEST_METHOD=="POST" && $mode=="restore" && empty($HTTP_POST_VARS["local_file"]) && $HTTP_POST_FILES["userfile"]["error"]) {
	$upload_error_codes = array();
	$upload_error_codes[1] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
	$upload_error_codes[2] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.";
	$upload_error_codes[3] = "The uploaded file was only partially uploaded.";
	$upload_error_codes[4] = "No file was uploaded.";

	$top_message["content"] = func_get_langvar_by_name("msg_adm_err_file_upload")." [".$HTTP_POST_FILES["userfile"]["error"].": ".$upload_error_codes[$HTTP_POST_FILES["userfile"]["error"]]."]";
	$top_message["type"] = "E";

	x_log_flag('log_database', 'DATABASE', $log_prefix.$top_message["content"]);
	func_header_location("db_backup.php");
}

if ($REQUEST_METHOD=="POST" && $mode=="restore" && (($userfile!="none" && $userfile!="") || ($HTTP_POST_VARS["local_file"] && $file_exists))) {

	require $xcart_dir."/include/safe_mode.php";

	x_log_flag('log_database', 'DATABASE', $log_prefix.'Started');

	$source_file = "uploaded";
	if ($HTTP_POST_VARS["local_file"]) {
		if ($fd = func_fopen($sqldump_file, "r", true))
			$source_file = "local";
	}


	if ($source_file == "uploaded")
		$userfile = func_move_uploaded_file("userfile");
	else
		$userfile = $sqldump_file;

	$fp = func_fopen($userfile, "rb", true);
	if ($fp === false) {
		$top_message["content"] = func_get_langvar_by_name("msg_adm_err_sql_file_not_found");
		$top_message["type"] = "E";
		x_log_flag('log_database', 'DATABASE', $log_prefix.func_get_langvar_by_name("msg_adm_err_sql_file_not_found",false,false,true));
		func_header_location("db_backup.php");
	}

	$command = "";
	echo func_get_langvar_by_name("lbl_please_wait",false,false,true)."<br />\n";
	$cmdcnt = 0;
	while (!feof($fp)) {
		$c = fgets($fp, 1500000);
		$c = chop($c);
		$c = ereg_replace("^[ \t]*(#|-- |---*).*", "", $c);
		$command.=$c;
		if (ereg(";$",$command)) {
			$command=ereg_replace(";$","",$command);
			if (ereg("CREATE TABLE ", $command)) {
				$table_name = ereg_replace(" .*$", "", eregi_replace("^.*CREATE TABLE ", "", $command));
				if( $cmdcnt > 1 ) echo "<br />\n";
				echo func_get_langvar_by_name("lbl_restoring_table_n", array("table" => $table_name),false,true)."<br />\n";

				flush();
				db_query("drop table if exists $table_name");
				$cmdcnt = 0;
			}

			$cmdcnt ++;
			if ($sql_reconnect_count>0 && $cmdcnt % $sql_reconnect_count == 0) {
				#
				# While restoring database re-establish connection
				# with mysql server before every Nth table row
				#
				db_connect($sql_host, $sql_user, $sql_password);
				db_select_db($sql_db) || die("Could not connect to SQL db");
			}

			db_query($command);
			if ($cmdcnt %   20 == 0)
				func_flush(".");
			if ($cmdcnt % 3000 == 0)
				func_flush("<br />\n");

			$myerr = mysql_error ();
			if (!empty($myerr)) {
				echo $myerr;
				break;
			}

			$command="";
			flush();
		}
	}

	if( $cmdcnt > 0 ) echo "<br />\n";

	fclose($fp);
	if ($source_file == "uploaded")
		@unlink($userfile);

	$smarty->clear_compiled_tpl(); # language variables may change

	echo "<p><b>".func_get_langvar_by_name("lbl_db_restored_successfully",false,false,true)."</b><p><a href=\"db_backup.php\">".func_get_langvar_by_name("lbl_go_back",false,false,true)."</a>";
	exit;
}

$smarty->assign("upload_max_filesize", ini_get("upload_max_filesize"));
#
# Smarty display code goes here
#
$smarty->assign("main","db_backup");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);
?>
