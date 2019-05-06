<?php


require_once('Dao.php');

$dao = new Dao();

$orderItemID = (isset($_POST["orderItemID"])) ? $_POST["orderItemID"] : null;
$status = (isset($_POST["status"])) ? $_POST["status"] : null;

$resp = $dao->updateOrderItemStatus($orderItemID, $status);