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
    var urlTransferController = "{$link->getPageLink('', true)|escape:'html':'UTF-8'}";
</script>

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="row title-row-business"> 
    <span class="title-business" id="title-container"></span>
    <div id="quantity-users"> Cantidad de Empleados</div>
    <div id="available-point" class="title-fluz">{l s="Fluz Totales: "}<span class="available-point">{$pointsAvailable}</span></div>
    <div id="available-point" class="title-fluz">{l s="Fluz en Dinero: "}<span class="available-point">{$pointsAvailable}</span></div>
</div>

<div class="row panel-employee">
    <div class="col-lg-2 item-employee" id="toggle-add-employees">
        <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <span id="option-list"></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu back-business" role="menu">
          <li>
              <a id="item-menu-principal" role="menuitem"  href="#">Modify an Employees</a>
          </li>
          <li>
              <a id="item-menu-add" role="menuitem"  href="#">Add an Employees</a>
          </li>
          <li>
              <a id="item-menu-upload" role="menuitem"  href="#">Upload Employees</a>
          </li>
        </ul>
        <!--<select class="back-business"> 
            <option value="add">Add an Employees</option>
            <option class="option2" value="upload">Upload Employees</option>
        </select>-->
    </div>
    <div class="col-lg-2 item-employee">Fill Balance</div>
    <div class="col-lg-2 item-employee">Allocate Fluz</div>
    <div class="col-lg-2 item-employee">Allocation History</div>
    <div class="col-lg-4 item-search">Buscar</div>
</div>
<div class="row container-info-users" id="container-info-users">
    <div class="row pagination-header">
        <div class="col-lg-4 pag-style"> Pagination </div>
        <div class="col-lg-8 btn-save-user">
            <div class="col-lg-8 div-toggle"> 
                <div class="col-lg-5 button dropdown"> 
                    <select id="select-distribute" name="select-distribute">
                       <option value="select-distribute">Select Distribution</option>
                       <option value="single-fluz">Distribute to Single</option>
                       <option value="all-fluz">Distribute to All</option>
                    </select>
                </div>
                <div class="col-lg-7" id="amount-use">
                    <input type="hidden" value="{$all_fluz}" id="ptosTotalOculto"/>
                    <div class="col-lg-8">
                        <input class="col-lg-12" type="number" min="1" max="{$all_fluz}" oninput="if(value>{$all_fluz})value={$all_fluz}" id="use_allfluz" autocomplete="off"/>
                    </div>
                    <div class="col-lg-4" id="ptosTotal">{l s=" de "}{$all_fluz}</div>
                </div>
            </div>
            <div class="col-lg-4 div-btn">
                <button class="btn btn-default btn-save-table" type="button" id="save-info">
                    <span> SAVE ALL </span>
                </button>
            </div>
        </div>
    </div>
    <div class="row bar-info-users">
        <div class="col-lg-1 item-users"></div>
        <div class="col-lg-2 item-users" id="firstname">First Name</div>
        <div class="col-lg-2 item-users" id="lastname">Last Name</div>
        <div class="col-lg-2 item-users" id="email">Email</div>
        <div class="col-lg-1 item-users" id="phone">Phone</div>
        <div class="col-lg-2 item-users" id="dni">Cedula</div>
        <div class="col-lg-2 item-users" id="amount">Amount</div>
    </div>
    <div class="row row-container-info" id="container-List-employees">
        {foreach from=$network item=net}
            <div class="row content-info-users">
                <input type="hidden" id="id_sponsor" value="{$net.id_customer}">
                <div class="col-lg-1 content-item-users">
                    <input type="checkbox" value="check-user">
                </div>
                <div class="col-lg-2 content-item-users">{$net.firstname}</div>
                <div class="col-lg-2 content-item-users">{$net.lastname}</div>
                <div class="col-lg-2 content-item-users">{$net.email}</div>
                <div class="col-lg-1 content-item-users">Phone</div>
                <div class="col-lg-2 content-item-users">{$net.dni}</div>
                <div class="col-lg-2 content-item-users">Amount</div>
            </div>
        {/foreach}
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
{literal}
    <style>
        #right_column{display: none;}
    </style>
{/literal}
{literal}
    <script>
        $(document).ready(function(){
            var add = $('#item-menu-principal').text();
            var title = 'EMPLOYER DASHBOARD';
            $('#option-list').html(add);
            $('#title-container').html(title);
            $('#amount-use').hide();
            
            $("#select-distribute").change(function() {
                var select = $('select[name=select-distribute]').val()
                
                if(select == 'all-fluz'){
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').show();
                }
                else{
                    $('#container-List-employees').removeClass("disabledbutton");
                    $('#amount-use').hide();
                }
            });
            
            $('#item-menu-principal').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'EMPLOYER DASHBOARD';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#container-info-users').show();
                $('#quantity-users').show();
            });
            
            $('#item-menu-add').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'ADD AN EMPLOYEE';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').show();
                $('#container-info-users').hide();
                $('#panel-upload-employee').hide();
                $('#quantity-users').hide();
            });
            
            $('#item-menu-upload').click(function(){
                var add = $('#item-menu-upload').text();
                var title = 'IMPORT EMPLOYEES';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#container-info-users').hide();
                $('#quantity-users').hide();
                $('#panel-upload-employee').show();
            });
            
        });
    </script>
{/literal}
{literal}
    <script>       
        $("#use_allfluz").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#use_allfluz').val();
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
            }else{
                valor2*=-1;
                $('#use_allfluz').val(valor2);
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