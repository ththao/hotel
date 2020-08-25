<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/room">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>THÊM PHÒNG</h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-edit">
            <form method="post" class="form-horizontal" action="/room/create">
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Phòng</label>
                    <div class="col-md-9">
                    	<input name="name" type="text" value="" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Tầng</label>
                    <div class="col-md-9">
                    	<select class="form-control" name="floor">
                    		<option value="1">Tầng 1</option>
                    		<option value="2">Tầng 2</option>
                    		<option value="3">Tầng 3</option>
                    		<option value="4">Tầng 4</option>
                    	</select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Mô tả</label>
                    <div class="col-md-9">
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo giờ</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="hourly_price" type="text" value="" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label" for="hour">/ Giờ</label>
                        <div class="clearfix"></div>
                        <div class="col-xs-6 price-day">
                            <input type="text" name="next_hourly_price" value="" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label" for="day">/ Giờ tiếp theo</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo đêm</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="night_price" type="text" value="" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label" for="hour">/ Đêm</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá theo ngày</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="daily_price" type="text" value="" class="form-control">
                        </div>
                        <label class="col-xs-6 control-label" for="hour">/ Ngày</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Phụ thu theo người</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="extra_price" type="text" value="" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giảm giá</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="discount" type="text" value="" class="form-control">
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