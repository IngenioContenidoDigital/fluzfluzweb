/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$('.action').on('click',function(){
    var pre1 = $('#pre1').val()
    var pre2 = $('#pre2').val()
    //var pre3 = $('#pre3').val()
    
    var valor = pre1+pre2//+pre3
    if((valor!="") && (valor.match(/^\d{10}$/))){
        var phone = valor;
        $.ajax({
            url: "index.php?fc=module&module=fluzfluzapi&controller=shoppingcart",
            method:"post",
            data:{
                phone:phone,
                action:'new'
            },
            success:function(response){
                if(response=="success"){
                    $(".nuevo").val("");
                    $("<option value='"+phone+"'>( "+phone.substring(0, 3)+' ) '+phone.substring(3, 10)/*+" - "+phone.substring(6, 10)*/+"</option>").insertAfter('#noselect');
                    $('.numero').first().focus();                        
                }else{
                    alert(response)
                }
            }
        })
    }else{
        alert('Ingresaste un n\u00FAmero incorrecto. Por Favor verifica los datos');
    }
});

$('.numero').on('change',function(){
    var product = $(this).attr('name');
    var phone = $(this).val();
    $.ajax({
        url: "index.php?fc=module&module=fluzfluzapi&controller=shoppingcart",
        method:"post",
        data:{
            product:product,
            phone:phone,
            action:'add'
        },
        success:function(response){
            if(response!='success'){
                alert("No ha sido posible guardar la selecci\u00F3n. Por Favor Intente de nuevo")
            }
        }
    })
})

$('#nextStep').on('click',function(e){
    var check =false;
    $('.numero').each(function(){
        if($(this).val()==0){
            check=true;
        }
    })
    if(check){
        e.preventDefault();
        $("#popup").modal();
        $(".numero").css('border','solid red 1px');
        $('.numero').first().focus();
    }
})

/*$('.nombre').mouseenter(function() {
    $(this).addClass("largo");
  })
  .mouseleave(function() {
    $(this).removeClass("largo");
  });*/
  
/*$('.nombre').on('click',function(){
    if($(this).hasClass('largo')){
        $(this).removeClass('largo');
    }else{
        $(this).addClass('largo');
    }
})*/

$('#pre1').keyup(function(e){
    if($(this).val().length==3){
        $('#pre2').focus();
    }
});

$('#pre2').keyup(function(e){
    /*if($(this).val().length==3){
        $('#pre3').focus();
    }*/
    if((e.keyCode==8 || e.keyCode==46) && ($(this).val()=="")){
        $('#pre1').focus();
    }
});
/*$('#pre3').keyup(function(e){    
    if((e.keyCode==8 || e.keyCode==46) && ($(this).val()=="")){
        $('#pre2').focus();
    }
})*/


$('#pre1').focus(function(){
    $(this).css('border-bottom','red solid 1px');
});
$('#pre2').focus(function(){
    $(this).css('border-bottom','red solid 1px');
});
$('#pre3').focus(function(){
    $(this).css('border-bottom','red solid 1px');
});

$('#pre1').focusout(function(){
    $(this).css('border-bottom','lightgray solid 1px');
});
$('#pre2').focusout(function(){
    $(this).css('border-bottom','lightgray solid 1px');
});
$('#pre3').focusout(function(){
    $(this).css('border-bottom','lightgray solid 1px');
});