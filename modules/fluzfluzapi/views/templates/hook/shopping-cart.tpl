<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-11" style="padding-left:0px;">
        <div class="form-group marco">
            <div class="row">
                <form>
                    <div class="tit">
                        <span class="ws-tit">{l s='Tus Numeros a recargar' mod='fluzfluzapi'}</span>
                    </div>
                    <br/>
                {foreach from=$productlist item=product}
                    {capture assign=new}select-{$product.id_product}{/capture}
                    <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 merchant"><img src="{$img_manu_dir}{$product.id_manufacturer}.jpg" width="45px"/></div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 nombre">{$product.name}</div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <select id="select-{$product.id_product}" name="{$product.id_product}" class="numero">
                        <option value="0" id="noselect">Elije Telefono</option>
                    {foreach from=$phones item=phone}
                        {if $phone.phone!=null or $phone.phone!=""}
                            <option {if $smarty.cookies.$new=={$phone.phone}}selected='selected'{/if} value="{$phone.phone}">(&nbsp;{$phone.phone|substr:0:3}&nbsp;)&nbsp;{$phone.phone|substr:3:7}</option>
                        {/if}
                    {/foreach}
                    </select>
                    </div>
                    <br><br>
                    </div>
                {/foreach}
                </form>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-3 col-sm-4 col-md-4 col-lg-3" style="background-color: transparent;">
                    <img class="action" src="/modules/fluzfluzapi/images/button-add.png" width="18px"/><span class="atit">&nbsp;&nbsp;A&ntilde;adir Nuevo&nbsp;&nbsp;</span>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right">
                    <div style="float:right; text-align: right;">
                        (&nbsp;<input type="number" size="3" maxlength="3" name="pre1" id="pre1" class="nuevo" placeholder="300"/>&nbsp;)&nbsp;
                        &nbsp;<input type="number" size="7" maxlength="7" name="pre2" id="pre2" class="nuevo" placeholder="1234567"/>&nbsp;
                        <!---&nbsp;<input type="number" size="4" maxlength="4" name="pre3" id="pre3" class="nuevo"/>-->
                    <div>
                </div>
            </div>
        </div>
    </div>
    </div>
        <div id="popup" class="modal fade" role="dialog" data-target="#popup">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Elije T&eacute;fono a Recargar</h4>
                    </div>
                    <div class="modal-body">
                        <p>Debes elegir el tel&eacute;fono a recargar desde la lista <strong>"Elegir tel&eacute;fono"</strong> o ingresar un nuevo n&uacute;mero y luego dar clic en el bot&oacute;n <strong>"<img class="action" src="/modules/fluzfluzapi/images/button-add.png" width="16px"/> a&ntilde;adir nuevo"</strong> y seleccionarlo en la lista posteriormente continuar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"></div>            
</div>
</div>
    