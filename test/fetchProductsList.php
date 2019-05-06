<?php

require_once ("../php/Dao.php");

$dao = new Dao();

$products = $dao->getProducts();

echo print_r($products);


/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 3/28/2019
 * Time: 3:39 PM
 */