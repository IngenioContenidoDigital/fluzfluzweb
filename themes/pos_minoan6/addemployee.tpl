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

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<form method="post" id="addemployeebusiness" class="contenedorEmployeeBusiness" name="addemployeebusiness">
        <input type="hidden" value="" id="ptosusedhiddenadde" name="ptosusedhiddenadde"/>
        <div class="row row-form-employee">
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="firstname">{l s='Nombre'}</label>
                <input type="text" onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode == 32))' class="col-lg-6 is_required validate form-employee" data-validate="isName" autocomplete="off" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="lastname">{l s='Apellido'}</label>
                <input type="text" onkeypress='return ((event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode == 32))' class="col-lg-6 is_required validate form-employee" data-validate="isName" autocomplete="off" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="username">{l s='Username'}</label>
                <input type="text" class="col-lg-6 is_required validate form-employee" data-validate="isUser" autocomplete="off" id="username" name="username" value="{if isset($smarty.post.username)}{$smarty.post.username}{/if}" />
            </div>
            <div class="row form-group">
                <label class="col-lg-12 l-form-employee" for="email">{l s='Email'}</label>
                <input class="col-lg-6 is_required validate account_input form-employee" data-validate="isEmail" autocomplete="off" type="email" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" />
            </div>
            <div class="row form-group">
                <label  class="col-lg-12 l-form-employee" for="phone_invoice">Celular</label>
                <input type="number" class="col-lg-6 form-employee" name="phone_invoice" id="phone_invoice" autocomplete="off" value="{if isset($smarty.post.phone_invoice) && $smarty.post.phone_invoice}{$smarty.post.phone_invoice}{/if}" />
            </div>
            <div class="row required dni form-group">
                <label class="col-lg-12 l-form-employee" for="dni">{l s='Cedula'}</label>
                <input class="col-lg-6 is_required validate account_input form-employee" type="number" autocomplete="off" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="address_customer">Direcci&oacute;n del Empleado</label>
                <input type="text" class="col-lg-6 is_required validate form-employee" data-validate="isUser" autocomplete="off" id="address_customer" name="address_customer" value="{if isset($smarty.post.address_customer)}{$smarty.post.address_customer}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="id_country">Pais</label>
                <select name="id_country" id="id_country"  class="col-lg-6 is_required validate form-employee" autocomplete="off">
                    <option value="" selected>-</option>
                    {foreach from=$countries item=v}
                        <option value="{$v.id_country}">{$v.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="city">Ciudad</label>
                <select id="city" name="city" class="col-lg-6 is_required validate form-employee" autocomplete="off">
                    <option value="" selected>-</option>
                    <option class="69" value="Bogota, D.C.">Bogot&aacute;, D.C.</option>
                    <option class="69" value="Medellin">Medell&iacute;n</option>
                    <option class="69" value="Cali">{l s="Cali"}</option>
                    <option class="69" value="Barranquilla">{l s="Barranquilla"}</option>
                    <option class="69" value="Bucaramanga">{l s="Bucaramanga"}</option>
                    {foreach from=$cities item=city}
                        <option class="{$city.country}" value="{$city.ciudad}">{$city.ciudad}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="row">        
            <div class="col-lg-6 div-btn">
                <button class="btn btn-default btn-save-employee" type="submit" id="add-employee" name="add-employee">
                    <span> AGREGAR EMPLEADO </span>
                </button>
            </div>    
        </div>    
    </form>
  
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}
{literal}

    <script>
        $("#city option").hide();
        $(document).on('change', '#id_country', function() {
            var country = $(this).val();
            $("#city option").hide();
            if (country) {
                $("."+country).show();
            }
        });
        
        $("#use_fluz_employee").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#use_fluz_employee').val();
            var availablepoint = $('#available-point span').html();
            
            if(valor2>=0){
                
                var calculo = availablepoint - valor2;
                var cashconvertion2='COP'+' '+'$' + (Math.round(calculo*25)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                
                $('#title-fluz span').html(cashconvertion2);
                $('#available-point span').html(calculo);
                $('#ptosusedhiddenadde').val(valor2);
                
                if(calculo <= 0){
                    $('#available-point span').html(0);
                    $('#title-fluz span').html(0);
                }
                
            }else{
                valor2*=-1;
                $('#use_fluz_employee').val(valor2);
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
            }
                
        }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
    </script>
    
{/literal}
