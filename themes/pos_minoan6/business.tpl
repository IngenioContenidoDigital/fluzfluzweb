p{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
* @license   Commercial license see license.txt
* Support by mail  : contact@prestaplugins.com
* Support on forum : Patanock
* Support on Skype : Patanock13
*}
<!-- MODULE Transfer Business -->
<script>
    var urlTransferController = "{$link->getPageLink('business', true)|escape:'html':'UTF-8'}";
</script>
<div id="error_p" style="display: none;">
    <div class="row border-report">
        <div class="col-lg-6 error_page" id="error_page">
            <h2 class="title-error">Reporte de Errores</h2>
        </div>
        <div  class="col-lg-6 download-error">
            <div><a href="../csvcustomer/carga_customer_example.csv" class="link-down">DESCARGAR</a></div>
        </div>
    </div>
    <div class="row">
        <p style="margin: 20px 0px;"> Listado de usuarios con errores. Por Favor Verificar la informaci&oacute;n de los empleados.</p>
    </div>
    <div class="row">
        <div class="container-error" id="container-error">
            
        </div>
    </div>
    <div class="row div-btn-error">
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
        <a class="btn btn-default btn-back-business" href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}"><span>{l s='regresar al panel principal'}</span></a>
    </div>
</div>
{if $error_csv}
<div id="error_p">
    <div class="row border-report">
        <div class="col-lg-6 error_page" id="error_page">
            <h2 class="title-error">Reporte de Errores</h2>
        </div>
        <div  class="col-lg-6 download-error">
            <div><a href="../csvcustomer/carga_customer_example.csv" class="link-down">DESCARGAR</a></div>
        </div>
    </div>
    <div class="row">
        <p style="margin: 20px 0px;"> Listado de usuarios con errores. Por Favor Verificar la informaci&oacute;n de los empleados.</p>
    </div>
    <div class="row">
        <div class="container-error">
            {foreach from=$error_csv item=errorc}
                <p class="error" style="margin-top: 10px;">
                    {if $errorc.email == 'email invalid'}
                        <span style="color: #EF4136;">&#33;</span> Direcci&oacute;n de email no es correcta.
                    {elseif $errorc.name == 'name invalid'}
                        <span style="color: #EF4136;">&#33;</span> El campo nombre o apellido <span style="color: #EF4136;">{$errorc.name_custom}</span> no es correcto.
                    {elseif $errorc.email_exists == 'email exists'}
                        <span style="color: #EF4136;">&#33;</span> Alguien con este email <span style="color: #EF4136;">{$errorc.email}</span> ya ha sido apadrinado
                    {elseif $errorc.dni_exists == 'dni exists'}
                        <span style="color: #EF4136;">&#33;</span> La Cedula <span style="color: #EF4136;">{$errorc.cedula}</span> ya se encuentra Registrada en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.valid_phone == 'valid phone'}
                        <span style="color: #EF4136;">&#33;</span> El Tel&eacute;fono <span style="color: #EF4136;">{$errorc.phone}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.valid_username == 'valid username'}
                        <span style="color: #EF4136;">&#33;</span> El Usuario <span style="color: #EF4136;">{$errorc.username}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.sponsor == 'no sponsor'}
                        {l s='No hay espacios disponibles en la red.'}
                    {else if $errorc.csv == 'already exists'}   
                        <span style="color: #EF4136;">&#33;</span> El achivo <span style="color: #EF4136;">{$errorc.csv_name}</span> ya existe. Por favor cambiar el nombre del archivo CSV. 
                    {else if $errorc.csv_number == 'registro'}
                        <span style="color: #EF4136;">&#33;</span> No es posible importar mas de 80 registros. Por favor validar y reducir la cantidad de registros.
                    {/if}
                </p>
            {/foreach}
        </div>
    </div>
    <div class="row div-btn-error">
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
        <a class="btn btn-default btn-back-business" href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}"><span>{l s='regresar al panel principal'}</span></a>
    </div>        
{literal}
    <style>
        #rewards_account{display: none;}
    </style>
{/literal}
</div>
{/if}
<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="row title-row-business"> 
    <div class="col-lg-12"><span class="title-business" id="title-container"></span></div>
    <div class="col-lg-8 margin-info">
            <div id="quantity-users" class="col-lg-12"> Cantidad de Empleados: <span class="available-point"> {$all_fluz} </span></div>
            <div style="padding-left:0px;" id="available-point" class="col-lg-12 title-fluz">{l s="Fluz Totales: "}<span class="available-point">{$pointsAvailable}</span></div>
            <div style="padding-left:0px;" class="col-lg-12 title-fluz" id="title-fluz">{l s="Fluz en Dinero: "}
                <input type="hidden" value="{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" id="cash-available">
                <span class="available-point"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </span>
            </div>
    </div>
    <div class="col-lg-4 div-img">
        <img src="/img/business/{$id_customer}.png" class="img-business" />
        <div class="text-business">{$username}</div>
    </div>
