<div class="panel">
    <h3>{l s='Agregar nuevo API'}</h3>
    <div class="row">
            <span>{l s = 'Para agregar un nuevo API. Haga click en el boton Guardar' mod="fluzfluzapi"}</span>
    </div>
    <br>
    <form method="post">
        <input type="hidden" name="option" value="{$option}" id="option"/>
        <div class='row'>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><label for='name' class="required">{l s='Nombre Webservice'}</label></div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"><input type='text' name='name' {if isset($row)}value="{$row.name}"{/if}/></div>
        </div>
            <div class='row'>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><label for='uri' class="required">{l s='Uri'}</label></div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"><input type='text' name='uri' {if isset($row)}value="{$row.uri}"{/if}/></div>
        </div>
            <div class='row'>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><label for='login' class="required">{l s='Login'}</label></div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"><input type='text' name='login' {if isset($row)}value="{$row.login}"{/if}/></div>
        </div>
            <div class='row'>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><label for='password' class="required">{l s='Password'}</label></div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"><input type='text' name='password' {if isset($row)}value="{$row.password}"{/if}/></div>
        </div>
            <div class='row'>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"><label for='request' class="required">{l s='Request'}</label></div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8"><textarea name='request' rows='20' cols='20'>{if isset($row)}{$row.request}{/if}</textarea></div>
            </div>
        </div>
        <a href="index.php?controller=AdminModules&configure=fluzfluzapi&tab_module=others&module_name=fluzfluzapi"><button class="button btn btn-default pull-left" id="cancel"><i class="process-icon-cancel"></i>{l s='Cancelar'}</button></a>
        <button type="submit" name="process_ws" class="button btn btn-default pull-right" value="submit"><i class="process-icon-save"></i>{l s='Guardar'}</button>
    </form>
</div>
{literal}
    <script>
        $('#cancel').click(function(){
            $('#option').val('cancel');
        })
    </script>
{/literal}