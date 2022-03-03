$(document).ready(function() {
	$('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
	$('select').select2();
	
	$(document).on('click', '.add-match', function(e) {
		e.preventDefault();

		$('#add-match').modal('show');
	});
	
	$(document).on('click', '#add-match .btn-save', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		
		$.ajax({
			method: "POST",
			url: '/soccer/input',
			dataType: 'json',
			data: $('#add-match .match-form').serialize(),
			success: function(data) {
				if (data.status) {
					//$('#add-match').modal('hide');
					$('#add-match .match-form').find('select[name="home_fb_team_id"]').val('').trigger('change');
					$('#add-match .match-form').find('select[name="guess_fb_team_id"]').val('').trigger('change');
					$('#add-match .match-form').find('input[name="home_plus"]').val('');
					$('#add-match .match-form').find('input[name="guess_plus"]').val('');
					$('#add-match .match-form').find('input[name="home_score"]').val('');
					$('#add-match .match-form').find('input[name="guess_score"]').val('');
					$('#add-match .match-form').find('select[name="home_fb_team_id"]').focus();
				} else {
					alert(data.message);
				}
			}
		});
	});
	
	$(document).on('click', '#add-match .btn-cancel', function(e) {
		if (current_marker) {
			current_marker.setMap(null);
			current_marker = null;
		}
	});
});