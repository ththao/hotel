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