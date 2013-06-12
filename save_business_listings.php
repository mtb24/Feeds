<?php

/*
//////////////////////////////
//////////////////////////////
//this is save_business_listings.php

//this script saves the data 
//in BUSINESS_LISTINGS to a remote text file
//////////////////////////////
//////////////////////////////
*/


//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('functions.php');


//name of the output file
$local_outfile = 'upload/google_business_listings.txt';
$remote_outfile = $feedSettings[3]['upload_path'];


////////////////////////////
//MAIN

//string which we'll concat all the data on to
//we'll initialize it as the column titles
$data_string =  'store code' . "\t" . 
		'name' . "\t" . 
		'main phone' . "\t" . 
		'address line 1' . "\t" . 
		'address line 2' . "\t" . 
		'city' . "\t" . 
		'state' . "\t" . 
		'postal code' . "\t" . 
		'country code' . "\t" . 
		'home page' . "\t" . 
		'category' . "\t" . 
		'hours' . "\t" . 
		'description' . "\t" . 
		'currency' . "\t" . 
		'established date' . "\t" . 
		'latitude' . "\t" . 
		'longitude' . "\n"; 


/////////////////////
//here's the query
$query = "select * from BUSINESS_LISTINGS where 1";
$results = mysql_query($query);
//check for general error
if(!$results){
	$error_message = "Im sorry, there was a database select error";
	errorHandler($error_message);
}
/////////////////////



//////////////////////////////////////
//////////////////////////////////////
//loop through the results and concat all the
//pertinent data on to the string
while( $row = mysql_fetch_assoc($results) ){

	//get the values we need
	$store_code = stripslashes($row['StoreID']);
	$store_name = stripslashes($row['StoreName']);
	$main_phone_num = stripslashes($row['MainPhoneNum']);
	$address_1 = stripslashes($row['Address1']);
	$address_2 = stripslashes($row['Address2']);
	$city = stripslashes($row['City']);
	$state = stripslashes($row['State']);
	$post_code = stripslashes($row['PostCode']);
	$country = stripslashes($row['Country']);
	$url = stripslashes($row['URL']);
	$category = stripslashes($row['Category']);
	$hours = stripslashes($row['Hours']);
	$description = stripslashes($row['Description']);
	$currency = stripslashes($row['Currency']);
	$established_date = stripslashes($row['EstablishedDate']);
	$latitude = stripslashes($row['Latitude']);
	$longitude = stripslashes($row['Longitude']);

	//concat the data onto our string
	//using tabs as delimiters
	$data_string .= $store_code . "\t" . 
			$store_name . "\t" . 
			$main_phone_num . "\t" . 
			$address_1 . "\t" . 
			$address_2 . "\t" . 
			$city . "\t" . 
			$state . "\t" . 
			$post_code . "\t" . 
			$country . "\t" . 
			$url . "\t" . 
			$category . "\t" . 
			$hours . "\t" . 
			$description . "\t" . 
			$currency . "\t" . 
			$established_date . "\t" . 
			$latitude . "\t" . 
			$longitude . "\n"; 
}
//echo "<br><br>data_string is $data_string<br>";

//////////////////////////////////////
//////////////////////////////////////

//save the data to a local text file
echo "Creating Google Business Listings file...<br />";
saveLocalTextFile($data_string, $local_outfile);

////////////////////////////
// Now upload file via FTP
echo "Uploading Google Business Listings file...<br />";
uploadFile($remote_outfile,$local_outfile,'Google Business Listings');
?>