<?php

/////////////////////////////////////////////
//this is mb_feeds_functions.php
//by Ken Downey <ken.downey@mikesbikes.com>
/////////////////////////////////////////////


//TURN ERROR REPORTING OFF
//error_reporting(0);

//set an unlimited execution time
set_time_limit(0);

//increased error reporting
//error_reporting(E_ALL | E_STRICT);
//ini_set('display_errors', 'On');

//this setting needs to be enabled to parse on some platforms
ini_set('auto_detect_line_endings', TRUE);

/* Set timezone for date() */
date_default_timezone_set('America/Los_Angeles');

require_once('config.php');
//////////////////////////////////////////////
//////////////////////////////////////////////
//MACROS

//store values
define('GOOGLE_AFFILIATE_ID', '?utm_campaign=Google Products&utm_source=Google&utm_medium=Comparison Shopping&utm_nooverride=1');
define('QBIKE_AFFILIATE_ID',  '?utm_campaign=Q Bike&utm_source=Q Bike&utm_medium=Comparison Shopping&utm_nooverride=1');
//the max allowed uploaded filesize in bytes
//we're using five megabytes for now
define('MAX_UPLOAD_FILESIZE', 5242880);
define('THE_DATE', date("d m y") );
define('THE_TIME', time() );
//////////////////////////////////////////////
//////////////////////////////////////////////

//this is the general error_function
//this has to go near the top of the functions file
function errorHandler($error_message){
	//$relative_url = "error_page.php?error_message=$error_message";
	//header("Location: $relative_url");
	echo "$error_message<br>";
	exit;
}

///////////////////////////////////////////////////////
///////////////////////////////////////////////////////
//we start by making a connection to our database
$db = mysql_connect($host, $user, $pass);

if(!$db){
	$error_message = 'unable to connect to database';
	errorHandler($error_message);
}

$sel = mysql_select_db($db_name);


if(!$sel){
	$error_message = "I'm sorry, there was a problem with the database";
	errorHandler($error_message);
}
///////////////////////////////////////////////////////
///////////////////////////////////////////////////////

//generic function to remove problematic 
//characters from strings
function cleanUpString($string){

	//regex pattern which checks for anything besides 
	//case-insensitive alphanumerics, dots, hyphens, underscores, forward slashes, and parens
	$pattern = '/[^a-zA-Z0-9\.\-_]/';

	//strip out the bad chars
	$string = preg_replace($pattern, '', $string);

	//remove any single quotes
	$string = str_replace("'", '', $string);

	//remove any double quotes
	$string = str_replace('"', '', $string);
	
	// remove any commas
	//$string = str_replace(',', '', $string);

	//remove any HTML
	$string = strip_tags($string);

	//remove any slashes
	//IS THIS THE RIGHT SYNTAX?
	//$string = str_replace('/', '', $string);
	
	//return it
	return $string;
}





// function to combine Online and Local tables matched on MPN
$sql = "SELECT * FROM LOCAL_PRODUCT_LISTINGS, ONLINE_LISTINGS\n"
    . "WHERE ( (`LOCAL_PRODUCT_LISTINGS`.`MPN` != \'\') AND (`ONLINE_LISTINGS`.`MPN` != \'\') ) \n"
    . " AND\n"
    . " ( `LOCAL_PRODUCT_LISTINGS`.`MPN` = `ONLINE_LISTINGS`.`MPN` )";
		
	
//function to insert a new Item
//WE'RE NOT INSERTING ITEMGROUP ID FOR NOW
function insertNewOnlineListing(
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
				$age_group)
{
	
	//escape the values for database insertion
	$link = mysql_real_escape_string($link);
	$condition = mysql_real_escape_string($condition);
	$brand = mysql_real_escape_string($brand);
	$title = mysql_real_escape_string($title);
	$description = mysql_real_escape_string($description);
	$image_link = mysql_real_escape_string($image_link);
	$product_type = mysql_real_escape_string($product_type);
	$google_product_category = mysql_real_escape_string($google_product_category);
	$price = mysql_real_escape_string($price);
	$availability = mysql_real_escape_string($availability);
	$expiration_date = mysql_real_escape_string($expiration_date);
	$id = mysql_real_escape_string($id);
	$mpn = mysql_real_escape_string($mpn);
	$gtin = mysql_real_escape_string($gtin);
	$color = mysql_real_escape_string($color);
	$size = mysql_real_escape_string($size);
	$shipping_weight = mysql_real_escape_string($shipping_weight);
	$shipping = mysql_real_escape_string($shipping);
	$gender = mysql_real_escape_string($gender);
	$age_group = mysql_real_escape_string($age_group);


	//here's our query
	$query = "insert into ONLINE_LISTINGS ( 
						Link,
                        ItemCondition,
						Brand,
						Title,
						Description,
						ImageLink,
						ProductType,
						GoogleProductCategory,
						Price,
						Availability,
						ExpirationDate,
						OldID,
						MPN,
						GTIN,
						Color,
						Size,
						ShippingWeight,
						shipping,
						gender,
						age_group)
					values 

					      ('$link',
					       '$condition',
					       '$brand',
					       '$title',
					       '$description',
					       '$image_link',
					       '$product_type',
					       '$google_product_category',
					       '$price',
					       '$availability',
					       '$expiration_date',
					       '$id',
					       '$mpn',
					       '$gtin',
					       '$color',
					       '$size',
					       '$shipping_weight',
					       '$shipping',
					       '$gender',
					       '$age_group')";

	//echo "<br><br>$query<br><br>";


	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database insert error. 379";
		//errorHandler($error_message);
	}
	
	return mysql_insert_id();
}

