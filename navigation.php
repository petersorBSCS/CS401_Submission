<?php

    if(!isset($_SESSION)) {session_start();}

if(!isset($_SESSION["validLogin"])){
    include("invalidSessionWarning.php");
} else {

    function page($pageRef, $pageName){

        $this_page = $_SESSION["this_page"];

        $rtn = "<a href=\"$pageRef\"";

        if ($this_page==$pageRef) {
            $rtn .= " id=\"currentPage\"";
        }
        $rtn .= ">".$pageName."</a>";

        return $rtn;
    }
?>
<?php
// No need for a navbar on the login screen
if ($_SESSION["this_page"]!="Home") {
    ?>
<div id="navigation">
    <ul>
        <?php
            foreach($_SESSION["path"] as $val) {
        ?>
                <li><?php echo page($val[0], $val[1]); ?></li>
        <?php
            }

        ?>
    </ul>
    <hr>
</div>

<?php }} ?>