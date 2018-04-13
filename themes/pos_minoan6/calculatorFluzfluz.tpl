{capture name=path}{l s='CalculatorFluzfluz'}{/capture}
    
<div class="section calculator">
    <div class="row content-fix">
        <h2 id="titleRow">Calculadora De Ganancias Fluz Fluz</h2>
        <div class='col-lg-8 col-md-8 col-sm-12 col-xs-12 sliders'>    
            <input type="hidden" id="no_fluzzer" value="">
            <input type="hidden" id="personal_fluzzer_expenditute" value="">
            <input type="hidden" id="network_fluzzer_expenditute" value="">
            <div class="row slider">
              <div class="row p first">
                Numero de Fluzzers en Tu Network 
                <a data-trigger="hover" href="#" data-toggle="popover" data-content="Esta es la cantidad de Fluzzers directos de tu network que contribuyen a tu retorno."><img class="help-calculator" src="{$img_dir|escape:'html':'UTF-8'}icon/pop-up-symbol-1.svg"/></a>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 style-slider">
                  <input id="downstreamFluzzers"  class="slider-input" type="range" min="0" max="65535" step="1" value="0"/>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                  <input class="number-input" id="downstreamFluzzersInputText" type="number" min="0" max="65535" inputmode="numeric" pattern="[0-9]*" title="Non-negative integral number" value="0">
              </div>
            </div>

            <div class="row slider">
              <div class="row p first">
                Promedio del valor de tus compras (Mensual) 
                <a data-trigger="hover" href="#" data-toggle="popover" data-content="El promedio en dinero que esperas que cada Fluzzer en tu network utilice durante el Mes. Puede variar dependiendo del network y del pa&iacute;s. Por ejemplo, algunas redes pueden compras m&aacute;s ropa y accesorios y otras m&aacute;s restaurantes."><img class="help-calculator" src="{$img_dir|escape:'html':'UTF-8'}icon/pop-up-symbol-1.svg"/></a>
              </div>
              <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 style-slider">
                <input type="range" class="slider-input" min="10000" max="5000000" step="10000" value="0" id="personalExpenditure"/>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                <input class="number-input" id="personalExpenditureInputText" type="number" min="100000" max="5000000" inputmode="numeric" pattern="[0-9]*" title="Non-negative integral number" value="10000">
              </div>
            </div>

          <div class="row slider">
            <div class="row p first">
             Promedio del valor de compras de los Fluzzers de tu Network (Mensual) 
                <a href="#" data-trigger="hover" data-toggle="popover" data-content="El promedio en dinero que esperas utilizar en Fluz Fluz en un mes."><img class="help-calculator" src="{$img_dir|escape:'html':'UTF-8'}icon/pop-up-symbol-1.svg"/></a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 style-slider">
              <input type="range" class="slider-input" min="10000" max="5000000" step="10000" value="0" id="downstreamFluzzerExpenditure"/>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
              <input class="number-input" id="downstreamFluzzerExpenditureInputText" type="number" min="100000" max="5000000" inputmode="numeric" pattern="[0-9]*" title="Non-negative integral number" value="10000">
            </div>
          </div>
    </div>  
    <div id="estimatedIncome" class="estimate col-lg-3 col-md-3 col-sm-11 col-xs-11">
        <div class="title">
          Ingresos mensuales estimados:
        </div>
        <div id="estimate" class="">
          $ 0 COP
        </div>
        <div class="comment">
            * Esta calculadora genera una estimaci&oacute;n de ingresos basada en el consumo promedio de un Fluzzer en las diferentes categor&iacute;as. 
            Esta Calculadora no es una garant&iacute;a de ingresos o retorno de ning&uacute;n tipo. 
            El retorno real a recibir puede variar con respecto a la estimaci&oacute;n de la calculadora de acuerdo con tu consumo real y el consumo real de los Fluzzers en tu network. 
            Por ejemplo, la categor&iacute;a de restaurantes, puede tener un mejor retorno que la categor&iacute;a de transporte y viajes.
        </div>
     </div>
    </div>
