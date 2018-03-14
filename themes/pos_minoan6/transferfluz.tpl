{*
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
<!-- MODULE allinone_rewards -->

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}

<script>
    var urlTransferController = "{$link->getPageLink('transferfluz', true)|escape:'html':'UTF-8'}";
</script>

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div class="row"> 
    <h3 class="title-myinfo"> MI INFORMACION PERSONAL </h3> 
</div>
<div class="row info-personal">
    <div class="row row-personal">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="left-info">{l s='Usuario'}</div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="text-infouser"> {$username} </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="left-info">{l s='Mis Fluz'}</div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="text-infouser"> {$pointsAvailable} </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <div class="left-info">{l s='Fluz en Dinero'}</div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <div class="text-infouser"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </div>
        </div>
    </div>    
</div>
<form method="post" id="trasnferfluz" class="contenedorCash" name="trasnferfluz">
    <input type="hidden" id="pt_parciales" name="pt_parciales" value=""/>
    <input type="hidden" id="pto_total" name="pto_total" value=""/>
    <input type="hidden" id="id_customer" name="id_customer" value="{$id_customer}"/>
    <input type="hidden" id="sponsor_identification" name="sponsor_identification" value="{if $id_member != ""}{$id_member}{else}{/if}"/>
    <input type="hidden" id="sponsor_name" name="sponsor_name" value="{if $name_member != ""}{$name_member}{else}{/if}"/>
    <input type="hidden" id="popup_s" name="popup_s" value="{if $popup != ""}{$popup}{else}false{/if}"/>
    
    <div class="row"> 
        <h3 class="title-myinfo"> TRANSFERENCIA FLUZ A FLUZZERS </h3> 
    </div>
    <div class="row info-personal">
        <div class="row row-personal">
            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                    <div class="left-info">{l s='Fluzzer Destino'}</div>
            </div>
            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12">
                <div class="text-infouser">             
                    <input type="text" name="busqueda" id="busqueda" value="{if $name_member != ""}{$name_member}{else}{/if}" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Fluzzer'}" required><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
                    <div id="resultados" class="result-find"></div>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 style-fluz">
                    <div class="left-info">{l s='Cantidad de Fluz a Transferir'}</div>
            </div>
            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12">
                <input class="slider-cash col-lg-6 col-md-5 col-sm-5 col-xs-5" type="range" id="rangeSlider" value="100" min="100" max="{$pointsAvailable}" step="20" data-rangeslider>
                <div class="info-cash col-lg-5 col-md-6 col-sm-6 col-xs-6">
                        <input class="output-cash col-lg-6 col-md-6 col-sm-6 col-xs-5" type="text" name="valorSlider" id="valorSlider" value=""/>
                        <span class="col-lg-6 cash-point col-md-3 col-sm-3 col-xs-4"> &nbsp;{l s="de"}&nbsp;{$pointsAvailable}&nbsp;{l s="Fluz."}</span>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                    <div class="left-info">{l s='Fluz en Dinero'}</div>
            </div>
            <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12 padding-cash">
                <div class="text-infouser">  
                    <span class="cashout-money col-lg-12 col-md-12 col-sm-12 col-xs-12"> {l s ="COP $"}&nbsp;<span id="value-cash"></span></span>
                    <span class="cashout-money col-lg-12" style="display:none;"> {l s ="COP"}&nbsp;<span id="value-money">{(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</span></span>
                </div>
            </div>
        </div>    
    </div>
    <div id="error_novalidos" style='display:none;'></div>            
    <div class="row btn-sendfluz">
        <div class="col-lg-6">
            <button class="myfancybox btn btn-default btn-account" href="#confirmTransfer" name="submitFluz" id="submitFluz">
                <span style="cursor:pointer;font-size: 15px;color: #fff; font-family: 'Capitalized';font-weight: bold;">
                    {l s="Transferir Fluz"}
                </span>
            </button>
        </div>
    </div>
    <div style="display:none;">
        <div id="confirmTransfer" class="myfancybox">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <br>
                <img class="logo img-responsive" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz" width="356" height="94">
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title_transfer"> Confirmaci&oacute;n Envio Fluz </div>
            <div class="row info-transfer">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 t-name">Fluzzer Destino: </div><div id="name_sponsor" class="col-lg-6 col-md-6 col-sm-6 col-xs-6 name_sponsor"></div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 t-name">Fluz a Enviar: </div><div id="fluz_send" class="col-lg-6 col-md-6 col-sm-6 col-xs-6 name_sponsor"></div>
            </div>
            <div class="row progress-container" id="progress-bar" style="display:none;">
                <div class="progress">
                    <div class="progress-bar">
                            <div class="progress-shadow"></div>
                    </div>
                </div>
                <div class="text-loader">Estamos Procesando tu solicitud de Transferencia. Por Favor Espera</div>
            </div>
            <div class="row row-btn-modal">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-cancel-modal">
                    <button class="btn btn-default btn-account" id="cancel_modal_fluz" onclick="cancelSubmit()">
                        <span class="btn_modal_f">
                            {l s="Cancelar"}
                        </span>
                    </button>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-confirm-modal">
                    <button class="btn btn-default btn-account" id="submit_modal_fluz" onclick="sendSubmit()">
                        <span class="btn_modal_f">
                            {l s="Confirmar"}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>    
</form>
<div id="url_fluz" style="display:none;">{$base_dir_ssl}</div>    
<!-- END TEMPLATE TRANSFER FLUZ -->
<!-- SCRIPT -->
{literal}
    <script>
        $(document).ready(function(){
            var total_point = $('#cash-point').text();
            var value_money = $('#value-money').text();
            $('#rangeSlider').change(function() 
                {
                  var value = $(this).val();
                  $("#valorSlider").val(value);
                  var result = ((parseInt(total_point))-(parseInt(value)));
                  $("#ptos_result").html(result);
                  $("#ptos_prueba").html(result);
                  $("#points_used").html(value);
                  $('#pto_total').val(value);
                  var mult = (result * value_money); 
                  $("#cash_result").html(mult);
                });
        });    
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById( 'valorSlider' ).value=0 ;
            var value_money = $('#value-money').text();
            var name_sponsor = $('#sponsor_name').val();
            
            $('#rangeSlider').change(function() 
            {
              var value = $(this).val();
              $('#valorSlider').val($(this).val());
              $('#pt_parciales').val(value);
              $('#fluz_send').html(value);
              var mult = (value * value_money); 
              $("#value-cash").html(mult);
              $("#value-confirmation").html(mult);
              var total = mult;
              $("#total-valor").html(total);
            });
            
            $('#name_sponsor').html(name_sponsor);
        });
    </script>
{/literal}
{literal}
    <script>
        $(document).ready(function(e){
            var id_customer = $("#id_customer").val();
            $("#busqueda").keyup(function(e){
                var username = $("#busqueda").val();
                if(username.length >= 3){
                    $.ajax({
                        type:"post",
                        url:"/transferfluzfunction.php",
                        data:'username='+username+'&id_customer='+id_customer,
                        success: function(data){
                            if(data != ""){
                                $("#resultados").empty();
                                data = jQuery.parseJSON(data);
                                
                                var content = '';
                                $.each(data, function (key, id) {
                                    content += '<div class="resultados" id="id_sponsor" onclick="myFunction(\''+data[key].username+'\',\''+data[key].id+'\')">'+data[key].username+' - '+data[key].dni+'</div>';
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
    </script>
    <script>
        function myFunction(name, id_sponsor) {
                $('#busqueda').val(name);
                $('#sponsor_identification').val(id_sponsor);
                $('#sponsor_name').val(name);
                $('#name_sponsor').html(name);
                $('.resultados').hide();
        }
        
        function sendSubmit(){
            
            var point_part = $('#pt_parciales').val();
            var id_sponsor = $('#sponsor_identification').val();
            var url = document.getElementById("url_fluz").innerHTML;
            var popup = $('#popup_s').val();
            
            $('#progress-bar').show();
            $('#progress-bar').css('margin-top','30px');
            $('.row-btn-modal').css('margin-top','70px');
            $('#submit_modal_fluz').prop('disabled', true);
            $.ajax({
                url : urlTransferController,
                type : 'POST',
                data : 'action=transferfluz&point_part='+point_part+'&sponsor_identification='+id_sponsor,
                success : function(a) {
                    console.log(popup);
                    if(popup == 'false'){
                        $('#progress-bar').hide();
                        window.location.replace(""+url+"confirmtransferfluz");
                    }
                    else{
                        $('#progress-bar').hide();
                        window.location.replace(""+url+"confirmtransferfluz?popup=true");
                    }
                    $.fancybox.close();
                }
            });
        }
        
        function cancelSubmit(){ 
            $.fancybox.close();
            location.reload();
        }
    </script>
{/literal}
{literal}
    <script>
        $('#submitFluz').click(function(e){
           var name_sponsor = $('#sponsor_name').val();
           var pto_parcial = $('#pt_parciales').val();
           var id_sponsor = $('#sponsor_identification').val();
           
           if(name_sponsor == ''){
               $('#error_novalidos').show();
               $('#error_novalidos').html('El campo fluzzer destino no puede ser vacio. Por favor verificar los datos.');
               $('#submitFluz').removeClass('myfancybox');
               location.reload();
           }
           if(pto_parcial== ''){
               $('#error_novalidos').show();
               $('#error_novalidos').html('El campo Cantidad de fluz a transferir no puede ser vacio. Por favor verificar los datos.');
               $('#submitFluz').removeClass('myfancybox');
               location.reload();
           }
           
           /*$.ajax({
                url : urlTransferController,
                type : 'POST',
                data : 'action=transferfluz&point_part='+pto_parcial+'&sponsor_identification='+id_sponsor,
                success : function(a) {
                    if(a == 1){
                        $('#error_novalidos').show();
                        $('#error_novalidos').html('No es correcto el valor ingresado para redimir. Por Favor verificar valor.');
                    }
                }
            });*/
        });
    </script>
{/literal}

{literal}
    <style>
        #right_column{display: none;}
    </style>
{/literal}

{if $popup}
    {literal}   
    <style>
        #header, #footer, #launcher, #right_column, .breadcrumb { display: none!important; }
        .searchimg{ display: none!important; }
        .right_column{display: none;}
    
        @media(max-width:400px){
            .name_sponsor{padding-left: 10px;}
            .center_column{width: 95%;}
            .t-name{font-size: 12px;}
            }
        @media(max-width:300px){
        .center_column{width: 78%;}
        .name_sponsor{padding-left: 5px;font-size: 11px;padding-right: 0px;}
        .t-name {font-size: 10px;padding: 0;text-align: left;}
        .btn-cancel-modal{padding: 5px;}
        .btn-account{font-size: 12px;}
        .btn-confirm-modal{padding: 5px;}
        .left-info{font-size: 10px;}
        .text-infouser{font-size: 10px;}
        .info-cash{padding-left: 0px;}
        .cash-point{padding-left: 5px;}
        .title-myinfo{font-size: 10px;}
        .title_transfer{font-size: 14px;}
        .btn_modal_f{font-size: 12px;}
        }
        </style>
    {/literal}
{/if}
    
