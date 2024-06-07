function getXMLHTTP() { //fuction to return the xml http object
    var xmlhttp = false;
    try {
        xmlhttp = new XMLHttpRequest();
    }
    catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e) {
            try {
                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e1) {
                xmlhttp = false;
            }
        }
    }

    return xmlhttp;
}


$(document).ready(function () {
    /*place jQuery actions here*/

    var req = getXMLHTTP();
    if (req) {

        var link = getBaseURL();

//*********************************************
//     purchase product Item add to cart
//*********************************************

        //$("table.purchase-products form").submit(function () {
        //
        //    var id = $(this).find('input[name=product_id]').val();
        //
        //    $.post(link + "admin/purchase/add_cart_item", {product_id: id, ajax: '1'},
        //        function (data) {
        //
        //            if (data == 'true') {
        //
        //                $.get(link + "admin/purchase/show_cart", function (cart) {
        //                    $("#cart_content").html(cart);
        //                });
        //
        //            }
        //
        //        });
        //
        //    return false;
        //});

//*********************************************
//     add new product to purchase cart
//*********************************************

		$(document).on("click", ".noprinted a", function (e) {			
			$.post(link + "admin/order/markasprinted", {			
				order_id: $("#order_id").val()
			},
			function (data) {
				if (data == 'true') {					
					$(".noprinted").addClass('printed');
					$(".noprinted").removeClass('noprinted');
				}
			});
		});
			
        $("#add-product form").submit(function () {

            var product_name = $(this).find('input[name=product_name]').val();
            var qty = $(this).find('input[name=qty]').val();
            var price = $(this).find('input[name=price]').val();

            $.post(link + "admin/purchase/add_new_product_to_cart", {
                    product_name: product_name,
                    qty: qty,
                    price: price,
                    ajax: '1'
                },
                function (data) {

                    if (data == 'true') {

                        $.get(link + "admin/purchase/show_cart", function (cart) {
                            $("#cart_content").html(cart);
                            $("#newform")[0].reset();

                        });

                    }

                });
            return false;
        });


    }

});


//*********************************************
//           Get Base URL
//*********************************************

function getBaseURL() {
    return link = $('body').data('baseurl');

};

//*********************************************
//       Update cart purchase cart item
//*********************************************

function purchase(arg) {
    $('#btn_purchase').attr('disabled', 'disabled');
    var val = arg.getAttribute('id');
    var id = val.slice(3);

    // do your stuff
    var qty = $("#qty" + id).val();
    var price = $("#pri" + id).val();
    //alert(price);
    var link = getBaseURL();
    $.post(link + "admin/purchase/update_cart_item", {rowid: id, qty: qty, price: price, ajax: '1'},
        function (data) {

            if (data == 'true') {

                $.get(link + "admin/purchase/show_cart", function (cart) {
                    $("#cart_content").html(cart);
                });

            }

        });

}

//*********************************************
//       Update order cart item
//*********************************************

function order(arg) {
    $.loader.open($data_loading);
    $('#btn_order').attr('disabled', 'disabled');
    $('#btn_envoie').attr('disabled', 'disabled');
    var req = getXMLHTTP();
	console.log(req);
	return false;
    if (req) {
        var val = arg;
        var id = val.slice(3);
        // do your stuff
        var qty = $("#qty" + id).val();
        var product_id = $("#code" + id).val();
        var link = getBaseURL();
        if (qty && product_id)
            $.post(link + "admin/order/update_cart_item", {
                    rowid: id,
                    qty: qty,
                    product_id: product_id,
                    ajax: '1'
                },
                function (data) {
                    if (data == 'false') {
                        var flag = 'inventory';
                        var url = link + "admin/order/new_order/" + flag;
                        $(location).attr("href", url);
                    }
                    if (data == 'true') {
                        $.get(link + "admin/order/show_cart", function (cart) {
                            $("#cart_content").html(cart);
                        });
                        $('#btn_order').removeAttr('disabled');
                        $('#btn_envoie').removeAttr('disabled');
                        $.loader.close(true);
                    }
                });
    }
}

