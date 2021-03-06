<?php
/*******************************************************************************

    Copyright 2012 Whole Foods Co-op

    This file is part of IT CORE.

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
/** 
   @class MoreThanQttyPM
   
   This method provides a discount
   on all items in the group beyond
   the required quantity. Discount is
   reflected as a percentage in groupprice.
   Groups are defined via mixmatch.
*/

class MoreThanQttyPM extends PriceMethod {

	function addItem($row,$quantity,$priceObj){
		if ($quantity == 0) return false;

		$pricing = $priceObj->priceInfo($row,$quantity);

		/* group definition: number of items
		   that make up a group, price for a
		   full set. Use "special" rows if the
		   item is on sale */
		$groupQty = $row['quantity'];
		$groupPrice = $row['groupprice'];
		if ($priceObj->isSale()){
			$groupQty = $row['specialquantity'];
			$groupPrice = $row['specialgroupprice'];	
		}

		/* count items in the transaction
		   from the given group */
		$mixMatch  = $row["mixmatchcode"];
		$queryt = "select sum(ItemQtty) as mmqtty, 
			mixMatch from localtemptrans 
			where trans_status <> 'R' AND 
			mixMatch = '".$mixMatch."' group by mixMatch";
		if (!$mixMatch || $mixMatch == '0') {
			$mixMatch = 0;
			$queryt = "select sum(ItemQtty) as mmqtty from "
				."localtemptrans where trans_status<>'R' AND "
				."upc = '".$row['upc']."' group by upc";
		}
		$dbt = Database::tDataConnect();
		$resultt = $dbt->query($queryt);
		$num_rowst = $dbt->num_rows($resultt);

		$trans_qty = 0;
		if ($num_rowst > 0){
			$rowt = $dbt->fetch_array($resultt);
			$trans_qty = floor($rowt['mmqtty']);
		}

		$trans_qty += $quantity;

		/* if purchases exceed then requirement, apply
		   the discount */
		if ($trans_qty >= $groupQty){
			$discountAmt = $pricing['unitPrice'] * $groupPrice;
			$pricing['discount'] = $discountAmt;
			$pricing['unitPrice'] -= $discountAmt;
		}
	
		TransRecord::addItem($row['upc'],
			$row['description'],
			'I',
			' ',
			' ',
			$row['department'],
			$quantity,
			$pricing['unitPrice'],
			MiscLib::truncate2($pricing['unitPrice'] * $quantity),
			$pricing['regPrice'],
			$row['scale'],
			$row['tax'],
			$row['foodstamp'],
			$pricing['discount'],		
			0,	
			$row['discount'],
			$row['discounttype'],
			$quantity,
			($priceObj->isSale() ? $row['specialpricemethod'] : $row['pricemethod']),
			($priceObj->isSale() ? $row['specialquantity'] : $row['quantity']),
			($priceObj->isSale() ? $row['specialgroupprice'] : $row['groupprice']),
			$row['mixmatchcode'],
			0,
			0,
			(isset($row['cost'])?$row['cost']*$quantity:0.00),
			(isset($row['numflag'])?$row['numflag']:0),
			(isset($row['charflag'])?$row['charflag']:'')
		);
	}
}

?>
