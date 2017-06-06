/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    $(".pendingsinvitation").fancybox();

    // search member
    $('.searchimg').click( function() {
        $('#formnetwork').submit();
    });

    // popup message
    $('.myfancybox').click( function() {
        $("#idsendmessage").val("");
        $("#idreceivemessage").val("");
        $("#messagesendmessage").val("");
        var data = $(this).attr('send').split('|');
        $("#idreceivemessage").val(data[0]);
        $("#namesendmessage").text(data[1]);
        $("#imgsendmessage").attr("src", data[2]);
        $("#idsendmessage").val(data[3]);
    });

    // send message
    $('#buttonsendmessage').click( function() {
        var idsend = $("#idsendmessage").val();
        var idreceive = $("#idreceivemessage").val();
        var message = $("#messagesendmessage").val();
        var jsSrcRegex = /([^\s])/;
        if ( idsend != "" && idreceive != "" && message != "" && jsSrcRegex.exec(message) ) {
            $.ajax({
                method:"POST",
                data: {
                    'action': 'sendmessage',
                    'idsend': idsend,
                    'idreceive': idreceive,
                    'message': message
                },
                url: '/messagesponsor.php', 
                success:function(response){
                    alert("Mensaje enviado exitosamente.");
                    $("#idsendmessage").val("");
                    $("#messagesendmessage").val("");
                    $.fancybox.close();
                    location.reload();
                }
            });
        }
    });
});