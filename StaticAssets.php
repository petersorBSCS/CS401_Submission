<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 2/15/2019
 * Time: 12:25 AM
 */

//namespace StaticAssets;


class StaticAssets
{

    function printBanner($type = 'landing'){


        $html_string = "<div class = \"banner\">";





        if ($type == 'landing') {




        } else if( $type == 'user' ) {
            $html_string = $staticDoc->getElementById("userBanner");
        } else {
            $html_string = $staticDoc->getElementById("landingBanner");
        }

        $html_string .= "</div>";

        return $html_string;
    }

}