<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

{literal}
    <style>
        #header, #footer, #launcher, #right_column, .breadcrumb { display: none!important; }
    </style>
{/literal}

<script>
    var url = "{$base_dir}";
    var cards = {$cards|@json_encode};
    var urlWalletController = "{$link->getPageLink('wallet', true)|escape:'html':'UTF-8'}";
</script>
<input type="hidden" id="id_customer" value="{$id_customer}">
<div class="container wallet-container">
    <div class="row row1">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label class="title title-line-bottom">B&oacute;veda</label>&nbsp;&nbsp;<label class="title">de C&oacute;digos</label>
        </div>
    </div>
    <div class="row row2">
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
            <p class="available-cards">C&oacute;digos - <tt id="available-cards">{$cards|@count}</tt> Total</p>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 filter-view">
            <div>
                <a class="btn dropdown-toggle sortby" data-toggle="dropdown" href="#">filtrar por<span class="icon icon-angle-down"></span></a>
                <ul id="sortby" class="dropdown-menu">
                    <li><a href="#" id="all">Todas<i class="icon icon-bullseye"></i></a></li>
                    <li><a href="#" id="unused">Disponible<i class="icon icon-circle"></i></a></li>
                    <li><a href="#" id="used">Usada<i class="icon icon-circle"></i></a></li>
                    <li><a href="#" id="finished">Terminada<i class="icon icon-circle"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row row3">
        {foreach from=$cards key=key item=card}
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 card" key="{$key}">
                <div class="row state-used state-used{$card.used}">
                    <i class="icon icon-circle"></i>
                </div>
                <div class="row container-card">
                    <div class="col-xs-5 col-sm-3 col-md-3 col-lg-3 col1">
                        <img src="{$s3}m/{$card.id_manufacturer}.jpg" class="img-manufacturer"/>
                    </div>
                    <div class="col-xs-6 col-sm-5 col-md-5 col-lg-5 col2">
                        <span class="name-manufacturer">{$card.manufacturer}</span>
                        <br>
                        <span class="">{$card.card_code}</span>
                    </div>
                    <div class="col-xs-0 col-sm-4 col-md-4 col-lg-4 col3">
                        <label class="price">{displayPrice price=$card.price_shop}</label>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="row row4">
        {*<ul class="pagination">
            <li><a href="#" id="prev-container-card"><</a></li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#" id="next-container-card">></a></li>
        </ul>*}
    </div>
    <div class="row row5 viewdetailcard">
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
            <div class="row cardview-info">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <img src="{$s3}m/{$cards.0.id_manufacturer}.jpg" class="img-manufacturer"/>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7 cardcontainfo">
                    <label class="name-manufacturer">{$cards.0.manufacturer}</label>
                    <p id="vencimiento" class="cardinfo">Vencimiento: <span id="expiration"></span></p>
                    <p class="cardinfo">Valor Original: <span id="value_original"></span></p>
                    <p class="cardinfo">Fecha Compra: <span id="date_buy"></span></p>
                </div>
            </div>
            <div class="row cardview-price">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <p class="cardinfo">Valor:</p>
                    <p class="price" id="value"></p>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <p class="cardinfo">Código Asociado:</p>
                    <p class="price" id="code"></p>
                </div>
            </div>
            <div class="row used-value">
                <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 text-center">
                    <p>Usado: <span class="price price-gray" id="value-used"></span></p>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-center">
                    <p>Disponible: <span class="price" id="value-used-available"></span></p>
                </div>
            </div>
            <div class="row cardviewupt-price">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <input type="number" id="upt-value" value="" placeholder="$" min="0" onkeypress='return event.charCode >= 48 && event.charCode <= 57' />
                    <label id="btnupt-value">Actualizar Valor</label>
                </div>
            </div>
            <div class="row cardviewupt-used">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="radio checkbox-success">
                        <label><input type="radio" name="optradio" id="used-card" value="1">Usada</label>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="radio">
                        <label><input type="radio" name="optradio" id="finished-card" value="2">Terminada</label>
                    </div>
                </div>
                <input type="hidden" id="card_product" value="" />
                <input type="hidden" id="card_key" value="" />
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 text-center">
                <img src="" id="img-code-bar" class="" />
            </div>
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 cardviewupt-instructions">
                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#instructions">Instrucciones<i class="icon icon-plus"></i></button>
                <div id="instructions" class="collapse"></div>
                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#terms">Términos<i class="icon icon-plus"></i></button>
                <div id="terms" class="collapse"></div>
                {if $addreses_manufacturer}
                    <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#locations">Direcciones<i class="icon icon-plus"></i></button>
                    <div id="locations" class="collapse">
                        {foreach from=$addreses_manufacturer key=key item=address}
                            <div class="row address-manufacturer">
                                <p><label>{$address.firstname}</label></p>
                                <p>{$address.address1}</p>
                                <p>{$address.city}</p>
                            </div>
                        {/foreach}
                        <div id="loadMoreAddress"><label class="more-address">{l s="Mostrar mas"}</label></div>
                        <div id="loadMenosAddress" style='display:none;'><label class="more-address">{l s="Mostrar menos"}</label></div>
                    </div>
                {/if}
            </div>
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 send_gift" id="send_gift">
                <button type="button" class="btn btn-info btn-gift" id="btn-gift">Obsequiar Como Bono de Regalo<i class="icon icon-th-large"></i></button>
            </div>
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5" id="container-gift" style="display:none;">
                <input type="text" name="busqueda" id="busqueda" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Ingresa Nombre del Fluzzer.'}" required>
                <div id="resultados" class="result-find"></div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 div_send_gift">
                    <button class="btn_send_gift"> Enviar Obsequio </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row row6">
        <label>Necesitas m&aacute;s c&oacute;digos para ganar m&aacute;s Fluz?</label>
    </div>
    <div class="row row7 text-center">
        <label id="btnbuy">Comprar</label>
    </div>
</div>