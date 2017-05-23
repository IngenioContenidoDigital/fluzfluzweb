{capture name=path}{l s='stateaccount'}{/capture}
    
    <div>
        <img src="{$img_dir}back-stateaccount.jpg" class="img-accountstate" />
    </div>
    
    <div class="row"><p class="text-title"> Hola {$username} </p></div>
    <div class="row margin-div"><p class="p-state">{l s='Explore su estado de cuenta mensual.'}</p></div>
    
    <div class="row bck-stats">
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Tama&ntilde;o de Network:</p>
             <p class="col-lg-5 data-r">{$tam_network}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Nuevos miembros en su network este mes:</p>
             <p class="col-lg-5 data-r">{$numero_de_cuenta}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Fluz ganada:</p>
             <p class="col-lg-5 data-r">{$lastPoint.points|string_format:"%d"}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">COP valor de Fluz:</p>
             <p class="col-lg-5 data-r">{displayPrice price=$lastPoint.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account"># de pedidos:</p>
             <p class="col-lg-5 data-r">{$num_orders.orders}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Bonos compradas:</p>
             <p class="col-lg-5 data-r">{$num_orders.num_bonos}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Valor de la tienda de bonos:</p>
             <p class="col-lg-5 data-r">{displayPrice price=$num_orders.price|escape:'html':'UTF-8'}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Fecha del estado de cuenta:</p>
             <p class="col-lg-5 data-r">{$num_orders.fecha_actual}</p>
        </div>
        
    </div>
    <div class="row">
        <h3 class="t-second"> Compare su rendimiento </h3>
    </div> 
    <div class="row">
        <h4 class="t-act"> Tu Actuaci&oacute;n </h4>
    </div> 
    <div class="row bck-stats">
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Fluz ganada:</p>
             <p class="col-lg-5 data-r">{$num_orders.points|string_format:"%d"}</p>
        </div>
        
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">COP valor de Fluz:</p>
             <p class="col-lg-5 data-r">{displayPrice price=$num_orders.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
    </div>
    <div class="row">
        <h4 class="t-net"> Mejor rendimiento en toda la network </h4>
    </div> 
    <div class="row bck-stats">
        {foreach from=$topPoint item=top}
        <div class="col-lg-6"></div>
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">Fluz ganada:</p>
             <p class="col-lg-5 data-r">{$top.points|string_format:"%d"}</p>
        </div>
        
        <div class="col-lg-6"></div>
        
        <div class="col-lg-6">
            <p class="col-lg-7 item-list-account">COP valor de Fluz:</p>
             <p class="col-lg-5 data-r">{displayPrice price=$top.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</p>
        </div>
        {/foreach}
    </div>    
    <form method="post" class="form-horizontal well hidden-print">
        <div class="row">
            <div class="col-lg-6"></div>
            <div class="col-lg-6">
                <p class="col-lg-7"> Enviar Email del estado de cuenta al correo electr&oacute;nico. </p>
                <div class="col-lg-5 div-btn-mail"><button type="submit" name="submitMailAccount" class="btn btn-primary btn-state-account">
                        {l s='Enviar Email'}
                     </button>
                </div>
            </div>
        </div>
    </form>
           