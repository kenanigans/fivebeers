jQuery(document).ready(function($) {

	$('#add-new-item input.new-item-name').focus();
	$body = $('body');

	var highest = 0;
	var current, prefix, setting;

	function trimspace(str) {
		return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	}

	if($body.hasClass('appearance_page_fivebeers-sidebars') == true){
		prefix = 'sidebar-';
		setting = 'unlimited_sidebars_settings';
	}

	$('.item-holder').each(function(){
		current = $(this).attr('id');
		current = parseInt(current.replace(prefix, ''), 10);
		if(current > highest){ highest = current; }
	});

	$('#add-new-item .add-item').on('click', function(e){
		e.preventDefault();
		var itemName = $('#add-new-item input.new-item-name').val();
		var $latest  = $('.sortable-wrap .item-holder.hidden');
		var $noItems = $('.sortable-wrap h4.no-items');
		if(itemName != ''){
			highest++;
			$clone = $latest.clone(true);
			$noItems.hide();
			$clone.appendTo('.sortable-wrap').attr('id', prefix+highest);
			$('.item-holder:last h4').text(itemName);
			$('.item-holder:last .item-name-input').val(itemName);
			$('.item-holder:last .item-info input').each(function(){
				var sufix = $(this).attr('name');
				$(this).attr('name', setting+'['+prefix+highest+']'+sufix);
			});
			$clone.removeClass('hidden');
			$('#add-new-item input.new-item-name').val('');
		}
	});

	$('#add-new-item .add-item').on('submit', function(){
		$('#add-new-item a').trigger('click');
	});

	$('.sortable-wrap').sortable({
		connectWith: '.sortable-wrap',
		placeholder: 'sortable-item-placeholder',
		handle: '.item-move',
		start: function(e,ui){
			var newHeight = ui.item.height() - 6;
			ui.placeholder.height(newHeight);
		}
	});

	$('.item-move').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
	});

	$('.repeatable-name').on('click', function(){
		var $this = $(this);
		$this.next().stop().slideToggle(200);
		$this.toggleClass('active');
	});

	$('.repeatable-form').on('submit', function(){
		$('.item-info input.item-name-input').each(function(){
			var $this = $(this);
			var name  = trimspace($this.val());
				if(name == ''){
					var empty = $this.parent().parent().attr('id');
					$this.val(empty);
				}
		});
	});

	$('a.delete-single-repeat').on('click', function(e){
		e.preventDefault();
		if (confirm(fivebeersRepeatables.alert)) {
			$(this).siblings('input').attr('name', '');
			$('.repeatable-form').submit();
		}
	});

});
