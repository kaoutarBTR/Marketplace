//==========================================================
//  Add Supplier Form Validation
//==========================================================
$().ready(function () {
    $("#addSupplierForm").validate({
        rules: {
            company_name: {
                required: true
            },
            supplier_name: {
                required: true
            },

            email: {
                required: true,
                email: true
            },

            phone: {
                required: true,
                number: true

            }
        },

        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        }
    })
});


//==========================================================
//  Add Product Form Validation
//==========================================================
jQuery.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "Space are not allowed");
$().ready(function () {
    $("#addProductForm").validate({
        ignore: [],
        rules: {

            product_name: {
                required: true
            },
            marque_id: {
                required: true
            },
            ligne_id: {
                required: true
            },
            coloris_id: {
                required: true
            },
            product_quantity: {
                required: true,
                number: true

            },
        },

        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }

    })
});

$("[id=submit]").submit(function (e) {
    if (e.preventDefault()) {
    }

});


//==========================================================
//  Damage Product Form Validation
//==========================================================
$().ready(function () {
    $("#addColoris").validate({
        rules: {
            marque_id: {
                required: true
            },
            ligne_id: {
                required: true
            },
            coloris_name: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
    $("#addLigne").validate({
        rules: {
            marque_id: {
                required: true
            },
            ligne_name: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    $("#addMarque").validate({
        rules: {
            marque_name: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

});

//ORDER
$(document).ready(function () {

    if (typeof flag != 'undefined') {
        if (flag == "add_products")
            activaTab("search-product");
        else if(flag == "add")
            activaTab("panier-tab");
    }

    var $tabs = $('.tabbable li');


    

    /*$("#save_order_client").validate({
        rules: {
            customer_id: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
             
            
            var link = getBaseURL();
            $.ajax({
                "url": link + "admin/order/save_client_session",
                "data": $("#save_order_client").serialize(),
                "success": function (msg) {
                    //$(".nav-tabs a[data-toggle=tab]").removeClass("disabled");
                    //activaTab("search-product");
                    window.location.href = link + "admin/order/new_order/add_products";
                },
                "type": "POST",
                "cache": false,
                "error": function () {
                    alert("Error detected when sending table data to server");
                }
            });

        }
    });*/

    

 
    $("#delete_order_client").validate({
        rules: {
            raison_suppression: {
                required: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        } 
    });
 

/**
 * Author : Mohamed AHERDANE
 * Description : Remplir la liste des clients selon le revendeur choisi
 * Params : id_sell (id revendeur)
 * Return : Json
 */
    $("#reseller_id").change(function(){
        var type_rep=$( "#reseller_id option:selected" ).attr("class");
        $( "#type_representant" ).val(type_rep);
        if($("#reseller_id").length>0){
            $(".choose_price option").prop("selected", false);        
            $(".choose_price option.opt_"+type_rep).prop("selected", true);  
        }
        var idresell = $(this).find('option:selected').val();        
        var link = getBaseURL();
        $.ajax({
            "type": "POST",
            "data": {
                id_resell : idresell
            },
            "url": link+"admin/order/get_reseller_customer",
            "success": function(data){
                $('#customer_id').html(""); 
                $('#customer_id').append("<option value=''>Choisir un client</option>");    
                $.each(JSON.parse(data), function(idx, obj) {
                    $('#customer_id').append("<option value='"+obj.customer_id+"'>"+obj.societe+"</option>");
                });
                $('#customer_id').val('').trigger("chosen:updated");                
                $('#customer_id').chosen({ no_results_text: "Oops, Aucun client trouvé !" });
            }
        });   
    });
    
 
    var cache = {}; 
    $( document ).on( "focus", ".product_ref_search", function() { 
        
        $('.product_ref_search').blur(function(){
         var keyEvent = $.Event("keydown");
         keyEvent.keyCode = $.ui.keyCode.ENTER;
         $(this).trigger(keyEvent);
        });      
        $('.product_ref_search').autocomplete({
                autoFocus: true,
                source : function(request, reponse) { 
                var term = request.term;
                if ( term in cache ) {
                  reponse( cache[ term ] );
                  return;
                }
                var link = getBaseURL();

                $.ajax({
                    url : link + 'ajax_get_product_by_ref.php',
                    type : 'GET',
                    dataType : 'json',
                    data : {
                        product_ref : request.term, 
                        maxRows : 5,
                        //type_representant :type_rep ,
                    },
                    success : function(data){
                    cache[ term ] = $.map(data, function(objet) { 

                                        return { label : objet.product_code + " - " + objet.product_name, value: objet.product_code, obj : objet};
                                    });
                       reponse(
                            $.map(data, function(objet) { 

                                return { label : objet.product_code + " - " + objet.product_name, value: objet.product_code, obj : objet};
                            }
                        ));

                    }
                });
            },

            select : function(a, b){ 
                if (b.item.obj) {
                    
                    var elementId = $(this).attr("id");

                    if(elementId) {
                        $("#" + elementId).next().next().val(b.item.obj.product_code);
                    }


                    //$(this).next(".product_id_search_hidden").val(b.item.obj.product_id); 
                    var currentIndex = $(this).next(".product_ref_search_hidden").val();
                    
                    // set marque

                    if(b.item.obj.marque_id) {
                        $("#marque_" + currentIndex).html("");
                        for (var k in b.item.obj.marque_id){
                            if (b.item.obj.marque_id.hasOwnProperty(k)) {
                                var optionHtml = "<option value=" + k + ">" + b.item.obj.marque_id[k] + "</option>";

                                $("#marque_" + currentIndex).append(optionHtml);
                            }
                        }
                    }


                    // set ligne
                    if(b.item.obj.ligne_id) {
                        $("#ligne_" + currentIndex).html("");
                        for (var k in b.item.obj.ligne_id){
                            if (b.item.obj.ligne_id.hasOwnProperty(k)) {
                                var optionHtml = "<option value=" + k + ">" + b.item.obj.ligne_id[k] + "</option>";
                                $("#ligne_" + currentIndex).append(optionHtml);
                            }
                        }
                    }

                    // set coloris
                    if(b.item.obj.coloris_id) {
                        validate_form();
                        $("#coloris_" + currentIndex).html("");
                        for (var k in b.item.obj.coloris_id){
                            if (b.item.obj.coloris_id.hasOwnProperty(k)) {
                                var optionHtml = "<option value=" + k + ">" + b.item.obj.coloris_id[k] + "</option>";
                                $("#coloris_" + currentIndex).append(optionHtml);
                            }
                        }
                    }

                    // set prise
                    var type_rep=$( "#reseller_id option:selected" ).attr("class");
                    if($(".prix_admin_" + currentIndex).is("select")){
                        $(".prix_admin_" + currentIndex).html("");
                            var selelctedval="";
                            if(type_rep=="france")   
                                selelctedval="selected='selected'";
                            var optionHtml = "<option class='opt_france' "+selelctedval+" value=" + b.item.obj.prix_france + ">" + b.item.obj.prix_france + "</option>";

                            if(type_rep=="borso")   
                                selelctedval="selected='selected'";
                            else
                                selelctedval="";
                            optionHtml += "<option class='opt_borso' "+selelctedval+" value=" + b.item.obj.prix_borso + ">" + b.item.obj.prix_borso + "</option>";

                            if(type_rep=="moyenne_orient")   
                                selelctedval="selected='selected'";
                            else
                                selelctedval="";                            
                            optionHtml += "<option class='opt_moyenne_orient' "+selelctedval+" value=" + b.item.obj.prix_moyenne_orient + ">" + b.item.obj.prix_moyenne_orient + "</option>";

                        $(".prix_admin_" + currentIndex).append(optionHtml);
                        //$(".prix_admin_" + currentIndex).attr("name","prix_admin_"+b.item.obj.product_id);

                    }else{
                        var type_rep=$( "#type_representant" ).val();
                        var selelctedval="";
                        if(type_rep=="france")   
                            selelctedval=b.item.obj.prix_france;                         

                        if(type_rep=="borso")   
                            selelctedval=b.item.obj.prix_borso;

                        if(type_rep=="moyenne_orient")   
                            selelctedval=b.item.obj.prix_moyenne_orient;                           
                        
                        $(".prix_admin_" + currentIndex).val(selelctedval);
                        //$(".prix_admin_" + currentIndex).attr("name","prix_admin_"+b.item.obj.product_id);
                        $(".prixsp_" + currentIndex).html(selelctedval);
                    }
                }
            }
        });





        $('.product_ref_search').bind('paste', function(e) {
            var thisSelector = $(this);
            setTimeout(function(){
                $(thisSelector).autocomplete('search', $(thisSelector).val()); 
            }, 0);
        });



    });




    
    $("#btn_envoie").on("click", function (e) {
        if($("#radioDiv").length > 0) {
            var port_du = $("#radioDiv input[type='radio']:checked").val();
            
            if(port_du == undefined) {
                alert("Merci de sélectionner les frais de port !");
                return false;
            }
        }
 
        e.preventDefault();
        $("#typecom").val("");
        $("#add_order").submit();
        
    });
    $(".nav-tabs a[data-toggle=tab]").on("click", function (e) {
        if ($(this).hasClass("disabled")) {
            alert("Merci de remplir les informations de la commande puis cliquer sur 'Suivant'");
            e.preventDefault();
            return false;
        }
    });
});

function activaTab(tab) {
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
};


//==========================================================
//  Add Customer Form Validation
//==========================================================
$(document).ready(function () {

    jQuery.validator.addMethod("phoneVal", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, ""); 
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
    }, "Numéro de téléphone invalide");

    jQuery.validator.addMethod("codePos", function(codePos, element) {
        codePos = codePos.replace(/\s+/g, ""); 
        return this.optional(element) || codePos.match(/^[0-9]{5}$/);
    }, "Code postal invalide");


    $("#addCustomerForm").validate({
        ignore: [],
        rules: {
            societe: {
                required: true
            },
            codepostal: {
                codePos: true
            },
            phone: {
                phoneVal: true
            },
            phonepo: {
                phoneVal: true
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        }
    });

    $("#change_status_form").validate({
        ignore: [],
        rules: {
            order_date_traitement: {
                required: {
                    depends: function (element) {
                        return !!($("#order_confirmation").val() == '2');
                    }
                }
            },
            order_date_recuperation: {
                required: {
                    depends: function (element) {
                        return !!($("#order_confirmation").val() == '4');
                    }
                }
            },
            order_commentaire: {
                required: {
                    depends: function (element) {
                        return !!($("#order_confirmation").val() == '3');
                    }
                }

            }
        },

        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        }
    });
});

//==========================================================
//  Image Validation
//==========================================================

function imageForm(thisform) {
    with (thisform) {

        if (validateFileExtension(product_image, "valid_msg", "Seul les format de type image sont autorisés",
                new Array("jpg", "jpeg", "gif", "png")) == false) {
            return false;
        }
        if (validateFileSize(product_image, 1048576, "valid_msg", "La taille de l'image utilisée excède 1MB !") == false) {
            return false;
        }
    }

}

function companyLogo(thisform) {

    with (thisform) {

        if (validateFileExtension(logo, "valid_msg", "Seul les format de type image sont autorisés",
                new Array("jpg", "jpeg", "gif", "png")) == false) {
            return false;
        }
        if (validateFileSize(logo, 1048576, "valid_msg", "La taille de l'image utilisée excède 1MB !") == false) {
            return false;
        }
    }
}

function employeeImage(thisform) {
    with (thisform) {

        if (validateFileExtension(employee_image, "valid_msg", "Seul les format de type image sont autorisés",
                new Array("jpg", "jpeg", "gif", "png")) == false) {
            return false;
        }
        if (validateFileSize(employee_image, 1048576, "valid_msg", "La taille de l'image utilisée excède 1MB !") == false) {
            return false;
        }
    }
}

function validateFileExtension(component, msg_id, msg, extns) {

    var flag = 0;
    with (component) {
        var ext = value.substring(value.lastIndexOf('.') + 1);
        if (ext) {
            for (i = 0; i < extns.length; i++) {
                if (ext == extns[i]) {
                    flag = 0;
                    break;
                }
                else {
                    flag = 1;
                }
            }
            if (flag != 0) {
                document.getElementById(msg_id).innerHTML = msg;
                component.value = "";
                component.style.backgroundColor = "#eab1b1";
                component.style.border = "thin solid #000000";
                component.focus();
                return false;
            }
            else {
                return true;
            }
        }
    }
}

function validateFileSize(component, maxSize, msg_id, msg) {
    if (navigator.appName == "Microsoft Internet Explorer") {
        if (component.value) {
            var oas = new ActiveXObject("Scripting.FileSystemObject");
            var e = oas.getFile(component.value);
            var size = e.size;
        }
    }
    else {
        if (component.files[0] != undefined) {
            size = component.files[0].size;
        }
    }
    if (size != undefined && size > maxSize) {
        document.getElementById(msg_id).innerHTML = msg;
        component.value = "";
        component.style.backgroundColor = "#eab1b1";
        component.style.border = "thin solid #000000";
        component.focus();
        return false;
    }
    else {
        return true;
    }
}

$(document).ready(function () {
    $(".en_stock").on("click", function () {
        var val = $(".en_stock:checked").val();
        if (val == 0) {
            $("#date_disponibilite").show();
            $("#product_quantity").val(0);            
            $("#product_quantity").prop("readonly", true);            
        } else {
            $("#product_quantity").val("");            
            $("#date_disponibilite").hide();
            $("#product_quantity").prop("readonly", false);            
        }
    });
    $(".ligne_reassort").on("click", function () {
        var val = $(".ligne_reassort:checked").val();
        if (val == 1) {
            $("#ligne_condition").show();
        } else {
            $("#ligne_condition").hide();
        }
    });
    $(".marque_reassort").on("click", function () {
        var val = $(".marque_reassort:checked").val();
        if (val == 1) {
            $("#marque_condition").show();
        } else {
            $("#marque_condition").hide();
        }
    });

    $(".coloris_reassort").on("click", function () {
        var val = $(".coloris_reassort:checked").val();
        if (val == 1) {
            $("#coloris_condition").show();
        } else {
            $("#coloris_condition").hide();
        }
    });

    $(".product_reassort").on("click", function () {
        var val = $(".product_reassort:checked").val();
        if (val == 1) {
            var value =$("#ligne_product_condition_2").val();
            $("#ligne_product_condition").val(value);
            $("#product_condition").show();
             $("#ligne_product_condition").css('dislay',' block !important');
        } else {
            $("#product_condition").hide();
             $("#ligne_product_condition").css('dislay','none');
        }
    });

    $('#marque_ligne').change(function () {
        var selected = $(this).find('option:selected');
        var reassort = selected.data('reassort');
        var condition = selected.data('condition');
        if (reassort) {
            $("input[name=ligne_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=ligne_reassort][value='0']").prop('disabled', true);
            $("input[name=ligne_condition]").val(condition);
            $("input[name=ligne_condition]").prop('readonly', true);
            $("#ligne_condition").show();
        } else {
            $("input[name=ligne_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=ligne_condition]").val("");
            $("input[name=ligne_reassort][value='0']").prop('disabled', false);
            $("input[name=ligne_condition]").prop('readonly', false);
            $("#ligne_condition").hide();
        }
    });

    $('#marque_coloris').change(function () {
        var selected = $(this).find('option:selected');
        var reassort = selected.data('reassort');
        var condition = selected.data('condition');
        var val = selected.val();
        get_ligne(val);
        if (reassort) {
            $("input[name=coloris_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=coloris_reassort][value='0']").prop('disabled', true);
            $("input[name=coloris_condition]").val(condition);
            $("input[name=coloris_condition]").prop('readonly', true);
            $("#coloris_condition").show();
        } else {
            $("input[name=coloris_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=coloris_condition]").val("");
            $("input[name=coloris_reassort][value='0']").prop('disabled', false);
            $("input[name=coloris_condition]").prop('readonly', false);
            $("#coloris_condition").hide();
        }
    });

    $('#marque_product').change(function () {
        var selected = $(this).find('option:selected');
        var reassort = selected.data('reassort');
        var condition = selected.data('condition');
        var val = selected.val();
        get_ligne(val);
        if (reassort) {
            $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=product_reassort][value='0']").prop('disabled', true);
            $("input[name=product_condition]").val(condition);
            $("input[name=product_condition]").prop('readonly', true);
            $("#product_condition").show();
        } else {
            $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
            $("input[name=product_condition]").val("");
            $("input[name=product_reassort][value='0']").prop('disabled', false);
            $("input[name=product_condition]").prop('readonly', false);
            $("#product_condition").hide();
        }
    });


    $('#ligne_product').change(function () {
        var selected = $(this).find('option:selected');
        var link = getBaseURL();
        $.ajax({
           url : link + "admin/product/get_ligne_promo/" + selected.val(),
           type : 'GET',
           dataType : 'html', // On désire recevoir du HTML
           success : function(data){ // code_html contient le HTML renvoyé
              if(data=='') {
                                $("#product_condition").css('display','');
              }else{
                $("#ligne_product_condition").val(data);
                    $("#ligne_product_condition_2").val(data);
              }
           }
        });

        var selected_marque = $("#marque_product").find('option:selected');
        var reassor_marque = selected_marque.data('reassort');

        var val = selected.val();
        get_coloris(val);
        if (!reassor_marque) {
            var selected = $(this).find('option:selected');
            var reassort = selected.data('reassort');
            var condition = selected.data('condition');
            var val = selected.val();
            if (reassort) {
                $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=product_reassort][value='0']").prop('disabled', true);
                $("input[name=product_condition]").val(condition);
                $("input[name=product_condition]").prop('readonly', true);
                $("#product_condition").show();
            } else {
                $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=product_condition]").val("");
                $("input[name=product_reassort][value='0']").prop('disabled', false);
                $("input[name=product_condition]").prop('readonly', false);
                $("#product_condition").hide();
            }
            // Page Coloris
            if (reassort) {
                $("input[name=coloris_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=coloris_reassort][value='0']").prop('disabled', true);
                $("input[name=coloris_condition]").val(condition);
                $("input[name=coloris_condition]").prop('readonly', true);
                $("#coloris_condition").show();
            } else {
                $("input[name=coloris_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=coloris_condition]").val("");
                $("input[name=coloris_reassort][value='0']").prop('disabled', false);
                $("input[name=coloris_condition]").prop('readonly', false);
                $("#coloris_condition").hide();
            }
        }
    });

    $('#coloris_product').change(function () {
        var selected_marque = $("#marque_product").find('option:selected');
        var reassor_marque = selected_marque.data('reassort');
        if (!reassor_marque) {
            var selected = $(this).find('option:selected');
            var reassort = selected.data('reassort');
            var condition = selected.data('condition');
            var val = selected.val();
            if (reassort) {
                $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=product_reassort][value='0']").prop('disabled', true);
                $("input[name=product_condition]").val(condition);
                $("input[name=product_condition]").prop('readonly', true);
                $("#product_condition").show();
            } else {
                $("input[name=product_reassort][value=" + reassort + "]").prop('checked', true);
                $("input[name=product_condition]").val("");
                $("input[name=product_reassort][value='0']").prop('disabled', false);
                $("input[name=product_condition]").prop('readonly', false);
                $("#product_condition").hide();
            }
        }
    });
    
    $('.product_ref_search').keyup(function() {
        var pid = $('#table_page').val();

        var k= 0;
        if(pid !=0){
            k = (pid*20) ;
        }

        var valid = 1;
        for (var i = k ; i <= (parseInt(pid)+1)*20 ; i++) {
                var input=$('#product_ref_'+i).val();
                if(input==''){
                    valid = 0;
                }
            }

            if(valid==0){
               $("#btn_div_suivant").css('display', 'none'); 
            }
    });

    $('#btn_order_reset').click(function() {
        window.location.href = '/admin/order/new_order_evo';
    });

});

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
    
function add_fields(){

        var rowCount = $('#products_infos tr').length + 1;
        var rowHtml = '';

        var page_id = ((rowCount-1)-((rowCount-1)%40))/40;

                var rowCountt = $('#products_infos page'+page_id).length;

        if(rowCountt < 40) {
            for (var i = 1; i <=30; i++) {
                var rowid= (page_id*40)+i;

                var rowid= rowCount+i;
            
            rowHtml += '<tr class="page'+page_id+'">';

                rowHtml += '<td class="refCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Ref#</label>';
                        rowHtml += '<input type="text" class="product_ref_search form-control" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '" value="" placeholder="Référence..." />';
                        rowHtml += '<input class="product_ref_search_hidden" type="hidden" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '_hidden" value="' + rowid + '" />';
                        rowHtml += '<input class="product_id_search_hidden" type="hidden" id="product_id_' + rowid + '" name="product_id_' + rowid + '_hidden" value="" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol1">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Coloris</label>';
                        rowHtml += '<select class="form-control choose_coloris" id="coloris_' + rowid + '" name="coloris_' + rowid + '">';
                            rowHtml += '<option value="">Coloris</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Quantité</label> ';
                        rowHtml += '<input class="form-control" type="number" id="qt_' + rowid + '" name="qt_' + rowid + '" value="1" min="1" step="1" onkeypress="return isNumberKey(event);" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';

                rowHtml += '<td class="attrCol">'; 
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Marque</label>';
                        rowHtml += '<select class="form-control choose_marque" id="marque_' + rowid + '" name="marque_' + rowid + '">';
                            rowHtml += '<option value="">Marque</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                    
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Ligne</label>';
                        rowHtml += '<select class="form-control choose_ligne" id="ligne_' + rowid + '" name="ligne_' + rowid + '">';
                            rowHtml += '<option value="">Ligne</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';

                    if($( "#reseller_id" ).length>0){
                        rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<select class="form-control choose_price prix_admin_' + rowid + '" id="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '">';
                                rowHtml += '<option value="">Prix</option>">';
                            rowHtml += '</select>'; 
                        rowHtml += '</div>';
                    }else{
                          rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<span class="form-control choose_price prixsp_' + rowid + '"></span>';
                            rowHtml += '<input type="hidden" class="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '" />';
                        rowHtml += '</div>';                  
                    }
                rowHtml += '</td>';
            rowHtml += '</tr>';
            }

            $("#products_infos").append(rowHtml);
        } else {
            $("#btn_div_suivant").css('display', '');
        } 

        return false;
    };

  function page_display(pid,status){


    $('#status').val(status);

    // form validation

    var custom_id = $("#customer_id");
    if(custom_id.val()==''){
        $('#cust_id_erro').css('display','');
                window.scrollTo(0, 0);
        return false;
    }else{
                $('#cust_id_erro').css('display','none');
    }

    var custom_id = $("#type_commande");
    if(custom_id.val()==''){
        $('#type_co_erro').css('display','');
        window.scrollTo(0, 0);
        return false;
    }else{
                $('#type_co_erro').css('display','none');
    }

    var custom_id = $("#mode_paiement");
    if(custom_id.val()==''){
        $('#mode_pai_erro').css('display','');
                window.scrollTo(0, 0);
        return false;
    }else{
                $('#mode_pai_erro').css('display','none');
    }

    var custom_id = $("#mode_livraison");
    if(custom_id.val()==''){
        $('#mode_liv_erro').css('display','');
                window.scrollTo(0, 0);
        return false;
    }else{
                $('#mode_liv_erro').css('display','none');
    }

    // form validation


    //verifier si les champs des produits ne sont pas vides
    if (!status) {
        var k = 0;
        if((pid-1) !=0){
            k = parseInt(pid)  ;
        }

        for (var i = (k-1)*20 ; i <= k*20 ; i++) {
            var input=$('#product_ref_'+i);
            if(input.val()==''){
                input.css('backgroundColor', 'red');
                input.scroll();
                return false;
            }else{
                  input.css('backgroundColor', '');  
            }
        }
    }


    //verifier si les champs des produits ne sont pas vides

        var frmf = $('#save_order_client_evo');

        var frm=frmf;

        var pages=$('.hide_tables').length;
        var pg=$('#table_page').val();

            $.ajax({
                url: frm.attr('action'), 
                type: frm.attr('method'), 
                data: frm.serialize(), 
                success: function(data) { 
                    //console.log(data.split('_')[0]);
                    //return false;
                    $('.product_ref_search').css('backgroundColor', '');
                    if(data.split('_')[0]=='error'){
                        if( $('.alert_duplicate').length==0 ){
                                $('.top-row').prepend('<div class="alert alert-danger alert_duplicate" role="alert" >Vous ne devez pas sélectionner deux produits avec les mêmes informations (Référence, Ligne, Coloris, Marque) !</div>');
                            }


                        $('html, body').animate({
                            scrollTop: $(".top-row").offset().top
                        }, 1000);

                        $('#product_ref_'+data.split('_')[1]).css('backgroundColor', 'red');
                        $('#product_ref_'+data.split('_')[1]).scroll();


                        return false;
                    }else{
                                $( ".alert_duplicate" ).remove();
                            }
                    $('#order_id').val(data);
                    if (status) {
                            window.location.href = '/admin/order/new_order_evo/add/'+data;
                    }
                }
            });

        var rowHtml='';

            setTimeout(function(){  

        var ii= parseInt(pid+1);

        $('#btn_div_suivant').empty().append('<button  id="btn_page_suivant" class="btn bg-navy btn-block " onclick="page_display('+ii+',0)" > Ajouter plus de produits </button>');
        $('#btn_div_end').empty().append('<button  id="btn_end" class="btn bg-navy btn-block " onclick="page_display('+ii+',1)" > Terminer </button>');

        var page_id= pid;
        var rowCountt = $('.table_page_'+page_id+' tr').length;


                        var ppg=pid-1;

    if( $('.alert_duplicate').length==0 ){

                            $('.hide_tables').css('display', 'none'); 

    if(rowCountt>0 ){

                window.history.pushState({}, document.title, page_id+1);


                $(".table_page_"+page_id).css('display', '');
                if($('table tr').length==((pid+1)*20)){
                    $("#btn_div_suivant").css('display', 'none');
                }else{
                    $("#btn_div_suivant").css('display', '');
                }
            }else{

                $("#btn_div_suivant").css('display', 'none');

                    rowHtml += ' <table id="products_infos" class="table_page_'+page_id+' hide_tables">'
                    for (var i = 0; i <=19; i++) {
                        var rowid= (pid*20)+i;
                        
                        rowHtml += '<tr class="page'+page_id+'">';

                            rowHtml += '<td class="refCol">';
                                rowHtml += '<div class="form-group fct">';
                                rowHtml += '<input type="text" name="product_order_id_'+rowid+'" value="'+$('#order_id').val()+rowid+'" style="display: none;">';
                                    rowHtml += '<label>Ref#</label>';
                                    rowHtml += '<input type="text"   class="product_ref_search form-control " id="product_ref_' + rowid + '" name="product_ref_' + rowid + '" value="" placeholder="Référence..." />';
                                    rowHtml += '<input class="product_ref_search_hidden" type="hidden" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '_hidden" value="' + rowid + '" />';
                                    rowHtml += '<input class="product_id_search_hidden" type="hidden" id="product_id_' + rowid + '" name="product_id_' + rowid + '_hidden" value="" />';
                                rowHtml += '</div>';
                            rowHtml += '</td>';
                            
                            rowHtml += '<td class="qtCol1" style="width: 18%;">';
                                rowHtml += '<div class="form-group fct">';
                                    rowHtml += '<label>Coloris</label>';
                                    rowHtml += '<select class="form-control choose_coloris" id="coloris_' + rowid + '" name="coloris_' + rowid + '">';
                                        rowHtml += '<option value="">Coloris</option>">';
                                    rowHtml += '</select>'; 
                                rowHtml += '</div>';
                            rowHtml += '</td>';
                            
                            rowHtml += '<td class="qtCol">';
                                rowHtml += '<div class="form-group fct">';
                                    rowHtml += '<label>Quantité</label> ';
                                    rowHtml += '<input class="form-control" type="number" id="qt_' + rowid + '" name="qt_' + rowid + '" value="1" min="1" step="1" onkeypress="return isNumberKey(event);" />';
                                rowHtml += '</div>';
                            rowHtml += '</td>';

                            rowHtml += '<td class="attrCol">'; 
                                rowHtml += '<div class="form-group fc">';
                                    rowHtml += '<label>Marque</label>';
                                    rowHtml += '<select class="form-control choose_marque" id="marque_' + rowid + '" name="marque_' + rowid + '">';
                                        rowHtml += '<option value="">Marque</option>">';
                                    rowHtml += '</select>'; 
                                rowHtml += '</div>';
                                
                                rowHtml += '<div class="form-group fc">';
                                    rowHtml += '<label>Ligne</label>';
                                    rowHtml += '<select class="form-control choose_ligne" id="ligne_' + rowid + '" name="ligne_' + rowid + '">';
                                        rowHtml += '<option value="">Ligne</option>">';
                                    rowHtml += '</select>'; 
                                rowHtml += '</div>';

                                if($( "#reseller_id" ).length>0){
                                    rowHtml += '<div class="form-group fc">';
                                        rowHtml += '<label>Prix</label>';
                                        rowHtml += '<select class="form-control choose_price prix_admin_' + rowid + '" id="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '">';
                                            rowHtml += '<option value="">Prix</option>">';
                                        rowHtml += '</select>'; 
                                    rowHtml += '</div>';
                                }else{
                                      rowHtml += '<div class="form-group fc">';
                                        rowHtml += '<label>Prix</label>';
                                        rowHtml += '<span class="form-control choose_price prixsp_' + rowid + '"></span>';
                                        rowHtml += '<input type="hidden" class="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '" />';
                                    rowHtml += '</div>';                  
                                }
                            rowHtml += '</td>';
                        rowHtml += '</tr>';
                        }
                        $('.hide_add_more').css('display','none');
                        rowHtml += '</table>';
                        $("#product_information").append(rowHtml);
            }
        $('#table_page').val(page_id);
        }   


            return false;

            }, 2000);

  }

  

    $("#btn_suivantt").click(function(e){
        e.preventDefault();
        var value = $("#customer_id").value;
        alert(value);
        var frmf = $('#save_order_client_evo');

        var frm=frmf;

        var pages=$('.hide_tables').length;
        var pg=$('#table_page').val();


            $.ajax({
                url: frm.attr('action'), // Le nom du fichier indiqué dans le formulaire
                type: frm.attr('method'), // La méthode indiquée dans le formulaire (get ou post)
                data: frm.serialize(), // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
                success: function(data) { // Je récupère la réponse du fichier PHP
                    $('#order_id').val(data);
                    window.location.href = '/admin/order/new_order_evo/add/'+data;
                }
            });
    });

    $("#save_order_client_evo").submit(function( event ) {
            event.preventDefault();
            var link = getBaseURL();
            var frm = $('#save_order_client_evo');

            $.ajax({
                url: frm.attr('action'), 
                type: frm.attr('method'), 
                data: frm.serialize(), 
                success: function(data) { 
                        if(data.split('_')[0]=='error'){
                            return false;
                        }
                        if($('#status').val()==1){
                            window.location.href = link + 'admin/order/new_order_evo/add/'+data;
                        }
                },
                "type": "POST",
                "cache": false,
                "error": function () {
                    alert("Error detected when sending table data to server");
                }
            });

    });

  /*$("#add_more_product").click(function(){
        var pg=$('#table_page').val();
        var char='.table_page_'+pg+' tr';
                var rowCount = $(char).length;

        var rowCount = $(char).length;
        var rowHtml = '';

        var page_id = ((rowCount-1)-((rowCount-1)%20))/20;

        if(rowCount < 20) {
            for (var i = 11; i <=20; i++) {
                var rowid= (pg*20)+i;
            
            rowHtml += '<tr class="page'+page_id+'">';

                rowHtml += '<td class="refCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Ref#</label>';
                        rowHtml += '<input type="text"  class="product_ref_search form-control" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '" value="" placeholder="Référence..." />';
                        rowHtml += '<input class="product_ref_search_hidden" type="hidden" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '_hidden" value="' + rowid + '" />';
                        rowHtml += '<input class="product_id_search_hidden" type="hidden" id="product_id_' + rowid + '" name="product_id_' + rowid + '_hidden" value="" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol1">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Coloris</label>';
                        rowHtml += '<select class="form-control choose_coloris" id="coloris_' + rowid + '" name="coloris_' + rowid + '">';
                            rowHtml += '<option value="">Coloris</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Quantité</label> ';
                        rowHtml += '<input class="form-control" type="number" id="qt_' + rowid + '" name="qt_' + rowid + '" value="1" min="1" step="1" onkeypress="return isNumberKey(event);" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';

                rowHtml += '<td class="attrCol">'; 
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Marque</label>';
                        rowHtml += '<select class="form-control choose_marque" id="marque_' + rowid + '" name="marque_' + rowid + '">';
                            rowHtml += '<option value="">Marque</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                    
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Ligne</label>';
                        rowHtml += '<select class="form-control choose_ligne" id="ligne_' + rowid + '" name="ligne_' + rowid + '">';
                            rowHtml += '<option value="">Ligne</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';

                    if($( "#reseller_id" ).length>0){
                        rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<select class="form-control choose_price prix_admin_' + rowid + '" id="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '">';
                                rowHtml += '<option value="">Prix</option>">';
                            rowHtml += '</select>'; 
                        rowHtml += '</div>';
                    }else{
                          rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<span class="form-control choose_price prixsp_' + rowid + '"></span>';
                            rowHtml += '<input type="hidden" class="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '" />';
                        rowHtml += '</div>';                  
                    }
                rowHtml += '</td>';
            rowHtml += '</tr>';
            }

            $('.table_page_'+pg).append(rowHtml);
        } else {
            $("#btn_div_suivant").css('display', '');
        } 

        return false;
    });*/

    


    /*function add_more_product(){
                   var pg=$('#table_page').val();
        var char='.table_page_'+pg+' tr';
                var rowCount = $(char).length;

        var rowCount = $(char).length;
        var rowHtml = '';

        var page_id = ((rowCount-1)-((rowCount-1)%40))/40;

        if(rowCount < 40) {
            for (var i = 11; i <=40; i++) {

                var rowid= (pg*40)+i;
            
            rowHtml += '<tr class="page'+pg+'">';

                rowHtml += '<td class="refCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Ref#</label>';
                        rowHtml += '<input type="text"  class="product_ref_search form-control" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '" value="" placeholder="Référence..." />';
                        rowHtml += '<input class="product_ref_search_hidden" type="hidden" id="product_ref_' + rowid + '" name="product_ref_' + rowid + '_hidden" value="' + rowid + '" />';
                        rowHtml += '<input class="product_id_search_hidden" type="hidden" id="product_id_' + rowid + '" name="product_id_' + rowid + '_hidden" value="" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol1">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Coloris</label>';
                        rowHtml += '<select class="form-control choose_coloris" id="coloris_' + rowid + '" name="coloris_' + rowid + '">';
                            rowHtml += '<option value="">Coloris</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                rowHtml += '</td>';
                
                rowHtml += '<td class="qtCol">';
                    rowHtml += '<div class="form-group fct">';
                        rowHtml += '<label>Quantité</label> ';
                        rowHtml += '<input class="form-control" type="number" id="qt_' + rowid + '" name="qt_' + rowid + '" value="1" min="1" step="1" onkeypress="return isNumberKey(event);" />';
                    rowHtml += '</div>';
                rowHtml += '</td>';

                rowHtml += '<td class="attrCol">'; 
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Marque</label>';
                        rowHtml += '<select class="form-control choose_marque" id="marque_' + rowid + '" name="marque_' + rowid + '">';
                            rowHtml += '<option value="">Marque</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';
                    
                    rowHtml += '<div class="form-group fc">';
                        rowHtml += '<label>Ligne</label>';
                        rowHtml += '<select class="form-control choose_ligne" id="ligne_' + rowid + '" name="ligne_' + rowid + '">';
                            rowHtml += '<option value="">Ligne</option>">';
                        rowHtml += '</select>'; 
                    rowHtml += '</div>';

                    if($( "#reseller_id" ).length>0){
                        rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<select class="form-control choose_price prix_admin_' + rowid + '" id="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '">';
                                rowHtml += '<option value="">Prix</option>">';
                            rowHtml += '</select>'; 
                        rowHtml += '</div>';
                    }else{
                          rowHtml += '<div class="form-group fc">';
                            rowHtml += '<label>Prix</label>';
                            rowHtml += '<span class="form-control choose_price prixsp_' + rowid + '"></span>';
                            rowHtml += '<input type="hidden" class="prix_admin_' + rowid + '" name="prix_admin_' + rowid + '" />';
                        rowHtml += '</div>';                  
                    }
                rowHtml += '</td>';
            rowHtml += '</tr>';
            }

            $('.table_page_'+pg).append(rowHtml);
        } else {
            $("#btn_div_suivant").css('display', '');
        } 

        return false;

    };*/

    function delete_item(item){
        $.ajax({
                type: "POST",
                url: "/admin/order/delete_item/"+item,
                success: function (data) {
                    var elem = $(".item"+item);
                    elem.remove();
                    },
                error: function (data) {
                    console.log('An error occurred.');
                    console.log(data);
                },
            });
    }


   function validate_form(){
    var pid = $('#table_page').val();

    var k= 0;
    if(pid !=0){
        k = (pid*20) ;
    }

    var valid = 1;
    for (var i = k ; i <= (parseInt(pid)+1)*20 ; i++) {
            var input=$('#product_ref_'+i).val();
            if(input==''){
                valid = 0;
            }
        }

        if(valid==1){
         $("#btn_div_suivant").css('display', '');
        }else{
           $("#btn_div_suivant").css('display', 'none'); 
        }
   }

