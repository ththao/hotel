<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name">
        <div class="dropdown-select" style="float: left;">
            <a class="btn btn-default btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        <h1 style="float: left; min-width: 195px; text-align: center;">TỔNG KẾT</h1>
        
    	<form method="get" action="/report" class="submit-form">
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
        <div id="report">
            <div class="report-content">
                <table id="report-table" class="table hotel-table">
                    <tr>
                        <td><h4>Thời điểm</h4></td>
                        <td align="right"><h4>Tiền nước</h4></td>
                        <td align="right"><h4>Doanh thu</h4></td>
                    </tr>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $ym => $amount): ?>
                        	<?php 
                        	if (strlen($ym) == 10) {
                        	    $dow = date('N', strtotime($ym));
                        	    if ($dow == 7) {
                        	        $ym = 'CN (' . date('d/m', strtotime($ym)) . ')';
                        	    } else {
                        	        $ym = 'T' . ($dow + 1) . ' (' . date('d/m', strtotime($ym)) . ')';
                        	    }
                        	}
                        	?>
                            <tr>
                                <td><?php echo $ym; ?></td>
                                <td align="right"><?php echo $amount['items_total'] ? number_format($amount['items_total'], 0) . 'vnd' : ''; ?></td>
                                <td align="right"><?php echo $amount['rent_total'] ? number_format($amount['rent_total'], 0) . 'vnd' : ''; ; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    	<tr>
                            <td colspan="2">Không có dữ liệu trong thời gian này</td>
                        </tr>
                    <?php endif; ?>
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