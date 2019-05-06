<?php

require_once('Dao.php');

if(!isset($_SESSION)){
    session_start();
}

$dao = new Dao();

$aboutFile = $dao->checkExistingDescr("grower",$_SESSION["userID"]);

if($aboutFile!=null) {
    // If I use html_entities here, it kills the whitespace, hmmm, what to do?
    echo file_get_contents("../descr/grower/".$aboutFile);
} else {
    echo "";
}
