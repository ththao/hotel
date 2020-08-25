<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name">
        <div class="dropdown-select" style="float: left;">
            <a class="btn btn-default btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        <h1 style="float: left; min-width: 195px; text-align: center;">DS THUÊ</h1>
    	<form method="get" action="/rent" class="submit-form">
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
            <div class="dropdown-select" style="float: right; width: 65px;">
                <select name="date" id="date" class="form-control ymd-select">
                    <option value="">Day</option>
                    <?php for ($d=1; $d<=31; $d++): ?>
                    	<option value="<?php echo $d; ?>" <?php echo $d == $date ? 'selected' : ''; ?>><?php echo $d; ?></option>
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
        <div id="paid">
            <div class="paid-content">
                <table id="paid-table" class="table hotel-table">
                    <tr>
                        <th>Phòng</th>
                        <th>Giờ</th>
                        <th>Tiền Nước</th>
                        <th>Số Tiền</th>
                        <th></th>
                    </tr>
                    <?php $total = 0; ?>
                    <?php foreach ($data as $item): ?>
                        <tr>
                            <td><strong><?php echo $item->room_name; ?></strong></td>
                            <td>
                                <p><?php echo date("d-m-Y H:i", $item->check_in); ?></p>
                                <p><?php echo $item->check_out ? date("d-m-Y H:i", $item->check_out) : '-'; ?></p>
                            </td>
                            <td><?php echo $item->used_items_price ? number_format($item->used_items_price, 0) : '-'; ?></td>
                            <td><?php echo $item->total_price ? number_format($item->total_price, 0) : '-'; ?></td>
                            <td>
                            	<a href="/rent/view/<?php echo $item->id; ?>">Xem chi tiết</a> 
                            	<a href="/rent/update/<?php echo $item->id; ?>">Cập nhật&nbsp|&nbsp</a>
                            </td>
                        </tr>
                        <?php $total += $item->total_price; ?>
                    <?php endforeach; ?>
                    <tfoot>
                    <tr>
                        <td colspan="3"><h4>Tổng cộng:</h4></td>
                        <td><h4><?php echo number_format($total, 0); ?>vnd</h4></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
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