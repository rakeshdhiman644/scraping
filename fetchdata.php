<?php
// ini_set('max_execution_time', '0');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include('simple_html_dom.php');

$links = file_get_contents('results.json');
$json = json_decode($links, true);
// echo '<pre>' . print_r($json, true) . '</pre>';

foreach(array_slice($json, 0, 12) as $key => $value){
	$html=file_get_html($value['Link']);
	$singleItem['Title'] = $html->find('h1.main-title' , 0)->plaintext;
	$singleItem['Link'] = $value['Link'];
	$singleItem['Pricebetween'] = $html->find('span.property-price' , 0)->plaintext;

	foreach($html->find('.panel .panel-body') as $data){

		foreach($data->find('ul li span') as $ul){
			$singleItem[ucfirst(str_replace('mre-','',$ul->class))]=$ul->plaintext;
		}
	}
	
	$dtArray = [];
	$ddArray = [];
	foreach($html->find('#tab-info .list-table dl') as $dl ) {
		foreach($dl->find('dt') as $dt){
			$dtArray[] = $dt->plaintext;
		}
		foreach($dl->find('dd') as $dd){
			$ddArray[] = $dd->plaintext;
		}
	}
	for($i=0;$i<count($dtArray);$i++){
		$singleItem[$dtArray[$i]] = $ddArray[$i];
	}
	
	$singleItem['Propertyfeatures'] = $html->find('.panel #listing-features' , 0)->plaintext;
	$singleItem['Description'] = $html->find('article .entry-content' , 0)->plaintext;

	$imgArray = [];

	foreach($html->find('#carousel ul.slides') as $ul){
		foreach($ul->find('li img') as $likey => $li){
			$imgArray[] = $li->src;
		}
	}

	for ($i=0; $i < 19 ; $i++) { 
		$singleItem['Image'.$i] = $imgArray[$i];
	}

	$result[] = $singleItem;
}
echo "<pre>";
print_r($result);

//$fp = fopen('data1.csv', 'a');

//fputcsv($fp,array('Title','Link','Pricebetween','Sq1','Sq2','Bed','Shower','Garden','Parking','Setting','Lift','Whitegoods','Furniture','Airport','City','Shop','Golf','Beach','Restaurant','Price','Price including Costs','Holding Deposit','Initial / First Payment','Stage Payment 1','Stage Payment 2','Stage Payment 3','Stage Payment 4','Final Payment','Community Fee','Property Insurance','Property Tax','Bank Guarantee','Energy Certificate','Property ID','Property type','Bedrooms','Bathrooms','Living rooms','Kitchens','Levels','Construction year','Porperty size','Constructed Area','Plot size','Take over','Financing','Propertyfeatures','Description','Image0','Image1','Image2','Image3','Image4','Image5','Image6','Image7','Image8','Image9','Image10','Image11','Image12','Image13','Image14','Image15','Image16','Image17','Image18','Train','Bus','Aircondition'));

foreach ($result as $fields) {
	//fputcsv($fp,$fields);
}



//fclose($fp);

?>