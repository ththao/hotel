<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/item">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>CẬP NHẬT NƯỚC</h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-edit">
            <form method="post" class="form-horizontal" action="/item/update/<?php echo $item->id; ?>">
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Nước</label>
                    <div class="col-md-9">
                    	<input name="name" type="text" value="<?php echo $item->name; ?>" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Giá</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="price" type="text" value="<?php echo $item->price; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Icon Class</label>
                    <div class="col-md-9">
                        <div class="col-xs-6 price-hour">
                            <input name="icon_class" type="text" value="<?php echo $item->icon_class; ?>" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                <div class="btn-group end-setting">
                    <button class="btn btn-success">Lưu</button>
                    <a href="/item" class="btn btn-default">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!--End content-->