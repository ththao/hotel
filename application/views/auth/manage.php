<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <h1 style="text-transform: uppercase;">KS <?php echo $this->session->userdata('fullname'); ?></h1>
    </div>
</div>

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div class="row col-md-12 floor">
            <a href="/room" class="col-xs-12 col-md-4 manage-item">PHÒNG</a>
            <a href="/item" class="col-xs-12 col-md-4 manage-item">NƯỚC</a>
            <a href="/paid" class="col-xs-12 col-md-4 manage-item">CHI PHÍ</a>
		</div>
		
		<?php if ($report_enable): ?>
            <div class="row col-md-12 floor">
                <a href="/rent" class="col-xs-12 col-md-6 manage-item">DANH SÁCH</a>
                <a href="/report/rent_list" class="col-xs-12 col-md-6 manage-item">BÁO LƯU TRÚ</a>
    		</div>
            <div class="row col-md-12 floor">
                <a href="/report" class="col-xs-12 col-md-6 manage-item">TỔNG KẾT</a>
                <a href="/report/tax" class="col-xs-12 col-md-6 manage-item">BÁO CÁO THUẾ</a>
    		</div>
		<?php endif; ?>
		
        <div class="row col-md-12 floor">
            <a href="/auth/password" class="col-xs-12 col-md-6 manage-item">ĐỔI MẬT KHẨU</a>
            <a href="/auth/admin_password" class="col-xs-12 col-md-6 manage-item">MẬT KHẨU ADMIN</a>
		</div>
		
		<?php if ($this->session->has_userdata('user_id') && $this->session->userdata('user_id') == 1 && $this->session->userdata('logged_in') == 1): ?>
        	<div class="row col-md-12 floor">
            	<a href="/remember" class="col-xs-12 col-md-4 manage-item">QUẢN LÝ ĐĂNG NHẬP</a>
            	<a href="/user" class="col-xs-12 col-md-4 manage-item">QUẢN LÝ ACCOUNTS</a>
        	</div>
        <?php endif; ?>
    </div>
</div>