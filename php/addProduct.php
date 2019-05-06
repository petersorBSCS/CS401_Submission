<?php


require_once('Dao.php');

$dao = new Dao();

$param = array();

// Fetch the POST data
$name = isset($_POST["name"]) ? $_POST["name"] : null; $param["name"] = $name;
$category = isset($_POST["category"]) ? $_POST["category"] : null; $param["category"] = $category;
$unit_type = isset($_POST["unit_type"]) ? $_POST["unit_type"] : null; $param["unit_type"] = $unit_type;
$stage = isset($_POST["stage"]) ? $_POST["stage"] : null; $param["stage"] = $stage;
$date = isset($_POST["date"]) ? $_POST["date"] : null; $param["harvest_date"] = $date;
$shelf_life = isset($_POST["shelf_life"]) ? $_POST["shelf_life"] : null; $param["shelf_life"] = $shelf_life;
$cost = isset($_POST["cost"]) ? $_POST["cost"] : null; $param["cost"] = $cost;
$price = isset($_POST["price"]) ? $_POST["price"] : null; $param["price"] = $price;
$yield = isset($_POST["yield"]) ? $_POST["yield"] : null; $param["yield"] = $yield;
$growerID = isset($_POST["growerID"]) ? $_POST["growerID"] : null; $param["growerID"] = $growerID;


echo "<pre>";
echo print_r($param);
echo "</pre>";

// Update the product
$dao->addProduct($param);