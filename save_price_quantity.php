<?php
//////////////////////////////
//this is save_price_quantity.php

//this script saves the data 
//in PRICE_QUANTITY to a remote text file
//////////////////////////////

//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('config.php');
require_once('functions.php');


//name of the output file
$local_outfile = 'upload/price_quantity.txt';
$remote_outfile = $feedSettings[5]['upload_path'];


////////////////////////////
//MAIN

//string which we'll concat all the data on to
//we'll initialize it as the column titles
$data_string =  'store code' . "\t" . 
		'itemid' . "\t" .
		'item_group_id' . "\t" .
		'quantity' . "\t" . 
		'price' . "\t" . 
		'availability' . "\n"; 


/////////////////////
//here's the query
$query = "select * from PRICE_QUANTITY where 1";
$results = mysql_query($query);
//check for general error
if(!$results){
	$error_message = "Im sorry, there was a database error";
	errorHandler($error_message);
}
/////////////////////



//////////////////////////////////////
//////////////////////////////////////
//loop through the results and concat all the
//pertinent data on to the string
while( $row = mysql_fetch_assoc($results) ){

	//get the values we need
	$store_id = stripslashes($row['StoreID']);
	$itemid = stripslashes($row['itemid']);
	$item_group_id = stripcslashes($row['item_group_id']);
	$quantity = stripslashes($row['Quantity']);
	$price = stripslashes($row['PriceOverride']);
	$availability = stripslashes($row['Availability']);

	//concat the data onto our string
	//using tabs as delimiters
	$data_string .= $store_id . "\t" . 
			$itemid . "\t" .
			$item_group_id . "\t" .
			$quantity . "\t" . 
			"USD $price" . "\t" . 
			$availability . "\n"; 
}

//////////////////////////////////////
//////////////////////////////////////

//save the data to a local text file
echo "Creating Google Price-Quantity file<br />";
saveLocalTextFile($data_string, $local_outfile);

////////////////////////////
// Now upload file via FTP
echo "Uploading Google Price-Quantity file...<br />";
uploadFile($remote_outfile,$local_outfile,'Google Price-Quantity');
?>