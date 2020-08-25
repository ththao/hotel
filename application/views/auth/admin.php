<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <h1 style="text-transform: uppercase;">KS <?php echo $this->session->userdata('fullname'); ?></h1>
    </div>
</div>

<div style="clear: both;"></div>

<div id="content">
    <div class="panel-heading">
		<h3 align="center">Nhập mật khẩu để truy xuất trang quản lý</h3>
	</div>

	<div align="center">
		<form method="post">
            <?php if ($message) { ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php } ?>
            <?php if ($err_message) { ?>
                <div class="alert alert-danger"><?php echo $err_message; ?></div>
            <?php } ?>
			<div style="width: 300px; display: flex; padding: 30px;">
				<input type="password" class="form-control txt-password" name="password" />&nbsp;&nbsp;
				<button class="btn btn-default btn-login">OK</button>
			</div>
		</form>
	</div>
</div>