</div>
{if $error}
    {foreach from=$error item=errorc}
        <p class="error" style="margin-top: 10px;">
            {if $errorc.email == 'email invalid'}
                Direcci&oacute;n de email no es correcta.
            {elseif $errorc.name == 'name invalid'}
                El campo nombre o apellido no es correcto.
            {elseif $errorc.email_exists == 'email exists'}
                <span style="color: #EF4136;">&#33;</span> Alguien con este email <span style="color: #EF4136;">{$errorc.email}</span> ya ha sido apadrinado
            {elseif $errorc.dni_exists == 'dni exists'}
                <span style="color: #EF4136;">&#33;</span> La Cedula <span style="color: #EF4136;">{$errorc.cedula}</span> ya se encuentra Registrada en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.valid_phone == 'valid phone'}
                <span style="color: #EF4136;">&#33;</span> El Tel&eacute;fono <span style="color: #EF4136;">{$errorc.phone}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.valid_username == 'valid username'}
                <span style="color: #EF4136;">&#33;</span> El Usuario <span style="color: #EF4136;">{$errorc.username}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.sponsor == 'no sponsor'}
                {l s='No hay espacios disponibles en la red.'}
            {else if $errorc.csv == 'already exists'}   
                El achivo {$csv} ya existe. Por favor cambiar el nombre del archivo CSV. 
            {/if}
        </p>
    {/foreach}
{/if}
<div class="row panel-employee">
    <div class="col-lg-1 item-employee" style='text-align:center;'><a href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}">Panel Principal</a></div>
    <div class="col-lg-2 item-employee" style='text-align:center;' id="toggle-add-employees">
        <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <span>Agregar Empleados</span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu back-business" role="menu">
            <li>
                <a id="item-menu-add" role="menuitem" href="#">Agregar Un Empleado</a>
            </li>
            <li>
                <a id="item-menu-upload" role="menuitem" href="#">Agregar Varios Empleados</a>
            </li>
        </ul>
    </div>
    <div class="col-lg-1 item-employee" style='text-align:center;'><a href="/inicio/485-precarga-de-saldo-fluzfluz.html">Comprar Fluz</a></div>
    <div class="col-lg-1 item-employee" style='text-align:center;' id="distribute-fluz"><a href="#">Distribuir Fluz</a></div>
    <div class="col-lg-2 item-employee" style='text-align:center;' id="shopping_fluz_history"><a href="#">Historial de Compras Fluz</a></div>
    <div class="col-lg-1 item-employee" style='text-align:center;' id="history-transfer"><a href="#">Historial de Transferencia</a></div>
    <div class="col-lg-1 item-employee" style='text-align:center;' id="history-purchase"><a href="#">Compras de Empleados</a></div>
