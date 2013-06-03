/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

//<![CDATA[
$j(document).ready(function(){
    $j("#overlay").click(function() {
        hideOverlay();
    });
});

function clearText(field){
    if(field.defaultValue == field.value){
        field.value = "";
    }
    else if(field.value == ""){
        field.value = field.defaultValue;
    }
}

function hideOverlay(){
    $j('#overlay').fadeOut();
    $j('#ajax_add_item_container').fadeOut();
}

function addProduct(proudctID, quantity)
{
    var overlay=$j('#overlay');
    var ajaxurl=baseurl+'checkout/cart/addAjax';
    $j.ajax({
        url: ajaxurl,
        data: 'product=' + proudctID + '&qty=' + quantity,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            var imageurl = baseurl+'skin/frontend/default/softss/images/ajax-loader.gif';
            overlay.html('<img id="imgloading" src="'+imageurl+'" />').show();
        },
        success: function(response) {
            overlay.html('');
            var ajaxContainer = $j('#ajax_add_item_container');

            ajaxContainer.html(response.additemhtml);
            ajaxContainer.css("top", ( $j(window).height() - ajaxContainer.height() ) / 2+$j(window).scrollTop() + "px");
            ajaxContainer.css("left", ( $j(window).width() - ajaxContainer.width() ) / 2+$j(window).scrollLeft() + "px");
            ajaxContainer.fadeIn();
            var shoppingcart = $j('.shoppingcartcontent');
            if(shoppingcart.length){
                shoppingcart.replaceWith(response.toplink);
            }
        },
        complete : function(){
            if($j('#ajax-container-realated-items').length){
                initCarousel();
            }
        },
        error: function(response) {
            overlay.hide();
            alert("Error: Product was not added to cart.");
        }
    });
}

function sortbyChange(selectObj){
    if(selectObj.value!="sortby")
        setLocation(selectObj.value);
}

function showAjaxLoginForm(){

       var overlay=$j('#overlaylogin');
       overlay.fadeIn();
       var ajaxContainer = $j('#ajax_login_container');
       ajaxContainer.css("top", ( $j(window).height() - ajaxContainer.height() ) / 2+$j(window).scrollTop() + "px");
       ajaxContainer.css("left", ( $j(window).width() - ajaxContainer.width() ) / 2+$j(window).scrollLeft() + "px");
       ajaxContainer.fadeIn();
}

function ajaxLoginPost(){

    var overlay=$j('#overlaylogin');
    var ajaxurl=baseurl+'customer/account/loginAjaxPost';
    var loginForm = new VarienForm('login-form-ajax', true);

    if(loginForm.validator && loginForm.validator.validate()){
        $j.ajax({
            url: ajaxurl,
            type: 'POST',
            data: $j('#login-form-ajax').serialize(),
            dataType: 'json',
            beforeSend: function() {
                var imageurl = baseurl+'skin/frontend/default/softss/images/ajax-loader.gif';
                overlay.html('<img id="imgloading" src="'+imageurl+'" />').show();
            },
            success: function(response) {
                overlay.html('');
                if(response.error) {
                     var message = '<ul class="messages"><li class="error-msg"><ul><li>' + response.message + '</li></ul></li></ul>';
                     $j('#message-div').html(message);
                } else {
                   window.location.href=response.url;
                }
                var ajaxContainer = $j('#ajax_login_container');
                ajaxContainer.fadeIn();
            },
            error: function(response) {
                overlay.hide();
                alert("Error while trying to login.");
            }
        });
    }
}

function hideLoginOverlay(){
    $j('#overlaylogin').fadeOut();
    $j('#ajax_login_container').fadeOut();
}

function ajaxCheckProductAvailability(){   
    
    var aUrl=baseurl+'checkout/cart/softDistributionProductAvailability';

        $j.ajax({
            url: aUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {   
                
                if(response.error) {
                     window.location.href = baseurl+'checkout/cart/softDistributionResponse?error=true';
                } else if(response.products) {
                     window.location.href = baseurl+'checkout/cart/softDistributionResponse?products='+response.products;                
                } else {
                    $('onestepcheckout-form').submit();
                }

            },
            error: function(response) {                
                 window.location.href = baseurl+'checkout/cart/softDistributionResponse?error=true';
            }
         
        });
}

//]]>