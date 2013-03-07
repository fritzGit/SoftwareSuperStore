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

//]]>