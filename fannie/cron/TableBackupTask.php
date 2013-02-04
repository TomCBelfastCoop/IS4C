<?php
/*******************************************************************************

    Copyright 2011 Whole Foods Co-op

    This file is part of Fannie.

    Fannie is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Fannie is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IT CORE; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

/* HELP

   nightly.table.snapshot.php

   Copies table contents to a backup table
   Currently applies to products & custdata.

*/

if (!isset($FANNIE_ROOT))
	include_once(dirname(__FILE__).'/../config.php');
include_once($FANNIE_ROOT.'src/SQLManager.php');
include_once($FANNIE_ROOT.'classlib2.0/FannieTask.php');
include_once($FANNIE_ROOT.'classlib2.0/data/FannieDB.php');

class TableBackupTask extends FannieTask {

	public $nice_name = 'Table Backups';
   	public $help_info = 'Copies table contents to a backup table
   Currently applies to products & custdata.';

	function run(){
		global $FANNIE_OP_DB, $FANNIE_SERVER_DBMS;
		set_time_limit(0);

		$sql = FannieDB::get($FANNIE_OP_DB);

		// drop and recreate because SQL Server
		// really hates identity inserts
		$sql->query("DROP TABLE productBackup");
		if ($FANNIE_SERVER_DBMS == "MSSQL"){
			$sql->query("SELECT * INTO productBackup FROM products");
		}
		else {
			$sql->query("CREATE TABLE productBackup LIKE products");
			$sql->query("INSERT INTO productBackup SELECT * FROM products");
		}

		$sql->query("DROP TABLE custdataBackup");
		if ($FANNIE_SERVER_DBMS == "MSSQL"){
			$sql->query("SELECT * INTO custdataBackup FROM custdata");
		}
		else {
			$sql->query("CREATE TABLE custdataBackup LIKE custdata");
			$sql->query("INSERT INTO custdataBackup SELECT * FROM custdata");
		}

	}
}

if (php_sapi_name() === 'cli'){
	$obj = new TableBackupTask();
	$obj->run();
}
?>