</div>
<div class="row block-information-csv">
    <div id="title-info">
        <span id="text-info">Tips para importaciones CSV &nbsp;</span>
        <span id="icon-info">!</span>
    </div>
    <ul id="list-info">
        <li>El archivo CSV debe estar en formato de separaci&oacute;n por puntos y comas (;).</li>
        <li>Todos los campos son requeridos (*).</li>
        <li>Ning&uacute;n dato incluido en el archivo debe contener caracteres especiales o tildes (&aacute;&ntilde;/\&deg;|&not;^&quot;&amp;&lt;&gt;) entre otros.</li>
    </ul>
</div>
<div id="panel-statistics">
    <div class="row">
        {include file="./statistics.tpl"}
    </div>
</div>  
<div id="panel-distribute-fluz" style="display:none;">
    <div class="row">
        {include file="./distribute_fluz.tpl"}
    </div>
</div>  
<div id="panel-shopping_fluz_history" style="display:none;">
    <div class="row">
        {include file="./shopping_fluz_history.tpl"}
    </div>
</div>
<div id="panel-add-employee" style="display:none;">
    <div class="row">
        {include file="./addemployee.tpl"}
    </div>
</div>  
<div id="panel-upload-employee" style="display:none;">
    <div class="row">
        {include file="./adduploademployee.tpl"}
    </div>
</div>  
<div id="panel-allocation-history" style="display:none;">
    <div class="row">
        {include file="./allocation_history.tpl"}
    </div>
</div> 
<div id="history_employee" style="display:none;">
    <div class="row">
        {include file="./history_purchase.tpl"}
    </div>
