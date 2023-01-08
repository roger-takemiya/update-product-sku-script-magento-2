<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;

require '../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');

$state->setAreaCode('frontend');

$productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');

$row = 0;

if (($handle = fopen("Tabela_para_Script.csv", "r")) !== FALSE) {

  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    
  	$row++;

  	if( $row == 1 ) continue;


    $old_sku = $data[0];
  	$new_sku = $data[1];

  	if($old_sku  ==   $new_sku){
  		echo  $old_sku . " => " . $new_sku . " SKIP " . PHP_EOL; 
  		continue;
  	}

  	$id = 0;

  	try {

  		$_product = $productRepository->get($old_sku);

  		$id = $_product->getId();

  		echo  $old_sku . " => " . $new_sku . PHP_EOL;  	  		

  		$_product->setSku($new_sku);

  		$_product->save($_product);

  	} catch (\Magento\Framework\Exception\NoSuchEntityException $e){
	    echo  $old_sku . " => " . $new_sku . ": NOT FOUND " . PHP_EOL; 
	}

	$txt = $id . ";" . $old_sku . "," . $new_sku . PHP_EOL;

	$myfile = file_put_contents('logs-update-csv.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

  }

  fclose($handle);

}