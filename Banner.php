<?php


namespace Banner;


class Banner
{

    public function print_banner($isHome=false, $dao=null){

        $rtn = "<div class=\"banner\">";
        $rtn .= "<a href='index.php'><img class=\"logo\" src=\"/sprout.png\" alt=\"logo\"/></a>";

        $rtn .= "<h1 id='homeTitle'>My Neighbor's Garden</h1>";

        if ($isHome){
            $rtn .= "<div class='banner-group'>";
            $rtn .= "<button type=\"button\" id=\"showRegistrationForm\">Signup</button>";
            $rtn .= "<button type=\"button\" id=\"showLoginForm\">Login</button>";
            $rtn .= "<button type=\"button\" id=\"guestShopperLogin\">Browse</button>";
            $rtn .= "</div>";
        } else {
            // Display the user's profile picture instead of the buttons
            $rtn .= "<div class='user-banner-group'>";
            $rtn .= "<div class='userGroup'>".$_SESSION["username"]."</div>";
#            $rtn .= "<i class=\"dropdown icon\"></i>";
#            $rtn .= "<div class=\"menu\">";
            $rtn .= "<a href='php/userLogout.php'>Logout</a>";

            // Get the number of order items in the shoppping cart
            if ($_SESSION["usertype"]=="shopper") {
                if (isset($_SESSION["orderID"]) && ($dao != null)) {
                    $respNumItems = $dao->fetchNumOrderItems($_SESSION["orderID"]);
                    $numCartItems = $respNumItems["numItems"];
                } else {
                    $numCartItems = 0;
                }

                $rtn .= "&nbsp<a href='viewCart.php' id='numCartItems'>$numCartItems</a>";
                $rtn .= "&nbsp<a href='viewOrders.php' id='viewOrdersLink'>Orders</a>";
            }
#            $rtn .= "</div>";

            $rtn .= "</div>";
//                $rtn .="<a class=\"sprite pointer\" href=\"userHome.php\"></a>";

 #           $rtn .= "<div>";
        }

        $rtn .= "</div>";
        $rtn .= "<hr>";

        print($rtn);
    }

}