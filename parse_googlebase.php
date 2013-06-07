<?php 
//////////////////////////////
//this is parse_googlebase.php

//////////////////////////////

//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('mb_feeds_functions.php');

//name of the text datafile
$datafile = $feedSettings[0]['file'];



//open the file
$handle = fopen($datafile, "r");

//////////////////////////////
if($handle){

	//empty the ONLINE_LISTINGS table
	emptyTable('ONLINE_LISTINGS'); 

	$count = 0;
	$shipping_deal_count = 0;

    ///////////////////////
    // loop through line by line, and do any text processing required
    while( ($line = fgets($handle, 4096) ) !== false){

		$exploded_tab_array = explode("\t", trim($line) );
		//echo "<pre>";
		//var_dump($exploded_tab_array);
		//echo '</pre><br>';

		$shipping = "";
		$gender = '';
		$age_group = '';

		//assign some reference variables
		$link = $exploded_tab_array[0];
		$condition = $exploded_tab_array[1];
		$brand = $exploded_tab_array[2];
		$title = stripHTML($exploded_tab_array[3]);
		$title = str_replace(',','',$title);
		$description = stripHTML($exploded_tab_array[4]);
		$description = str_replace(',','',$description);
		$image_link = $exploded_tab_array[5];
		$product_type = $exploded_tab_array[6];
		$google_product_category = $exploded_tab_array[7];
		$price = $exploded_tab_array[8];
		$availability = $exploded_tab_array[9];
		$expiration_date = $exploded_tab_array[10];
		$id = $exploded_tab_array[11];
		$mpn = $exploded_tab_array[12];
		$gtin = $exploded_tab_array[13];
		$color = $exploded_tab_array[14];
		$size = $exploded_tab_array[15];
		$shipping_weight = $exploded_tab_array[16];
		(in_array($mpn, $freeShippingByMPN)) ? $shipping = 'US:::0.00 USD' : $shipping = '';

		///////////////
		//hack so we don't insert the first line
		if($count > 0){
			
			//call our function to add a new Item and get back the InsertID
			$item_id = insertNewOnlineListing(
						$link,
						$condition,
						$brand,
						$title,
						$description,
						$image_link,
						$product_type,
						$google_product_category,
						$price,
						$availability,
						$expiration_date,
						$id,
						$mpn,
						$gtin,
						$color,
						$size,
						$shipping_weight,
						$shipping,
						$gender,
						$age_group);
		}

		//incremement the counter
		$count++;
		///////////////
    }
    ///////////////////////
    //error check
    if( !feof($handle) ){
        echo "Error: unexpected fgets() fail\n";
    }
    ///////////////////////
    fclose($handle);
    
    // store item count
    storeItemCount('SE', $count);
}
//all done!
echo '<br />Finished processing SE file! - '.$count.' items<br /><br />';
?>