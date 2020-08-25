<?php
if ($this->session->userdata('expired_at') > time()) {
    $text = 'Tài khoản của bạn có hạn sử dụng đến ngày ' . date('d', $this->session->userdata('expired_at')) . ' tháng ' . date('m', $this->session->userdata('expired_at')) . ' năm ' . date('Y', $this->session->userdata('expired_at'));
} else {
    $text = 'Tài khoản của bạn đã hết hạn sử dụng vào ngày ' . date('d', $this->session->userdata('expired_at')) . ' tháng ' . date('m', $this->session->userdata('expired_at')) . ' năm ' . date('Y', $this->session->userdata('expired_at')) . '. Vui lòng click "Gia Hạn" để tiếp tục sử dụng.';
}
?>

<?php if ($this->session->userdata('expired_at') <= time() + 7*86400): ?>
<div class="runtext-container">
	<div class="main-runtext">
		<marquee onmouseover="this.stop();" onmouseout="this.start();">
    		<div class="holder">
    			<div class="text-container"><?php echo $text; ?></div>
    		</div>
		</marquee>
    </div>
</div>
<?php endif; ?>

<div class="left-menu">
    <button type="button" class="btn btn-menu dropdown-toggle text-transform" data-toggle="dropdown" aria-expanded="false">
        <img src="../../../../images/left-menu/1.png">
    </button>
    <ul class="dropdown-menu space-dropdown" role="menu">
        <li>
            <a href="/site" class="left-sidebar-active">
                <div class="left-menu-item">
                    <span class="menu-icon menu-home"></span>
                    <div class="clearfix"></div>
                    <p class="">Trang Chủ</p>
                </div>
            </a>
        </li>
        <li>
            <a href="/auth/admin">
                <div class="left-menu-item open-account">
                    <i class="menu-icon menu-setting"></i>
                    <div class="clearfix"></div>
                    <p class="">QUẢN LÝ</p>
                </div>
            </a>
        </li>
        <li>
            <a href="/report/rent_list">
                <div class="left-menu-item open-account">
                    <i class="menu-icon menu-paid"></i>
                    <div class="clearfix"></div>
                    <p class="">BÁO LƯU TRÚ</p>
                </div>
            </a>
        </li>
        <li>
            <a href="/auth/logout">
                <div class="left-menu-item open-account">
                    <i class="menu-icon menu-logout"></i>
                    <div class="clearfix"></div>
                    <p class="">ĐĂNG XUẤT</p>
                </div>
            </a>
        </li>
    </ul>
</div>
<!--End left menu-->