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

{literal}
    <script>
        var listcopy = '';
        var list_customer = '';
    </script>
{/literal}

<form method="post" enctype="multipart/form-data" id="uploademployeebusiness" class="contenedorEmployeeBusiness" name="uploademployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Importar desde CSV </p>
        </div>
        <div class="col-lg-12 browse-div">
            <div class="custom-file-upload">
                <!--<label for="file">File: </label>--> 
                <input type="file" name="file" id="file" />
            </div>
        </div>
    </div>
    <div class="row">
        <span> Si tienes problemas para subir tu CSV. Aseg&uacute;rate de que tu hoja de c&aacute;lculo est&eacute; formateada correctamente. Descargar <a href="../csvcustomer/carga_customer_example.csv" class="link-down">CSV de Ejemplo</a></span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-employee" name="upload-employee">
                <span> SUBIR ARCHIVO </span>
            </button>
        </div>    
    </div>
</form>
<div class="row block-information-csv">
    <span id="text-info">Tener en cuenta: CSV</span>
    <span id="icon-info">!</span>
    <br>
    <ul id="list-info">
        <li>El archivo CSV debe estar en formato de separaci&oacute;n por puntos y comas (;).</li>
        <li>Todos los campos son requeridos (*).</li>
        <li>Ning&uacute;n dato incluido en el archivo debe contener caracteres especiales o tildes (&aacute;&ntilde;/\&deg;|&not;^&quot;&amp;&lt;&gt;) entre otros.</li>
    </ul>
</div>
<form class="copy-form-csv" method="post" enctype="multipart/form-data" id="uploadcopyemployeebusiness" name="uploadcopyemployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> O Arrastre el Archivo CSV</p>
        </div>
    </div>
    <article>
        <div id="holder"></div> 
        <p id="status">API de archivos y API de FileReader no compatibles</p>
    </article>
    <div class="row">
        <span> Si tienes problemas para subir tu CSV. Aseg&uacute;rate de que tu hoja de c&aacute;lculo est&eacute; formateada correctamente. Descargar <a href="../csvcustomer/carga_customer_example.csv" class="link-down">CSV de Ejemplo</a> </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-copy" name="upload-copy">
                <span> SUBIR ARCHIVO </span>
            </button>
        </div>    
    </div>
</form>
<div id="url_fluz" style="display:none;">{$base_dir_ssl}</div>
<div class="progress-container" style="display:none;">
    <div class="myfancybox">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <br>
            <img class="logo img-responsive" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz" width="300" height="94">
        </div>
	<div class="progress">
		<div class="progress-bar">
			<div class="progress-shadow"></div>
		</div>
	</div>
        <div class="text-loader">Estamos Procesando tu archivo CSV. Por Favor Espera</div>
    </div>    
</div>
{literal}
    <script>
        $('#upload-employee').click(function(e){
             
            var box = $(".myfancybox");
            box.fancybox({
                // API options here
            }).click();
        });    
    </script>
{/literal}
{literal}
    <script>
        var holder = document.getElementById('holder'),
        state = document.getElementById('status');

        if (typeof window.FileReader === 'undefined') {
            state.className = 'fail';
        } else {
            state.className = 'success';
            state.innerHTML = 'File API & FileReader available';
        }

        holder.ondragover = function() {
            this.className = 'hover';
            return false;
        };
        holder.ondragend = function() {
            this.className = '';
            return false;
        };
        holder.ondrop = function(e) {
            this.className = 'viewcsv';
            e.preventDefault();

            var file = e.dataTransfer.files[0],
            reader = new FileReader();
            reader.onload = function(event) {
                
                holder.innerText = event.target.result;
                
                var array = event.target.result;
                var extractValidString = array.split(/[\n;]+/);
                var noOfCols = 8;
                var objFields = extractValidString.splice(0,noOfCols);
                
                var arr = [];
                while(extractValidString[0] != '') {
                    var obj = {};
                    var row = extractValidString.splice(0,noOfCols)
                    for(var i=0;i<row.length;i++) {
                        obj[objFields[i]] = row[i].trim()
                    }
                    arr.push(obj);
                }
                listcopy = JSON.stringify(arr);
            };
            reader.readAsText(file);

            return false;
        };
        
        $('#upload-copy').click(function(e){
            var url = document.getElementById("url_fluz").innerHTML;  
            var box = $(".myfancybox");
            box.fancybox({
                closeClick: false,
            }).click();
            
            $.ajax({
                url : urlTransferController,
                type : 'POST',
                data : 'action=submitcopy&listcopy='+listcopy,
                
                success : function(data) {
                    
                   if(data == 1){
                        $('.progress-container').css('display','none');
                        window.location.replace(url+"confirmtransfercustomer");
                   }    
                   else {
                        $('#rewards_account').hide();    
                        $('#error_p').show();
                        $('.progress-container').css('display','none');
                        
                        setTimeout("$.fancybox.close()", 500);
                        
                        data = jQuery.parseJSON(data);
                        var content = '';
                        $.each(data, function (key, id) {
                            if(data[key].username != '' && data[key].username != undefined){
                               content += '<div class="resultados_error" id="resultados_error"><span style="color: #EF4136;">&#33;</span> El Usuario <span style="color:#ef4136;">'+data[key].username+'</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.</div><br>';
                            }
                            if(data[key].cedula != '' && data[key].cedula != undefined){
                               content += '<div class="resultados_error" id="resultados_error" ><span style="color: #EF4136;">&#33;</span> La Cedula <span style="color:#ef4136;">'+data[key].cedula+'</span> ya se encuentra Registrada en Fluz Fluz. Por Favor Revisa tu CSV.</div><br>';
                            }
                            if(data[key].name != '' && data[key].name != undefined){
                               content += '<div class="resultados_error" id="resultados_error" ><span style="color: #EF4136;">&#33;</span> El Nombre <span style="color:#ef4136;">'+data[key].name_customer+'</span> no es correcto. Por Favor Revisa tu CSV.</div><br>';
                            }
                            if(data[key].email != '' && data[key].email != undefined){
                               content += '<div class="resultados_error" id="resultados_error" ><span style="color: #EF4136;">&#33;</span> La Direcci&oacute;n de email <span style="color:#ef4136;">'+data[key].email_customer+'</span> no es correcta. Por Favor Revisa tu CSV.</div><br>';
                            }
                            if(data[key].csv_number != '' && data[key].csv_number != undefined){
                               content += '<div class="resultados_error" id="resultados_error" ><span style="color: #EF4136;">&#33;</span> No es posible importar mas de 140 registros. Por favor validar y reducir la cantidad de registros.</div><br>';
                            }
                            if(data[key].valid_phone != '' && data[key].valid_phone != undefined){
                               content += '<div class="resultados_error" id="resultados_error" ><span style="color: #EF4136;">&#33;</span> El tel&eacute;fono <span style="color:#ef4136;">'+data[key].phone+'</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.</div><br>';
                            }
                        })
                        
                        $("#container-error").html(content);
                   }
                }
            });
            e.preventDefault();
        });
    </script>
{/literal}
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}
