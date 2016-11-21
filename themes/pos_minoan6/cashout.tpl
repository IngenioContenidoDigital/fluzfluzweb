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
<div id="prueba" style="display:none;">{$base_dir_ssl}</div>
{capture name='blockPosition4'}{hook h='blockPosition4'}{/capture}
            {if $smarty.capture.blockPosition4}
                {$smarty.capture.blockPosition4}
            {/if}
<ul class="step clearfix" id="order_step" style="padding-left:0px; padding-top: 20px;">
        <li  class="{if $current_step=='summary'}step_current {elseif $current_step=='login'}step_done_last step_done{else}{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address' || $current_step=='login'}step_done{else}step_todo{/if}{/if} first">
		{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address' || $current_step=='login'}
		<a href="{$link->getPageLink('order', true)}">
			<em>01.</em> {l s='Monto'}
		</a>
		{else}
                    <span id="step-one"><em>01.</em> {l s='Monto'}</span>
		{/if}
	</li>
	<li class="{if $current_step=='login'}step_current{elseif $current_step=='address'}step_done step_done_last{else}{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address'}step_done{else}step_todo{/if}{/if} second">
		{if $current_step=='payment' || $current_step=='shipping' || $current_step=='address'}
		<a href="{$link->getPageLink('order', true, NULL, "{$smarty.capture.url_back}&step=1{if $multi_shipping}&multi-shipping={$multi_shipping}{/if}")|escape:'html':'UTF-8'}">
			<em>02.</em> {l s='Forma de Redencion'}
		</a>
		{else}
                    <span class="span-second"><em>02.</em> {l s='Forma de Redencion'}</span>
		{/if}
	</li>
	<li id="step_end" class="{if $current_step=='payment'}step_current{else}step_todo{/if} last">
		<span class="span-confirmation"><em>03.</em> {l s='Confirmacion'}</span>
	</li>
</ul>
<div class="row">
    <!--<h1 class="page-heading">{l s='Cash Out'}</h1>-->
    {if !$payment_button_allowed}
        <div style="padding: 15px 0; color: #EE4A42;">
            <span>{l s="Aun no alcanzas el umbral requerido para redimir tus Fluz por dinero en efectivo."}</span>
        </div>
    {/if}     
</div>
{if $rewards}
    {if $payment_button_allowed}
    <div id="rewards-step1">
        <div class="row">    
            <div class="cashoutDiv col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="cashoutTitle col-lg-6 col-md-6 col-sm-6 col-xs-6">{l s="Fluz Totales: "}</span>
                <span class="cashoutPoint col-lg-6 col-md-6 col-sm-6 col-xs-6" id="cash-point"> {$totalAvailable}</span>
            </div>
            <div class="cashoutDiv col-lg-12 col-md-12 col-xs-12">
                <span class="cashoutTitle col-lg-6 col-md-6 col-sm-6 col-xs-6">{l s="Redencion Total en Efectivo: "}</span>
                <span class="cashoutPoint col-lg-6 col-md-6 col-sm-6 col-xs-6" id="cash-price"> {displayPrice price=$pago currency=$payment_currency}</span>
            </div>
        </div>
        <div class="row">
            <form method="post" id="voucher-cash" class="contenedorCash" name="voucher-cash">
                <h2 class="select-amount">{l s="Seleccione Cantidad"}</h2>
                <div id="alert" class="alert-validation"style="display: none;">{l s="Seleccione una opcion de pago"}</div>
                <div class="col-lg-5 col-md-5 col-sm-10 col-xs-12 full-amount" id="all-point">
                    <div class="title-full">
                        <input type="radio" id="all-option" name="selector" value="0" required>
                        <span style="margin-left:20px;">{l s="Seleccion Monto"}</span>
                    </div>
                    <span class="col-lg-6 col-md-6 col-sm-6 col-xs-12 avail-full" id="cash-pointselected"> {$totalAvailable}&nbsp;&nbsp;{l s="Puntos."}</span>
                    <span class="col-lg-6 col-md-6 col-sm-6 col-xs-12 avail-price"> {displayPrice price=$pago currency=$payment_currency}</span>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-10 col-xs-12 full-amount" id="partial-point">
                    <div class="title-full">
                        <input type="radio" id="partial-option" name="selector" value="1" required>
                        <span style="margin-left:20px;">{l s="Seleccion Monto Parcial"}</span>
                    </div>
                    <div class="row">
                        <input class="slider-cash col-lg-6 col-md-5 col-sm-5 col-xs-5" type="range" id="rangeSlider" value="1800" min="1800" max="{$totalAvailable}" step="100" data-rangeslider>
                        <div class="info-cash col-lg-5 col-md-6 col-sm-6 col-xs-6">
                                <span class="money-cash col-lg-2 col-md-1 col-sm-2 col-xs-2">$</span>
                                <input class="output-cash col-lg-6 col-md-6 col-sm-6 col-xs-5" type="text" name="valorSlider" id="valorSlider" value=""/>
                                <span class="col-lg-3 cash-point col-md-3 col-sm-3 col-xs-4"> &nbsp;{l s="de"}&nbsp;{$totalAvailable}&nbsp;{l s="Pts."}</span>
                        </div>
                                <span class="cashout-money col-lg-12 col-md-12 col-sm-12 col-xs-12"> {l s ="COP"}&nbsp;<span id="value-cash"></span></span>
                                <span class="cashout-money col-lg-12" style="display:none;"> {l s ="COP"}&nbsp;<span id="value-money">{(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</span></span>
                    </div>
                </div>
            </form>            
        </div>            
            {literal}
                <script>
                    $('.contenedorCash').on("click",'input:radio[name=selector]',function()
                        {   
                            var val = $('input:radio[name=selector]:checked').val();
                            var value_money = $('#value-money').text();
                            if(val == 0){
                               var total_point = $('#cash-point').text();
                               var total_point2 =  $('#cash-pointselected').text();
                               var result = ((parseInt(total_point))-(parseInt(total_point2)));
                               var cash_result = 0;
                               var cash_confirmation = ((parseInt(total_point))*(value_money));
                               var total = cash_confirmation - 7000;
                               $("#all-point").addClass("border-select");
                               $("#partial-point").removeClass("border-select");
                               $("#total-valor").html(total);
                               $("#ptos_result").html(result);
                               $("#ptos_prueba").html(result);
                               $("#cash_result").html(cash_result);
                               $("#points_used").html(total_point);
                               $('#pto_total').val(total_point);
                               $("#value-confirmation").html(cash_confirmation);
                            }
                            else if(val==1){
                              var total_point = $('#cash-point').text();
                              $("#partial-point").addClass("border-select");
                              $("#all-point").removeClass("border-select");
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
                            }
                            $('#radio').val(val);
                        });
                </script>
                <script type="text/javascript">
                    $(document).ready(function(){
                        document.getElementById( 'valorSlider' ).value=1800 ;
                        var value_money = $('#value-money').text();
                        $('#rangeSlider').change(function() 
                        {
                          var value = $(this).val();
                          $('#valorSlider').val($(this).val());
                          $('#pt_parciales').val(value);
                          var mult = (value * value_money); 
                          $("#value-cash").html(mult);
                          $("#value-confirmation").html(mult);
                          var total = mult - 7000;
                          $("#total-valor").html(total);
                        });
                        
                    });
                </script>
            {/literal}
        <div class="row" style="margin-top:40px;">
            <div class="cashoutDiv col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="cashoutTitle col-lg-6 col-md-6 col-sm-6 col-xs-6">{l s="Fluz Disponibles despues de Redencion: "}</span>
                <span class="cashoutPoint col-lg-6 col-md-6 col-sm-6 col-xs-6"><span id="ptos_result"></span>&nbsp;{l s ="Puntos"}</span>
            </div>
            <div class="cashoutDiv col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="cashoutTitle col-lg-6 col-md-6 col-sm-6 col-xs-6">{l s="Redencion en efectivo Disponible: "}</span>
                <span class="cashoutPoint col-lg-6 col-md-6 col-sm-6 col-xs-6"> {l s ="COP $"}&nbsp;&nbsp;<span id="cash_result"></span></span>
            </div>
        </div>    
{if isset($payment_error)}
	{if $payment_error==1}
	<p class="error">{l s='Please fill all the required fields' mod='allinone_rewards'}</p>
	{elseif $payment_error==2}
	<p class="error">{l s='An error occured during the treatment of your request' mod='allinone_rewards'}</p>
	{/if}
{/if}

	<div id="general_txt" style="padding-bottom: 20px">{$general_txt|escape:'string':'UTF-8'}</div>

{if $return_days > 0}
	<p>{l s='Rewards will be available %s days after the validation of each order.'  sprintf={$return_days|intval} mod='allinone_rewards'}</p>
{/if}
	    
	{if $voucher_minimum_allowed}
            <div id="min_transform" style="clear: both">{l s='The minimum required to be able to transform your rewards into vouchers is' mod='allinone_rewards'} <b>{$voucherMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}
	{if $payment_minimum_allowed}
            <div id="min_payment" style="clear: both">{l s='The minimum required to be able to ask for a payment is' mod='allinone_rewards'} <b>{$paymentMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}
        
        <p class="cart_navigation clearfix">
            <a class="button btn btn-default standard-checkout button-medium" id="nextStep" name="nextStep" title="{l s='Next Step'}">
                    <span>{l s='Confirmar Monto'}<i class="icon-chevron-right right"></i></span>
            </a>
        </p>
</div>
            <div class="row" id="cheque-oculto" style='display:none; margin-bottom: 10px;'>
                <div onClick="$('#cheque_form').toggle()" class="cheque-cashout col-lg-12">{l s='Mailed Cheque'}</div>
                <form id="cheque_form" class="std form-cheque" method="post" action="{$pagination_link|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" style="display: {if isset($smarty.post.payment_details)}block{else}none{/if}">
                    <fieldset class="cheque-style">
                        <div class="row cheque-row" style="margin-top:40px;">
                            <div class="col-lg-4" style="text-align: left;">
                               <input type="radio" name="cheque" id="cheque" value="1"><span style="margin-left:20px;color: #333333;font-size: 14px;">{l s="same as billing address"}</span>
                            </div>
                            <div class="col-lg-8">
                                <label class="col-lg-12 required label-cheque">{l s='Cheque Written to'}</label>
				<input class="col-lg-12 input-cash" type="text" class="is_required validate form-control" data-validate="isName" id="name-cheque" name="name-cheque"/>
                            </div>
                        </div>
                        <div class="row cheque-row">
                            <div class="col-lg-3">
                                <label class="col-lg-12 required label-cheque" for="firstname">{l s='Nombre'}</label>
				<input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isName" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                            </div>
                            <div class="col-lg-3 required">
                                <label class="col-lg-12 required label-cheque" for="lastname">{l s='Apellido'}</label>
				<input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isName" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
                            </div>
                            <div class="col-lg-3">
                                <label class="required col-lg-12 label-cheque" for="email">{l s='Email'}</label>
                                <input type="email" class="col-lg-12 input-cash is_required validate" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" OnFocus="this.blur()"/>
                            </div>
                            <div class="col-lg-3">
                                <label class="required col-lg-12 label-cheque" for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} {/if}</label>
                                <input type="number" class="col-lg-12 input-cash is_required validate" data-validate="isPhoneNumber" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                            </div>
                        </div>        
                        <div class="row cheque-row">
                            <div class="col-lg-3">
                                <label class="col-lg-12 required label-cheque" for="address1">{l s='Address'}</label>
                                <input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isAddress" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                            </div>
                            <div class="col-lg-3">
                                <label class="col-lg-12 required label-cheque" for="address2">{l s='Address (Line 2)'}</label>
                                <input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isAddress" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                            </div>
                            <div class="col-lg-3">
                                <label class="col-lg-12 required label-cheque" for="city">{l s='City'}</label>
                                <input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isCityName" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
                            </div>
                            <div class="col-lg-3">
                                <label class="col-lg-12 required label-cheque" for="country">{l s='Pais'}</label>
                                <input type="text" class="col-lg-12 input-cash is_required validate" data-validate="isCountryName" name="country" id="country" value="{if isset($smarty.post.country)}{$smarty.post.country}{/if}" />
                            </div>
                        </div> 
                    </fieldset>
                    <div class="row">
                        <div class="col-lg-12">
                            <a id="next-step2" class="btn-order2 col-lg-3">{l s="Siguiente Paso"}</a>
                        </div>
                    </div>
                </form>
            </div>
    <div class="row" id="card-oculto" style='display:none;'>
        <div onClick="$('#card_form').toggle()" class="cheque-cashout col-lg-12">{l s='Transferencia Electronica'}</div>
        <form id="card_form" class="std form-cheque" method="post" action="{$pagination_link|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" style="display: {if isset($smarty.post.payment_details)}block{else}none{/if}">
            <fieldset class="cheque-style">
                <div id="alert2" class="alert-validation2" style="display:none;">{l s="Ingrese sus Datos Completos"}</div>
                <div class="row cheque-row" style="margin-top:40px;">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 required label-cheque" for="firstnameCard">{l s='Nombre'}</label>
                        <input type="text" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 input-cash is_required validate" data-validate="isName" id="firstnameCard" name="firstnameCard" value="{if isset($smarty.post.firstnameCard)}{$smarty.post.firstnameCard}{/if}" required/>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 required">
                        <label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 required label-cheque" for="lastnameCard">{l s='Apellido'}</label>
                        <input type="text" class="col-lg-12 col-sm-12 col-md-12 col-xs-12 input-cash is_required validate" data-validate="isName" id="lastnameCard" name="lastnameCard" value="{if isset($smarty.post.lastnameCard)}{$smarty.post.lastnameCard}{/if}" required/>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <label for="numerot" class="required col-lg-12 col-md-12 col-sm-12 col-xs-12 label-cheque">{l s='Numero de Cuenta Bancaria'}</label>
                        <input type="text" name="numeroCard" id="numeroCard" class="col-lg-12 col-xs-12 col-md-12 col-sm-12 input-cash is_required validate" required/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 style-bank">
                        <label for="pse_bank" class="required col-lg-12 label-cheque">Banco</label>
                        <div class="col-lg-12"  style="padding-left:0px;">
                            <select id="pse_bank" name="pse_bank" onchange="bank();" class="col-lg-12" required>
                                <option value="">Seleccione una entidad</option>
                                <option value="BA">BANCO AGRARIO</option>
                                <option value="BCS">BANCO CAJA SOCIAL</option>
                                <option value="BCSD">BANCO CAJA SOCIAL DESARROLLO</option>
                                <option value="BC">BANCO COLPATRIA UAT</option>
                                <option value="BCAV">BANCO COMERCIAL AV VILLAS S.A.</option>
                                <option value="BD">BANCO DAVIVIENDA</option>
                                <option value="BB">BANCO BOGOTA</option>
                                <option value="BBA">BANCO BANCOLOMBIA</option>
                                <option value="BP">BANCO POPULAR</option>
                                <option value="BBVA">BANCO BBVA COLOMBIA S.A.</option>
                                <option value="BBVA">BANCO COOPERATIVO COOPCENTRAL</option>
                                <option value="BBVA">BANCO COOMEVA S.A.</option>
                                <option value="BBVA">BANCO CORPBANCA</option>
                                <option value="BBVA">BANCO FALABELLA</option>
                                <option value="BBVA">BANCO PSE</option>
                                <option value="BBVA">BANCO PICHINCHA S.A.</option>
                                <option value="BBVA">BANCO TEQUENDAMA</option>
                                <option value="BBVA">BANCO PROCEDIT COLOMBIA</option>
                                <option value="BBVA">BANCO GNB SUDAMERIS</option>
                                <option value="BBVA">BANCO M A P G</option>
                                <option value="BBVA">BANCO GNB COLOMBIA</option>
                                <option value="BBVA">BANCO NVO_WLSD</option>
                            </select> 
                            <input type="hidden" value="" name="name_bank" id="name_bank"/>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 style-bank">
                        <label for="pse_bank_account" class="required col-lg-12 label-cheque">Seleccion Tipo de Cuenta</label>
                        <div class="col-lg-12"  style="padding-left:0px;">
                            <select id="pse_bank_account" name="pse_bank_account" class="col-lg-12" required>
                                <option value="">Seleccione una entidad</option>
                                <option value="Cuenta Ahorros">CUENTA AHORROS</option>
                                <option value="Cuenta Corriente">CUENTA CORRIENTE</option>
                            </select> 
                            <input type="hidden" value="" name="name_bank" id="name_bank"/>
                        </div>
                    </div>
                    
        </div>    
            </fieldset>
            <div class="col-lg-6" id="stepBack">
                <a class="btn-order2 col-lg-6" id="step-back" name="step-back" style="float:left;">
                   <i class="icon-chevron-left left"></i><span>{l s='Back'}</span>
                </a>
            </div>            
            <div class="col-lg-6">
                <a class="btn-order2 col-lg-6" id="nextstep2" name="next-step2" style="float:right;">
                   <span>{l s="Siguiente Paso"}</span><i class="icon-chevron-right right"></i>
                </a>
            </div>
        </form>
    </div>
    </div>
    <div id="payment_cash" style="display:none;">
        <form id="payment_form" class="std" method="post" action="{$link->getPageLink('cashout', true)|escape:'html':'UTF-8'}" enctype="multipart/form-data">
            <input type="hidden" id="nombre-customer" name="nombre-customer" value=""/>
            <input type="hidden" id="lastname-customer" name="lastname-customer" value=""/>
            <input type="hidden" id="numero_tarjeta" name="numero_tarjeta" value=""/>
            <input type="hidden" id="bank_cash" name="bank_cash" value=""/>
            <input type="hidden" id="bank_account" name="bank_account" value=""/>
            <input type="hidden" id="pt_parciales" name="pt_parciales" value=""/>
            <input type="hidden" id="pto_total" name="pto_total" value=""/>
            <input type="hidden" id="radio" name="radio" value=""/>
            <div class="row confirmation-cashout">
                <div class="row c-cashout">
                    <label class="col-lg-8 col-md-8 col-sm-8 col-xs-7 l-step3">{l s="Fluz Disponibles"}</label>
                    <span class="p-step3 col-lg-4 col-md-4 col-sm-4 col-xs-5"><span id="ptos_prueba"></span></span>
                </div>
                <div class="row c-cashout">
                    <label class="col-lg-8 col-md-8 col-sm-8 l-step3 col-xs-7">{l s="Fluz Utilizados en Redencion"}</label>
                    <span class="col-lg-4 col-md-4 col-sm-4 col-xs-5 p-step3">-<span id="points_used"></span></span>
                </div>
                <div class="row c-cashout">
                    <label class="col-lg-8 col-md-8 col-sm-8 l-step3 col-xs-7">{l s="Monto Redimido en Efectivo"}</label>
                    <span class="col-lg-4 col-md-4 col-sm-4 col-xs-5 pstep3"><span id="value-confirmation"></span></span>
                </div>
                <div class="row c-cashout">
                    <label class="col-lg-8 col-md-8 col-sm-8 l-step3 col-xs-7">{l s="Costo Transferencia"}</label>
                    <span class="col-lg-4 col-md-4 col-sm-4 col-xs-5 pstep3">-{$costoTransferencia}</span>
                </div>
                <div class="row c-cashout">
                    <label class="col-lg-8 col-md-8 col-sm-8 col-xs-7 l-step3">{l s="Redencion Total en Efectivo"}</label>
                    <span class="col-lg-4 col-md-4 col-sm-4 col-xs-5 pstep3"><span id="total-valor"></span></span>
                </div>
            </div>
            <div class="col-lg-6" id="step2" style="display:none;">
                <a class="btn-order2 col-lg-6" id="step2" name="step2" style="float:left;">
                   <i class="icon-chevron-left left"></i><span>{l s='Back'}</span>
                </a>
            </div> 
            <div class="col-lg-6 paid-btn">    
                <input class="col-lg-6 button btn-cash" type="submit" value="{l s='REQUEST DEPOSIT'}" name="submitPayment" id="submitPayment" style="float:right;">   
            </div>
        </form>
    </div>
    <!--<div id="payment" style="display:none;">
            <a onClick="$('#payment_form').toggle()">{l s='Payment of your available rewards'} <span>{displayPrice price=$pago currency=$payment_currency}</span></a>
            <form id="payment_form" class="std" method="post" action="{$pagination_link|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
                    <fieldset>
                        <h4 class="directDep">{l s='Direct Deposit'}</h4>
                         <div class="contDirectDep">

                            <div id="payment_txt">{$payment_txt|escape:'string':'UTF-8'}</div>
                            <div class="required formCash form-group col-lg-6 col-md-6">
                                <p class="required">
                                <label class="required textCard">
                                    {l s='Numero de Tarjeta'}
                                </label>
                                <div class="col-xs-12 col-sm-7 col-md-12 col-lg-8" style="padding-left:0px;">
                                    <input type="text" pattern="[0-9]{literal}{13,16}{/literal}" class="imageCard formCardCash form-control" id="payment_details" name="payment_details" autocomplete="off" required/>
                                </div>
                                </p>
                            </div>
                            <p class="{if $payment_invoice}required{/if} text">
                                    <label style="display:none;" for="payment_invoice">{l s='Invoice' mod='allinone_rewards'} ({displayPrice price=$totalForPaymentDefaultCurrency currency=$payment_currency}) {if $payment_invoice}<sup>*</sup>{/if}</label>
                                    <input id="payment_invoice" name="payment_invoice" type="file" accept="application/pdf" required>
                            </p>
                            <input class="button" type="submit" value="{l s='REQUEST DEPOSIT'}" name="submitPayment" id="submitPayment">
                         </div>
                    </fieldset>
            </form>
    </div>-->
        {/if}                         
    {/if}
        
    </div>
        {if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
        <ul class="footer_links clearfix">
                <li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account' mod='allinone_rewards'}</span></a></li>
                <li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}"><span><i class="icon-chevron-left"></i> {l s='Home' mod='allinone_rewards'}</span></a></li>
        </ul>
        {else}
    <ul class="footer_links clearfix">
            <li><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="" class="icon" /> {l s='Back to your account' mod='allinone_rewards'}</a></li>
            <li class="f_right"><a href="{$base_dir|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'html':'UTF-8'}icon/home.gif" alt="" class="icon" /> {l s='Home' mod='allinone_rewards'}</a></li>
    </ul>
{/if}
{literal}
    <style>
        #left_column{display: none !important;}
        #center_column{min-width: 100% !important; margin: 0px;}
        #columns{margin-bottom: 0px !important; min-width: 100%;}
        .banner-home{margin: 0px;}
        .footer_links{display: none;}
        #min_payment{display: none;}
        .rewards{width: 80%; margin: 0 auto;margin-top: 30px;margin-bottom: 30px;}
        .rewards table.std td { font-size: 11px; line-height: 13px; padding: 10px !important; background:#f9f9f9; border: #fff 5px solid; border-right:none; border-left:none;}
        .page-heading{margin:0;}
        .breadcrumb{margin-left: 11%; font-size:12px;}
    </style>
{/literal}
{literal}
    <script>
            $('#numCard').on('keyup',function(){
                $(this).removeClass('visa');
                $(this).removeClass('mastercard');
                $(this).removeClass('amex');
                $(this).removeClass('discover');
                if($(this).val()===""){
                    $(this).removeClass('visa');
                }else{
                    $(this).validateCreditCard(function(result) {    
                        switch(result.card_type.name){
                            case 'visa':
                                $(this).addClass('visa');
                                break;
                            case 'mastercard':
                                $(this).addClass('mastercard');
                                break;
                             case 'amex':
                                $(this).addClass('amex');
                                break;
                             case 'discover':
                                $(this).addClass('discover');
                                break;
                            default:
                                $(this).removeClass('visa');
                                $(this).removeClass('mastercard');
                                $(this).removeClass('amex');
                                $(this).removeClass('discover');
                                break;
                        }
                    })
                }
            });
    </script>
    
{/literal}
{literal}
    <script>
        $(document).ready(function(){
                
		$("#nextStep").on( "click", function() {
                    var val = $('input:radio[name=selector]:checked').val();
                    if (val == 0 || val == 1){
                        $('#rewards-step1').hide();
                        $('#payment_cash').hide();
                        document.getElementById('card-oculto').style.display = 'block';
                        document.getElementById('stepBack').style.display = 'block';
                        $('.second').addClass('second-cash');
                        $('.span-second').addClass('span-cash');
                        $('.first').removeClass('first');
                    }
                    else{
                        document.getElementById('alert').style.display = 'block';
                    }
		 });
                 $("#nextstep2").on( "click", function() {
                    
                    var bank = $( "#pse_bank option:selected" ).text();
                    var bank_account = $( "#pse_bank_account option:selected" ).text();
                    var name = $("#firstnameCard").val();
                    var lastname = $("#lastnameCard").val();
                    var num = $("#numeroCard").val();
                    var url = document.getElementById("prueba").innerHTML;
                    if ( name == "" || lastname == "" || num == "" || bank == "Seleccione una entidad") {
                        document.getElementById('alert2').style.display = 'block';
                    }
                    else{
                    $('#card-oculto').hide();
                    $('#rewards-step1').hide();
                    $('#stepBack').hide();
                    document.getElementById('payment_cash').style.display = 'block';
                    document.getElementById('step2').style.display = 'block';
                    $("#nombre-customer").val(name);
                    $("#lastname-customer").val(lastname);
                    $("#numero_tarjeta").val(num);
                    $("#bank_cash").val(bank);
                    $("#bank_account").val(bank_account);
                    $('.second').removeClass('second-cash');
                    $('.first').removeClass('first');
                    $('.span-second').removeClass('span-cash');
                    $('.span-confirmation').addClass('second-cash');
                    }
		 });
                 $("#step-back").on( "click", function() {
                    $('#rewards-step1').show();
                    $('#payment_form').hide();
                    $('#stepBack').hide();
                    $('#payment_cash').hide();
                    $('#card-oculto').hide();
                    $('#step-one').addClass('second-cash');
                    $('.second').removeClass('second-cash');
                    $('.span-second').removeClass('span-cash');
		 });
                 $("#step2").on( "click", function() {
                    $('#rewards-step1').hide();
                    $('#payment_cash').hide();
                    $('#step2').hide();
                    document.getElementById('card-oculto').style.display = 'block';
                    document.getElementById('stepBack').style.display = 'block';
                    $('.span-confirmation').removeClass('second-cash');
                    $('#step-one').removeClass('second-cash');
                    $('.second').addClass('second-cash');
                    $('.span-second').removeClass('span-cash');
                    $('.span-second').addClass('span-cash');
		 });
	});
    </script>
{/literal}    
{literal}
    <style>
    div.uploader span.filename{margin-left: 18px !important;}
    </style>
{/literal}
{literal}
    <script>       
        $("#toUse").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#toUse').val();
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
            }else{
                valor2*=-1;
                $('#toUse').val(valor2);
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
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

<!-- END : MODULE allinone_rewards -->
