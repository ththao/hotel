<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/room">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>CẬP NHẬT <?php echo $room->name; ?></h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-edit">
            <form method="post" class="form-horizontal" action="/room/update/<?php echo $room->id; ?>">
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Phòng</label>
                    <div class="col-md-9">
                    	<?php if ($room->id): ?>
                    		<label><?php echo $room->name; ?></label>
                    	<?php else: ?>
                        	<input name="name" type="text" value="<?php echo $room->name; ?>" class="form-control">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Tầng</label>
                    <div class="col-md-9">
                    	<select class="form-control" name="floor">
                    		<option value="1" <?php echo $room->floor == 0 ? 'selected' : ''; ?>>Tầng Trệt</option>
                    		<option value="1" <?php echo $room->floor == 1 ? 'selected' : ''; ?>>Tầng 1</option>
                    		<option value="2" <?php echo $room->floor == 2 ? 'selected' : ''; ?>>Tầng 2</option>
                    		<option value="3" <?php echo $room->floor == 3 ? 'selected' : ''; ?>>Tầng 3</option>
                    		<option value="4" <?php echo $room->floor == 4 ? 'selected' : ''; ?>>Tầng 4</option>
                    		<option value="4" <?php echo $room->floor == 5 ? 'selected' : ''; ?>>Tầng 5</option>
                    		<option value="4" <?php echo $room->floor == 6 ? 'selected' : ''; ?>>Tầng 6</option>
                    		<option value="4" <?php echo $room->floor == 7 ? 'selected' : ''; ?>>Tầng 7</option>
                    		<option value="4" <?php echo $room->floor == 8 ? 'selected' : ''; ?>>Tầng 8</option>
                    		<option value="4" <?php echo $room->floor == 9 ? 'selected' : ''; ?>>Tầng 9</option>
                    		<option value="4" <?php echo $room->floor == 10 ? 'selected' : ''; ?>>Tầng 10</option>
                    	</select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Mô tả</label>
                    <div class="col-md-9">
                        <textarea name="description" class="form-control"><?php echo $room->description; ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo giờ</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="hourly_price" type="text" value="<?php echo $room->hourly_price; ?>" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label">/ Giờ</label>
                        <div class="clearfix"></div>
                        <div class="col-xs-6 price-day">
                            <input type="text" name="next_hourly_price" value="<?php echo $room->next_hourly_price; ?>" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label">/ Giờ tiếp theo</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo đêm</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="night_price" type="text" value="<?php echo $room->night_price; ?>" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label">/ Đêm</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo ngày</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="daily_price" type="text" value="<?php echo $room->daily_price; ?>" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label">/ Ngày</label>
                    </div>
                </div>

                <div class="form-group hide">
                    <label class="col-md-3 control-label">Phụ thu theo người</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="extra_price" type="text" value="<?php echo $room->extra_price; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group hide">
                    <label class="col-md-3 control-label">Giảm giá</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="discount" type="text" value="<?php echo $room->discount; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group hide">
                    <label class="col-md-3 control-label">Áp dụng giá phòng:</label>
                    <div class="col-md-9">
                        <div style="display: flex;">
                        <div class="room-col room-col-1">
                        <label><input id="apply_all_rooms" type="checkbox" style="height: 20px; width: 20px;"> Tất cả</label>
                        </div>
                        </div>
                        
                        <?php $floor = ''; ?>
                        <?php $room_index = 1; ?>
                        <?php $max_room_index = 1; ?>
                        
                        <?php foreach ($rooms as $r): ?>
                        <?php if ($floor != '' && $floor != $r->floor) { ?>
                        <?php $room_index = 1; ?>
                        </div>
                        <?php } ?>
                        <?php if ($floor == '' || $floor != $r->floor) { ?>
                        <div style="display: flex;">
                        <?php } ?>
                        
                        <div class="room-col room-col-<?php echo $room_index; ?>">
                        <?php if ($r->id != $room->id): ?>
                        <label style="with: 20%;"><input class="apply_room" name="rooms[]" type="checkbox" style="height: 20px; width: 20px;" value="<?php echo $r->id; ?>"> <?php echo $r->name; ?></label>
                        <?php endif; ?>
                        </div>
                        
                        <?php $floor = $r->floor; ?>
                        <?php $max_room_index = $max_room_index < $room_index ? $room_index : $max_room_index; ?>
                        <?php $room_index ++; ?>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                <div class="btn-group end-setting">
                    <button class="btn btn-success">Lưu</button>
                    <a href="/room" class="btn btn-default">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!--End content-->

<script>
$(document).ready(function() {
    $('.room-col').css('width', '<?php echo (100/$max_room_index) . '%'; ?>');
	$(document).on('click', '#apply_all_rooms', function() {
		if ($(this).is(':checked')) {
		    $('.apply_room').prop('checked', true);
		} else {
		    $('.apply_room').prop('checked', false);
		}
	});
	$(document).on('click', '.apply_room', function() {
		if ($(this).is(':checked') && $('.apply_room').length == $('.apply_room:checked').length) {
		    $('#apply_all_rooms').prop('checked', true);
		} else {
		    $('#apply_all_rooms').prop('checked', false);
		}
	});
});
</script>