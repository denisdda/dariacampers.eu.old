/*
* functions for add vehicles to wish list 
* add in version 3.6.6
*/
var add = "add_to_wishlist";
var remove = "remove_from_wishlist";

function addToWishlist(vehid, userid) {
    jQuerVEH(document).ready(function(){
        var id = "#icon"+vehid;
        var user_id = userid; //<?php echo $my->id; ?>;
        if (user_id == 0 ) {
            alertVehicleMessage('Please Login!');
            // alert('Please Login!');
            return;
        }
        if (jQuerVEH(id).hasClass('fa-star-o')) {
            replaceClass(id, 'fa-star-o', 'fa-star');
            sendRequest(add, vehid);
        } else {
            replaceClass(id, 'fa-star', 'fa-star-o');
            sendRequest(remove, vehid);
        }
    });
}

function sendRequest(task, vehid) {
    jQuerVEH.post("index.php?option=com_vehiclemanager&task="+task+"&id="+vehid, function(response) {
    console.log(response);
    var html = jQuerVEH.parseJSON(response);
    // alert(html.message);
    alertVehicleMessage(html.message);
    });
}

function replaceClass(id, first, second) {
    jQuerVEH(id).removeClass(first);
    jQuerVEH(id).addClass(second);
}

function removeFromWishlist(vehid) {
    var id ="#vehicle" + vehid;
    sendRequest(remove, vehid);
    jQuerVEH(id).hide();
}

function alertVehicleMessage(message) {
    jQuerVEH('.vehicle-overlay').attr('style', 'display:block;');
    jQuerVEH('.vehicle-overlay').addClass('visible');
    jQuerVEH('.vehicle-modal-text').text(message);
    jQuerVEH('.vehicle-close, .vehicle-overlay' ).click(function(){
        jQuerVEH('.vehicle-overlay').attr('style', 'display:none;');
        jQuerVEH('.vehicle-overlay').removeClass('visible');

    });
}
// end functions for wishlist