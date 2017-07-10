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
                <label class="col-lg-12 l-form-employee" for="firstname">{l s='First name'}</label>
                <input type="text" class="col-lg-6 is_required validate form-employee" data-validate="isName" autocomplete="off" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="lastname">{l s='Last name'}</label>
                <input type="text" class="col-lg-6 is_required validate form-employee" data-validate="isName" autocomplete="off" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
            </div>
            <div class="row form-group">
                <label class="col-lg-12 l-form-employee" for="email">{l s='Email address'}</label>
                <input class="col-lg-6 is_required validate account_input form-employee" data-validate="isEmail" autocomplete="off" type="email" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" />
            </div>
            <div class="row form-group">
                <label  class="col-lg-12 l-form-employee" for="phone_invoice">{l s='Phone Number'}</label>
                <input type="text" class="col-lg-6 form-employee" name="phone_invoice" id="phone_invoice" autocomplete="off" value="{if isset($smarty.post.phone_invoice) && $smarty.post.phone_invoice}{$smarty.post.phone_invoice}{/if}" />
            </div>
            <div class="row required dni form-group">
                <label class="col-lg-12 l-form-employee" for="dni">{l s='Cedula'}</label>
                <input class="col-lg-6 is_required validate account_input form-employee" type="text" autocomplete="off" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
            </div>
            <div class="row required form-group">
                <label class="col-lg-12 l-form-employee" for="Amount">{l s='Amount'}</label>
                <input class="col-lg-6 is_required validate form-employee" value="" type="number" min="1" max="{$pointsAvailable}" oninput="if(value>{$pointsAvailable})value={$pointsAvailable}" id="use_fluz_employee" autocomplete="off"/>
            </div>
        </div>
        <div class="row">        
            <div class="col-lg-6 div-btn">
                <button class="btn btn-default btn-save-employee" type="submit" id="add-employee" name="add-employee">
                    <span> ADD EMPLOYEE </span>
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