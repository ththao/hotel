<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/room">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>CẬP NHẬT GIÁ</h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-edit">
            <form method="post" class="form-horizontal" action="/room/prices">
                <?php if (isset($message) && $message): ?>
                <div style="width: 100%; text-align: center; padding: 20px; color: green; font-size: 20px;"><label><?php echo $message; ?></label></div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Cập nhật giá</label>
                    <div class="col-md-9">
                    	<select class="form-control" name="price_name">
                    	    <option value="">Chọn giá cần cập nhật</option>
                    		<option value="hourly_price">Giá theo giờ</option>
                    		<option value="night_price">Giá theo đêm</option>
                    		<option value="daily_price">Giá theo ngày</option>
                    	</select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá mới</label>
                    <div class="col-md-9">
                        <div class="col-xs-6">
                            <input name="new_price" type="text" value="" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
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
                        <label style="with: 20%;"><input class="apply_room" name="rooms[]" type="checkbox" style="height: 20px; width: 20px;" value="<?php echo $r->id; ?>"> <?php echo $r->name; ?></label>
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