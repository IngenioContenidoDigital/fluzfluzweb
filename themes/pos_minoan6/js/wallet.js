$(document).ready(function() {
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
        } else {
            $("#btnupt-value").parent().css("display","none");
            $("#value-used").parent().css("display","none");
        }
        markUsed( $("#card_product").val(),$(this).attr("value") );
    });
});

function renderViewCard(key, card) {
    $("#expiration").html( card.expiration );
    $("#value_original").html( "COP $ "+Math.round(card.price) );
    $("#value").html( "COP $ "+Math.round(card.price_shop) );
    $("#date_buy").html( card.date );
    $("#code").html( card.card_code );
    $("#instructions").html( card.description_short );
    $("#terms").html( card.description );
    $("#card_product").val( card.id_product_code );
    $("#card_key").val( key );
    
    $("#finished-card").parent().removeClass("checked");
    $("#used-card").parent().removeClass("checked");
    $("#value-used").parent().css("display","none");
    $("#btnupt-value").parent().css("display","none");
    $("#value-used").html( "" );
    $("#upt-value").val("");
    switch ( card.used ) {
        case "2":
            $("#finished-card").parent().addClass("checked");
            $("#value-used").parent().css("display","none");
            break;
        case "1":
            $("#used-card").parent().addClass("checked");
            $("#value-used").html( "COP $ "+card.price_card_used );
            $("#value-used").parent().css("display","block");
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
            } else {
                alert("Ha ocurrido un error. Por favor intente mas tarde.");
            }
        }
    });
}