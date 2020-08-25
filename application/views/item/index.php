<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/auth/manage">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>DS PHỤC VỤ</h1>
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
                        <th>Tên</th>
                        <th>Giá</th>
                        <th>Tình Trạng</th>
                        <th></th>
                    </tr>
					<?php foreach ($items as $item): ?>
                    <tr>
                        <td><p class="pad10px"><?php echo $item->name; ?></p></td>
                        <td class="text-transform color-default text-weight">
                            <p><?php echo number_format($item->price, 0); ?>vnd</p>
                        </td>
                        <td><?php echo $item->removed ? 'Không bán' : 'Đang bán'; ?></td>
                        <td align="right">
                        	<a href="/item/update/<?php echo $item->id; ?>">Cập nhật</a> | 
                            <?php if ($item->removed): ?>
                            	<a href="/item/getback/<?php echo $item->id; ?>">Bỏ Xóa</a>
                            <?php else: ?>
                            	<a href="/item/delete/<?php echo $item->id; ?>">Xóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <div class="add-floor-button pull-right">
                	<a href="/item/create">
                        <img src="../../../../images/add.png" id="add-floor" class="pointer">
                        <p><strong>THÊM</strong></p>
                    </a>
                </div>
            </div>
            <div class="clear-fix"></div>
        </div>
    </div>
</div>
<!--End content-->