</div>
{literal}
 <!-- Bootstrap core JavaScript -->
    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();   
        });
    </script>
    <script>
        $(document).ready(function(){
            
            $('#downstreamFluzzers').change(function() 
            {
                var value = $(this).val();
                $('#downstreamFluzzersInputText').val(value);
                $('#no_fluzzer').val(value);
                recalcExpectedIncome();
            });

            $('#personalExpenditure').change(function() 
            {
                var value = $(this).val();
                $('#personalExpenditureInputText').val(value);
                $('#personal_fluzzer_expenditute').val(value);
                recalcExpectedIncome();
            });
            
            $('#downstreamFluzzerExpenditure').change(function() 
            {
                var value = $(this).val();
                $('#downstreamFluzzerExpenditureInputText').val(value);
                $('#network_fluzzer_expenditute').val(value);
                recalcExpectedIncome();
            });
            
            $("#downstreamFluzzersInputText").on("keyup",function(){
                var value = $(this).val();
                $('#downstreamFluzzers').val(value);
                $('#no_fluzzer').val(value);
                recalcExpectedIncome();
            });
            
            $("#personalExpenditureInputText").on("keyup",function(){
                var value = $(this).val();
                $('#personalExpenditure').val(value);
                $('#personal_fluzzer_expenditute').val(value);
                recalcExpectedIncome();
            });
            
            $("#downstreamFluzzerExpenditureInputText").on("keyup",function(){
                var value = $(this).val();
                $('#downstreamFluzzerExpenditure').val(value);
                $('#network_fluzzer_expenditute').val(value);
                recalcExpectedIncome();
            });
            
        });
        
        
      function recalcExpectedIncome() {
        var rewards_percentage = 100*(0.025*0.6 + 0.04*0.3 + 0.1*0.1);
        var personalExpenditure = $('#personal_fluzzer_expenditute').val();
        var downstreamFluzzerExpenditure = $('#network_fluzzer_expenditute').val();
        var downStreamFluzzerCount = $('#no_fluzzer').val();
        var estimated_income = (personalExpenditure*rewards_percentage*0.01*0.5) + (downstreamFluzzerExpenditure*rewards_percentage*0.01*0.5*downStreamFluzzerCount)/15;
        document.getElementById("estimate").innerHTML = "$ " + estimated_income.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}) + " COP";
      }
      
      /*function sliderChangeEventHandler() {
        console.log('entasasd');  
        $("#downstreamFluzzersInputText").val(downstreamFluzzersSlider.val());
        $("#personalExpenditureInputText").val(personalExpenditureSlider.val());
        $("#downstreamFluzzerExpenditureInputText").val(downstreamFluzzerExpenditureSlider.val());
        //recalcExpectedIncome();
      }*/
      /*function textChangeEventHandler() {
        $("#downstreamFluzzers").slider('setValue', $("#downstreamFluzzersInputText").val());
        $("#personalExpenditure").slider('setValue', $("#personalExpenditureInputText").val());
        $("#downstreamFluzzerExpenditure").slider('setValue', $("#downstreamFluzzerExpenditureInputText").val());
        recalcExpectedIncome();
      }*/
      /*function createValidator(element) {
          return function() {
              var min = parseInt(element.getAttribute("min")) || 0;
              var max = parseInt(element.getAttribute("max")) || 0;
              var value = parseInt(element.value) || min;
              element.value = value; // make sure we got an int
              if (value < min) element.value = min;
              if (value > max) element.value = max;
          }
      }
      var elms = document.body.querySelectorAll("input[type=number]");
      [].forEach.call(elms, function(elm) {
        elm.onkeyup = createValidator(elm);
      });
      var downstreamFluzzersSlider = $("#downstreamFluzzers").slider();
      var personalExpenditureSlider = $("#personalExpenditure").slider();
      var downstreamFluzzerExpenditureSlider = $("#downstreamFluzzerExpenditure").slider();
      recalcExpectedIncome();
      downstreamFluzzersSlider.change(this, sliderChangeEventHandler);
      personalExpenditureSlider.change(this, sliderChangeEventHandler);
      downstreamFluzzerExpenditureSlider.change(this, sliderChangeEventHandler);
      $("#downstreamFluzzersInputText").on("keyup", textChangeEventHandler);
      $("#personalExpenditureInputText").on("keyup", textChangeEventHandler);
      $("#downstreamFluzzerExpenditureInputText").on("keyup", textChangeEventHandler);*/
    </script>
{/literal}      