</div> 
{literal}
    <style>
        #right_column{display: none;}
    </style>
{/literal}
<div id="url_fluz" style="display:none;">{$base_dir_ssl}</div>
{literal}
    <script>
        $(document).ready(function(e){
              
                $("#busqueda").keyup(function(e){
                    var username = $("#busqueda").val();
                    
                    $('#container-List-employees > div').each(function () {
                        $(this).show();
                        var email = $(this).find('.email-id').html().toLowerCase();
                        var dni = $(this).find('.dni-id').html().toLowerCase();
                        if (email.indexOf(username.toLowerCase()) != -1){

                        }
                        else if(dni.indexOf(username.toLowerCase()) != -1){

                        }
                        else {
                            $(this).hide();
                        }
                    });
                });
            });
    </script>
{/literal}
{literal}
    <script>
        $(document).ready(function(){
            
            var add = $('#item-menu-principal').text();
            var title = 'PANEL PRINCIPAL';
            $('#save-info').hide();
            $('#busqueda').hide();
            $('.div-btn-delete-info').hide();
            $('#title-container').html(title);
            $('#amount-use').hide();
            $('#row-upload-transfer').hide();
            $('#delete_employee').hide();
            $('.block-information-csv').hide();
            var select = $('select[name=select-distribute]').val()
            if(select == 'select-option'){
                $('#container-List-employees').addClass("disabledbutton");
            }
            
            $("#select-distribute").change(function() {
                var select = $('select[name=select-distribute]').val()
                
                if(select == 'all-fluz'){
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').show();
                    $('#save-info').show();
                    $('#busqueda').hide();
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('#row-upload-transfer').hide();
                    $('#delete_employee').hide();
                    $('.block-information-csv').hide();
                    $('.div-btn-delete-info').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                    $('#use_allfluz').empty();
                    
                    $('#save-info').click(function(){
                        var ptoUsed = $('#ptosusedhidden').val();       
                        if(ptoUsed == '' || ptoUsed == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }
                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                       //var ptoDistribute = $('#ptosdistributehidden').val();
                       var ptoUsed = $('#ptosusedhidden').val();       
                       $(this).prop("disabled",true);
                       $('#cancel_modal_fluz').prop('disabled',true);
                       $('#progress-bar').show();
                       $('#progress-bar').css('margin-top','30px');
                       $('.row-btn-modal').css('margin-top','70px');
                       var listEdit = [];  
                       var total_point = 0;
                       var url = document.getElementById("url_fluz").innerHTML;
                       
                       $( ".r_clase" ).each(function( index ) {
                            var id_sponsor = $(this).attr("sponsor");
                            var amount_edit = ($(this).val())/25;
                            console.log(amount_edit);
                            if($('#partial_amount-'+id_sponsor).val() !== ''){
                                amount_edit = $('#partial_amount-'+id_sponsor).val();
                            }
                            total_point += Number($(this).val());

                            var item = {}
                            if(amount_edit != 0){
                                item ["id_sponsor"] = id_sponsor;
                                item ["amount"] = amount_edit;
                            }

                            listEdit.push(item);
                        });
                       
                        listEdit = JSON.stringify(listEdit);
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=allFLuz&listEdit='+listEdit+'&ptoUsed='+ptoUsed,
                            success : function(data) {
                                //console.log(data);
                                if(data == 1){
                                     $('#progress-bar').hide();
                                     $('#error').show();
                                     $('#error').html('No es correcto el valor ingresado para redimir. Por Favor verificar valor.');
                                     $.fancybox.close();
                                }else{   
                                    $('#progress-bar').hide();
                                    window.location.replace(""+url+"confirmtransferfluzbusiness");
                                }
                            }
                        });
                    });
                    
                }
                else if(select == 'single-fluz'){
                    
                    $('#container-List-employees').removeClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#row-upload-transfer').hide();
                    $('#save-info').show();
                    $('#busqueda').show();
                    $(".amount_unit").prop('disabled', true);
                    $(".amount_unit").css('background', 'transparent');
                    $(".amount_unit").val(0);
                    $('#delete_employee').show();
                    $('.block-information-csv').hide();
                    $('.div-btn-delete-info').show();
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('.check_user').click(function() {
                        if ($(this).is(':checked')) {
                            //codigo para eliminar usuario de la red+
                            
                            var url = document.getElementById("url_fluz").innerHTML;
                            var check_delete = $(this).val();
                            var name = $('#name_employee-'+check_delete).html();
                            var lastname = $('#lastname_employee-'+check_delete).html();
                            
                            // $('#delete_employee').show();
                            $('#user_delete').html(name+' '+lastname);
                            $('#delete-info-process').click(function(){
                                $.ajax({
                                    url : urlTransferController,
                                    type : 'POST',
                                    data : 'action=kickoutemployee&id_employee='+check_delete,
                                    success : function(data) {
                                         console.log(id);
                                         window.location.replace(""+url+"confirmdeleteusers");
                                    }
                                });
                            });
                        }
                        else{
                            check_delete = "";
                            // $('#delete_employee').hide();
                        }
                    });   
                    
                    $('#save-info').click(function(){
            
                        var total_point = 0;            
                        $( ".amount_edit" ).each(function( index ) {

                            total_point += Number($(this).val());

                        });
                        var fluz = Math.round((total_point/25));
                        var cashconvertionfluz='COP'+' '+'$' + Math.round(total_point).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        $('#fluz_send').html(fluz);
                        $('#fluz_send_cash').html(cashconvertionfluz);
                        
                        if(total_point== '' || total_point == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }

                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                    
                    var listEdit = [];  
                    var total_point = 0;
                    var url = document.getElementById("url_fluz").innerHTML;
                    $(this).prop("disabled",true);
                    $('#cancel_modal_fluz').prop('disabled',true);
                    $('#progress-bar').show();
                    $('#progress-bar').css('margin-top','30px');
                    $('.row-btn-modal').css('margin-top','70px');
                    
                    $( ".amount_edit" ).each(function( index ) {
                        var id_sponsor = $(this).attr("sponsor");
                        var amount_edit = ($( this ).val())/25;
                        total_point += Number($(this).val());
                        
                        var item = {}
                        item ["id_sponsor"] = id_sponsor;
                        item ["amount"] = amount_edit;
                        
                        listEdit.push(item);
                    });
                       
                       listEdit = JSON.stringify(listEdit);   
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=editFLuz&listEdit='+listEdit+'&ptosTotal='+total_point,
                            success : function(data) {
                                if(data == 1){
                                     $('#progress-bar').hide();
                                     $('#error').show();
                                     $('#error').html('No es correcto el valor ingresado para redimir. Por Favor verificar valor.');
                                     $.fancybox.close();
                                }else{   
                                    $('#progress-bar').hide();
                                    window.location.replace(""+url+"confirmtransferfluzbusiness");
                                }
                            }
                        });
                    });
                }
                else if(select == 'all-group'){
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').show();
                    $('#busqueda').hide();
                    $('#row-upload-transfer').show();
                    $('#delete_employee').hide();
                    $('.block-information-csv').show();
                    $('.div-btn-delete-info').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                    $('#use_allfluz').empty();
                    
                    $('#save-info').click(function(){
                        var ptoUsed = $('#ptosusedhidden').val();       
                        if(ptoUsed == '' || ptoUsed == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }
                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                       //var ptoDistribute = $('#ptosdistributehidden').val();
                       var ptoUsed = $('#ptosusedhidden').val();       
                       $(this).prop("disabled",true);
                       $('#cancel_modal_fluz').prop('disabled',true);
                       $('#progress-bar').show();
                       $('#progress-bar').css('margin-top','30px');
                       $('.row-btn-modal').css('margin-top','70px');
                       var listEdit = [];  
                       var total_point = 0;
                       var url = document.getElementById("url_fluz").innerHTML;
                       
                       $( ".r_clase" ).each(function( index ) {
                            var id_sponsor = $(this).attr("sponsor");
                            var amount_edit = ($( this ).val())/25;

                            if($('#partial_amount-'+id_sponsor).val() !== ''){
                                amount_edit = $('#partial_amount-'+id_sponsor).val();
                            }
                            total_point += Number($(this).val());

                            var item = {}
                            if(amount_edit != 0){
                                item ["id_sponsor"] = id_sponsor;
                                item ["amount"] = amount_edit;
                            }

                            listEdit.push(item);
                        });
                       
                        listEdit = JSON.stringify(listEdit);
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=allFLuz&listEdit='+listEdit+'&ptoUsed='+ptoUsed,
                            success : function(data) {
                                if(data == 1){
                                     $('#progress-bar').hide();
                                     $('#error').show();
                                     $('#error').html('No es correcto el valor ingresado para redimir. Por Favor verificar valor.');
                                     $.fancybox.close();
                                }else{   
                                    $('#progress-bar').hide();
                                    window.location.replace(""+url+"confirmtransferfluzbusiness");
                                }
                            }
                        });
                    });
                }
                else{
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('#delete_employee').hide();
                    $('.block-information-csv').hide();
                    $('.div-btn-delete-info').hide();
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').hide();
                    $('#busqueda').hide();
                    $('#row-upload-transfer').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                }
            });
            
            $('#item-menu-principal').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'PANEL PRINCIPAL';
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#quantity-users').show();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').hide();
                $('#history_employee').hide();
                $('.block-information-csv').hide();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').removeClass('active_btn');
            });
            
            $('#item-menu-add').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'AGREGAR UN EMPLEADO';
                $('#title-container').html(title);
                $('#panel-add-employee').show();
                $('#panel-upload-employee').hide();
                $('#quantity-users').hide();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').hide();
                $('#history_employee').hide();
                $('.block-information-csv').hide();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').removeClass('active_btn');
            });
            
            $('#item-menu-upload').click(function(){
                var add = $('#item-menu-upload').text();
                var title = 'AGREGAR VARIOS EMPLEADOS';
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#quantity-users').hide();
                $('#panel-upload-employee').show();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').hide();
                $('#history_employee').hide();
                $('.block-information-csv').show();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').removeClass('active_btn');
            });
            
            $('#history-transfer').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'HISTORIAL DE TRANSFERENCIAS';
                $('#title-container').html(title)
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').hide();
                $('#panel-allocation-history').show();
                $('#panel-shopping_fluz_history').hide();
                $('#history_employee').hide();
                $('#quantity-users').hide();
                $('.block-information-csv').hide();y
                $('#history-transfer').addClass('active_btn');
                $('#history-purchase').removeClass('active_btn');
            });
            
            $('#history-purchase').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'HISTORIAL DE COMPRAS DE EMPLEADOS';
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').hide();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').hide();
                $('#history_employee').show();
                $('#quantity-users').hide();
                $('.block-information-csv').hide();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').addClass('active_btn');
            });
            
            $('#distribute-fluz').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'DISTRIBUIR FLUZ';
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').show();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').hide();
                $('#history_employee').hide();
                $('#quantity-users').hide();
                $('.block-information-csv').hide();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').addClass('active_btn');
            });
            
            $('#shopping_fluz_history').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'HISTORIAL DE COMPRAS FLUZ';
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#panel-statistics').hide();
                $('#panel-distribute-fluz').hide();
                $('#panel-allocation-history').hide();
                $('#panel-shopping_fluz_history').show();
                $('#history_employee').hide();
                $('#quantity-users').hide();
                $('.block-information-csv').hide();
                $('#history-transfer').removeClass('active_btn');
                $('#history-purchase').addClass('active_btn');
            });
            
            $('.check_user').click(function() {
                if ($('.check_user').is(':checked') ) {
                    $('#delete_employee').removeAttr("disabled");
                    $('.div-btn-delete-info').hide();
                } else {
                    $('#delete_employee').attr("disabled",true);
                    $('.div-btn-delete-info').show();
                }
            });
            
        });
        
        function edit(id){
            $('#single-'+id).prop('disabled', false);
            $('#single-'+id).removeClass('amount_unit');
            $('#single-'+id).addClass('amount_edit');
            var availablepoint = $('#available-point span').html();
            var availablecash = $('#cash-available').val();
            
            $("#single-"+id).on("keyup",function(event){
                
                var value_edit=$("#single-"+id).val();
                $("#partial_amount-"+id).val(value_edit);
                var valor2 = $("#partial_amount-"+id).val();
                var cashamount = (Math.round(valor2/25));
                var cashconvertion= cashamount +' '+'Fluz';
                var ptosMax = availablepoint+' '+'Fluz';
                var calculo = availablepoint - cashamount;
                var cashconvertion2='COP'+' '+'$' + (Math.round(calculo*25)).toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                
                $('#available-point span').html(calculo);
                $('.r_clase').attr('oninput', availablecash);
                $("#amount_unit_cash-"+id).html(cashconvertion);
                $('#title-fluz span').html(cashconvertion2);
                
                if(calculo <= 0){
                    
                    $("#amount_unit_cash-"+id).html(ptosMax);
                    $('.r_clase').attr('oninput', availablepoint);
                    $("#single-"+id).val(availablecash);
                    $('#available-point span').html(0);
                    $('#title-fluz span').html(0);
                }
                
            }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
        }
        
    </script>
{/literal}
{literal}
    <script>       
        $("#use_allfluz").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#use_allfluz').val();
            var t_user = $('#total_users').val();
            var availablecash = $('#cash-available').val();
            $('.custom-file-upload').prop('disabled',true);
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round((valor2/25)/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptosingle = Math.round((valor2/25));
                var cashconvertionfluz='COP'+' '+'$' + Math.round(valor2).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var ptoListview = (Math.round((valor2/25)/t_user))+' '+'Fluz';
                var ptoList = (Math.round((valor2/25)/t_user));
                var cashamount = ptoList * 25;
                var cashconvertion= cashamount.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var result2 = valor1 - (ptoList*t_user);
                var resultCop = availablecash - valor2;
                var cashconvertion2='COP'+' '+'$' + (Math.round(resultCop)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosusedhidden').val(valor2);
                $('#fluz_send').html(ptosingle);
                $('#fluz_send_cash').html(cashconvertionfluz);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(cashconvertion);
                $('.amount_unit_cash').html(ptoListview);
                $('.text_fluz').html('$ COP');
                $('#available-point span').html(result2);
                $('#title-fluz span').html(cashconvertion2);
                $('.ptos_all').val(ptoList);
                
            }else{
                valor2*=-1;
                $('#use_allfluz').val(valor2);
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round(valor2/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptoList = (Math.round(valor2/t_user));
                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(ptoList);
                $('.amount_unit_cash').html(cashconvertion);
                $('.text_fluz').html('Fluz');
                $('.ptos_all').val(ptoList);
            }
                
        }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
        function calcular(valor1,valor2)
        {   
            return (valor1-valor2);
        }
    </script>
{/literal}
{literal}
    <script>
        function cancelSubmit(){ 
            $.fancybox.close();
            location.reload();
        }
    </script>
    <script>
        $("#file").change(function(e) {
            //$('#use_allfluz').prop('disabled',true);
            $('#use_allfluz').css('opacity', '0.5');
            $('#error').css('display','none');
            $('#success').css('display','none');
            $('#save-info').prop('disabled',false);
            var file = document.getElementById('file').files[0],
            reader = new FileReader();
            reader.onload = function(event) {
                
                var array = event.target.result;
                var extractValidString = array.match(/[\w @.]+(?=,?)/g);
                var noOfCols = 3;
                var objFields = extractValidString.splice(0,noOfCols);
                var arr = [];
                var flag = true;
                while(extractValidString.length>0) {
                    var obj = {};
                    var row = extractValidString.splice(0,noOfCols)
                    if(row.length == 3){
                        for(var i=0;i<row.length;i++) {
                            obj[objFields[i]] = row[i].trim();
                            flag = true;
                        }
                        arr.push(obj)
                    }
                    else{
                        flag = false;
                    }   
                }
                
                var sum = 0;
                
                $.each(arr, function( index, value ) {
                            if(flag == true){
                                var monto = value.montotransferencia;
                                var convmonto = monto.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                                var monto_fluz_view = value.montotransferencia/25+' '+'Fluz';
                                var monto_fluz = value.montotransferencia/25;
                                sum += parseInt(monto);
                                $('#container-List-employees > div').each(function () {
                                    var email = $(this).find('.email-id').html();
                                    var id_custom = $(this).find('#id_sponsor').val();
                                    if(email == value.email){
                                        $('#single-'+id_custom).val(convmonto);
                                        $('#amount_unit_cash-'+id_custom).html(monto_fluz_view);
                                        $("#partial_amount-"+id_custom).val(monto_fluz);
                                    }
                                });
                           }
                        });
                
                if(sum != '' && sum != 0){
                    $('#ptosusedhidden').val(sum);   
                    var fluz = Math.round((sum/25));
                    var cashconvertionfluz='COP'+' '+'$' + Math.round(sum).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    $('#fluz_send').html(fluz);
                    $('#fluz_send_cash').html(cashconvertionfluz);
                }
                
                if(flag == false){
                    $('#error').css('display','block');
                    $('#success').css('display','none');
                    $('#error').append("<b>Tu Archivo CSV contiene errores o Campos Vacios. Por Favor Verificarlo.</b>");;
                    $('#file').attr({ value: '' });
                    $('#save-info').prop('disabled',true);
                }
                else{
                    list_transfer = JSON.stringify(arr);
                    console.log(list_transfer);
                    $('#success').text(' ');
                    $('#error').text(' ');
                    $.ajax({
                    url : urlTransferController,
                    type : 'POST',
                    data : 'action=uploadtransfers&list_transfer='+list_transfer,
                        success : function(data) {
                           console.log(data); 
                           if(data != 1){ 
                                $('#error').css('display','block');
                                $('#success').css('display','none');
                                $('#error').text(data);
                                $('#file').attr({ value: '' });
                                $('#save-info').prop('disabled',true);
                            }
                            else{
                                $('#file').attr({ value: '' });
                                $('#error').css('display','none');
                                $('#success').css('display','block');
                                $('#success').append("<b>Tu Archivo CSV se Cargo Correctamente.</b>");;
                            }
                        }
                    });
                }
            };
            reader.readAsText(file);
        });
    </script>
{/literal}
