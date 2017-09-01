$(document).ready(function() {
    $(".state-used1").parent().css("display","none");
    $(".state-used2").parent().css("display","none");
    
    $("#sortby>li>a").click(function(){
        var sortby = $(this).attr("id");
        var numCards = 0;

        $(".state-used0").parent().css("display","none");
        $(".state-used1").parent().css("display","none");
        $(".state-used2").parent().css("display","none");

        switch ( sortby ) {
            case "unused":
                $(".state-used0").parent().css("display","block");
                numCards = $(".state-used0").length;
                break;
            case "used":
                $(".state-used1").parent().css("display","block");
                numCards = $(".state-used1").length;
                break;
            case "finished":
                $(".state-used2").parent().css("display","block");
                numCards = $(".state-used2").length;
                break;
            case "all":
                $(".state-used0").parent().css("display","block");
                $(".state-used1").parent().css("display","block");
                $(".state-used2").parent().css("display","block");
                numCards = $(".state-used0").length + $(".state-used1").length + $(".state-used2").length;
                break;
        }
        
        $("#available-cards").html(numCards);
    });

    $(".cardviewupt-instructions>.btn-info").click(function(){        
        if ( $(this).find(".icon-plus").length ) {
            $(this).find("i").removeClass("icon-plus");
            $(this).find("i").addClass("icon-minus");
        } else {
            $(this).find("i").removeClass("icon-minus");
            $(this).find("i").addClass("icon-plus");
        }
    });

    $(".card").click(function(){
        $(".container-card").css("border","1px solid #E8E8E8");
        $(this).find(".container-card").css("border","1px solid #F15E54");
        renderViewCard( $(this).attr("key"), cards[$(this).attr("key")] );
    });
    
    $(".card_gift").click(function(){
        $(".container-card").css("border","1px solid #E8E8E8");
        $(this).find(".container-card").css("border","1px solid #F15E54");
        renderViewCard( $(this).attr("key"), gift_cards[$(this).attr("key")] );
    });

    $("#btnbuy").click(function(){
        window.top.location = "/content/6-categorias";
    });
    
    $("#btnupt-value").click(function(){
        if ( $("#upt-value").val() != "" ) {
            setValueUsed( $("#card_product").val(), $("#upt-value").val() );
        }
    });
    
    $(".address-manufacturer").slice(0, 3).show();
    if ( $(".address-manufacturer").length <= 3 ) {
        $("#loadMoreAddress").css('display','none');
    }
    
    $("#loadMoreAddress").click(function(){
        $(".address-manufacturer:hidden").slice(0, 100000000).toggle('slow');
        if ( $(".address-manufacturer:hidden").length == 0 ) {
            $("#loadMoreAddress").css('display','none');
            $("#loadMenosAddress").css('display','block');
        }
    });
    
    $("#loadMenosAddress").click(function(){
        $(".address-manufacturer:visible").slice(3, 100000000).slideUp('slow');
        $("#loadMoreAddress").css('display','block');
        $("#loadMenosAddress").css('display','none');
    });
    
    $("#finished-card, #used-card").click(function(){
        if ( $(this).attr("value") == 1 ) {
            $("#btnupt-value").parent().css("display","block");
            $("#value-used").parent().css("display","block");
            $("#value-used-available").parent().css("display","block");
        } else {
            $("#btnupt-value").parent().css("display","none");
            $("#value-used").parent().css("display","none");
            $("#value-used-available").parent().css("display","none");
        }
        markUsed( $("#card_product").val(),$(this).attr("value") );
    });
    
    $('#btn-gift').click(function(){
       
        $('#container-gift').show();
        
    });
    var id_customer = $("#id_customer").val();
    
    $("#busqueda").keyup(function(e){
        var username = $("#busqueda").val();
        if(username.length >= 3){
            $.ajax({
                type:"post",
                url:"/transferfluzfunction.php",
                data:'username='+username+'&id_customer='+id_customer,
                success: function(data){
                    console.log(data);
                    if(data != ""){
                        $("#resultados").empty();
                        data = jQuery.parseJSON(data);

                        var content = '';
                        $.each(data, function (key, id) {
                            content += '<div class="resultados" id="id_sponsor" onclick="myFunction(\''+data[key].username+'\',\''+data[key].id+'\')">'+data[key].username+' - '+data[key].dni+'</div>';
                            content += '<input type="hidden" id="id_sponsor_sel" value='+data[key].id+'>'
                        })

                        $("#resultados").html(content);
                    }
                    else{
                        $("#resultados").empty();
                    }
                }
            });
        }
        else{
            $("#resultados").empty();
        }
    });
    
});

