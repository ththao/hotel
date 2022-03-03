<div id="content">
	<form method="get">
	<div class="" style="display: flex;">
		<div class="">
			Tỉnh/Thành: 
			<select class="province">
				<option value="">Chọn Tỉnh/Thành</option>
				<?php if ($provinces): ?>
				<?php foreach ($provinces as $province): ?>
				<option value="<?php echo $province->id; ?>" <?php echo isset($province_id) && $province_id == $province->id ? 'selected' : ''; ?>><?php echo $province->name; ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="">
			Huyện/Quận: 
			<select class="district">
				<option value="">Chọn Huyện/Quận</option>
				<?php if ($districts): ?>
				<?php foreach ($districts as $district): ?>
				<option value="<?php echo $district->id; ?>" <?php echo isset($district_id) && $district_id == $district->id ? 'selected' : ''; ?>><?php echo $district->name; ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<div class="">
			Xã/Phường: 
			<select class="ward" name="w">
				<option value="">Chọn Xã/Phường</option>
				<?php if ($wards): ?>
				<?php foreach ($wards as $ward): ?>
				<option value="<?php echo $ward->id; ?>" <?php echo isset($ward_id) && $ward_id == $ward->id ? 'selected' : ''; ?>><?php echo $ward->name; ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<button type="submit">Tìm Kiếm</button>
	</div>
	</form>
	
    <div id="location-map" style="height: 700px;">
    	
    </div>
</div>

<?php if ($this->session->has_userdata('user_id') && isset($ward_id) && $ward_id) { ?>
<div id="add-property" class="modal fade">
    <div class="modal-dialog asb-modal-dialog modal-dialog-centered">
        <div class="asb-table modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thêm mới</h4>
                <button type="button" class="close close-this-box back-fuel" data-dismiss="modal">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <form class="property-form">
                    <div class="form-group">
                        <label class="control-label">Địa Phương</label>
                        <div style="display: flex;">
                            <select class="province form-control" name="province_id" style="width: 30%; margin-right: 5%; height: 35px;">
                				<option value="">Chọn Tỉnh/Thành</option>
                				<?php if ($provinces): ?>
                				<?php foreach ($provinces as $province): ?>
                				<option value="<?php echo $province->id; ?>" <?php echo isset($province_id) && $province_id == $province->id ? 'selected' : ''; ?>><?php echo $province->name; ?></option>
                				<?php endforeach; ?>
                				<?php endif; ?>
                			</select>
                			<select class="district form-control" name="district_id" style="width: 30%; margin-right: 5%; height: 35px;">
                				<option value="">Chọn Huyện/Quận</option>
                				<?php if ($districts): ?>
                				<?php foreach ($districts as $district): ?>
                				<option value="<?php echo $district->id; ?>" <?php echo isset($district_id) && $district_id == $district->id ? 'selected' : ''; ?>><?php echo $district->name; ?></option>
                				<?php endforeach; ?>
                				<?php endif; ?>
                			</select>
                			<select class="ward form-control" name="ward_id" style="width: 30%; height: 35px;">
                				<option value="">Chọn Xã/Phường</option>
                				<?php if ($wards): ?>
                				<?php foreach ($wards as $ward): ?>
                				<option value="<?php echo $ward->id; ?>" <?php echo isset($ward_id) && $ward_id == $ward->id ? 'selected' : ''; ?>><?php echo $ward->name; ?></option>
                				<?php endforeach; ?>
                				<?php endif; ?>
                			</select>
            			</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Latitude/Longitude/STST</label>
                        <div style="display: flex;">
                            <input class="form-control latitude" name="latitude" value="" style="width: 30%; margin-right: 5%;" placeholder="Latitude">
                            <input class="form-control longitude" name="longitude" value="" style="width: 30%; margin-right: 5%;" placeholder="Longitude">
                        	<input class="form-control map_id" name="map_id" value="" style="width: 30%;" placeholder="Số tờ/Số thửa">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Diện Tích/Giá/Ngày đăng</label>
                        <div style="display: flex;">
                            <input class="form-control acreage" name="acreage" value="" style="width: 30%; margin-right: 5%;" placeholder="Diện tích">
                            <input class="form-control price" name="price" value="" style="width: 30%; margin-right: 5%;" placeholder="Giá">
                            <input class="form-control" name="value_date" value="<?php echo date('Y-m-d'); ?>" style="width: 30%;" placeholder="Ngày đăng">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Nguồn</label>
                        <input class="form-control source_url" name="source_url" value="">
                    	<div class="source_url_warning hide" style="color: red;">Nguồn tin đã tồn tại</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Ghi chú</label>
                        <textarea class="form-control" name="details"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn-primary btn-cancel" data-dismiss="modal" href="#" style="padding: 5px;">Hủy</a>
                <a class="btn-success btn-save" href="#" style="padding: 5px;">Lưu</a>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
/*var osmUrl = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
var mymap = L.map("location-map", {attributionControl: true, center: [10.78157890, 106.94938700], zoom: 14, minZoom: 1, maxZoom: 19});
mymap.on('click', addMarker);
L.tileLayer(osmUrl, {}).addTo(mymap);
var markers = L.markerClusterGroup();
var current_marker = null;

$(document).ready(function() {
	mymap.addLayer(markers);
});*/

if (navigator.geolocation) {
	var MAP = new google.maps.Map(document.getElementById('location-map'), {
		center : {
			lat : <?php echo $latitude; ?>,
			lng : <?php echo $longitude; ?>
		},
		zoom : 15
	});

	MAP.addListener('click', function(e) {
		addMarker(e);
		MAP.panTo(e.latLng);
	});

	var infowindow = new google.maps.InfoWindow({
	    content: ''
	});
	
	var current_marker = null;
	
	<?php if ($markers): ?>
	<?php foreach ($markers as $marker): ?>
    	new google.maps.Marker({
    		position : new google.maps.LatLng(<?php echo $marker['latitude']; ?>, <?php echo $marker['longitude']; ?>),
    		map : MAP,
    		title: '<?php echo $marker['title']; ?>',
    		draggable : true
    	}).addListener('click', function(e) {
        	console.log(e.latLng.lat());
        	console.log(e.latLng.lng());
    		infowindow.setContent(nl2br(this.getTitle()));
        	infowindow.open(MAP, this);
    	});
	<?php endforeach;?>
	<?php endif; ?>
	
} else {
	// Browser doesn't support Geolocation
	handleLocationError(false);
}
</script>