//function to insert a new record in LOCAL_PRODUCT_LISTINGS
function insertNewLocalProductListing(
				        $title,
					$webitemid,
					$item_group_id,
					$gtin, 
					$mpn, 
					$brand, 
					$price, 
					$condition, 
					$url, 
					$image_url, 
					$size, 
					$color)
{
	
	//escape the values for database insertion
	$title = mysql_real_escape_string($title);
	$webitemid = mysql_real_escape_string($webitemid);
	$item_group_id = mysql_real_escape_string($item_group_id);
	$gtin = mysql_real_escape_string($gtin);
	$mpn = mysql_real_escape_string($mpn);
	$brand = mysql_real_escape_string($brand);
	$price = mysql_real_escape_string($price);
	$condition = mysql_real_escape_string($condition);
	$url = mysql_real_escape_string($url);
	$image_url = mysql_real_escape_string($image_url);
	$size = mysql_real_escape_string($size);
	$color = mysql_real_escape_string($color);

	//here's our query
	$query = "insert into LOCAL_PRODUCT_LISTINGS (
	                                                Title,
							webitemid,
							item_group_id,
							GTIN,
							MPN,
							Brand, 
							Price,
							ProductCondition,
							URL,
							ImageURL, 
							Size,
							Color)
						values 

							('$title',
							'$webitemid',
							'$item_group_id',
							'$gtin',
							'$mpn',
							'$brand',
							'$price',
							'$condition',
							'$url',
							'$image_url', 
							'$size',
							'$color')";

	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database insert error";
		//errorHandler($error_message);
	}
	
	return mysql_insert_id();
}


//function to insert a new record in PRICE_QUANTITY
function insertNewPriceQuantity($storeid,$productid,$item_group_id,$local_quantity,$reg_price,$store_availability) {
	
	//escape the values for database insertion
	$item_group_id = mysql_real_escape_string($item_group_id);
	$local_quantity = mysql_real_escape_string($local_quantity);
	$reg_price = mysql_real_escape_string($reg_price);
	$store_availability = mysql_real_escape_string($store_availability);

	//here's our query
	$query = "insert into PRICE_QUANTITY (  StoreID,
	                                        ProductID,
						item_group_id,
						Quantity,
						PriceOverride,
						Availability)
					values 

						('$storeid',
						 '$productid',
						 '$item_group_id',
						 '$local_quantity',
						 '$reg_price',
						 '$store_availability')";

	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database insert error";
		//errorHandler($error_message);
	}
	//echo "$query<br><br>";
	return mysql_insert_id();
}


//function to get a record from the ITEMS table 
//from the GTIN1 value found in the inv.txt file
function getItemAssocByMPN($mpn){

	//declare an assoc array which we'll use to return the results
	$results_assoc = array();

	$query = "select * from ITEMS where MPN like '%$mpn%'";
	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database select error. 477";
		errorHandler($error_message);
	}
	
	//if there were no results then return false
	if( mysql_num_rows($results) < 1 ){
		return false;
	}

	
	//otherwise keep going
	//loop through the results and add the keys and values to our assoc array
	while($row = mysql_fetch_assoc($results)){
	
		foreach($row as $my_key => $my_val){
		
			//strip slashes
			$stripped_val = stripslashes($my_val);
			
			//add it to the assoc array to return
			$results_assoc[$my_key] = $stripped_val;
		}
	}
	
	
	//finally return the assoc array
	return $results_assoc;
}

// Arrays to do string match and replace on Google Categories

