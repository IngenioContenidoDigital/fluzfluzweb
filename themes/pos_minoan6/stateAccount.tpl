{capture name=path}{l s='stateaccount'}{/capture}
    
    <!--<div>
        <img src="{$img_dir}back-stateaccount.jpg" class="img-accountstate" />
    </div>-->
    
    <!--<div class="row"><p class="text-title"> Hola {$username} </p></div>-->
    <div class="row margin-div"><p class="p-state">{l s='Explora tu estado de cuenta mensual.'}</p></div>
    
    <div class="row bck-stats">
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">Tama&ntilde;o de mi Network:</p>
            <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$tam_network}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">Nuevos miembros en mi network este mes:</p>
            <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$numero_de_cuenta}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">Fluz ganados este mes:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$lastPoint.points|string_format:"%d"}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">COP valor de Fluz ganados este mes:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{displayPrice price=$lastPoint.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account"># de pedidos que he realizado:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$num_orders.orders}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account"># de c&oacute;digos que he comprado:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$num_orders.num_bonos}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">COP valor de mis compras:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{displayPrice price=$num_orders.price|escape:'html':'UTF-8'}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-6 item-list-account">Fecha del estado de cuenta:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-6 data-r">{$num_orders.fecha_actual}</p>
        </div>
        
    </div>
    <div class="row margin-div">
        <p class="p-state">{l s='Compara tu rendimiento con el mejor Fluzzer del mes'}</p>
    </div>
    <!--<div class="row">
        <h4 class="t-act"> Tu Rendimiento </h4>
    </div>--> 
    <!--<div class="row bck-stats">
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-8 item-list-account">Fluz ganados:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-4 data-r">{$num_orders.points|string_format:"%d"}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-8 item-list-account">COP valor de Fluz:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-4 data-r">{displayPrice price=$num_orders.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
    </div>-->
    <div class="row">
        <h4 class="t-act"> Mejor rendimiento del mes </h4>
    </div> 
    <div class="row bck-stats">
        {foreach from=$topPoint item=top}
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-8 item-list-account">Fluz ganados:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-4 data-r">{$top.points|string_format:"%d"}</p>
        </div>
        <div class="row">
            <p class="col-lg-7 col-md-7 col-sm-6 col-xs-8 item-list-account">COP valor de Fluz:</p>
             <p class="col-lg-5 col-md-5 col-sm-6 col-xs-4 data-r">{displayPrice price=$top.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
        {/foreach}
    </div>    
    <form method="post" class="form-horizontal">
        <div class="row form-state">
            <div class="row div-send-state">
                <p class="col-lg-6 col-md-8 col-sm-6 col-xs-6 p-btn" style="padding-left: 0px;color: #000;"> Enviar Email del estado de cuenta al correo electr&oacute;nico. </p>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 div-btn-mail">
                     <button type="submit" name="submitMailAccount" class="btn btn-primary btn-state-account">
                        {l s='Enviar Email'}
                     </button>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 div-btn-account">
                    <a class="btn btn-primary btn-state-back" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
                </div>
            </div>
        </div>
    </form>
{literal}
    
    <style>
        #center_column{background: #f4f4f4 !important;}
    </style>
    
{/literal}           