function order_all() {
    $.loader.open($data_loading);
    var req = getXMLHTTP();
    var $test = false;
    var datas = [];
    if (req) {
    	$( ".qty_line" ).each(function() {
	        var val = $(this).attr('id');
	        var id = val.slice(3);
	        // do your stuff
	        var qty = $("#qty" + id).val();
	        var product_id = $("#code" + id).val();
	        var link = getBaseURL();
	        if (qty && product_id) {
	        
	       datas.push({
	                    rowid: id,
	                    qty: qty,
	                    product_id: product_id,
	                    ajax: '1'
	                });
	        }
	                
       });
       //console.log(JSON.stringify(datas));
       
       $.post(link + "admin/order/update_cart_items", {datas:JSON.stringify(datas)},
	                function (data) {
	                //console.log(data);
	                   if (data == 'true') {
	                        $.get(link + "admin/order/show_cart", function (cart) {
			            $("#cart_content").html(cart);
			            $.loader.close(true);
			        });			        			        
	                    }
	                });
    }
}

function add_franco_port(arg) {
$.loader.open($data_loading);
    var req = getXMLHTTP();
    if (req) {
        // do your stuff
        var franco = $("#franco").val();
        var frais_port = $("#frais_port").val();
        var link = getBaseURL();
        $.post(link + "admin/order/update_franco_port", {
                franco: franco,
                frais_port: frais_port,
                ajax: '1'
            },
            function (data) {
                if (data == 'true') {
                    $.get(link + "admin/order/show_cart", function (cart) {
                        $("#cart_content").html(cart);
                        $.loader.close(true);
                    });
                }
            });
    }
}

//*********************************************
//     Custom Price Operation
//*********************************************

function price_checkbox(me) {

    var val = me.id
    var id = val.slice(3);

    //alert(id);
    var result = $("#pri" + id).prop('disabled');
    if (result) {
        $("#pri" + id).prop('disabled', false);
    } else {
        $("#pri" + id).prop('disabled', true);
    }

    if (result == false) {
        $('#btn_order').attr('disabled', 'disabled');
        $('#btn_envoie').attr('disabled', 'disabled');

        var req = getXMLHTTP();
        if (req) {
            // do your stuff
            var qty = $("#qty" + id).val();
            var price = $("#pri" + id).val();
            var product_code = $("#code" + id).val();
            var custom_price = 'unchecked';
            var link = getBaseURL();

            $.post(link + "admin/order/update_cart_item", {
                    rowid: id,
                    qty: qty,
                    price: price,
                    product_code: product_code,
                    custom_price: custom_price,
                    ajax: '1'
                },
                function (data) {

                    if (data == 'true') {

                        $.get(link + "admin/order/show_cart", function (cart) {
                            $("#cart_content").html(cart);
                        });

                        $.get(link + "admin/order/show_cart_summary", function (cart_summary) {
                            $("#cart_summary").html(cart_summary);
                        });

                    }

                });
        }

    }
}


//*********************************************
//     Customer phone number check
//*********************************************

function check_phone(phone) {

    var customer_id = $("#customer_id").val();
    //alert(customer_id);
    var link = getBaseURL();
    var strURL = link + "admin/customer/check_customer_phone/" + phone + "/" + customer_id;
    var req = getXMLHTTP();


    if (req) {

        req.onreadystatechange = function () {
            if (req.readyState == 4) {
                // only if "OK"
                if (req.status == 200) {
                    var result = req.responseText;
                    document.getElementById('phone_result').innerHTML = result;
                    if (result) {
                        $('#customer_btn').prop('disabled', true);
                    } else {
                        $('#customer_btn').prop('disabled', false);
                    }
                    validation_check();
                } else {
                    alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                }
            }
        }
        req.open("POST", strURL, true);
        req.send(null);
    }

}

//*********************************************
//     Employee user name check
//*********************************************

