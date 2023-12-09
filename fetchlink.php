<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('simple_html_dom.php');

$html=file_get_html("https://mediter.com/property/?posts_per_page=189");

foreach($html->find('.avia-content-slider-inner article') as $article) {
    $item['Link'] = $article->find('h3 a', 0)->href;
    $property[] = $item;
}

// $fp = fopen('results.json', 'w') or die("Unable to open file!");
// fwrite($fp,json_encode($property));
// fclose($fp)

?>