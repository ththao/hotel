<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/paid">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>CẬP NHẬT</h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-edit">
            <form method="post" class="form-horizontal" action="/paid/update/<?php echo $item->id; ?>">
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Ngày</label>
                    <div class="col-md-9">
                    	<input name="paid_date" type="text" value="<?php echo $item->paid_date; ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Lý Do</label>
                    <div class="col-md-9">
                    	<select class="form-control" name="reason">
                    		<option value="1" <?php echo $item->reason == 1 ? 'selected' : ''; ?>>Lương nhân viên</option>
                    		<option value="2" <?php echo $item->reason == 2 ? 'selected' : ''; ?>>Nhập hàng</option>
                    		<option value="3" <?php echo $item->reason == 3 ? 'selected' : ''; ?>>Chi phí khác</option>
                    	</select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="room" class="col-md-3 control-label">Ghi chú</label>
                    <div class="col-md-9">
                        <textarea name="notes" class="form-control"><?php echo $item->notes; ?></textarea>
                        <i>Mẹo: dùng dấu ; khi bạn muốn hiển thị xuống dòng</i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">Số tiền</label>
                    <div class="col-md-9">
                        <input name="amount" type="text" value="<?php echo $item->amount; ?>" class="form-control">
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                <div class="btn-group end-setting">
                    <button class="btn btn-success">Lưu</button>
                    <a href="/paid" class="btn btn-default">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!--End content-->

<script>
$(document).ready(function() {
	$('input[name="paid_date"]').datepicker({dateFormat: 'dd-mm-yy'});
});
</script>