function check_user_name(str) {
    $('#sbtn').attr('disabled', 'disabled');
    var user_name = $.trim(str);
    var user_id = $.trim($("#employee_id").val());

    var link = getBaseURL();
    //alert(link);
    var strURL = link + "admin/global_controller/check_existing_user_name/" + user_name + "/" + user_id;
    var req = getXMLHTTP();
    if (req) {
        req.onreadystatechange = function () {
            if (req.readyState == 4) {
                // only if "OK"
                if (req.status == 200) {
                    var result = req.responseText;
                    document.getElementById('username_result').innerHTML = result;
                    var msg = result.trim();
                    if (msg) {
                        document.getElementById('sbtn').disabled = true;
                    } else {
                        document.getElementById('sbtn').disabled = false;
                    }

                } else {
                    alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                }
            }
        }
        req.open("POST", strURL, true);
        req.send(null);
    }
}

//*********************************************
//     Product Category to Subcategory
//*********************************************

function get_category(str) {

    if (str == '') {
        $("#subcategory").html("<option value=''>Select Subcategory</option>");
    } else {
        $("#subcategory").html("<option value=''>Select Subcategory</option>");

        var link = getBaseURL();
        var strURL = link + "admin/product/get_subcategory_by_category/" + str;
        var req = getXMLHTTP();
        if (req) {

            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        //alert(result);
                        $("#subcategory").html("<option value=''>Select Subcategory</option>");
                        $("#subcategory").append(result);
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }
    }
}

function get_ligne(str) {
    $("#ligne_product").html("<option value='' data-reassort='' data-condition=''>Merci de patienter ...</option>");
    $("#coloris_product").html("<option value='' data-reassort='' data-condition=''>Séléctionnez un coloris</option>");

    if (str != '') {
        var link = getBaseURL();
        var strURL = link + "admin/product/get_ligne_by_marque/" + str;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        //alert(result);
                        $("#ligne_product").html("<option value='' data-reassort='' data-condition=''>Séléctionnez une ligne</option>");
                        $("#ligne_product").append(result);
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }
    } else {
        $("#ligne_product").html("<option value='' data-reassort='' data-condition=''>Séléctionnez une ligne</option>");
    }
}

function get_coloris(str) {
    $("#coloris_product").html("<option value='' data-reassort='' data-condition=''>Merci de patienter ...</option>");

    if (str != '') {
        /**********Coloris************/
        var strURL2 = link + "admin/product/get_coloris_by_ligne/" + str;
        var req2 = getXMLHTTP();
        if (req2) {

            req2.onreadystatechange = function () {
                if (req2.readyState == 4) {
                    // only if "OK"
                    if (req2.status == 200) {
                        var result2 = req2.responseText;
                        //alert(result);
                        $("#coloris_product").html("<option value='' data-reassort='' data-condition=''>Séléctionnez un coloris</option>");
                        $("#coloris_product").append(result2);
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req2.statusText);
                    }
                }
            }
            req2.open("POST", strURL2, true);
            req2.send(null);
        }
    } else {
        $("#coloris_product").html("<option value='' data-reassort='' data-condition=''>Séléctionnez un coloris</option>");
    }
}

//*********************************************
//     Change Password method div show hide
//*********************************************
function setVisibility() {
    var a = $('#change_password').val();
    var result = a.toString()
    if (result == 'Change Password') {
        $('#change_password').val('Hide Password'),
            $('#password_flag').val('ok'),
            $('#password_div').show();
    } else {
        $('#change_password').val('Change Password'),
            $('#password_flag').val('no'),
            $('#password_div').hide();
    }
}

//*********************************************
//     Purchase payment method show hide
//*********************************************

$(function () {
    $('#payment_type').change(function () {

        $("#pri" + id).prop('disabled', false);

        if (val == 'cheque') {
            $('#payment').show();
        }
        else if (val == 'card') {
            $('#payment').show();
        } else {
            $('#payment').hide();
        }
    });
});

//*********************************************
//     Order payment method show hide
//*********************************************

$(function () {
    $('#order_payment_type').change(function () {
        var val = $("#order_payment_type").val();

        if (val == 'cheque') {
            $('#payment').show();
        }
        else if (val == 'card') {
            $('#payment').show();
        } else {
            $('#payment').hide();
        }
    });
});