// SE Category
//$SE_categories = array("Bicycling Catalog > Bikes > Road > Comp/Race", "Bicycling Catalog > Bikes > Mountain > Trail (Full Suspension)", "Bicycling Catalog > Bikes > Road > Ergo/Comfort", "Bicycling Catalog > Bikes > Mountain > 29-Inch Wheel (29ers)", "Bicycling Catalog > Bikes > Mountain > Hardtail", "Bicycling Catalog > Bikes > Commuter/Urban > Urban", "Bicycling Catalog > Wheels > Wheels > 700c, 27-Inch & 650c", "Bicycling Catalog > Clothing > Shorts/Bottoms", "Bicycling Catalog > Bikes > Road > Road Frames", "Bicycling Catalog > Clothing > Jerseys/Tops", "Bicycles: Mountain > Cross Country (Full Suspension)", "Bicycling Catalog > Bikes > Hybrid Bikes", "Bicycling Catalog > Parts > Forks > Suspension", "Bicycling Catalog > Shoes > Shoes", "Bicycles: Road > Comp/Race", "Bicycling Catalog > Bikes > Mountain", "Bicycling Catalog > Gift Cards", "Bicycling Catalog > Bikes > Mountain > Cross Country (Full Suspension)", "Bicycling Catalog > Helmets > Adults", "Bicycling Catalog > Bikes > Comfort Bikes", "Bicycling Catalog > Parts > Seatposts", "Bicycling Catalog > Bikes > Road > Recreation", "Bicycling Catalog > Clothing > Outerwear", "Bicycling Catalog > Bikes > Mountain > All-Mountain (Full Suspension)", "Bicycling Catalog > Accessories > Electronics > GPS", "Bicycling Catalog > Shoes > Cycling Shoes", "Bicycles: Mountain > Trail (Full Suspension)", "Bicycling Catalog > Bikes > Cruiser Bikes", "Bicycling Catalog > Bikes > Mountain > Downhill/Freeride", "Bicycling Catalog > Bikes > Cyclocross > Cyclocross Bikes", "Bicycling Catalog > Bikes > Mountain > Mountain Frames", "Components > Seatposts", "Bicycling Catalog > Bikes > Road", "Gift Cards", "Bicycling Catalog > Bikes > Commuter/Urban > Electric", "Bicycles: Comfort/Hybrid > Bicycles: Hybrid", "Shoes > Shoes", "Electronics > GPS", "Bicycling Catalog > Parts > Groups", "Bicycling Catalog > Accessories > Lighting > Headlights", "Bicycling Catalog > Wheels > Wheels > 26-Inch", "Bicycling Catalog > Parts > Pedals & Accessories", "Bicycles: Road > Ergo/Comfort", "Forks > Suspension", "Bicycling Catalog > Parts > Brakes/Levers/Pads", "Clothing > Jerseys/Tops", "Clothing > Outerwear", "Bicycling Catalog > Accessories > Electronics > Helmet Cameras", "Bicycling Catalog > Bikes > Children's > 20-Inch (5-8 yr. old)", "Bicycling Catalog > Bikes > Children's > 16-Inch (3-6 yr. old)", "Clothing > Shorts/Bottoms", "Bicycling Catalog > Tires/Tubes > Tires > 700c, 27-Inch & 650c", "Bicycles: All-Terrain > Full Suspension", "Bicycling Catalog > Parts > Cassettes/Freewheels", "Bicycling Catalog > Parts > Saddles/Pads", "Bicycling Catalog > Wheels > Track", "Bicycling Catalog", "Bicycles: Urban > Urban", "Bicycling Catalog > Parts > Derailleurs > Rear", "Bicycles: Children's > 16-Inch (3-6 yr. old)", "Wheels/Wheel Parts > Track", "Bicycling Catalog > Car Racks > Hitch-Mount", "Components > Brakes, Levers & Pads", "Bicycles: Road > Womens Road", "Bicycling Catalog > Bikes > Children's > 24-Inch (7+ yr. old)", "Bicycles: Comfort/Hybrid > Bicycles: Comfort", "Bicycling Catalog > Wheels", "Bicycles: Mountain > Hardtail", "Bicycles: Children's > 24-Inch (7+ yr. old)", "Bicycling Catalog > Tires/Tubes > Tires > 26-Inch", "Bicycles: Children's > 20-Inch (5-8 yr. old)", "Bicycling Catalog > Accessories > Repair & Maintenance > Tools", "Bicycling Catalog > Tires/Tubes > Tubes: All Sizes/Types", "Bicycling Catalog > Bikes > Other > Folding", "Bicycling Catalog > Clothing > Gloves", "Components > Pedals & Accessories", "Frames > Road", "Bicycling Catalog > Parts > Cranksets > Cranksets", "Bicycles: Mountain > All-Mountain (Full Suspension)", "Bicycling Catalog > Bikes", "Bicycling Catalog > Accessories > Travel Cases", "Bicycling Catalog > Clothing", "Bicycling Catalog > Accessories > Hydration > Packs/Systems", "Wheels/Wheel Parts > 700c, 27-Inch & 650c", "Components > Saddles", "Bicycling Catalog > Bikes > Road > Women's Road", "Bicycling Catalog > Bikes > Mountain > Dirt Jump", "Bicycles: Mountain > Recreation", "Bicycling Catalog > Car Racks > Trunk-Mount", "Electronics > Helmet Cameras", "Bicycling Catalog > Accessories > Repair & Maintenance > Workstands", "Bicycling Catalog > Accessories > Trailers/Strollers > Child Trailers", "Bicycling Catalog > Parts > Stems", "Bicycles: Comfort/Hybrid > Bicycles: Cruiser", "Bicycling Catalog > Accessories > Repair & Maintenance > 5 Year Service Policy", "Bicycles: Children's > 12-Inch (2-4 yr. old)", "Bicycling Catalog > Bikes > Children's > 12-Inch (2-4 yr. old)", "Components > Shifters", "Components > Cassettes & Freewheels", "Bicycles: Cyclocross", "Car Racks > Hitch-Mount", "Bicycles: Mountain > Womens Hardtail", "Bicycling Catalog > Accessories > Pumps/Inflation > Floor", "Bicycling Catalog > Parts > Shifters", "Bicycling Catalog > Bikes > BMX > BMX Race", "Bicycling Catalog > Bikes > Commuter/Urban > Fixed/One-Speed", "Bicycles: Urban > Folding", "Bicycling Catalog > Accessories > Trainers/Rollers", "Helmets > Adults'", "Bicycling Catalog > Accessories > Electronics > Cyclo-Computers", "Components > Groups", "Bicycling Catalog > Bikes > BMX > BMX", "Bicycling Catalog > Clothing > Accessories", "Wheels/Wheel Parts > 26-Inch", "Bicycling Catalog > Accessories > Lighting > Taillights", "Bicycling Catalog > Bikes > Cyclocross", "Bicycling Catalog > Bikes > Commuter/Urban > Cargo Bicycles", "Components > Cranksets & Accessories", "Components > Derailleurs-Rear", "Bicycling Catalog > Bikes > Road > Single Speed / Fixed", "Clothing > Gloves", "Bicycling Catalog > Clothing > Undergarments", "Bicycling Catalog > Car Racks > Roof-Mount", "Bicycling Catalog > Accessories > Hydration > Bottles/Cages", "Trainers/Rollers", "Bicycling Catalog > Accessories > Nutrition > Gels/Chewables", "Bicycling Catalog > Parts > Headsets", "Bicycling Catalog > Shoes", "Bicycling Catalog > Clothing > Hats", "Bicycling Catalog > Helmets > Kids", "Bicycling Catalog > Parts > Pedals > Pedals", "Bicycling Catalog > Accessories > Child Seats", "Bicycles: Mountain > Dirt Jump", "Bicycling Catalog > Parts > Shift/Brake Combinations", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Messenger Bags", "Bicycling Catalog > Accessories > Pumps/Inflation > Mini", "Bicycling Catalog > Bikes > Children's", "Tires/Tubes > Tires: 26-Inch", "Tires/Tubes > Tires: 700c, 27-Inch & 650c", "Car Racks > Trunk-Mount", "Bicycling Catalog > Accessories > Repair & Maintenance > Lubes/Cleaners", "Bicycling Catalog > Wheels > Wheels", "Bicycling Catalog > Accessories > Eyewear > Glasses", "Bicycling Catalog > Clothing > Socks", "Bicycles: Tandem > Comfort/Cross", "Bicycling Catalog > Wheels > Wheels > 29-Inch", "Lights > Taillights", "Bicycling Catalog > Bikes > Cyclocross > Cyclocross Frames", "Hydration > Packs & Systems", "Bicycling Catalog > Car Racks > Pickup/Spare-Tire Mount", "Bicycling Catalog > Helmets", "Bicycling Catalog > Parts > Grips", "Bicycling Catalog > Wheels > Wheels > 24-Inch & Smaller", "Bicycling Catalog > Tires/Tubes", "Repair/Maintenance > Tools", "Bicycling Catalog > Clothing > Protective/Armor", "Bicycling Catalog > Accessories > Nutrition > Drinks", "Bicycling Catalog > Bikes > Other > Three Wheelers", "Bicycling Catalog > Parts > Handlebars > Drop", "Components > Rear Shocks", "Child Trailers/Carriers", "Components > Shift/Brake Combinations", "Bicycling Catalog > Parts > Chains", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Panniers", "Travel Cases", "Components > Handlebars", "Bicycling Catalog > Parts > Handlebars > Riser", "Electronics > Cyclo-Computers", "Clothing > Accessories", "Lights > Headlights", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Racks (on-bike)", "Bicycling Catalog > Clothing > Shirts/Tops (off-bike wear)", "Bicycling Catalog > Accessories > Locks/Security > U-Locks", "Bicycling Catalog > Parts > Saddles", "Bicycling Catalog > Accessories > Fenders", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Baskets", "Bicycles: Road > Single Speed", "Clothing > Hats", "Bicycling Catalog > Tires/Tubes > Tires", "Bicycling Catalog > Accessories > Trailers/Strollers > Accessories/Parts", "Bicycling Catalog > Accessories > Electronics > Heart-Rate Monitors", "Bicycling Catalog > Tires/Tubes > Tires > 29-Inch", "Bicycling Catalog > Parts > Bottom Brackets", "Bicycling Catalog > Parts > Derailleurs > Front", "Bicycling Catalog > Bikes > BMX", "Tires/Tubes > Tires: 29-Inch", "Bicycling Catalog > Accessories > Nutrition > Bars", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Packs", "Packs/Racks/ Baskets > Baskets", "Bicycling Catalog > Parts > Handlebars > Aero", "Bicycling Catalog > Parts > Shocks", "Bicycles: Commuter/Town", "Bicycling Catalog > Accessories > Eyewear", "Bicycling Catalog > Accessories > Locks/Security > Lock Set", "Bicycling Catalog > Accessories > Pumps/Inflation > Accessories/Parts", "Bicycling Catalog > Shoes > Accessories/Parts", "Packs/Racks/ Baskets > Panniers", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Rack-Top Bags (trunks)", "Bicycling Catalog > Accessories > Locks/Security > Cables/Chains", "Bicycling Catalog > Accessories", "Tires/Tubes > Tubes: All Sizes/Types", "Bicycling Catalog > Clothing > Youth", "Car Racks > Roof-Mount", "Bicycling Catalog > Accessories > Hydration > Accessories/Parts", "Bicycling Catalog > Parts > Bar Ends", "Bicycling Catalog > Parts > Rear Shocks", "Clothing > Protective", "Bicycling Catalog > Accessories > Locks/Security > Other", "Bicycling Catalog > Accessories > Pumps/Inflation > CO2", "Bicycling Catalog > Accessories > Storage", "Bicycling Catalog > Accessories > Electronics > Accessories/Parts", "Locks/Security", "Bicycling Catalog > Wheels > Accessories/Parts", "Bicycling Catalog > Parts > Other", "Clothing > Socks", "Fenders", "Bicycling Catalog > Accessories > Packs/Racks/Baskets", "Repair/Maintenance > Workstands", "Bicycling Catalog > Accessories > Pumps/Inflation", "Bicycling Catalog > Tires/Tubes > Tires > 12- to 24-Inch", "Bicycling Catalog > Car Racks > Accessories/Parts", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Gear Bags", "Pumps/Inflation > Pumps/Inflators", "Bicycling Catalog > Parts > Handlebars", "Bicycling Catalog > Accessories > Media/Resources > Books", "Components > Stems", "Bicycling Catalog > Accessories > Pumps/Inflation > Frame", "Components > Grips", "Bicycling Catalog > Parts > Cranksets", "Components > Headsets", "Bicycling Catalog > Parts > Chainguides", "Bicycling Catalog > Accessories > Hydration", "Bicycling Catalog > Accessories > Safety > Bells/Horns", "Bicycling Catalog > Accessories > Electronics", "Hydration > Bottles & Cages", "Shoes > Accessories/Parts", "Bicycling Catalog > Parts > Handlebar Grips/Tape > Tape", "Bicycling Catalog > Parts > Handlebar Grips/Tape > Grips", "Bicycling Catalog > Accessories > Nutrition", "Bicycling Catalog > Parts > Pedals > Accessories/Parts", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Seat Bags", "Packs/Racks/ Baskets > Racks (on-bike)", "Wheels/Wheel Parts > 24-Inch", "Bicycling Catalog > Parts > Chainrings", "Bicycling Catalog > Accessories > Pumps/Inflation > Shock", "Helmets > Kids'", "Storage", "Components > Chains", "Bicycling Catalog > Accessories > Repair & Maintenance", "Components > Cables", "Bicycling Catalog > Parts", "Bicycling Catalog > Accessories > Nutrition > Supplements", "Bicycling Catalog > Wheels > Hubs", "Clothing > Undergarments", "Bicycling Catalog > Accessories > Locks/Security", "Bicycling Catalog > Tires/Tubes > Accessories", "Packs/Racks/ Baskets > Rack-Top Bags (trunks)", "Pumps/Inflation > Accessories/Parts", "Bicycling Catalog > Accessories > Chamois Cream", "Components > Bottom Brackets", "Bicycling Catalog > Car Racks", "Components > Handlebar Grips", "Bicycling Catalog > Accessories > Lighting", "Bicycling Catalog > Accessories > Packs/Racks/Baskets > Frame Bags", "Bicycling Catalog > Accessories > Cycling DVDs", "Components > Bar Ends", "Packs/Racks/ Baskets > Seat Bags", "Books & DVDs > Books", "Accessories > Bells/Horns", "Components > Miscellaneous", "Bicycles: All-Terrain > Front Suspension", "Electronics > Accessories/Parts", "Bicycling Catalog > Accessories > Lighting > Combos/Systems", "Bicycling Catalog > Parts > Handlebar Grips/Tape", "Bicycling Catalog > Parts > Cables", "Repair/Maintenance > Lubes/Cleaners", "Nutrition");
// Google Category
//$Google_categories = array("Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Hybrid Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Apparel & Accessories > Clothing > Activewear > Bicycle Shorts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Frames", "Apparel & Accessories > Clothing > Activewear > Bicycle Jerseys", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Hybrid Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Forks", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Arts & Entertainment > Gift Giving > Gift Certificates", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Helmets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Cruisers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Seatposts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Electronics > GPS > Sport GPS", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Cruisers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Frames", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Seatposts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Arts & Entertainment > Gift Giving > Gift Certificates", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Electric Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Hybrid Bicycles", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Electronics > GPS > Sport GPS", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Pedals", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Forks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Brake Parts", "Apparel & Accessories > Clothing > Activewear > Bicycle Jerseys", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Cameras & Optics > Cameras > Video Cameras", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Apparel & Accessories > Clothing > Activewear > Bicycle Shorts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Cassettes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Saddles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Hybrid Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Derailleurs", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Brake Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Cruisers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Tools, Cleaners & Lubricants", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing > Bicycle Gloves", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Pedals", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Frames", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Cranks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Transport Bags & Cases", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Saddles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Cameras & Optics > Cameras > Video Cameras", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Stands & Storage", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Trailers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Stems", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Cruisers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Shifters", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Cassettes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Shifters", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Hybrid Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Trainers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Helmets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Computers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Cranks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Derailleurs", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing > Bicycle Gloves", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Cages", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Trainers", "Health & Beauty > Health Care > Fitness & Nutrition > Nutrition Bars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Helmets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Pedals", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Mountain Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Brake Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Tools, Cleaners & Lubricants > Bicycle Lubrication", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Apparel & Accessories > Clothing Accessories > Sunglasses", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Frames", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Helmets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Tools, Cleaners & Lubricants", "Apparel & Accessories > Clothing > Activewear", "Health & Beauty > Health Care > Fitness & Nutrition > Electrolytes", "Sporting Goods > Outdoor Recreation > Cycling > Tricycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Baby & Toddler > Baby Transport > Baby Carriers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Brake Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Chains", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Transport Bags & Cases", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Computers", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Saddles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Fenders", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Baskets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles > Road Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Clothing", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Trailers", "Health & Beauty > Health Care > Biometric Monitors > Heart Rate Monitors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Bottom Brackets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Derailleurs", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Health & Beauty > Health Care > Fitness & Nutrition > Nutrition Bars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Baskets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Baskets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Baskets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Apparel & Accessories > Clothing Accessories > Sunglasses", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tubes", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Health & Beauty > Health Care > Fitness & Nutrition", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Stands & Storage", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheel Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Fenders", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Baskets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Stands & Storage", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Media > Books > Non-Fiction > Sports Books", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Stems", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Chainrings", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Health & Beauty > Health Care > Fitness & Nutrition", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bells & Horns", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Cages", "Apparel & Accessories > Shoes > Athletic Shoes > Bicycle Shoes", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Health & Beauty > Health Care > Fitness & Nutrition", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Pedals", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheels", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Chainrings", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Helmets", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Stands & Storage", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Chains", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Stands & Storage", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Health & Beauty > Health Care > Fitness & Nutrition", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Wheel Parts > Bicycle Hubs", "Apparel & Accessories > Clothing > Activewear", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Locks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Tires", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Front & Rear Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Pumps", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Drivetrain Parts > Bicycle Bottom Brackets", "Vehicles & Parts > Automotive Exterior > Automotive Carrying Racks > Automotive Bicycle Racks", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Media > DVDs & Videos > Sports Videos", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Handlebars", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bags & Panniers", "Media > Books > Non-Fiction > Sports Books", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Bells & Horns", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycles", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Lights & Reflectors", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts > Bicycle Grips and Handlebar Tape", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Parts", "Sporting Goods > Outdoor Recreation > Cycling > Bicycle Accessories > Bicycle Tools, Cleaners & Lubricants", "Health & Beauty > Health Care > Fitness & Nutrition");

