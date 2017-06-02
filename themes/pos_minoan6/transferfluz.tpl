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

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div class="row"> 
    <h3 class="title-myinfo"> MI INFORMACION PERSONAL </h3> 
</div>
<div class="row info-personal">
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Username'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> {$username} </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Mis Fluz'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> {$pointsAvailable} </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Dinero en Fluz'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </div>
        </div>
    </div>    
</div>
<form method="post" id="trasnferfluz" class="contenedorCash" name="trasnferfluz">
    <input type="hidden" id="pt_parciales" name="pt_parciales" value=""/>
    <input type="hidden" id="pto_total" name="pto_total" value=""/>
    <input type="hidden" id="id_customer" name="id_customer" value="{$id_customer}"/>

    <div class="row"> 
        <h3 class="title-myinfo"> TRANSFERENCIA FLUZ A FLUZZERS </h3> 
    </div>
    <div class="row info-personal">
        <div class="row row-personal">
            <div class="col-lg-6">
                    <div class="left-info">{l s='Fluzzer Destino'}</div>
            </div>
            <div class="col-lg-6">
                <div class="text-infouser">             
                    <input type="text" name="busqueda" id="busqueda" value="" class="is_required validate form-control input-infopersonal textsearch" placeholder="{l s='Search member'}" value="{$searchnetwork}"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
                    <div id="resultados" class="result-find"></div>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6 style-fluz">
                    <div class="left-info">{l s='Cantidad de Fluz a enviar'}</div>
            </div>
            <div class="col-lg-6">
                <input class="slider-cash col-lg-6 col-md-5 col-sm-5 col-xs-5" type="range" id="rangeSlider" value="100" min="100" max="{$pointsAvailable}" step="100" data-rangeslider>
                <div class="info-cash col-lg-5 col-md-6 col-sm-6 col-xs-6">
                        <input class="output-cash col-lg-6 col-md-6 col-sm-6 col-xs-5" type="text" name="valorSlider" id="valorSlider" value=""/>
                        <span class="col-lg-6 cash-point col-md-3 col-sm-3 col-xs-4"> &nbsp;{l s="de"}&nbsp;{$pointsAvailable}&nbsp;{l s="Fluz."}</span>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6">
                    <div class="left-info">{l s='Dinero en Fluz'}</div>
            </div>
            <div class="col-lg-6 padding-cash">
                <div class="text-infouser">  
                    <span class="cashout-money col-lg-12 col-md-12 col-sm-12 col-xs-12"> {l s ="COP $"}&nbsp;<span id="value-cash"></span></span>
                    <span class="cashout-money col-lg-12" style="display:none;"> {l s ="COP"}&nbsp;<span id="value-money">{(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</span></span>
                </div>
            </div>
        </div>    
    </div>
    <div class="row btn-sendfluz">
        <div class="col-lg-6">
            <button class="btn btn-default btn-account" type="submit" name="submitFluz" id="submitFluz">
                <span style="cursor:pointer;font-size: 15px;color: #fff; font-family: 'Capitalized';font-weight: bold;">
                    {l s="Enviar Fluz"}
                </span>
            </button>
        </div>
    </div>
</form>
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
            document.getElementById( 'valorSlider' ).value=100 ;
            var value_money = $('#value-money').text();
            $('#rangeSlider').change(function() 
            {
              var value = $(this).val();
              $('#valorSlider').val($(this).val());
              $('#pt_parciales').val(value);
              var mult = (value * value_money); 
              $("#value-cash").html(mult);
              $("#value-confirmation").html(mult);
              var total = mult;
              $("#total-valor").html(total);
            });

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
                                    content += '<div class="resultados" onclick="myFunction()">'+data[key].username+'</div>';
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
        function myFunction() {
            
        }
    </script>
{/literal}