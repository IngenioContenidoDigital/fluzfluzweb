/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function(){
	$(document).on('submit', '#create-account_form', function(e){
		e.preventDefault();
		submitFunction();
	});
        //$("#submitAccount").attr('disabled', true);
        //$("#submitAccount").css('background', "#fff").css("color", "#c9b197");
        $('#acceptterms').change(function() {
            if($(this).is(":checked")) {
                $("#submitAccount").attr('disabled', false);
                $("#submitAccount").css('background', "#c9b197").css("color", "#fff");
            } else {
                $("#submitAccount").attr('disabled', true);
                $("#submitAccount").css('background', "#fff").css("color", "#c9b197");
            }
        });
	$('.is_customer_param').hide();
        $('#submitTc').remove();
        $('#submitPSE').remove();
        $( "#account-creation_form" ).submit(function() {
            $('#psebank').val( $('#pse_bank').val() );
            $('#namebank').val( $('#name_bank').val() );
            $('#psetypecustomer').val( $('#pse_tipoCliente').val() );
            $('#psetypedoc').val( $('#pse_docType').val() );
            $('#psenumdoc').val( $('#pse_docNumber').val() );
            $('#formPayUPse').reset();
        });
        $("#typedocument").change(function() {
            $("#checkdigit").val("");
            if ( $("#typedocument").val() == 1 ) {
                $(".blockcheckdigit").css("display", "block");
                $("#gover").attr("data-validate", "isNITNumber");
            } 
            if ( $("#typedocument").val() == 2 ) {
                $(".blockcheckdigit").css("display", "none");                
                $("#gover").attr("data-validate", "isGoverNumberCE");                
            }
            if ( $("#typedocument").val() == 0 ) {
                $(".blockcheckdigit").css("display", "none");                
                $("#gover").attr("data-validate", "isGoverNumber");                
            }
            
        });
});

function submitFunction()
{
	$('#create_account_error').html('').hide();
	$.ajax({
		type: 'POST',
		url: baseUri + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		headers: { "cache-control": "no-cache" },
		data:
		{
			controller: 'authentication',
			SubmitCreate: 1,
			ajax: true,
			email_create: $('#email_create').val(),
			back: $('input[name=back]').val(),
			token: token
		},
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(error in jsonData.errors)
					//IE6 bug fix
					if(error != 'indexOf')
						errors += '<li>' + jsonData.errors[error] + '</li>';
				$('#create_account_error').html('<ol>' + errors + '</ol>').show();
			}
			else
			{
				// adding a div to display a transition
				$('#center_column').html('<div id="noSlide">' + $('#center_column').html() + '</div>');
				$('#noSlide').fadeOut('slow', function()
				{
					$('#noSlide').html(jsonData.page);
					$(this).fadeIn('slow', function()
					{
						if (typeof bindUniform !=='undefined')
							bindUniform();
						if (typeof bindStateInputAndUpdate !=='undefined')
							bindStateInputAndUpdate();
						document.location = '#account-creation';
					});
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			error = "TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
			if (!!$.prototype.fancybox)
			{
				$.fancybox.open([
				{
					type: 'inline',
					autoScale: true,
					minHeight: 30,
					content: "<p class='fancybox-error'>" + error + '</p>'
				}],
				{
					padding: 0
				});
			}
			else
				alert(error);
		}
	});
}