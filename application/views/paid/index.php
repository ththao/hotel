<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name">
        <div class="dropdown-select" style="float: left;">
            <a class="btn btn-default btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        
        <h1 style="float: left; min-width: 195px; text-align: center;">CHI PHÍ</h1>
        
    	<form method="get" action="/paid" class="submit-form">
            <div class="dropdown-select" style="float: right; width: 85px;">
                <select name="year" id="years" class="form-control ymd-select">
                    <option value="">Year</option>
                    <?php $cy = intval(date('Y')); ?>
                    <?php for ($y=$cy-5; $y<=$cy; $y++): ?>
                    	<option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="dropdown-select" style="float: right; width: 65px;">
                <select name="month" id="month" class="form-control ymd-select">
                    <option value="">Month</option>
                    <?php for ($m=1; $m<=12; $m++): ?>
                    	<option value="<?php echo $m; ?>" <?php echo $m == $month ? 'selected' : ''; ?>><?php echo $m; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-setting">
            <div class="manage-room">
                <table id="manage-room-table" class="table hotel-table">
                    <tr>
                        <th>Ngày</th>
                        <th>Số Tiền</th>
                        <th>Lý Do</th>
                        <th>Ghi Chú</th>
                        <th></th>
                    </tr>
                    <?php $total = 0; ?>
					<?php foreach ($data as $item): ?>
    					<?php 
    					   $reason = $item->reason == 1 ? 'Lương nhân viên' : ($item->reason == 2 ? 'Nhập hàng' : 'Chi phí khác');
    					   $notes = explode(';', $item->notes);
    					   $total += $item->amount;
    					?>
                        <tr>
                            <td><p class="pad10px"><?php echo $item->paid_date; ?></p></td>
                            <td><p><?php echo number_format($item->amount, 0); ?></p></td>
                            <td><p><?php echo $reason; ?></p></td>
                            <td>
                                <?php foreach ($notes as $note): ?>
                                	<p><?php echo $note; ?></p>
                                <?php endforeach; ?>
                            </td>
                            <td align="right">
                                <a href="/paid/update/<?php echo $item->id; ?>">Cập nhật</a> | 
                                <a href="/paid/delete/<?php echo $item->id; ?>">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr>
                        <td><p class="pad10px">Tổng cộng</p></td>
                        <td><p><?php echo number_format($total, 0); ?></p></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <div class="add-floor-button pull-right">
                	<a href="/paid/create">
                        <img src="../../../../images/add.png" id="add-paid" class="pointer">
                        <p><strong>Thêm Chi Phí</strong></p>
                    </a>
                </div>
            </div>
            <div class="clear-fix"></div>
        </div>
    </div>
</div>
<!--End content-->

<script>
$(document).ready(function() {
	$(".ymd-select").change(function() {
		$(".submit-form").submit();
	});
});
</script>