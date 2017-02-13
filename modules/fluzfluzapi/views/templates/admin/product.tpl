<div class="panel">
    <h3>Fluz Fluz API</h3>
    <div class="bootstrap" id="message-success" style="display:none;">
        <div class="alert alert-success">Proceso ejecutado con &eacute;xito</div>
    </div>
    <div class="bootstrap" id="message-error" style="display:none;">
        <div class="alert alert-error">Error en el proceso. Por favor intente de nuevo.</div>
    </div>
    
    <p>{l s = 'Por favor elija el webservice con el cual se validara el producto' mod="fluzfluzapi"}</p>
    <br />
    <form action="" method="post" >
        <input type="hidden" value="{$product}" name="product" id="product" />
        <select name="webservice" id="webservice">
            <option value="0">{l s='Sin Servicio Web' mod='fluzfluzapi'}</option>
            {foreach $results as $result}
            <option value="{$result.id_webservice_external}" {if $result.id_webservice_external==$webservice.id_webservice_external}selected="selected"{/if}>{$result.name}</option>
            {/foreach}
        </select>
        <br>
        <select name="operador" id="operador" {if $webservice.id_webservice_external!=1}style="display:none;"{/if}>
            <option value="0">Elija Operador</option>
            {foreach $operators as $operator}
            <option value="{$operator.id_operator}" {if $operator.id_operator==$webservice.id_operator}selected="selected"{/if}>{$operator.name}</option>
            {/foreach}
        </select>
        <br>
        <button type="submit" name="process_ws_assign" class="button btn btn-default pull-right" value="submit"><i class="process-icon-save"></i>{l s='Save'}</button>
    </form>
    <br />
    <p>{l s = 'El producto se autenticara con el servicio web seleccionado' mod='fluzfluzapi'}</p>
</div>

<script>
    $('#webservice').change(function(){
        var val = $(this).val();
        if (val==1){
            $('#operador').show();
        }else{
            $('#operador').val(0);
            $('#operador').hide();
        }
    })
    
    $('[name="process_ws_assign"]').click(function(e){
        e.preventDefault();
        var ws=$('#webservice').val();
        var op=$('#operador').val();
        var product = $('#product').val();
        
        if(ws==1 && op==0){
            alert('Elija el operador')
        }else{
            $.ajax({
               method:"post",
               url: '{$module_dir}ajax/ajaxcalls.php',
               data:{
                   action:'api',
                   operador:op,
                   webservice:ws,
                   product:product
               },
               success:function(response){
                   if(response=='success'){
                       $('#message-success').fadeIn('slow').delay(1800).fadeOut('slow');
                   }else{
                       $('#message-error').fadeIn('slow').focus().delay(1800).fadeOut('slow');
                   }
               }
            });
        }
    });
</script>