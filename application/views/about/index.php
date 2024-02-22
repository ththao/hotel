<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-8 page-name" style="float: left;">
            <h1>Hướng dẫn sử dụng</h1>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content" class="guide-content">
    <div class="container-fluid">
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p1" style="text-transform: uppercase;">I. Thiết lập thông tin phòng</a>
					</h4>
				</div>
				<div id="p1" class="panel-collapse collapse in">
					<div class="panel-body">
						<p>Để thiết lập thông tin phòng, cần nhập mật khẩu quản lý tại <a href="/admin/auth">đây</a>.</p>
						<p>Sau khi đăng nhập thành công, click nút <b>PHÒNG</b> để xem/cập nhật/thêm phòng.</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p2" style="text-transform: uppercase;">II. Thiết lập thông tin nước uống</a>
					</h4>
				</div>
				<div id="p2" class="panel-collapse collapse in">
					<div class="panel-body">
						<p>Để thiết lập thông tin nước uống, cần nhập mật khẩu quản lý tại <a href="/admin/auth">đây</a>.</p>
						<p>Sau khi đăng nhập thành công, click nút <b>NƯỚC</b> để xem/cập nhật/thêm nước uống.</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p3" style="text-transform: uppercase;">III. Thuê phòng</a>
					</h4>
				</div>
				<div id="p3" class="panel-collapse collapse in">
					<div class="panel-body">
						
                		<div class="panel-group">
                			<div class="panel panel-default">
                				<div class="panel-heading">
                					<h4 class="panel-title">
                						<a data-toggle="collapse" href="#p31" style="text-transform: uppercase;">1. Nhận phòng</a>
                					</h4>
                				</div>
                				<div id="p31" class="panel-collapse collapse in">
                					<div class="panel-body">
                						<p>Tại <a href="/site">trang chính</a>, các phòng chưa có người thuê sẽ hiển thị màu xanh nhạt và các nút <b>THUÊ GIỜ</b> hoặc <b>THUÊ NGÀY</b></p>
                						<p>Khi khách thuê nhận phòng thì click vào 1 trong 2 nút kể trên (tùy vào khách thuê loại hình nào), sẽ chuyển sang trang thông tin thuê phòng.</p>
                						<p>Hệ thống bắt đầu tính tiền ngay tại thời điểm nhấn nút.</p>
                						<p>Tại <a href="/site">trang chính</a>, vào trang thông tin thuê phòng bằng cách nhấn nút <b>ĐANG THUÊ GIỜ</b> hoặc <b>ĐANG THUÊ NGÀY</b></p>
                					</div>
                				</div>
                			</div>
                		</div>
						
                		<div class="panel-group">
                			<div class="panel panel-default">
                				<div class="panel-heading">
                					<h4 class="panel-title">
                						<a data-toggle="collapse" href="#p32" style="text-transform: uppercase;">2. Thông tin thuê phòng</a>
                					</h4>
                				</div>
                				<div id="p32" class="panel-collapse collapse in">
                					<div class="panel-body">
                						<p>Tại trang thông tin thuê phòng:</p>
                						<p>- Nếu khách thuê gửi giấy tờ như CMND, Bằng Lái, Hộ Chiếu, ... thì click vào nút <b>GIẤY TỜ</b>.</p>
                						<p>- Nếu khách thuê gửi xe máy/ ôtô thì click vào nút <b>XE</b>.</p>
                						<p>- Nếu khách thuê sử dụng nước uống thì click vào nút tương ứng như <b>NƯỚC SUỐI</b> hoặc <b>BIA</b>.</p>
                						<p>- Nếu khách thuê đi nhiều người và cần tính phí phụ thu thì click vào nút <b>PHỤ THU</b>, click 1 lần tương ứng với 1 ngày phụ thu.</p>
                					</div>
                				</div>
                			</div>
                		</div>
						
                		<div class="panel-group">
                			<div class="panel panel-default">
                				<div class="panel-heading">
                					<h4 class="panel-title">
                						<a data-toggle="collapse" href="#p33" style="text-transform: uppercase;">3. Đổi/hủy phòng</a>
                					</h4>
                				</div>
                				<div id="p33" class="panel-collapse collapse in">
                					<div class="panel-body">
                						<p>Tại trang thông tin thuê phòng:</p>
                						<p>- Nếu khách thuê muốn đổi phòng thì click vào nút <b>ĐỔI PHÒNG</b>.</p>
                						<p>- Nếu khách thuê muốn hủy phòng thì cần vào trang quản lý, thực hiện giống như <b>4. Thay đổi thông tin thuê phòng</b> và click nút <b>XÓA</b>.</p>
                					</div>
                				</div>
                			</div>
                		</div>
						
                		<div class="panel-group">
                			<div class="panel panel-default">
                				<div class="panel-heading">
                					<h4 class="panel-title">
                						<a data-toggle="collapse" href="#p34" style="text-transform: uppercase;">4. Thay đổi thông tin thuê phòng</a>
                					</h4>
                				</div>
                				<div id="p34" class="panel-collapse collapse in">
                					<div class="panel-body">
                						<p>Để thay đổi thông tin thuê phòng, cần nhập mật khẩu quản lý tại <a href="/admin/auth">đây</a>.</p>
                						<p>Click nút <b>CẬP NHẬT</b> trên dòng thông tin thuê phòng cần thay đổi.</p>
                						<p>Trang cập nhật thông tin thuê phòng sẽ cho phép thay đổi các thông tin như giờ vào/ giờ ra/ số tiền.</p>
                					</div>
                				</div>
                			</div>
                		</div>
						
                		<div class="panel-group">
                			<div class="panel panel-default">
                				<div class="panel-heading">
                					<h4 class="panel-title">
                						<a data-toggle="collapse" href="#p35" style="text-transform: uppercase;">5. Trả phòng</a>
                					</h4>
                				</div>
                				<div id="p35" class="panel-collapse collapse in">
                					<div class="panel-body">
                						<p>Tại trang thông tin thuê phòng:</p>
                						<p>- Nếu khách thuê trả phòng thì click vào nút <b>TRẢ PHÒNG</b>, số tiền cần thu sẽ được hiển thị.</p>
                					</div>
                				</div>
                			</div>
                		</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p4" style="text-transform: uppercase;">IV. Đổi mật khẩu</a>
					</h4>
				</div>
				<div id="p4" class="panel-collapse collapse in">
					<div class="panel-body">
						<p>Để thiết lập lại mật khẩu, cần nhập mật khẩu quản lý tại <a href="/admin/auth">đây</a>.</p>
						<p>- Click nút <b>ĐỔI MẬT KHẨU</b> để thiết lập lại mật khẩu đăng nhâp.</p>
						<p>- Click nút <b>MẬT KHẨU ADMIN</b> để thiết lập lại mật khẩu quản lý.</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p5" style="text-transform: uppercase;">V. Xem báo cáo</a>
					</h4>
				</div>
				<div id="p5" class="panel-collapse collapse in">
					<div class="panel-body">
						<p>Để xem các báo cáo, cần nhập mật khẩu quản lý tại <a href="/admin/auth">đây</a>.</p>
						<p>- Click nút <b>BÁO CÁO</b> để xem tổng doanh thu theo ngày/tháng.</p>
						<p>- Click nút <b>DANH SÁCH</b> để xem thông tin thuê phòng theo ngày.</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#p6" style="text-transform: uppercase;">VI. Liên hệ</a>
					</h4>
				</div>
				<div id="p6" class="panel-collapse collapse in">
					<div class="panel-body">
						<p>Trường hợp quý khách có yêu cầu đặc biệt hoặc cần hỗ trợ vui lòng liên hệ:</p>
						<p>Email: ththao@ceresolutions.com</p>
						<p>Điện thoại: 0828868779</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>