// array for shipping promotions by matching on MPN
$freeShippingByMPN = array("BIK15661","BIK17386","BIK17387","BIK17388","BIK17389","BIK17382","BIK17383","BIK17384","BIK17385","BIK15626","BIK15525","BIK15526","BIK15527","BIK15528","BIK15529","BIK15530","BIK15532","BIK15533","BIK15534","BIK15535","BIK15536","BIK15537","BIK17363","BIK17364","BIK17365","BIK17366","BIK17367","BIK17368","BIK17351","BIK17352","BIK17353","BIK17354","BIK17355","BIK17356","BIK17375","BIK17376","BIK17377","BIK17378","BIK17379","BIK17380","BIK17345","BIK17346","BIK17347","BIK17348","BIK17349","BIK17350","BIK15649","BIK15638","BIK15640","BIK15641","BIK15632","BIK15633","BIK15652","BIK15655","BIK15657","A12042050-1","A12042050-2","A12042050-3","A12042050-4","A12042050-5","K11539910-2","K11539910-3","K11539910-4","K11539910-5","K11539910-6","K7067110-2","K7067110-3","K7067110-4","K7067110-5","K7067110-6","B9041008-2","B9041008-3","B9041008-4","B9041008-5","B9041008-6","B9041008-7","P7581010-2","P7581010-3","P7581010-4","P7581010-5","P7581001-2","P7581001-3","P7581001-4","P7581001-5","Q7583010-2","Q7583010-3","Q7583010-4","Q7583010-5","L11038010-1","L11038010-2","L11038010-3","L11038010-4","L11038010-5","11189956","11189958","11189962","11189966","11190056","11190058","11190062","11190066","6568","A11029001-1","A11029001-2","A11029001-3","A11029001-4","A11029001-5","A11029010-1","A11029010-2","A11029010-3","A11029010-4","A11029010-5","A11029030-1","A11029030-2","A11029030-3","A11029030-4","A11029030-5","A11029061-1","A11029061-2","A11029061-3","A11029061-4","A11029061-5","010-00899-30","010-00947-10","CHDOH-002","CHDHH-001","10546056","10546058","10546062","10546066","99647356","99647358","99647362","99647366","B8550032-2","B8550032-3","B8550032-4","B8550032-5","B8550032-6","B8550032-7","L12211110-2","L12211110-3","L12211110-4","L12211110-5","L12211110-6","L12211110-7","K12087010-1","K12087010-2","K12087010-3","K12087010-4","K12087010-5","H12081001","A12050010-1","A12050010-2","A12050010-3","A12050010-4","A12050010-5","12978756","12978758","12978762","12978766","12814756","12814758","12814762","12814766","12814456","12814458","12814462","12814466","12814556","12814558","12814562","12814566","12141654","12141656","12141658","12141662","12141666","12141754","12141756","12141758","12141762","12141766","11189654","11189656","11189658","11189662","11189666","11189854","11189856","11189858","11189862","11189866","10545956","10545958","10545962","10545966","99647256","99647258","99647262","99647266","99648156","99648158","99648162","99648166","10544259","10544261","10544265","12140659","12140661","12140665","12140669","11791659","11791661","11791665","11791759","11791761","11791765","12143559","12143561","12143565","B10081010-1","B10081010-2","B10081010-3","B10081010-4","B10081010-5","R7586001-2","R7586001-3","R7586010-2","R7586010-3","6570","010-00829-13","H11059010","11783756","11783758","11783762","11783766","11783656","11783658","11783662","11783666","11783668","010-00829-06","A12010101-2","A12010101-3","A12010101-4","A12010101-5","A12010101-6","A12010101-7","010-00978-00","A12012123-2","A12012123-3","A12012123-4","A12012123-5","A12012123-6","A12012123-7","6564","6566","6569","B10094008-2","B10094008-3","B10094008-4","B10094008-5","B10094008-6","B10094008-7","A12019010-2","A12019010-3","A12019010-4","A12019010-5","A12019010-6","A12019010-7","R12062001-2","R12062001-4","L12043050-1","L12043050-2","L12043050-3","L12043050-4","L12043050-5","010-00741-21","A12017055-2","A12017055-3","A12017055-4","A12017055-5","A12017055-6","A12017055-7","L12004123-2","L12004123-3","L12004123-4","L12004123-5","L12004123-6","L12004123-7","010-00899-00","6572","BIK15661","K1911724","K1911725","K1911722","K1911723","925-01-000","925-01-001","925-01-005","925-01-006");

