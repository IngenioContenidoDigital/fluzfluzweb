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
	$('#product-tab-content-ModuleAllinone_rewards').delegate('img.delete_reward', 'click', function(){
		var row = $(this).parents('tr');
		jConfirm(delete_reward_label, delete_reward_title, function(r) {
		    if (r) {
				$.ajax({
					type	: 'POST',
					cache	: false,
					url		: product_rewards_url,
					data 	: 'action=delete_reward&reward_product_id='+row.attr('id'),
					dataType: 'json',
					success : function(data) {
						if (data && data.error)
							alert(data.error);
						else
							row.remove().fadeIn(1000);
						manageEmptyRow();
					}
				});
		    }
		});
	});

	$('#product-tab-content-ModuleAllinone_rewards').delegate('img.edit_reward', 'click', function(){
		$('#new_reward').hide();
		$('#update_reward').show();
		var row = $(this).parents('tr');
		$('#reward_product_id').val(row.attr('id'));
		$('#reward_product_value').val(row.find('.reward_value').html());
		$('#reward_product_from').val(row.find('.reward_from').html());
		$('#reward_product_to').val(row.find('.reward_to').html());
		if (row.find('.reward_type').html() == '%')
			$('#reward_product_type').val(0);
		else
			$('#reward_product_type').val(1);
		showVirtualValue();
	});

	$('#product-tab-content-ModuleAllinone_rewards').delegate('#submitRewardProduct', 'click', function(){
		$.ajax({
			type	: 'POST',
			cache	: false,
			url		: product_rewards_url,
			data 	: 'action=submit_reward&reward_product_id='+$('#reward_product_id').val()+'&reward_product_value='+$('#reward_product_value').val()+'&reward_product_type='+$('#reward_product_type').val()+'&reward_product_from='+$('#reward_product_from').val()+'&reward_product_to='+$('#reward_product_to').val(),
			dataType: 'json',
			success : function(data) {
				if (data.error)
					alert(data.error);
				else {
					$('#reward_product_list tr[id='+$('#reward_product_id').val()+']').remove().fadeIn(1000);
					var r = '<tr style="display: none" id="'+data.reward_product.id+'"><td><span class="reward_value">'+(data.reward_product.value * 1).toFixed(2)+'</span> <span class="reward_type">'+(data.reward_product.type==0 ? '%' : currency_sign)+'</span></td><td class="reward_from">'+$('#reward_product_from').val()+'</td><td class="reward_to">'+$('#reward_product_to').val()+'</td><td><img style="cursor: pointer" class="edit_reward" src="../img/admin/edit.gif"><img style="cursor: pointer" class="delete_reward" src="../img/admin/delete.gif"></td></tr>';
					$row = $(r);
					$row.prependTo('#reward_product_list tbody').fadeIn(1000);
					resetRewardProduct();
				}
				manageEmptyRow();
			}
		});
	});

	$('#product-tab-content-ModuleAllinone_rewards').delegate('#cancelRewardProduct', 'click', function(){
		resetRewardProduct();
	});

	$('#product-tab-content-ModuleAllinone_rewards').delegate('#reward_product_type', 'click', function(){
		showVirtualValue();
	});

	$('#product-tab-content-ModuleAllinone_rewards').delegate('#reward_product_value', 'blur', function(){
		showVirtualValue();
	});
});

function showVirtualValue() {
	if ($('#reward_product_type').val() == 1 && !isNaN($('#reward_product_value').val()))
		$('#virtualvalue').html('('+($('#reward_product_value').val() * virtual_value).toFixed(2)+' '+virtual_name+')').show();
	else
		$('#virtualvalue').html('');
}

function manageEmptyRow() {
	if ($('#reward_product_list tbody tr').length == 1)
		$('#reward_product_list tbody tr[id=0]').fadeIn(1000);
	else
		$('#reward_product_list tbody tr[id=0]').hide();
}

function resetRewardProduct() {
	$('#update_reward').hide();
	$('#new_reward').show();
	$('#reward_product_id').val('');
	$('#reward_product_value').val('');
	$('#reward_product_type').val(0);
	$('#reward_product_from').val('');
	$('#reward_product_to').val('');
	showVirtualValue();
}