function renderViewCard(key, card) {
    if ( card.expiration == '00/00/0000' ) {
        $('#vencimiento').hide();
    } else {
        $("#expiration").html( card.expiration );
        $('#vencimiento').show();
    }
    
    if(card.send_gift != 1){
        $("#code").html( card.card_code );
        $('#send_gift').show();
    }
    else{
        $("#code").html( 'Bono Obsequiado' );
        $('#send_gift').hide();
        $('#container-gift').hide();
    }
    
    $("#value_original").html( "COP $ "+Math.round(card.price) );
    $("#value").html( "COP $ "+Math.round(card.price_shop) );
    $("#date_buy").html( card.date );
    $("#instructions").html( card.description_short );
    $("#terms").html( card.description );
    $("#card_product").val( card.id_product_code );
    $("#card_key").val( key );
    
    $("#img-code-bar").removeClass("img-code-bar-0 img-code-bar-1 img-code-bar-2 img-code-bar-3");
    if ( card.code_bar != "" ) {
        $("#img-code-bar").show();
        $("#img-code-bar").prop("src",url+card.code_bar);
        $("#img-code-bar").addClass("img-code-bar-"+card.codetype);
    } else {
        $("#img-code-bar").hide();
        $("#img-code-bar").prop("src","");
    }

    $("#finished-card").parent().removeClass("checked");
    $("#used-card").parent().removeClass("checked");
    $("#value-used").parent().css("display","none");
    $("#value-used-available").parent().css("display","none");
    $("#btnupt-value").parent().css("display","none");
    $("#value-used").html( "" );
    $("#value-used-available").html( "" );
    $("#upt-value").val("");
    switch ( card.used ) {
        case "2":
            $("#finished-card").parent().addClass("checked");
            $("#value-used").parent().css("display","none");
            $("#value-used-available").parent().css("display","none");
            break;
        case "1":
            var price_card_used_available = card.price_shop - card.price_card_used;
            $("#used-card").parent().addClass("checked");
            $("#value-used").html( "COP $ "+card.price_card_used );
            $("#value-used-available").html( "COP $ "+price_card_used_available );
            $("#value-used").parent().css("display","block");
            $("#value-used-available").parent().css("display","block");
            $("#btnupt-value").parent().css("display","block");
            break;
        case "0":
            $("#finished-card").parent().removeClass("checked");
            $("#used-card").parent().removeClass("checked");
            break;
    }

    $(".viewdetailcard").css("display","block");
}

function markUsed(card,used) {
    var key = $("#card_key").val();
    $.ajax({
        url : urlWalletController,
        type : 'POST',
        data : 'action=mark-used&card='+card+'&used='+used,
        success : function(response) {
            response = jQuery.parseJSON(response);
            if ( response.success ) {
                cards[key].used = used;
                $("div[key='"+key+"']").find(".state-used").removeClass("state-used0 state-used1 state-used2");
                $("div[key='"+key+"']").find(".state-used").addClass("state-used"+used);
            } else {
                alert("Ha ocurrido un error. Por favor intente mas tarde.");
            }
        }
    });
}

function myFunction(name, id_sponsor) {
        $('#busqueda').val(name);
        $('#sponsor_identification').val(id_sponsor);
        $('#sponsor_name').val(name);
        $('#name_sponsor').html(name);
        $('.resultados').hide();
}

function send_gift(){
    var $id_customer_receive = $('#id_sponsor_sel').val();
    var id_customer = $("#id_customer").val();
    var code_s = $('#code').text();
    var code_card = code_s.replace(/\s/g, '');
    var id_product_code = $('#card_product').val();
    
    $.ajax({
        url : urlWalletController,
        type : 'POST',
        data : 'action=send_gift_card&$id_customer_receive='+$id_customer_receive+'&id_customer='+id_customer+'&code_card='+code_card+'&id_product_code='+id_product_code,
        success : function(response) {
            if ( response != '' ) {
                window.top.location = "confirmtransfergift";
            } else {
                console.log('fallo');
            }
        }
    });
}

function setValueUsed(card,value) {
    var key = $("#card_key").val();
    $.ajax({
        url : urlWalletController,
        type : 'POST',
        data : 'action=set-value-used&card='+card+'&value='+value,
        success : function(response) {
            response = jQuery.parseJSON(response);
            if ( response.success ) {
                cards[key].price_card_used = value;
                $("#upt-value").val("");
                $("#value-used").html("COP $ "+value);
                var price_card_used_available = cards[key].price_shop - value;
                $("#value-used-available").html( "COP $ "+price_card_used_available );
            } else {
                alert("Ha ocurrido un error. Por favor intente mas tarde.");
            }
        }
    });
}