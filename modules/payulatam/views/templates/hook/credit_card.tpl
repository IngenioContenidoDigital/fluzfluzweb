{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($error)}
<p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}

 <script src="/modules/payulatam/js/jquery.creditCardValidator.js"></script>

{literal}
    <script type="text/javascript">
        function validar_texto(e){
            tecla = (document.all) ? e.keyCode : e.which;
            //Tecla de retroceso para borrar, siempre la permite
            if ((tecla==8)||(tecla==0)){
                return true;
            }
            // Patron de entrada, en este caso solo acepta números
            patron =/[0-9]/;
            tecla_final = String.fromCharCode(tecla);
            return patron.test(tecla_final);
        }

         function pulsar(e) {
            tecla = (document.all) ? e.keyCode :e.which;
            return (tecla!=13);
        } 
    </script>
{/literal}
<script type="text/javascript">
    $(function(){


        $('#numerot').validateCreditCard(function(result) {
         if(result.valid){

          
         if(result.card_type != null && result.valid && result.length_valid && result.luhn_valid){
            $('#ctNt').removeClass("form-error");
            $('#ctNt').addClass("form-ok");
         }else{
            $('#ctNt').removeClass("form-ok");
             $('#ctNt').addClass("form-error");
         } 
          }
        });

      
    var validator = $('#formPayU').validate({
{literal}
                  wrapper: 'div',
            errorPlacement: function (error, element) {
                error.addClass("alert alert-danger");
                error.insertAfter(element);
            },
{/literal}            
            rules :{                
                numerot : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 14 , //para validar campo con minimo 3 caracteres
                    maxlength : 16 //para validar campo con maximo 9 caracteres                                   
                },                
                codigot : {
                    required : true,
                    number : true,   //para validar campo solo numeros
                    minlength : 3 , //para validar campo con minimo 3 caracteres
                    maxlength : 4 //para validar campo con maximo 9 caracteres                                   
                },
                nombre : {
                  required : true
                },
                Month : {
                    required : true
                },
                year : {
                    required : true
                  },
                cuotas : {
                  required : true
                }
            },
            messages: {
                        numerot: { 
                            required: "Se requiere el numero de tarjeta",
                            number : "Solo se aceptan números",
                            minlength: "Es demasiado corto",
                            maxlength: "Es demasiado largo",
                        },
                        codigot: { 
                            required: "Se requiere el código de verificación",
                            number : "Solo se aceptan números",
                            minlength: "Es demasiado corto",
                            maxlength: "Es demasiado largo",
                        },
                        nombre : {
                            required : "Se requiere el nombre del titular."
                        },
                        Month : {
                        	required : "Se requiere el mes"
                        },
                        year : {
                        	required : "Se requiere el año"
                        },
                        cuotas : {
                            required : "Selecciona el numero de cuotas"
                        }
                    },
        });


 $("#formPayU").submit(function(event) {

      $('#numerot').validateCreditCard(function(result) {

                 if(result.card_type != null && result.valid && result.length_valid && result.luhn_valid){
            $('#ctNt').removeClass("form-error");
            $('#ctNt').addClass("form-ok");
         }else{
            $('#ctNt').removeClass("form-ok");
             $('#ctNt').addClass("form-error");
              event.preventDefault();
         } 
        });
    });

    });

</script>

    <div class="container">      
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-md-8"> 
                <form role="form" class="form-horizontal" method="POST" action="{$base_dir|regex_replace:"/[http://]/":""|escape:'htmlall':'UTF-8'}/modules/payulatam/credit_card.php" id="formPayU" autocomplete="off"> 
                    <div class="form-group">
                        <label for="nombre" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Nombre Del Titular</label>
                        <div class="col-xs-12 col-sm-6"><input type="text" name="nombre" id="nombre" class="form-control" placeholder="(Tal cual aparece en la tarjeta de Crédito)"/></div>
                    </div>
                    <div class="form-group required" id="ctNt">
                        <label for="numerot" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Número De Tarjeta De Crédito</label>
                        <div class="col-xs-12 col-sm-6"><input type="text" name="numerot" id="numerot" class="form-control"/></div>
                    </div> 
                    <div class="form-group">
                        <label for="datepicker" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Fecha De Vencimiento</label>
                            <div class="col-xs-6 col-sm-2">{html_select_date prefix=NULL end_year="+15" month_format="%m"
                            year_empty="year" year_extra='id="year" class="form-control"'
                            month_empty="mes" month_extra='id="mes" class="form-control"'
                            display_days=false  display_years=false
                            field_order="DMY" time=NULL}</div>
                            <div class="col-xs-6 col-sm-2">{$year_select}</div>
                    </div>
                    <div class="form-group">
                        <label for="codigot" class="control-label col-xs-12 col-sm-6 " style="text-align: left;">Código De Verificación</label>
                        <div class="col-xs-12 col-sm-6"><input type="password" name="codigot" id="codigot" class="form-control"/></div>
                    </div>
                    <div class="form-group">
                        <label for="cuotas" class="control-label col-xs-12 col-sm-6 " style="text-align: left;">Número De Cuotas</label>
                        <div class="col-xs-12 col-sm-6"><select name="cuotas" id="cuotas" class="form-control">
                            {for $foo=1 to 36}
                                <option value="{$foo|string_format:'%2d'}">{$foo|string_format:"%2d"}</option>
                            {/for}
                        </select></div>
                    </div>
                   <div class="form-group">
                        <label for="submitTc" class="control-label hidden-xs col-sm-6 " style="text-align: left;"></label>
                        <div class="col-xs-12 col-sm-6">
                            <button type="submit" id="submitTc" class="button btn btn-default standard-checkout button-medium">
                                <span> Pagar Ahora
                                    <i class="icon-chevron-right right"></i>
                                </span>
                            </button>
                        </div>
                    </div>                                             
                   <div style="display: none;">
                        <input type="hidden" value="{$deviceSessionId}"  name="deviceSessionId" />
                        <p style="background:url(https://maf.pagosonline.net/ws/fp?id={$deviceSessionId}80200"></p> 
                        <img src="https://maf.pagosonline.net/ws/fp/clear.png?id={$deviceSessionId}80200"> 
                        <script src="https://maf.pagosonline.net/ws/fp/check.js?id={$deviceSessionId}80200"></script>
                        <object type="application/x-shockwave-flash" 
                            data="https://maf.pagosonline.net/ws/fp/fp.swf?id={$deviceSessionId}80200" width="1" height="1" id="thm_fp">
                            <param name="movie" value="https://maf.pagosonline.net/ws/fp/fp.swf?id={$deviceSessionId}80200"/>
                        </object>
                    </div>

                </form>
            </div>
        </div>
    </div>

{/if} 

<style>
    .selector { width: 100%!important; }
    .selector span { width: 100%!important; }
    .radio-inline { margin-left: 15px!important; }
    .radio { padding-top: 3px!important; }
</style>