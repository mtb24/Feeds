<?php 

/*
//////////////////////////////
//////////////////////////////
//this is parse_inv.php

//this script parses the inv.txt file
//and inserts records in the database
//table called LOCAL_PRODUCT_LISTINGS
//it then creates commensurate records 
//in the PRICE_QUANTITY table
//////////////////////////////
//////////////////////////////
*/

//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('mb_feeds_functions.php');

//name of the text datafile
$datafile = $feedSettings[1]['file'];
//$datafile = 'download/inv.txt';

//open the file
$handle = fopen($datafile, "r");

$mpn_matches = 0;
$gtin_matches = 0;

//////////////////////////////
if($handle){

    echo "RPRO file downloaded. Opening...<br />";
	
    //empty the LOCAL_PRODUCT_LISTINGS table
    emptyTable('LOCAL_PRODUCT_LISTINGS'); 

    //empty the PRICE_QUANTITY table
    emptyTable('PRICE_QUANTITY'); 

    //declare a counter so we don't insert the first line
    $count = 0;

    ///////////////////////
    //loop through line by line
    while( ($line = fgets($handle, 4096) ) !== false){

	///////////////
	//hack so we don't insert the first line
	if($count > 0){

		$exploded_tab_array = explode("\t", $line );
		//echo "<pre>";
		//var_dump($exploded_tab_array);
		//echo '</pre><br>';

		//assign some reference variables
		$sku = $exploded_tab_array[0];
		$mpn = $exploded_tab_array[1];
		$brand = $exploded_tab_array[2];
		$description = $exploded_tab_array[3];
		$last_modified = $exploded_tab_array[4];
		$price = $exploded_tab_array[5];
		$sales_price = $exploded_tab_array[6];
		$sale_start_date = $exploded_tab_array[7];
		$sale_end_date = $exploded_tab_array[8];
		$min_reorder = $exploded_tab_array[9];
		$max_reorder = $exploded_tab_array[10];
		$boh = $exploded_tab_array[11];
		$boh_location = $exploded_tab_array[12];
		$boo = $exploded_tab_array[13];
		$last_sold_date = $exploded_tab_array[14];
		$created_date = $exploded_tab_array[15];
		$active = $exploded_tab_array[16];
		$display = $exploded_tab_array[17];
		$gtin1 = $exploded_tab_array[18];
		$gtin2 = $exploded_tab_array[19];


		// prepend '00' the GTIN
		$concatted_gtin = '00' . $gtin1;

		// Check if a matching record exists in ONLINE_LISTINGS table
		$mpn_assoc = getOnlineListingAssocFromMPN($mpn);
		$gtin_assoc = getOnlineListingAssocFromGTIN($concatted_gtin);
		

		//////////////////////////
		// IF MATCHING GTIN FOUND
		if($gtin_assoc){
			
			$gtin_matches++;

			// Check if item is in a group, and update tables if true
			$online_id = $gtin_assoc['OldID'];
			$item_group_id = (checkItemsForGroups($online_id)) ? '"'.my_split($online_id).'"' : '';
			
			// insert a new record in LOCAL_PRODUCT_LISTINGS
			$insert_id = insertNewLocalProductListing(
								$description,
								$online_id,
								$item_group_id,
								$gtin1, 
								$mpn, 
								$brand, 
								$price, 
								'new', 
								$gtin_assoc['Link'], 
								$gtin_assoc['ImageLink'], 
								$gtin_assoc['Size'],  
								$gtin_assoc['Color']);
			// reset the array before the next row
			//$gtin_assoc = array();

		}
		//////////////////////////


		//////////////////////////
		// IF MATCHING MPN FOUND
		else if($mpn_assoc){

			$mpn_matches++;

 			// Check if item is in a group, and update tables if true
			$online_id = $mpn_assoc['OldID'];
			$item_group_id = (checkItemsForGroups($online_id)) ? '"'.my_split($online_id).'"' : '';
			
			// insert a new record in LOCAL_PRODUCT_LISTINGS
			$insert_id = insertNewLocalProductListing(
								$description,
								$online_id,
								$item_group_id,
								$gtin1, 
								$mpn, 
								$brand, 
								$price, 
								'new', 
								$mpn_assoc['Link'], 
								$mpn_assoc['ImageLink'], 
								$mpn_assoc['Size'],  
								$mpn_assoc['Color']);
		}

		///////////////////////////////////////////
		// if there was a GTIN or MPN match insert into PRICE_QUANTITY
		// THEN update tables if item is in a group
		if($mpn_assoc || $gtin_assoc){
			
			//split up the boh_location first on *three pipes*
			$exploded_pipe_array = explode('|||', $boh_location);
		
			//now we also need to split each element of
			//that array to get the store name and number of items
			foreach($exploded_pipe_array as $my_var){

				$store_array = explode('||', $my_var);
				$store_name = $store_array[0];
				$quantity = $store_array[1];

				//call our function to get this storeID from the name
				$store_id = getStoreIDFromName($store_name);

				//insert the new records in the PRICE_QUANTITY table
				if($quantity > 1){
					insertNewPriceQuantity(
						       $store_id, 
						       $online_id,
						       $item_group_id,
						       $quantity,
						       $price,
						       'in stock');
				}
			}
		}
		///////////////////////////////////////////
	}
	//incremement the count so we don't insert the first line containing field headers
	$count++;
	///////////////
    }
    /////////////////////// END of ROW

    ///////////////////////
    //error check
    if( !feof($handle) ){
        echo '<span style="color:red;">Error: unexpected fgets() fail</span><br />';
    }
    ///////////////////////
    fclose($handle);
    
    
    // output numbers
    $total_matches = ($mpn_matches+$gtin_matches);
    echo "<br />Finished processing RPRO file - $count items<br />Finished matching items between SE and RPRO files<br />MPN Matches: $mpn_matches<br />GTIN Matches: $gtin_matches<br />";
    echo "======================<br />";
    echo "Total matching items - $total_matches<br /><br />";
    
    // store item counts in DB
    storeItemCount('RPRO', $count);
    storeItemCount('MPN', $mpn_matches);
    storeItemCount('GTIN', $gtin_matches);
    storeItemCount('total_matches', $total_matches);
    
    exit;
    
} else {
	
    // Ooops! There was a problem
    echo '<span style="color:red;">ERROR! Could not open RPRO file</span>';
    exit;
}
?>