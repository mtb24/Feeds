<?php 
//////////////////////////////
//this is parse_specialized.php

//////////////////////////////

//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('functions.php');

//name of the text datafile
$datafile = $feedSettings[8]['file'];



//open the file
$handle = fopen($datafile, "r");

//////////////////////////////
if($handle){

	$count = 0;

    ///////////////////////
    // loop through line by line, and do any text processing required
    while( ($line = fgets($handle, 4096) ) !== false){

		$exploded_tab_array = explode("\t", trim($line) );
		//echo "<pre>";
		//var_dump($exploded_tab_array);
		//echo '</pre><br>';


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
		$google_product_category = $exploded_tab_array[7];  //str_replace($SE_categories, $Google_categories, $product_type);
		$price = $exploded_tab_array[8];
		$availability = $exploded_tab_array[9];
		$expiration_date = $exploded_tab_array[10];
		$itemid = $exploded_tab_array[11];
		$mpn = $exploded_tab_array[12];
		$gtin = $exploded_tab_array[13];
		$color = $exploded_tab_array[14];
		$size = $exploded_tab_array[15];
		$shipping_weight = $exploded_tab_array[16];
		$shipping = '';
		$gender = $exploded_tab_array[17];
		$age_group = $exploded_tab_array[18];


		///////////////
		//hack so we don't insert the first line
		if($count > 0){
			
			//call our function to add a new Item and get back the InsertID
			$item_id = insertNewOnlineListing($link, $condition, $brand, $title, $description, $image_link, $product_type, $google_product_category, $price, $availability, $expiration_date, $itemid, $mpn, $gtin, $color, $size, $shipping_weight, $shipping, $gender, $age_group);
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
    //storeItemCount('SE', $count); // DO NOT STORE
}
//all done!
echo '<br />Finished processing Specialized Products file! - '.$count.' items<br /><br />';
?>