//function to get a record from the ONLINE_LISTINGS table 
//from the MPN value found in the inv.txt file
////////////////////////////////
//WE NEED TO CLEAN THE PARAM
////////////////////////////////
function getOnlineListingAssocFromMPN($mpn){

	//declare an assoc array which we'll use to return the results
	$results_assoc = array();

	$query = "select * from ONLINE_LISTINGS where MPN like '%$mpn%'";
	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database select error";
		return false;
	}
	
	//if there were no results then return false
	//if( !mysql_num_rows($results) ){
	//	return false;
	//}

	
	//otherwise keep going
	//loop through the results and add the keys and values to our assoc array
	while($row = mysql_fetch_assoc($results)){
	
		foreach($row as $my_key => $my_val){
		
			//strip slashes
			$stripped_val = stripslashes($my_val);
			
			//add it to the assoc array to return
			$results_assoc[$my_key] = $stripped_val;
		}
	}
	
	
	//finally return the assoc array
	return $results_assoc;
}





//function to get a record from the ONLINE_LISTINGS table 
//from the GTIN1 value found in the inv.txt file
function getOnlineListingAssocFromGTIN($gtin){

	//declare an assoc array which we'll use to return the results
	$results_assoc = array();

	$query = "select * from ONLINE_LISTINGS where GTIN like '$gtin' limit 1";
	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "Im sorry, there was a database select error";
		return false;
	}
	
	//if there were no results then return false
	if( mysql_num_rows($results) < 1 ){
		return false;
	}

	
	//otherwise keep going
	//loop through the results and add the keys and values to our assoc array
	while($row = mysql_fetch_assoc($results)){
	
		foreach($row as $my_key => $my_val){
		
			//strip slashes
			$stripped_val = stripslashes($my_val);
			
			//add it to the assoc array to return
			$results_assoc[$my_key] = $stripped_val;
		}
	}
	
	
	//finally return the assoc array
	return $results_assoc;
}

