/* @author Pp Galvan - LdrMx */
	
/*delete ads*/
$(document).on('click', '.admin-delete-ads-from-list', function(e){ e.preventDefault();
	var self = $(this);																					
	var ad_id = self.attr('data-id');
	self.closest('tr').hide();
	$.ajax({type: 'post', url: site_ajax+ "?f=admin_ads", 
		data: {'task':'ad_delete', 'ad_id':ad_id, 'only':'ads'}, cache : false, success:function(result){ 
			if(result.result == '1'){
				self.closest('tr').remove();
			} else {
				self.closest('tr').show();
			}
			$('#_modal').modal('hide');
		}
	});		
});


/*activate ads*/
$(document).on('click', '.admin-activate-ads-from-list', function(e){ e.preventDefault();
	var self = $(this);																					
	var ad_id = self.attr('data-id');
	self.hide();
	$.ajax({type: 'post', url: site_ajax+ "?f=admin_ads",
		data: {'task':'ad_activate', 'ad_id':ad_id, 'only':'ads'}, cache : false, success:function(result){ 	
			if(result.result == '1'){   
				self.closest('tr').find('.ad_quantity').html(result.quantity);
				self.closest('tr').find('.ad_status').html('Active');
				_modal('#_modal-message', {title: 'Ads Actived', message: 'This ads is actived!' });
				self.remove();
			}else{ 
				self.show(); 
				_modal('#_modal-message', {title: 'Error', message: 'There is some thing went worng!'});
			}	
		}			   
	});
});


/*delete plan*/
$(document).on('click', '.admin-delete-plan-from-list', function(e){ e.preventDefault();
	var self = $(this);																					
	var plan_id = self.attr('data-id');
	self.closest('tr').hide();
	$.ajax({type: 'post', url: site_ajax+ "?f=admin_ads",
		data: {'task':'plan_delete', 'plan_id':plan_id, 'only':'ads'}, cache : false, success:function(result){ 
			if(result.result == '1'){
				self.closest('tr').remove();
				} else {
				self.closest('tr').show();
			}
			$('#_modal').modal('hide');
		}			   
	});
});