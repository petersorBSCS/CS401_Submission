/*
    forms.js -- Used for validating forms before sending them to the server for processing
*/

// Make the global (for client side validation)
var disableLogin;
var disableRegister;
var disableShop;
var disableSubmitOrder;

var newPicNewProduct;

$(document).ready(function() {

    // Send the clien't timezone to the server
    var tz = jstz.determine();
    var timezone = tz.name();

    newPicNewProduct = true;


    $.post("php/setClientTimezone.php", {tz: timezone}, function(data){

    });

    // These are used to disable buttons on the homepage
    disableLogin = false;
    disableRegister = false;
    disableShop = false;

    // Attach event listeners
    $("#invalidLogin").hide();

    // Checking the password validity on each keypress
    $("input[name='password']").keyup(validatePassword);

    // Check the email validity on each keypress
    $("input[name='email']").keyup(checkUniqueEmail);

    // Form is submitted, run full validation
    //$('#submitRegistration').click(validateRegistration);

    // Checking the username validity on each keypress
    $("input[name='username']").keyup(checkUniqueUsername);

    // Show / Hide the User Registration form
    $("button#showRegistrationForm").click(function(e){
        if (disableRegister){return;}
        $("div.userForm#userRegistrationForm").fadeIn(300);
        $("body").blur();
        disableLogin = true;
        disableShop = true;
    });

    $("button#submitRegistration").click(function(e) {
        validateRegistration();
        if (!disableRegister) {
            //$("#registrationForm").submit();
            // Submit the registration
            submitRegistration();
            disableLogin = false;
            disableShop = false;
        }
    });
/*
    $("#checkEmailPromptButton").click(function(e){
        $("#checkEmailPrompt").fadeOut(150);
    })
*/
    $("button#cancelRegistration").click(function(e){
        $("div.userForm#userRegistrationForm").fadeOut(300);
        $("button#showLoginForm").removeAttr("disabled"); // enable the login button
        $(".error").remove();
        disableLogin = false;
        disableShop = false;
    });

    // Show / Hide the user login form
    $("button#showLoginForm").click(function(e){
        if (disableLogin) {return;}
        $("div.userForm#userLoginForm").fadeIn(300);
        disableRegister = true;
        disableShop = true;
    });

    $("button#submitLogin").click(function(e){

        var val = true; // Call a validation first FIXME
        if (val){ validateLogin(); }
        disableRegister = false;
        disableShop = false;
    });

    $("button#cancelLogin").click(function(){
        $("div.userForm#userLoginForm").fadeOut(300);
        $(".error").remove();
        disableRegister = false;
        disableShop = false;
    });

    $("button#guestShopperLogin").click(function(){
        if(disableShop) {return;}
        disableLogin = false;
        disableRegister = false;
        window.location.assign("php/guestLogin.php");
    });

    // Modal for showing product details
    $(".product").click(showProductDetails);

    // Button for removing an order
    $("button[id^=removeOrderItem_]").on("click",showRemoveOrderItem);

    // Button for editing an order
    $("button[id^=editOrderItem_]").on("click",showEditOrderItem);

    // Button for cancelling an order
    $("#cancelOrderButton").on("click",showCancelOrderConfirm);

    // Button for cancelling an order
    $("#submitOrderButton").on("click",submitOrder);

    // Button for searching for an item
    $("#productSearchFormButton").on("click",filterItems);


    $("button#prevProfilePic").on('click',prevProfPic);

    $("button#nextProfilePic").on('click',nextProfPic);

    $("#changeProfilePicButton").on('click',function(){
        $("#changeProfilePic").click();
    });

    $("#addProfilePicButton").on('click',function(){
        $("#addProfilePic").click();
    });

    $("#addProfilePic").on('change', addProfilePic);

    $("#removeProfilePicButton").on('click', removeProfilePic);

    $("#editGrowerProfile").on('click', showEditGrowerProfile);

    fillAboutField();

    $("#manageOrdersButton").on('click',function(){
        window.location.assign("manageOrders.php");
    });

    $("#manageInventoryButton").on('click',function(){
        window.location.assign("manageProducts.php");
    });

    // Button for editing a product
    $("button[id^=editProduct_]").on("click",showEditProduct);

    // Button for removing a product
    $("button[id^=removeProduct_]").on("click",showRemoveProduct);

    // Button for adding a product
    $("#addProductButton").on("click",showAddProduct);

    // Changing the order status (on Grower's Orders page)
    $("button.changeOrderItemStatusButton").on('click', changeOrderItemStatus);

    $("button.addProductToCart").on('click', function(){
        console.log($(this).attr('id'));
        var thisButton = $(this).attr('id').split("_");
        var thisUnitType = thisButton.pop();
        var thisProdID = thisButton.pop();
        var thisProdName = thisButton.pop();
        var thisProdPrice = thisButton.pop();

        $("#addToCart label[for='qty']").html("Quantity ["+thisUnitType+"]");
        $("#addToCart h3").html("Add "+thisProdName+" to your cart?");
        $("#addToCart").fadeIn(200);

        $("#cancelAddToCart").on('click',function(){
            $("#addToCart").fadeOut(200);
        });

        $("#submitAddToCart").on('click',function(){

            var value = {
                "quantity"  : $("#addToCart input[name='qty']").val(),
                "shipDate"  : $("#addToCart input[name='date']").val(),
                "prodID"    : thisProdID,
                "unitPrice" : thisProdPrice,
                "status"    : "ordered",
                "name"      : thisProdName
            };

            $.ajax({
                type: "POST",
                url: "php/addOrderItem.php",
                data: value,
                success: function(resp) {
                    // Update the number of items in the cart
                    // in the banner
                    $("a#numCartItems").html(resp);
                    $("#addToCart").fadeOut(200);
                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });



        });



    });

});


function changeOrderItemStatus(){

    var orderItemID = $(this).attr('id');
    console.log("This: ["+$(this)+"]orderItemID: "+orderItemID);
    var buttonParent = $(this).parent();
    var button = $(this);

    button.unbind('click');
    var buttonSibling = $(this).siblings();
    var buttonHTML = buttonParent.html();

    button.css("display","none");
    buttonSibling.css("display","unset");

    buttonSibling.focus(function(){

    });

    buttonSibling.focus();

    buttonSibling.focusout(function(){
        button.css("display","unset");
        buttonSibling.css("display","none");
        button.on('click', changeOrderItemStatus);
        buttonSibling.unbind('focusout');
    });


    buttonSibling.change(function(){

        button.on('click', changeOrderItemStatus);
        button.css("display","unset");
        buttonSibling.css("display","none");

        // Unbind this handler
        buttonSibling.unbind('change');

        var selection = buttonSibling.val();

        if (selection==="null") {return;}

        // AJAX request to change this order item's status, and update the order
        // status accordingly
        var value = { "orderItemID" : orderItemID, "status" : selection };

        $.ajax({
            type: "POST",
            url: "php/updateOrderItemStatus.php",
            data: value,
            success: function(resp) {

                // Regenerate the grower orders page
                var value = { "regenerate" : "true" };

                // Re-build the orders container
                $.ajax({
                    type: "POST",
                    url: "php/generateGrowerOrders.php",
                    data: value,
                    success: function(resp) {

                        $("#growerOrders").html(resp);

                        // Re-bind all the update button handlers
                        $("button.changeOrderItemStatusButton").on('click', changeOrderItemStatus);

                    }
                })
                    .done(function(){
                    })
                    .fail(function(){
                    })
                    .done(function(){
                    });

            }
        })
            .done(function(){
            })
            .fail(function(){
            })
            .done(function(){
            });

    });




  /*

    $("span.changeOrderItemStatus select").focus();

        $("span.changeOrderItemStatus").focusout(function(){
            buttonParent.html(buttonHTML);
            $("button.changeOrderItemStatus").on('click', changeOrderItemStatus);
        });


    $("select[name='orderStatus']").change(function(){
        buttonParent.html(buttonHTML);
        $("button.changeOrderItemStatus").on('click', changeOrderItemStatus);
    });
*/
    //console.log(oldHTML);

}

function showAddProduct(){

    // first, check if the user has completed their profile

    // Re-fetch the inventory
    var value = {};

    $.ajax({
        type: "GET",
        url: "php/isProfileComplete.php",
        data: value,
        success: function(resp) {

            if(resp=="false"){
                // Prompt the grower to complete their profile first

                $("#growerCompleteProfilePrompt").fadeIn(200);

                // Bind the ok button
                $("#growerCompleteProfileButton").on('click',function(){
                    $("#growerCompleteProfilePrompt").fadeOut(200);
                });

                return;
            }


            var val = {};

            $.ajax({
                type: "GET",
                url: "php/fetchEditProductDetails.php",
                data: val,
                success: function (resp) {
                    $("#productDetails span").html(resp);
                    $("#productDetails").fadeIn(200);

                    $("#closeProdDetails").on('click',function(){
                        $("#productDetails").fadeOut(200);
                    });

                    $("#editProductSubmitButton").on('click',function(){

                        var val = $("#editProductForm").serialize();

                        // Add in some client side validation here
                        $.ajax({
                            type: "POST",
                            url: "php/addProduct.php",
                            data: val,
                            success: function (resp) {

                                // Re-fetch the inventory
                                var value = {};

                                $.ajax({
                                    type: "GET",
                                    url: "php/generateGrowerInventory.php",
                                    data: value,
                                    success: function(resp) {

                                        $("#growerInventory").html(resp);

                                        // Need to rebind these buttons
                                        $("button[id^=editProduct_]").on("click",showEditProduct);
                                        $("button[id^=removeProduct_]").on("click",showRemoveProduct);
                                        $("#addProductButton").on("click",showAddProduct);

                                        // Adios the addProduct DIV
                                        $("#productDetails").fadeOut(200);

                                        newPicNewProduct = true;
                                    }
                                })
                                    .done(function(){
                                    })
                                    .fail(function(){
                                    })
                                    .done(function(){
                                    });
                            }
                        })
                            .done(function () {
                            })
                            .fail(function () {
                            })
                    });
                }
            })
                .done(function () {
                })
                .fail(function () {
                })

        }

    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function fillAboutField() {

    var growerAboutField = $("#dispGrowerAbout");
    var growerAboutInput = $("#growerAboutInput");

    if((growerAboutField!=null)||(growerAboutInput!=null)){
        var val = {};

        $.ajax({
            type: "GET",
            url: "php/fetchGrowerAbout.php",
            data: val,
            success: function (resp) {
                if(growerAboutField!=null){
                    growerAboutField.html(resp);
                }

                if(growerAboutInput!=null){
                    var string = resp.replace(/(<br \/\>)/gm,"");
                    growerAboutInput.html(string);
                }
            }
        })
            .done(function () {
            })
            .fail(function () {
            })
            .done(function () {
            });
    }

}



function growerZipEntered() {

    if($("#GrowerZip").val().length != 5){
        return;
    }

    var val = {"zip" : $("#GrowerZip").val()};

    $.ajax({
        type: "POST",
        url: "php/fetchCityStateZip.php",
        data: val,
        success: function (resp) {

            var status = resp.split("___");

            if (status[0]!="error") {

                resp = status[1];

                // Fetch the city/state info from the API
                var cityState = resp.split("_");
                var city = cityState[0];
                var state = cityState[1];

                $("#GrowerCity").val(city);
                $("#GrowerState").val(state).change();
            }
        }
    })
        .done(function () {
        })
        .fail(function () {
        })
        .done(function () {
        });


}

function submitEditGrowerProfile(){

    var val = $("#growerCompleteProfile").serialize();

    $.ajax({
        type: "POST",
        url: "php/finishRegistration.php",
        data: val,
        success: function(resp) {
            var val = {};
            $.ajax({
                type: "POST",
                url: "php/fetchGrowerInfo.php",
                data: val,
                success: function(resp) {
                    $("#growerInfoContainer span").html(resp);
                    fillAboutField();
                    $("#editGrowerProfile").on('click', showEditGrowerProfile);
                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function showEditGrowerProfile(){

    var value = {"editProfile" : "true" };

    $.ajax({
        type: "POST",
        url: "php/fetchGrowerInfo.php",
        data: value,
        success: function(resp) {
            $("#growerInfoContainer span").html(resp);
            fillAboutField();
            $("#submitEditGrowerProfile").on('click', submitEditGrowerProfile);
            $("#GrowerZip").keyup(growerZipEntered);
            $("#cancelEditGrowerProfile").on('click',function(){
                var val = {};
                $.ajax({
                    type: "POST",
                    url: "php/fetchGrowerInfo.php",
                    data: val,
                    success: function(resp) {
                        $("#growerInfoContainer span").html(resp);
                        fillAboutField();
                        $("#editGrowerProfile").on('click', showEditGrowerProfile);
                    }
                })
                    .done(function(){
                    })
                    .fail(function(){
                    })
                    .done(function(){
                    });
            });
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function removeProfilePic(){

    // Get the ID for the image that's currently being displayed
    var currImgID = $(".displayImg").attr('id').split("_").pop();
    var currImgDB_entryID = $("input[id^='userImgID_"+currImgID+"_']").attr('id').split("_").pop();
    console.log("currIMD_DB: "+currImgDB_entryID);

    var value = {"id" : currImgDB_entryID };

    $.ajax({
        type: "POST",
        url: "php/removeProfilePic.php",
        data: value,
        success: function(resp) {
            console.log(resp);
            regenerateProfilePics();

        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function addProfilePic(){

    var form = document.getElementById('profilePicAddForm');
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.open('POST','php/addProfilePic.php',false);
    xhr.send(formData);
    regenerateProfilePics();

}

function prevProfPic() {

    // Make sure we aren't at the beginning of the list
    var currProfImg = $(".displayImg");

    var currProfImgIdx = eval(currProfImg.attr('id').split("_").pop());
    if(currProfImgIdx==0){
        return;
    }

    // Fetch all the product images as a collection
    var profImgs = $("img[id^=userImg_]");

    var numImgs = eval(profImgs.length);

    for(var i=0;i<numImgs;i++){
        // Get this element's index
        var imgIdx = eval(profImgs[i].id.split("_").pop());
        if(imgIdx==(currProfImgIdx-1)){
            currProfImg.attr('class',"hideImg");
            profImgs[i].className = "displayImg";
        }
    }
}

function nextProfPic (){

    // Make sure we aren't at the end of the list
    var currProfImg = $(".displayImg");

    // Fetch all the product images as a collection
    var profImgs = $("img[id^=userImg_]");

    var currProfImgIdx = eval(currProfImg.attr('id').split("_").pop());
    if(currProfImgIdx==eval(profImgs.length-1)){
        return;
    }

    var numImgs = eval(profImgs.length);

    for(var i=0;i<numImgs;i++){
        // Get this element's index
        var imgIdx = eval(profImgs[i].id.split("_").pop());

        if(imgIdx==(currProfImgIdx+1)){
            currProfImg.attr('class',"hideImg");
            profImgs[i].className = "displayImg";
        }
    }

}


function regenerateProfilePics(){


    var value = {"email" : $("#email").val()};


    $.ajax({
        type: "GET",
        url: "php/generateProfilePics.php",
        data: value,
        success: function(resp) {

                $("#growerImgContainer").html(resp);

                // Re-attach these handlers, Re-building the DOM
                $("#addProfilePic").change(addProfilePic);
                $("button#prevProfilePic").on('click',prevProfPic);
                $("button#nextProfilePic").on('click',nextProfPic);
                $("#addProfilePicButton").on('click',function(){
                    $("#addProfilePic").click();
                });
                $("#removeProfilePicButton").on('click', removeProfilePic);
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });


}




function filterItems(){

    // Fetch the products that match the search filters
    // Fetch the orderID from the DOM


    var namesInput = $("#itemsList input[type='checkbox']:checked");
    var distanceInput = $("#productSearchForm select option:selected").text();
    var zipInput = $("#productSearchForm input[name='zip']").val();
    var beforeAfterInput = $("#productSearchForm input[type='radio']:checked").val();
    var dateInput = $("#productSearchForm input[type='date']").val();
    var growerInput = $("#productSearchForm input[name='grower']").val();


    // Fetch the list of product names as csv
    var names = "";
    var namesIdx = 0;
    $.each(namesInput, function(){
        //names.push($(this).attr('name'));
        if(namesIdx != 0){
            names += ",";
        }
            names += $(this).attr('name');
        namesIdx++;
    });

    var filters = {};

    // Decide whether to add these inputs to the filter

    if (names.length>0){ filters["names"] =  names; }
    if ( ['5','15','20','50','100','200','500'].includes(distanceInput) ) { filters["distance"] = distanceInput; }
    if (zipInput.match(/^[0-9]{5}$/g)!=null) { filters["zip"] = zipInput; }
    if ( ["past","future"].includes(beforeAfterInput)) { filters["beforeAfter"] = beforeAfterInput; }
    if (dateInput.match(/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/g)!=null) { filters["date"] = dateInput; }
    if (growerInput.length>0) { filters["grower"] = growerInput; }

console.log(growerInput);
    /*
    for(var key in filters){
        console.log(key+":"+filters[key]);
    }
*/

    // Schedule an event to show the "Loading screen after 1s
    var timeout = setTimeout(showLoading, 1000);

    $.ajax({
        type: "POST",
        url: "php/generateItems.php",
        data: filters,
        success: function(resp) {

            clearTimeout(timeout);
            $("#Loading").fadeOut(200);

            $("#productResults").html(resp);

            // Need to re-bind this since we're rebuilding this part of the page
            $(".product").on('click', showProductDetails);

            var data = {};

            $.ajax({
                type: "GET",
                url: "php/fetchNumProdInSearch.php",
                data: data,
                success: function(resp) {

                    var batch_btns = "<div>" +
                        "<button class='generalButton' id='prevProdBatch'>prev</button>" +
                        "<button class='generalButton' id='nextProdBatch'>next</button>" +
                        "</div>";

                    var numResults = resp.split("##prevNextIdx##");

                    var indices = numResults.pop().split("_");

                    var num = numResults[0].split(" ");

                    // If we can fit the results on one page, then no need for the buttons
                    if (num[0]<=20){
                        batch_btns="";
                    }

                    // If there are a lot of results, then reduce the size of the buttons text

                    $("#numProdInSearch").html(numResults+batch_btns);

                    // Set the text of the buttons
                    $("#prevProdBatch").html(indices[0]+"-"+indices[1]);
                    $("#nextProdBatch").html(indices[2]+"-"+indices[3]);

                    $("#prevProdBatch").css('font-size','16pt');
                    $("#nextProdBatch").css('font-size','16pt');

                    $("#nextProdBatch").click(function(){

                        // Fetch the orderID from the DOM
                        var val = { "dir" : "next"};

                        $.ajax({
                            type: "POST",
                            url: "php/fetchProdBatch.php",
                            data: val,
                            success: function(resp) {

                                var results = resp.split("##prevNextIdx##");

                                var indices = results.pop().split("_");

                                // If there are a lot of results, then reduce the size of the buttons text
                                var maxItem = Math.max.apply(Math,indices);

                                // Set the text of the buttons
                                $("#prevProdBatch").html(indices[0]+"-"+indices[1]);
                                $("#nextProdBatch").html(indices[2]+"-"+indices[3]);

                                // Once we go over 999, we have layout problems
                                if (maxItem>999){
                                    var prevWidth = parseFloat($("#prevProdBatch").textWidth());
                                    var nextWidth = parseFloat($("#prevProdBatch").textWidth());
                                    console.log("Width: "+(prevWidth+nextWidth));
                                    if ((prevWidth+nextWidth)>160) {
                                        // Reduce the text size to make the buttons fit
                                        $("#prevProdBatch").css('font-size','14pt');
                                        $("#nextProdBatch").css('font-size','14pt');
                                    }
                                } else {

                                    $("#prevProdBatch").css('font-size','16pt');
                                    $("#nextProdBatch").css('font-size','16pt');
                                }

                                $("#productResults").html(results);

                                // Need to re-bind this since we're rebuilding this part of the page
                                $(".product").on('click', showProductDetails);
                            }
                        })
                            .done(function(){
                            })
                            .fail(function(){
                            })
                            .done(function(){
                            });

                    });


                    $("#prevProdBatch").click(function(){

                        // Fetch the orderID from the DOM
                        var val = {"dir" : "prev"};

                        $.ajax({
                            type: "POST",
                            url: "php/fetchProdBatch.php",
                            data: val,
                            success: function(resp) {

                                var results = resp.split("##prevNextIdx##");

                                var indices = results.pop().split("_");

                                // If there are a lot of results, then reduce the size of the buttons text
                                var maxItem = Math.max.apply(Math,indices);

                                // Set the text of the buttons
                                $("#prevProdBatch").html(indices[0]+"-"+indices[1]);
                                $("#nextProdBatch").html(indices[2]+"-"+indices[3]);

                                // Once we go over 999, we have layout problems
                                if (maxItem>999){
                                    var prevWidth = parseFloat($("#prevProdBatch").textWidth());
                                    var nextWidth = parseFloat($("#prevProdBatch").textWidth());
                                    console.log("Width: "+(prevWidth+nextWidth));
                                    if ((prevWidth+nextWidth)>160) {
                                        // Reduce the text size to make the buttons fit
                                        $("#prevProdBatch").css('font-size','14pt');
                                        $("#nextProdBatch").css('font-size','14pt');
                                    }
                                } else {

                                    $("#prevProdBatch").css('font-size','16pt');
                                    $("#nextProdBatch").css('font-size','16pt');
                                }

                                $("#productResults").html(results);

                                // Need to re-bind this since we're rebuilding this part of the page
                                $(".product").on('click', showProductDetails);
                            }
                        })
                            .done(function(){
                            })
                            .fail(function(){
                            })
                            .done(function(){
                            });

                    });




                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });


        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function showLoading() {
    $("#Loading").fadeIn(200);
}

$.fn.textWidth = function(){
    // Calculate the size of the element with text
    var elem = $('<div />').css({margin: 0, padding: 0});
    $(this).append(elem);
    var width = elem.width();
    elem.remove();
    return width;
}

function submitOrder() {

    // Make sure the user has a complete profile
    // Fetch the orderID from the DOM
    var blank = {};

    $.ajax({
        type: "GET",
        url: "php/isProfileComplete.php",
        data: blank,
        success: function(resp) {


            if (resp==="true") {
                // Fetch the orderID from the DOM
                var orderID = {"orderID": $("#orderSummary input[type='hidden']").val()};

                $.ajax({
                    type: "POST",
                    url: "php/orderCheckout.php",
                    data: orderID,
                    success: function (resp) {

                        // Order has been cancelled, show the user the modal confirming
                        $("#orderSubmitted").fadeIn(200);

                        $("#orderSubmitted button").click(function () {
                            $("#orderSubmitted").fadeOut(200);
                        });

                    }
                })
                    .done(function () {
                    })
                    .fail(function () {
                    })
                    .done(function () {
                    });
            } else {

                $("#closeShopperCompleteProfilePrompt").on('click',function(){
                    $("#shopperCompleteProfilePrompt").fadeOut(300);
                });


                // Display the prompt to complete user the user profile form
                $("#shopperCompleteProfilePrompt").fadeIn(300);

                $("#zip").keyup(function(){

                    if($("#zip").val().length != 5){
                        return;
                    }

                    var val = {"zip" : $("#zip").val()};

                    $.ajax({
                        type: "POST",
                        url: "php/fetchCityStateZip.php",
                        data: val,
                        success: function (resp) {

                            var status = resp.split("___");

                            if (status[0]!="error") {

                                resp = status[1];

                                // Fetch the city/state info from the API
                                var cityState = resp.split("_");
                                var city = cityState[0];
                                var state = cityState[1];

                                $("#city").val(city);
                                $("#state").val(state).change();
                            }
                        }
                    })
                        .done(function () {
                        })
                        .fail(function () {
                        })
                        .done(function () {
                        });


                });

                $("#submitFinishShopperRegistration").on('click',finishRegistration);
                $("#cancelFinishShopperRegistration").on('click',function(){
                    // Display the prompt to complete user the user profile form
                    $("#shopperCompleteProfilePrompt").fadeOut(300);
                });

            }
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function finishRegistration(){
    var val = true;

    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var address = $("#address").val();
    var zip = $("#zip").val();
    var city = $("#city").val();
    var county = $("#county").val();
    var state = $("#state").val();

    // Validate this registration form
    var regex = new RegExp("^[a-zA-Z-']+$");
    if (!regex.test(firstname) || (firstname.length===0)) {
        $("#firstname").css('border',"2px solid red");
        val = false;
    }
    if (!regex.test(lastname) || (lastname.length===0)) {
        $("#lastname").css('border',"2px solid red");
        val = false;
    }
    if (address.length===0){
        $("#address").css('border',"2px solid red");
        val = false;
    }
    if (zip.length===0){
        $("#zip").css('border',"2px solid red");
        val = false;
    }
    if (!regex.test(county) || county.length===0){
        $("#county").css('border',"2px solid red");
        val = false;
    }
    if (!regex.test(city) || city.length===0){
        $("#city").css('border',"2px solid red");
        val = false;
    }
    if (!regex.test(state) || state.length===0){
        $("#state").css('border',"2px solid red");
        val = false;
    }

    if(val) {

        // Fetch the orderID from the DOM
        var val = $("#shopperCompleteProfilePrompt form").serialize();

        $.ajax({
            type: "POST",
            url: "php/finishRegistration.php",
            data: val,
            success: function (resp) {

                $("#shopperCompleteProfilePrompt").fadeOut(300);

            }
        })
            .done(function () {
            })
            .fail(function () {
            })
            .done(function () {
            });


    } else {
        $("#shopperCompleteProfilePrompt h2").html("Please fix your errors.");
    }
}

function showCancelOrderConfirm(){

    // Show the modal
    $("#confirmCancelOrder").fadeIn(300);

    // Bind the buttons
    $("#confirmCancelOrderButton").click(function(){

        $("#confirmCancelOrder").fadeOut(300);

        // Fetch the orderID from the DOM
        var orderID = { "orderID" : $("#orderSummary input[type='hidden']").val()};

        $.ajax({
            type: "POST",
            url: "php/removeOrder.php",
            data: orderID,
            success: function(resp) {

                // Order has been cancelled, show the user the modal confirming
                $("#orderCancelled").fadeIn(200);

            }
        })
            .done(function(){
            })
            .fail(function(){
            })
            .done(function(){
            });


    });

    $("#cancelCancelOrderButton").click(function(){
        $("#confirmCancelOrder").fadeOut(300);
    });
}

function cyclePics(dir,imgType){

    if(dir==="prev") {

            // Make sure we aren't at the beginning of the list
            var currProdImg = $(".displayImg");
            var currProdImgIdx = eval(currProdImg.attr('id').split("_").pop());
            if (currProdImgIdx == 0) {
                return;
            }

            // Fetch all the product images as a collection
            var prodImgs = $("img[id^="+imgType+"_]");

            var numImgs = eval(prodImgs.length);

            for (var i = 0; i < numImgs; i++) {
                // Get this element's index
                var imgIdx = eval(prodImgs[i].id.split("_").pop());
                if (imgIdx == (currProdImgIdx - 1)) {
                    currProdImg.attr('class', "hideImg");
                    prodImgs[i].className = "displayImg";
                }
            }
    } else if(dir==="next") {
            // Make sure we aren't at the end of the list
            var currProdImg = $(".displayImg");

            // Fetch all the product images as a collection
            var prodImgs = $("img[id^="+imgType+"_]");

            var currProdImgIdx = eval(currProdImg.attr('id').split("_").pop());
            if (currProdImgIdx == eval(prodImgs.length - 1)) {
                return;
            }

            var numImgs = eval(prodImgs.length);

            for (var i = 0; i < numImgs; i++) {
                // Get this element's index
                var imgIdx = eval(prodImgs[i].id.split("_").pop());

                if (imgIdx == (currProdImgIdx + 1)) {
                    currProdImg.attr('class', "hideImg");
                    prodImgs[i].className = "displayImg";
                }
            }
    }
}

function regenerateProductPics(prodID){

    console.log("ProdID: "+prodID);

    var value = {"prodID" : prodID, "ajax" : "true"};


    $.ajax({
        type: "POST",
        url: "php/generateProductPics.php",
        data: value,
        success: function(resp) {

            //console.log(resp);

            $("#imgContainer").html(resp);

            // Re-attach these handlers, Re-building the DOM
            $("#addProductPic").change(addProductPic);

            $("#nextProdPic").on('click', function(){ cyclePics("next","prodImg"); });
            $("#prevProdPic").on('click', function() { cyclePics("prev","prodImg"); });

            $("#addProductPicButton").on('click',function(){
                $("#addProductPic").click();
            });

            $("#removeProductPicButton").on('click', removeProductPic);
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });


}

function addProductPic(){

    var form = document.getElementById('productPicAddForm');
    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.open('POST','php/addProductPic.php',false);
    xhr.send(formData);
    regenerateProductPics($("#productPicAddForm input[type='hidden']").val());

}

function removeProductPic(){

    // Get the ID for the image that's currently being displayed
    var currImgID = $(".displayImg").attr('id').split("_").pop();
    var currImgDB_entryID = $("input[id^='prodImgID_"+currImgID+"_']").attr('id').split("_").pop();
    console.log("currIMD_DB: "+currImgDB_entryID);

    var value = {"id" : currImgDB_entryID };

    $.ajax({
        type: "POST",
        url: "php/removeProductPic.php",
        data: value,
        success: function(resp) {
            // Regenerate the product pics in the edit product modal
            regenerateProductPics($("#productPicAddForm input[type='hidden']").val());


            // Regenerate the grower inventory
            var value = {};

            $.ajax({
                type: "GET",
                url: "php/generateGrowerInventory.php",
                data: value,
                success: function(resp) {

                    $("#growerInventory").html(resp);

                    // Need to rebind these buttons
                    $("button[id^=editProduct_]").on("click",showEditProduct);
                    $("button[id^=removeProduct_]").on("click",showRemoveProduct);
                    $("#addProductButton").on("click",showAddProduct);
                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });






        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function regenerateInventory() {
    // Fetch the quantity and order date
    var value = {};

    $.ajax({
        type: "GET",
        url: "php/generateGrowerInventory.php",
        data: value,
        success: function(resp) {

            $("#growerInventory").html(resp);

            // Need to rebind these buttons
            $("button[id^=editProduct_]").on("click",showEditProduct);
            $("button[id^=removeProduct_]").on("click",showRemoveProduct);
            $("#addProductButton").on("click",showAddProduct);

            // Adios the editProduct DIV
            $("#productDetails").fadeOut(200);
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function showEditProduct() {

    // Form a post request using this button's ID to remove the order item from the
    // Database
    var productID = this.id.split("_").pop();

    // Fetch the quantity and order date
    var value = { "prodID" : productID };

    $.ajax({
        type: "POST",
        url: "php/fetchEditProductDetails.php",
        data: value,
        success: function(resp) {

            $("#productDetails span").html(resp);
            $("#productDetails").fadeIn(200);

            $("#closeProdDetails").on('click',function(){
                regenerateInventory();
                $("#productDetails").fadeOut(200);
            });

            $("#nextProdPic").on('click', function(){ cyclePics("next","prodImg"); });
            $("#prevProdPic").on('click', function() { cyclePics("prev","prodImg"); });

            $("#addProductPicButton").on('click',function(){
                $("#addProductPic").click();
            });

            $("#addProductPic").on('change', addProductPic);

            $("#removeProductPicButton").on('click', removeProductPic);

            // Bind the submit action of the form
            $("#editProductSubmitButton").on('click', function(){

                // Update the product details

                // Fetch the quantity and order date
                var value = $("form[id='editProductForm']").serialize();

                $.ajax({
                    type: "POST",
                    url: "php/updateProductDetails.php",
                    data: value,
                    success: function(resp) {



                        // Now, rebuild the grower_products page
                        regenerateInventory();
/*
                        // Fetch the quantity and order date
                        var value = {};

                        $.ajax({
                            type: "GET",
                            url: "php/generateGrowerInventory.php",
                            data: value,
                            success: function(resp) {

                                $("#growerInventory").html(resp);

                                // Need to rebind these buttons
                                $("button[id^=editProduct_]").on("click",showEditProduct);
                                $("button[id^=removeProduct_]").on("click",showRemoveProduct);
                                $("#addProductButton").on("click",showAddProduct);

                                // Adios the editProduct DIV
                                $("#productDetails").fadeOut(200);
                            }
                        })
                            .done(function(){
                            })
                            .fail(function(){
                            })
                            .done(function(){
                            });
*/
                    }
                })
                    .done(function(){
                    })
                    .fail(function(){
                    })
                    .done(function(){
                    });

            });

        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function showRemoveProduct(){
    // Show the modal to confirm the grower wants to remove this item
    var productID = this.id.split("_").pop();

    $("#confirmRemoveProduct h2").html("Remove product #"+productID+"?");
    $("#confirmRemoveProduct").fadeIn(200);

    $("#cancelRemoveProductButton").on('click',function(){
        $("#confirmRemoveProduct").fadeOut(200);
    });

    $("#confirmRemoveProductButton").on('click',function(){

        // Remove this product and all its images from the DB and
        // the server

        var value = {"prodID" : productID};

        $.ajax({
            type: "POST",
            url: "php/removeProduct.php",
            data: value,
            success: function(resp) {

                // Fetch the quantity and order date
                var value = {};

                $.ajax({
                    type: "GET",
                    url: "php/generateGrowerInventory.php",
                    data: value,
                    success: function(resp) {

                        $("#growerInventory").html(resp);

                        // Need to rebind these buttons
                        $("button[id^=editProduct_]").on("click",showEditProduct);
                        $("button[id^=removeProduct_]").on("click",showRemoveProduct);
                        $("#addProductButton").on("click",showAddProduct);

                        $("#confirmRemoveProduct").fadeOut(200);
                    }
                })
                    .done(function(){
                    })
                    .fail(function(){
                    })
                    .done(function(){
                    });

            }
        })
            .done(function(){
            })
            .fail(function(){
            })
            .done(function(){
            });

    });

}

function showEditOrderItem() {

    // Form a post request using this button's ID to remove the order item from the
    // Database
    var orderItemID = this.id.split("_").pop();

    // Change the ID of the hidden form input so we know what order item to update
    $("#editOrderForm input[type='hidden']").attr('value',orderItemID);

    // Fetch the quantity and order date
    var value = { "order_itemID" : orderItemID, "type" : "editItem" };

    $.ajax({
        type: "POST",
        url: "php/fetchOrderItemInfo.php",
        data: value,
        success: function(resp) {

            var qty_shipDate = resp.split(" ");
            var qty = qty_shipDate[0];
            var ship_date = qty_shipDate[1];
            var units = qty_shipDate[2];

            $("#editOrderForm label[for='quantity']").html("Quantity ["+units+"]");
            $("#editOrderForm input[name='quantity']").attr('value', qty);
            $("#editOrderForm input[name='shipDate']").attr('value', ship_date);


            console.log(ship_date);

        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });


    // Bind the cancel button
    $("#cancelEditOrderItemButton").click(function(){
        $("#editOrderItem").fadeOut(300);
    });

    // Bind the edit submit button
    $("#submitEditOrderItemButton").on('click',editOrderItem);

    // Show the modal
    $("#editOrderItem").fadeIn(300);

}

function editOrderItem(){

    var value = $("#editOrderForm").serialize();

    $.ajax({
        type: "POST",
        url: "php/updateOrderItem.php",
        data: value,
        success: function(resp) {

            // Now that we've edited that order item, refetch the order items
            // Form a get request to update the shopping cart view
            value = {};

            $.ajax({
                type: "GET",
                url: "cartItems.php",
                data: value,
                success: function(resp) {

                    // Rebuild the shopping cart area
                    $("#shoppingCart").html(resp);

                    // Need to rebind these events, since the DOM is being rebuilt
                    $("button[id^=removeOrderItem_]").on("click",showRemoveOrderItem);
                    $("button[id^=editOrderItem_]").on("click",showEditOrderItem);
                    $("#cancelOrderButton").on("click",showCancelOrderConfirm);
                    $("#submitOrderButton").on("click",submitOrder);
                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });

        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function showRemoveOrderItem() {

    // Form a post request using this button's ID to remove the order item from the
    // Database
    var orderItemID = this.id.split("_").pop();

    // Change the ID of the remove confirm button so we know what to delete
    $confirmRemoveItemButton = $("#confirmRemoveOrderItemButton");
    $confirmRemoveItemButton.attr('id', 'confirmRemoveOrderItem'+"_"+orderItemID);

    var value = { "order_itemID" : orderItemID };

    $.ajax({
        type: "POST",
        url: "php/fetchOrderItemInfo.php",
        data: value,
        success: function(resp) {

            $("#confirmItemRemovalSummary").html(resp);

        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });



    // Bind the cancel button
    $("#cancelRemoveOrderItemButton").click(function(){
        $("#confirmRemoveOrderItem").fadeOut(300);
    });

    // Bind the click events
    //$("#confirmRemoveOrderItemButton").on('click',removeOrderItem);
    $confirmRemoveItemButton.on('click',removeOrderItem);

    // Show the modal
    $("#confirmRemoveOrderItem").fadeIn(300);

}

function removeOrderItem(){

    // Form a post request using this button's ID to remove the order item from the
    // Database
    var orderItemID = this.id.split("_").pop();
    console.log("Order Item ID = "+orderItemID);

    var value = { "order_itemID" : orderItemID };

    // Reset the confirm button's ID
    $(this).attr('id', 'confirmRemoveOrderItem');

    $.ajax({
        type: "POST",
        url: "php/removeOrderItem.php",
        data: value,
        success: function(resp) {



            $("#numCartItems").html(resp);

            // Form a get request to update the shopping cart view
            value = {};

            $.ajax({
                type: "GET",
                url: "cartItems.php",
                data: value,
                success: function(resp) {

                    // Remove the confirmation modal
                    $("#confirmRemoveOrderItem").fadeOut(300);

                    // Rebuild the shopping cart area
                    $("#shoppingCart").html(resp);

                    // Need to rebind the event, since the DOM is being rebuilt
                    $("button[id^=removeOrderItem_]").on("click",showRemoveOrderItem);
                    $("button[id^=editOrderItem_]").on("click",showEditOrderItem);
                    $("#cancelOrderButton").on("click",showCancelOrderConfirm);
                    $("#submitOrderButton").on("click",submitOrder);
                }
            })
                .done(function(){
                })
                .fail(function(){
                })
                .done(function(){
                });
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function showProductDetails(event){
    // Fetch this product's ProductID
    var product = {"prodID" : this.className.split(" ").pop()};

    $.ajax({
        type: "POST",
        url: "php/fetchProdDetails.php",
        data: product,
        success: function(resp) {

            $("#productDetails span").html(resp);
            $("#productDetails").fadeIn(300);

            $("button#closeProdDetails").click(function(e){
                $("#productDetails").fadeOut(300);
            });

            $("button#prevImg").click(function(e){

                // Make sure we aren't at the beginning of the list
                var currProdImg = $(".displayImg");
                var currProdImgIdx = eval(currProdImg.attr('id').split("_").pop());
                if(currProdImgIdx==0){
                    return;
                }

                // Fetch all the product images as a collection
                var prodImgs = $("img[id^=prodImg_]");

                var numImgs = eval(prodImgs.length);

                for(var i=0;i<numImgs;i++){
                    // Get this element's index
                    var imgIdx = eval(prodImgs[i].id.split("_").pop());
                    if(imgIdx==(currProdImgIdx-1)){
                        currProdImg.attr('class',"hideImg");
                        prodImgs[i].className = "displayImg";
                    }
                }

            });

            $("button#nextImg").click(function(e){

                // Make sure we aren't at the end of the list
                var currProdImg = $(".displayImg");

                // Fetch all the product images as a collection
                var prodImgs = $("img[id^=prodImg_]");

                var currProdImgIdx = eval(currProdImg.attr('id').split("_").pop());
                if(currProdImgIdx==eval(prodImgs.length-1)){
                    return;
                }

                var numImgs = eval(prodImgs.length);

                for(var i=0;i<numImgs;i++){
                    // Get this element's index
                    var imgIdx = eval(prodImgs[i].id.split("_").pop());

                    if(imgIdx==(currProdImgIdx+1)){
                        currProdImg.attr('class',"hideImg");
                        prodImgs[i].className = "displayImg";
                    }
                }

            });

            $("button#placeOrder").click(function(e){

                // Form an AJAX request to either tell the user to register (guest) or
                // Show an order form
                var value = {};

                $.ajax({
                    type: "GET",
                    url: "php/checkIfGuest.php",
                    data: value,
                    success: function(resp) {
                        if(resp=="guest"){
                            // Tell the guest to sign up for an account
                            $("#guestSignUpPrompt").fadeIn(300);

                            $("#closeGuestSignUpPrompt").click(function(){
                                $("#guestSignUpPrompt").fadeOut(300);
                            });

                        } else if(resp=="incompleteProfile") {
                            // Prompt the user with a more complete form
                            // for them to complete their profile
                            // FIXME
                        } else {
                            $("#placeOrderFieldSet").fadeIn(300);
                            $("button#placeOrder").html("Add to Cart");
                            // Change the event listener for this button
                            $("button#placeOrder").attr('id',"addToCart");
                            $("button#addToCart").click(function(e){

                                // Show the order confirmation
                                    // fetch the info from the userForm
                                    var orderForm = {};
                                    var formFields = $("#placeOrderForm input");
                                    formFields.each(function(){
                                       orderForm[this.name] = this.value;
                                    });

                                    // Concatenate it into a sentence for the user
                                    var orderDetails = orderForm["quantity"]+" "+orderForm["unit_type"]+"s ";
                                    orderDetails += "of "+orderForm["name"];
                                    orderDetails += " from <a href=\"growerPage.php?growerID="+orderForm["grower_id"]+"\">"+orderForm["grower_name"]+"</a>";
                                    orderDetails += " shipped on "+orderForm["shipDate"]+".";

                                    // Display it
                                    $("#confirmOrder p").html(orderDetails);

                                $("#confirmOrder").fadeIn(150);
                                disableSubmitOrder = false;
                                $("button#confirmOrderButton").click(function(e){
                                    submitOrderItem(); // Submit the order form
                                    disableSubmitOrder = true;
                                    $("#confirmOrder").fadeOut(150);
                                    $("#productDetails").fadeOut(300);
                                    $("#itemsAddedToCart").fadeIn(300);
                                    $("button#okItemAdded").click(function(){
                                        $("#itemsAddedToCart").fadeOut(300);
                                    });
                                });
                                $("button#cancelOrderButton").click(function(e){
                                    $("#confirmOrder").fadeOut(150);
                                });
                            });
                        }
                    }
                })
                    .done(function(){
                    })
                    .fail(function(){
                    })
                    .done(function(){
                    });




            });


        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function submitOrderItem(){

    // AJAX was making this fire 3 times ...?
    if (disableSubmitOrder) {return;}

    // Grab the form data
    var form = $("#placeOrderForm");
    var value = form.serialize();

    $.ajax({
        type: "POST",
        url: "php/addOrderItem.php",
        data: value,
        success: function(resp) {
            // Update the number of items in the cart
            // in the banner
            $("a#numCartItems").html(resp);
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function submitRegistration(){

    var value = {
        "email" : $("#email").val(),
        "password" : $("#password").val(),
        "username" : $("#username").val(),
        "usertype" : $("input[type='radio'][name='usertype']:checked").val()
    };

    $.ajax({
        type: "POST",
        url: "php/registerUser.php",
        data: value,
        success: function(resp) {
            if(resp=="invalid"){
                // Bad Registration Attempt
                $("#invalidRegister h3").html("Invalid Registration Attempt");
                $("#invalidRegister").show();
                $("#invalidRegister button").click(function(){
                    $("#invalidRegister").hide();
                });
                disableRegister = true;
            } else if(resp=="already_shopper") {
                $("#invalidRegister h3").html("There is already a shopper profile registered to that email.");
                $("#invalidRegister").show();
                $("#invalidRegister button").click(function(){
                    $("#invalidRegister").hide();
                });
                disableRegister = true;
            } else if(resp=="already_grower") {
                $("#invalidRegister h3").html("There is already a gardener profile registered to that email.");
                $("#invalidRegister").show();
                $("#invalidRegister button").click(function(){
                    $("#invalidRegister").hide();
                });
                disableRegister = true;
            } else {
                // Sucessful Registration
                disableRegister = false;
                $("div.userForm#userRegistrationForm").fadeOut(300);
                $("#goodReg").fadeIn(300);
                setTimeout(closeGoodReg,2000);
            }
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });

}

function closeGoodReg(){
    $("#goodReg").fadeOut(300);
}

function validateLogin() {

    var value = {"login" : $("#login").val(), "passwordLogin" : $("#passwordLogin").val()};
    console.log(value);

    $.ajax({
        type: "POST",
        url: "php/userLogin.php",
        data: value,
        success: function(resp) {

            console.log(resp);

            if(resp=="invalid"){
                // Bad Login Creds
                $("#invalidLoginText").innerHTML = "Invalid Login Credentials.";
                $("#invalidLogin").show();
            } else if (resp=="multiple") {
                // Valid login, but two accounts present
                $("#userLoginForm").hide();
                $("#selectSessionType").show();

                $("#choseGrowerSession").click(function(){
                    // AJAX to set the session type to grower
                    value["usertype"] = "grower";
                    $.ajax({
                        type: "POST",
                        url: "php/userLogin.php",
                        data: value,
                        success: function(resp) {
                            console.log(resp);
                            window.location.assign("userHome.php");
                        }
                    })
                        .done(function(){
                        })
                        .fail(function(){
                        })
                        .done(function(){
                        });
                });

                $("#choseShopperSession").click(function(){
                    // AJAX to set the session type to shopper
                    value["usertype"] = "shopper";

                    $.ajax({
                        type: "POST",
                        url: "php/userLogin.php",
                        data: value,
                        success: function(resp) {
                            console.log(resp);
                            window.location.assign("userHome.php");
                        }
                    })
                        .done(function(){
                        })
                        .fail(function(){
                        })
                        .done(function(){
                        });
                });

            } else {

                window.location.assign("userHome.php");
            }
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function checkUniqueEmail(){

    $("#emailError").remove();

    var value = {"email" : $("#email").val()};


    $.ajax({
        type: "POST",
        url: "php/checkUniqueEmail.php",
        data: value,
        success: function(resp) {
            console.log(resp);

            if(resp>=2){
                // Email is taken
                $("input[name='email']").after('<span class="error" id="emailError">That email is already in our system!</span>');
                disableRegister = true;
            } else {
                // Let the other validation steps enable it
                disableRegister = true;
            }
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}

function checkUniqueUsername(){

    var value = {"username" : $("#username").val()};
    console.log(value);

    $("#usernameError").remove();

    $.ajax({
        type: "POST",
        url: "php/checkUniqueUsername.php",
        data: value,
        success: function(resp) {
            if(resp>0){
                // Username is taken
                $("input[name='username']").after('<span class="error" id="usernameError">That username is taken!</span>');
                disableRegister = true;
            } else {
                // Let the other validation steps enable it
                disableRegister = false;
            }
        }
    })
        .done(function(){
        })
        .fail(function(){
        })
        .done(function(){
        });
}


function validateRegistration(e) {

        // If it makes it through validation, then enable the submission
        disableRegister = false;

        // Local form inputs
        var email = $("input[name='email']");
        var password = $("input[name='password']");
        var password_confirm = $("input[name='password_confirm']");
        var username = $("input[name='username']");
        var usertype = $("input[type='radio'][name='usertype']:checked");

        // Get rid of the existing errors
        $(".error").remove();

        // Validate the email
        if (email.val().length < 1 || email.val() == "e.g. 'someone@com.com'") {
            // Make sure they typed something in
            email.after('<span class="error" id="emailError">This field is required</span>');
            //email.innerText = email.innerText + '<span class="error">This field is required</span>';//  .after('<span class="error">This field is required</span>');
            disableRegister = true;
        } else {
            // Verify a valid email address
            var regex = new RegExp("^[a-zA-Z0-9\._]+@[a-zA-Z0-9\._]+.[a-zA-Z]+$");
            if (!regex.test(email.val())) {
                email.after('<span class="error" id="emailError">Enter a valid email</span>');
                disableRegister = true;
            }
        }

        if (username.val().length < 1 || username.val() == "e.g. 'Doofy'") {
            // Make sure they typed something in
            username.after('<span class="error" id="usernameError">This field is required</span>');
            disableRegister = true;
        } else {
            // Verify a valid username
            var regex = new RegExp("^[a-zA-Z0-9\._]+$");
            if (!regex.test(username.val())) {
                username.after('<span class="error" id="usernameError">Enter a valid username</span>');
                disableRegister = true;
            }
        }

        if (usertype.val() === undefined) {
            // Stick an alert beside the usertype box
            $('fieldset#accountType > input[value="shopper"]').after('<span class="error" id="usertypeError">Select an account type</span>');
            disableRegister = true;
        }

        if (password.val().length < 1) {
            password.after('<span class="error" id="passwordError">This field is required</span>');
            disableRegister = true;
        } else {
            // Check to make sure the password requirements are met
            var regex = new RegExp("(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})");

            if (!regex.test(password.val())) {
                password.after('<span class="error" id="passwordError">Enter a valid password</span>');
                disableRegister = true;
            }
        }

        if (!(password_confirm.val() === password.val())) {
            password_confirm.after('<span class="error">Passwords do not match</span>');
            disableRegister = true;
        }

    }

function validatePassword(e){

    e.preventDefault();

    var password = $("input[name='password']");
    $("#passwordError").remove();

    if (password.val().length < 1){
        password.after('<span class="error" id="passwordError">This field is required</span>');
    } else {
        // Check to make sure the password requirements are met
        var regex = new RegExp("(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})");

        if (!regex.test(password.val())){
            password.after('<span class="error" id="passwordError">Enter a valid password</span>');
        }
    }
}