//*********************************************
//     Order Confirmation Method
//*********************************************

$(function () {
    $('#order_confirmation').change(function () {
        var val = $("#order_confirmation").val();
        if (val == '2') {
            $('#payment_method_block').show();
        } else {
            $('#payment_method_block').hide();
        }
    });

    $("#btn_ajout_produit").on("click", function (e) {
        e.preventDefault();
        var product_id = $("#product_id").val();
        var product_quantity = $("#product_quantity").val();
        var order_id = $("#order_id").val();
        if (product_id && product_quantity) {
            var link = getBaseURL();
            //alert(link);
            var strURL = link + "admin/order/add_product/" + order_id + "/" + product_id + "/" + product_quantity;
            var req = getXMLHTTP();
            if (req) {
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        // only if "OK"
                        if (req.status == 200) {
                            var result = req.responseText;
                            var msg = result.trim();
                            if (msg) {
                                $(".add_produit_order").html(msg);
                            }
                        } else {
                            alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                        }
                    }
                }
                req.open("POST", strURL, true);
                req.send(null);
            }
        }
    });

    $("#partielle_filter_commande").submit(function () {
		var order_date_recuperation = $("#order_date_recuperation_for_statut").val();
		if(order_date_recuperation)
			$("#date_recup_partielle").val(order_date_recuperation);
		
		var order_commentaire = $("#order_commentaire_for_statut").val();
		if(order_commentaire)
			$("#remarque_partielle").val(order_commentaire); 
		
		//return false;
	});
	
    $("#order_confirmation").on("change", function () {
        var order_status = $(this).val();
        var order_status_actuel = $("#order_status_actuel").val();

        if (order_status != order_status_actuel) {
            $("#modifier-status").show();
            $("#change_status_form").submit(function (e) {
                return true;
            });
        } else {
            $("#modifier-status").hide();
            $("#change_status_form").submit(function (e) {
                return false;
            });
        }

        $("#order_date_traitement").hide();
        $("#order_date_recuperation").hide();
        $("#order_commentaire").hide();
        if (order_status == 2) {
            $("#order_date_traitement").show();
            $("#order_commentaire").show();
        }
        if (order_status == 3) {
            $("#order_commentaire").show();
        }
        if (order_status == 4) {
            $("#order_date_recuperation").show();
            $("#order_commentaire").show();
        }
		
		if(order_status == 5) {
			$("#change_statut_submit").hide();
			$("#change_status_form").submit();
			//alert("qsd");
		}



    });
});


//*********************************************
//     Email Campaign
//*********************************************

$("#sendEmail form").submit(function () {
    var campaign_id = $(this).find('input[name=campaign_id]').val();
    var req = getXMLHTTP();
    if (req) {
        var link = getBaseURL();
        $.post(link + "admin/campaign/send_email", {
                campaign_id: campaign_id,
                ajax: '1'
            },
            function (data) {

                if (data == 'true') {
                    $.get(link + "admin/purchase/show_cart", function (cart) {
                        $("#cart_content").html(cart);
                        $("#newform")[0].reset();

                    });

                }

            });
        return false;
    }
});
/************ Action sur Button ***********/
$(function () {
    $(document).on("click", ".save_quantite", function (e) {
        e.preventDefault();
        var product_id = $(this).data("id");
        var product_quantity = $("#qty_" + product_id).val();
        var order_id = $("#order_id").val();

        if (product_id && product_quantity) {
            var link = getBaseURL();
            //alert(link);
            var strURL = link + "admin/order/add_product/" + order_id + "/" + product_id + "/" + product_quantity;
            var req = getXMLHTTP();
            if (req) {
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        // only if "OK"
                        if (req.status == 200) {
                            var result = req.responseText;
                            var msg = result.trim();
                            if (msg) {
                                alert('Votre quantité a été modifié avec succès !')
                                $(".add_produit_order").html(msg);
                            }
                        } else {
                            alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                        }
                    }
                }
                req.open("POST", strURL, true);
                req.send(null);
            }
        }
    });
});