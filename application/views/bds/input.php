<div id="content">
    <div class="container-fluid">
        <div id="detail">
            <div class="col-md-12">
                <form method="post">
                	<input type="hidden" name="id" value="" />
                	<div class="row">
                    	<div class="col-md-3">Tỉnh/Thành</div>
                    	<div class="col-md-3">
                    		<select class="province form-control" name="province_id">
                				<option value="">Chọn Tỉnh/Thành</option>
                				<?php if ($provinces): ?>
                				<?php foreach ($provinces as $province): ?>
                					<option value="<?php echo $province->id; ?>" <?php echo isset($province_id) && $province_id == $province->id ? 'selected' : ''; ?>><?php echo $province->name; ?></option>
                				<?php endforeach; ?>
                				<?php endif; ?>
                			</select>
                    	</div>
                	</div>
                	
                	<div class="row">
                    	<div class="col-md-3">Huyện/Quận</div>
                    	<div class="col-md-3">
                    		<select class="district form-control" name="district_id">
                				<option value="">Chọn Huyện/Quận</option>
                			</select>
                    	</div>
                	</div>
                	
                	<div class="row">
                    	<div class="col-md-3">Xã/Phường</div>
                    	<div class="col-md-3">
                    		<select class="ward form-control" name="ward_id">
                				<option value="">Chọn Xã/Phường</option>
                			</select>
                    	</div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Số tờ/ Số Thửa</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="map_id" value="" placeholder="Ex: 1/1"></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">latitude</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="latitude" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">longitude</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="longitude" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Đường</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="street" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Diện Tích</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="acreage" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Giá</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="price" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Ngày</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="value_date" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Nguồn</div>
	                	<div class="col-md-3"><input class="form-control" type="text" name="source_url" value=""></div>
                	</div>
                	
                	<div class="row">
	                	<div class="col-md-3">Ghi chú</div>
	                	<div class="col-md-3"><textarea class="form-control" name="details"></textarea></div>
                	</div>
                	
    	        	<div class="row">
    	        		<div class="col-md-3"></div>
    	        		<div class="col-md-3">
    		        		<button type="submit" class="btn btn-success btn-save">Lưu</button>
    	        		</div>
    	        	</div>
            	</form>
            </div>
        </div>
    </div>
</div>