// function to store item counts
function storeItemCount($count_type, $count) {
	$query = "INSERT INTO `feeds`.`ITEM_COUNTS` (`id`, `item_type`, `item_count`, `timestamp`) VALUES (NULL, '$count_type', '$count', NOW())";
	$results = mysql_query($query);
	if(!$results) {
		$error_message = "Error inserting into ITEM_COUNTS table: ".mysql_error();
		errorHandler($error_message);
		exit;
	}
}

// function to get item counts by type for last XX days
function getItemCounts($itemType,$numDays,$single = false) {
	$query = "select item_count, timestamp from ITEM_COUNTS where item_type = '$itemType' AND DATEDIFF(CURDATE(), timestamp) < $numDays order by timestamp ASC";
	$results = mysql_query($query);
	if(!$results) {
		$error_message = "Error selecting from ITEM_COUNTS table: ".mysql_error();
		errorHandler($error_message);
		exit;
	} else {
		$count_results = array();
		while ($row = mysql_fetch_assoc($results)) {
			$row['timestamp'] = strtotime($row['timestamp']) * 1000;
			if($single){
				return '['.$row['timestamp'].','.(INT)$row['item_count'].']';
			} else {
				$count_results[] = array($row['timestamp'],(INT)$row['item_count']);
			}
		}
		return json_encode($count_results);
	}
}

