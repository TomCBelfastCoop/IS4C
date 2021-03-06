<?php
/*******************************************************************************

    Copyright 2011 Whole Foods Co-op, Duluth, MN

    This file is part of Fannie.

    IT CORE is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IT CORE is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IT CORE; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/
include('../../config.php');
include($FANNIE_ROOT.'src/mysql_connect.php');

$page_title = "Fannie :: Patronage Tools";
$header = "Update Net Purchases";

include($FANNIE_ROOT.'src/header.html');

$q = "UPDATE patronage_workingcopy SET
	net_purch = purchase + discounts + rewards";
$r = $dbc->query($q);
echo '<i>Net purchases updated</i>';

echo '<br /><br />';
echo '<a href="index.php">Patronage Menu</a>';
include($FANNIE_ROOT.'src/footer.html');
?>
