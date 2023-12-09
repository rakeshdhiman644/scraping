<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include('simple_html_dom.php');

$links = file_get_contents('results.json');
$json = json_decode($links, true);
//echo '<pre>' . print_r($json, true) . '</pre>';


foreach(array_slice($json, 0, 20) as $key => $value){
	
	$html=file_get_html($value['Link']);
  $singleItem['Title'] = $html->find('h1.main-title' , 0)->plaintext;
  $singleItem['Link'] = $value['Link'];
  $singleItem['Pricebetween'] = $html->find('span.property-price' , 0)->plaintext;

  $ulArray = [];
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

  for ($i=0; $i <count($imgArray) ; $i++) { 
    $singleItem['Image'.$i] = $imgArray[$i];
  }
	$result[] = $singleItem;
}
echo "<pre>";
print_r($result);

function create_csv_string($data) {

  // Open temp file pointer
  if (!$fp = fopen('data.csv', 'w+')) return FALSE;

  // Loop data and write to file pointer
  foreach ($data as $line) fputcsv($fp, $line);

  // Place stream pointer at beginning
  rewind($fp);

  // Return the data
  return stream_get_contents($fp);

}

function send_csv_mail ($csvData, $body, $to = 'taskrakesh591@gmail.com', $subject = 'Test email with attachment', $from = 'webmaster@example.com') {

  // This will provide plenty adequate entropy
  $multipartSep = '-----'.md5(time()).'-----';

  // Arrays are much more readable
  $headers = array(
    "From: $from",
    "Reply-To: $from",
    "Content-Type: multipart/mixed; boundary=\"$multipartSep\""
  );

  // Make the attachment
  $attachment = chunk_split(base64_encode(create_csv_string($csvData))); 

  // Make the body of the message
  $body = "--$multipartSep\r\n"
        . "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
        . "Content-Transfer-Encoding: 7bit\r\n"
        . "\r\n"
        . "$body\r\n"
        . "--$multipartSep\r\n"
        . "Content-Type: text/csv\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . "Content-Disposition: attachment; filename=\"file.csv\"\r\n"
        . "\r\n"
        . "$attachment\r\n"
        . "--$multipartSep--";

   // Send the email, return the result
   return @mail($to, $subject, $body, implode("\r\n", $headers)); 

}

send_csv_mail($result, "Hello World!!!\r\n This is simple text email message.");


?>