// function to get all item counts for last XX days
function getItemCountsJSON($numDays) {
	$query = "select item_type, item_count, timestamp from ITEM_COUNTS where DATEDIFF(CURDATE(), timestamp) < $numDays order by timestamp ASC";
	$results = mysql_query($query);
	if(!$results) {
		$error_message = "Error selecting from ITEM_COUNTS table: ".mysql_error();
		errorHandler($error_message);
		exit;
	} else {
		$count_results = array();
		while ($row = mysql_fetch_assoc($results)) {
			//$row['timestamp'] = strtotime($row['timestamp']) * 1000;
			list($date, $time) = split(' ', $row['timestamp']);
			$row['timestamp'] = $date;
			$row['item_count'] = (INT)$row['item_count'];
			$count_results[] = $row;
		}
		return json_encode($count_results);
	}
}
function getItemCountsByTypeJSON($itemType,$numDays) {
	$query = "select item_count, timestamp from ITEM_COUNTS where item_type = '$itemType' AND DATEDIFF(CURDATE(), timestamp) < $numDays order by timestamp ASC";
	$results = mysql_query($query);
	if(!$results) {
		$error_message = "Error selecting from ITEM_COUNTS table: ".mysql_error();
		errorHandler($error_message);
		exit;
	} else {
		$count_results = array();
		while ($row = mysql_fetch_assoc($results)) {
			list($date, $time) = split(' ', $row['timestamp']);
			$row['timestamp'] = $date;
			$row['item_count'] = (INT)$row['item_count'];
			$count_results[] = $row;
		}
		return json_encode($count_results);
	}
}

