<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="dropdown-select" style="float: left;">
            <a class="btn btn-default btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        <div class="dropdown-select" style="float: right; width: 85px;">
            <select name="year" id="years" class="form-control ymd-select">
                <option value="">Year</option>
                <?php $cy = intval(date('Y')); ?>
                <?php for ($y=$cy-5; $y<=$cy; $y++): ?>
                	<option value="<?php echo $y; ?>" <?php echo $y == date('Y') ? 'selected' : ''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="dropdown-select" style="float: right; width: 65px;">
            <select name="month" id="month" class="form-control ymd-select">
                <option value="">Month</option>
                <?php for ($m=1; $m<=12; $m++): ?>
                	<option value="<?php echo $m; ?>" <?php echo $m == date('m') ? 'selected' : ''; ?>><?php echo $m; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
	<br/><br/>
    <div class="container-fluid">
        <div class="row col-md-3 floor">
		</div>
        <div class="row col-md-6 floor">
            <a href="#" class="col-xs-12 manage-item btn-download">DOWNLOAD BÁO CÁO THUẾ</a>
		</div>
        <div class="row col-md-3 floor">
		</div>
	</div>
</div>
<!--End content-->

<script>
$(document).ready(function() {
	$(".btn-download").click(function(e) {
		e.preventDefault();

		var win = window.open('/report/download?y=' + $('#years').val() + '&m=' + $('#month').val(), '_blank');
		if (win) {
		    //Browser has allowed it to be opened
		    win.focus();
		}
	});
});
</script>