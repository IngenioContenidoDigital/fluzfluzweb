{if isset($error)}
<p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}
    


<script type="text/javascript">
    $(function(){ 
            var id_sel_b = $('#pse_bank').val();
            if (id_sel_b === ""){  
                $('#pse_bank').html('<option value="" selected="selected">Cargando Listado de Bancos</option>');          
                $.ajax({
                    type: "post",
                    url: "{$base_dir|regex_replace:"/[http://]/":""}/modules/payulatam/ajax_listado_b.php",
                    data: {
                        "id_state":id_sel_b
                    },
                    success: function(response){
                        var json = $.parseJSON(response);                        
                        $('#pse_bank').html('<option value="" selected="selected">Seleccione una entidad</option>'+json.results);
                    }
                });
            }


        $('#formPayUPse').validate({
            {literal}
                  wrapper: 'div',
            errorPlacement: function (error, element) {
                error.addClass("alert alert-danger");
                error.insertAfter(element);
            },
            {/literal}            
            rules :{                
                pse_bank : {
                    required : true                                                
                },                
                pse_tipoCliente : {
                    required : true                     
                },
                pse_docType : {
                  required : true
                },
                pse_docNumber : {
                    required : true,                    
                    minlength : 5 , //para validar campo con minimo 3 caracteres
                    maxlength : 16 //para validar campo con maximo 9 caracteres      
                }
            },
            messages: {
                        pse_bank: { 
                            required: "Seleccione un banco."
                        },
                        pse_tipoCliente: { 
                            required: "Seleccione el tipo de cliente."
                        },
                        pse_docType : {
                            required: "Hace falta un numero de documento."
                        },
                        pse_docNumber : {
                            required : "El numero de decumento debe ser numerico",
                            minlength: "Por favor ingrese m&iacute;nimo 5 caracteres.",
                            maxlength: "Por favor ingrese m&aacute;ximo 16 caracteres.",
                        }
                    },
        });

        setTimeout(function(){
            $("#collapseTwo").removeClass( "in");
            }, 1000 );
            
    });
    
  function bank()
  {
   $("#name_bank").val($("#pse_bank :selected").text()); 
  }

</script>
  

<div class="container">      
    <div class="row">
         <div class="col-xs-12 col-sm-10 col-md-8"> 
            <form  method="POST" class="form-horizontal" action="{$base_dir|regex_replace:"/[http://]/":""|escape:'htmlall':'UTF-8'}/modules/payulatam/payuPse.php" id="formPayUPse" name="formPayUPse" autocomplete="off" >
         
                     <div class="form-group">
                        <label for="pse_bank" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Banco</label>
                        <div class="col-xs-12 col-sm-6">
                            <select id="pse_bank" name="pse_bank" onchange="bank();" class="form-control">
                                <option value="">Seleccione una entidad</option>
                            </select> 
                            <input type="hidden" value="" name="name_bank" id="name_bank"/>
                        </div>
                    </div>
                
                     <div class="form-group">
                        <label for="pse_tipoCliente" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Tipo de cliente</label>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="pse_tipoCliente" id="pse_tipoCliente" checked value="N">
                                Natural
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="pse_tipoCliente" id="pse_tipoCliente"  value="J">
                                Juridico
                            </label>
                        </div>
                    </div> 
                
                     <div class="form-group">
                        <label for="pse_docType" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Tipo de documento</label>
                        <div class="col-xs-12 col-sm-6">
				<select id="pse_docType" name="pse_docType" class="form-control">
					<option value="">Seleccione un tipo de documento</option>
					<option value="CC">Cédula de ciudadanía.</option>
					<option value="CE">Cédula de extranjería.</option>
					<option value="NIT">NIT, en caso de ser una empresa.</option>
					<option value="TI">Tarjeta de Identidad.</option>
					<option value="PP">Pasaporte.</option>
					<option value="IDC">Identificador único de cliente, para el caso de ID’s únicos de clientes/usuarios de servicios públicos.</option>
					<option value="CEL">Número Móvil, en caso de identificar a través de la línea del móvil.</option>
					<option value="RC">Registro civil de nacimiento.</option>
					<option value="DE">Documento de identificación Extranjero.</option>
				</select> 
                        </div>
                    </div>   
                
                     <div class="form-group">
                        <label for="pse_docNumber" class="control-label col-xs-12 col-sm-6 text-left" style="text-align: left;">Número de documento</label>
                        <div class="col-xs-12 col-sm-6">
                              <input type="text" id="pse_docNumber" name="pse_docNumber" class="form-control">
                        </div>
                    </div>
                
                   <div class="form-group btnpayment">
                        <label for="submitPSE" class="control-label hidden-xs col-sm-6 " style="text-align: left;"></label>
                        <div class="col-xs-12 col-sm-6">
                            <button btn btn-default standard-checkout button-medium type="submit" id="submitPSE" class="button btn btn-default standard-checkout button-medium">
                                <span> Pagar Ahora
                                    <i class="icon-chevron-right right"></i>
                                </span>
                            </button>
                        </div>
                    </div>         

                    <div class="">
                        <p><br> Recuerda tener habilitada tu cuenta corriente/ahorros para realizar compras  vía  internet. 
                           <br> No  olvides desbloquear las ventanas emergentes de tu navegador para evitar inconvenientes a la hora de realizar el pago.
                        </p>
                    </div>
            </form>
         </div>
    </div>
</div>
{/if}

