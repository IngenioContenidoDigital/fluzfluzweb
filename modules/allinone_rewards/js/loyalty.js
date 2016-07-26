/**
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
 */

jQuery(function($){
	getAttributeForReward();

	$(document).on('click', '.color_pick', function(e){
		getAttributeForReward();
	});

	$(document).on('change', '.attribute_select', function(e){
		getAttributeForReward();
	});

	$(document).on('click', '.attribute_radio', function(e){
		getAttributeForReward();
	});
});

function getAttributeForReward() {
        if ( url_allinone_loyalty == "" ) {
            var url_allinone_loyalty;
        }
    
	//create a temporary 'choice' array containing the choices of the customer
	var id_product_attribute = 0;
	var choice = [];
	var radio_inputs = parseInt($('#attributes .checked > input[type=radio]').length);
	if (radio_inputs)
		radio_inputs = '#attributes .checked > input[type=radio]';
	else
		radio_inputs = '#attributes input[type=radio]:checked';

	$('#attributes select, #attributes input[type=hidden], ' + radio_inputs).each(function(){
		choice.push(parseInt($(this).val()));
	});

	if (typeof combinations == 'undefined' || !combinations)
		combinations = [];

	//testing every combination to find the combination's ID choosen by the user
	for (var combination = 0; combination < combinations.length; ++combination){
		//verify if this combinaison is the same that the user's choice
		var combinationMatchForm = true;
		$.each(combinations[combination]['idsAttributes'], function(key, value){
			if (!in_array(parseInt(value), choice)) {
				combinationMatchForm = false;
				return;
			}
		});

		if (combinationMatchForm) {
			id_product_attribute = combinations[combination]['idCombination'];
			break;
		}
	}

	$.ajax({
		type	: 'POST',
		cache	: false,
		url		: url_allinone_loyalty,
		dataType: 'html',
		data 	: 'id_product='+$('#product_page_product_id').val()+'&id_product_attribute='+id_product_attribute,
		success : function(data) {
			if (data == '')
				$('#loyalty').hide().html('');
			else
				$('#loyalty').html(data).show();
		}
	});
}