//function to get a StoreID from a StoreName
function getStoreIDFromName($name){

	$query = "select * from BUSINESS_LISTINGS where StoreName = \"$name\"";

	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "I'm sorry, there was a database select error. 355";
		errorHandler($error_message);
	}

	//get the value we need
	$row = mysql_fetch_assoc($results);
		
	$store_id = stripslashes($row['StoreID']);
	return $store_id;
}

// function to strip any HTML tags
function stripHTML($string) {
	return preg_replace ('/<[^>]*>/', '', $string);
}

//a function to format a string by truncating it, adding an elipsis, etc.
function formatString($string, $truncate_length){

	//add one to the truncate length since the substr
	//function starts at zero
	$truncate_length += 1;

	//truncate the string to the number of chars we specified
	$string = substr($string, 0, $truncate_length);

	/*
	//now we'll explode it on single spaces
	$exploded_space_array = explode(' ', $string);

	//the last element is likely a truncated word so
	//we'll remove it
	array_pop($exploded_space_array);

	//now we'll implode the array back into a string
	//delimited by spaces
	$string = implode(' ', $exploded_space_array);

	//now we'll check to see if the last character
	//in the string is a space
	//if it is then we'll remove it
	$last_character = substr($string, -1, 1);

	if($last_character == ' '){
		//remove the space
		$truncate_length -= 1;
		$string = substr($string, 0, $truncate_length);
	}


	//now we'll add an elipsis to the end
	$string .= '...';
	*/

	//return the formatted string
	return $string;
}


//function to save a string to a textfile
function saveLocalTextfile($string, $textfile_name){

	//create a file for writing and get the filehandle
	$fp = fopen("$textfile_name", 'w');
	if(!$fp){
		echo "couldn't open file! - $textfile_name";
	}

	//now write the value to it
	$result = fwrite($fp, $string);

	//assuming all went well, close the file
	fclose($fp);

	//return false if couldn't write to the file
	if(!$result){
		return false;
	}

	//if we got here we can return true
	return true;
}

// function to FTP files
function uploadFile($dest,$source,$file_name)
{
    global $ftpserver;
    global $ftp_user_name;
    global $ftp_user_pass;
    $conn = ftp_connect("$ftpserver") or die("Could not connect to $ftpserver");
    ftp_login($conn,"$ftp_user_name","$ftp_user_pass");
    if( ftp_put($conn,"$dest","$source",FTP_ASCII) ) {
	    echo '<span style="color:green;">FTP Upload Successful for '.$file_name.'!</span><br /><br />';
    } else { 
	    echo '<span style="color:red;">FTP Upload Failed for '.$file_name.'!</span><br /><br />';
    }
    ftp_close($conn);
}

//function to truncate the table passed as arg
function emptyTable($table)
{
	$query = "truncate $table";

	$results = mysql_query($query);
	//check for general error
	if(!$results){
		$error_message = "I'm sorry, there was a database truncate error";
		errorHandler(  mysql_error() );
	}
}

// split id on '-' and return first part of string
function my_split($string)
{
	$parts = preg_split('/-/', $string, 1);
	return $parts[0];
}

?>