$(document).ready(function() {
	$(document).on('change', 'select.province', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		if ($(selected).val() == '') {
			return false;
		}
		
		$.ajax({
			method: "POST",
			url: '/bds/load_districts',
			dataType: 'json',
			data: {
				province_id: $(selected).val()
			},
			success: function(data) {
				if (data.status) {
					$(selected).parents('form').find('select.district').html(data.html);
				}
			}
		});
	});
	
	$(document).on('change', 'select.district', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		if ($(selected).val() == '') {
			return false;
		}
		
		$.ajax({
			method: "POST",
			url: '/bds/load_wards',
			dataType: 'json',
			data: {
				district_id: $(selected).val()
			},
			success: function(data) {
				if (data.status) {
					$(selected).parents('form').find('select.ward').html(data.html);
				}
			}
		});
	});
	
	$(document).on('click', '#add-property .btn-save', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		
		$.ajax({
			method: "POST",
			url: '/bds/input',
			dataType: 'json',
			data: $('#add-property .property-form').serialize(),
			success: function(data) {
				if (data.status) {
					if (current_marker) {
						current_marker.setMap(null);
						current_marker = null;
					}
					new google.maps.Marker({
			    		position : new google.maps.LatLng(data.latitude, data.longitude),
			    		map : MAP,
			    		title: data.title,
			    		draggable : true
			    	}).addListener('click', function(e) {
			    		infowindow.setContent(nl2br(this.getTitle()));
			        	infowindow.open(MAP, this);
			        	console.log(e.latLng.lat());
			        	console.log(e.latLng.lng());
			    	});
					
					MAP.setCenter({lat: data.latitude, lng: data.longitude});
					
					$('#add-property').modal('hide');
				} else {
					alert(data.message);
				}
			}
		});
	});
	
	$(document).on('click', '#add-property .btn-cancel', function(e) {
		if (current_marker) {
			current_marker.setMap(null);
			current_marker = null;
		}
	});
	
	$(document).on('blur', '#add-property .source_url', function(e) {
		var selected = $(this);
		if ($.trim($(selected).val()) == '') {
			$('#add-property .source_url_warning').addClass('hide');
			return false;
		}
		
		$.ajax({
			method: "POST",
			url: '/bds/check_source_url',
			dataType: 'json',
			data: {
				source_url: $(selected).val()
			},
			success: function(data) {
				if (data.duplicated) {
					$('#add-property .source_url_warning').removeClass('hide');
				} else {
					$('#add-property .source_url_warning').addClass('hide');
				}
			}
		});
	});
});

function handleLocationError(browserHasGeolocation) {
	var mesage = browserHasGeolocation ? 'Turn on location services to can determine your location.' : 'Turn on location services to can determine your location.';
	alert(message);
}

function addMarker(e) {
	if ($('#add-property').length == 0) {
		return false;
	}
	if (current_marker) {
		$('#add-property').modal('show');
		return false;
	}
	var marker = new google.maps.Marker({
		position : new google.maps.LatLng(e.latLng.lat(), e.latLng.lng()),
		map : MAP,
		title: 'Click Marker để thêm mới.',
		draggable : true
	});
	
	marker.addListener('click', function(e) {
		MAP.panTo(e.latLng);
		
		$('#add-property').find('.latitude').val(e.latLng.lat());
		$('#add-property').find('.longitude').val(e.latLng.lng());
		$('#add-property .source_url_warning').addClass('hide');
		$('#add-property').modal('show');
	});
	
	current_marker = marker;

	$('#add-property').find('.latitude').val(e.latLng.lat());
	$('#add-property').find('.longitude').val(e.latLng.lng());
	$('#add-property .source_url_warning').addClass('hide');
	$('#add-property').find('.map_id').val('');
	$('#add-property').find('.acreage').val('');
	$('#add-property').find('.price').val('');
	$('#add-property').find('.source_url').val('');
	$('#add-property').modal('show');
}

function nl2br(str) {
	return str.replaceAll("\n", "<br/>");
}