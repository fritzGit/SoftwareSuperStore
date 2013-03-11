/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

//<![CDATA[
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
        error: function(response) {
            overlay.hide();
            alert("Error: Product was not added to cart.");
        }
    });
}

//]]>