<?php
$options = '';
foreach ($items as $index => $item) {
    $html = '';
	if ($item->type == 'id_card') {
	    $html .= 'CMND ' . $item->name;
	}
	if ($item->type == 'passport') {
	    $html .= 'Hộ chiếu ' . $item->name;
	}
	if ($item->type == 'cavet') {
	    $html .= 'Cà vẹt ' . $item->name;
	}
	if ($item->type == 'driving_license') {
	    $html .= 'Bằng lái ' . $item->name;
	}
	if ($item->type == 'bike') {
	    $html .= 'Xe biển số ' . $item->number;
	}
	$options .= '<option class="form-control" value="' . $item->id . '"' . ($index == 0 ? ' selected' : '') . '>' . $html . '</option>';
}
?>

<div class="row">
	<div class="col-xs-6 no-padding-left">
    	<select name="item_receive_id" class="form-control">
    		<?php echo $options; ?>
    	</select>
	</div>
	<div class="col-xs-6 no-padding-right">
		<input class="txt-number form-control" type="text" name="number" placeholder="Số" value="" />
	</div>
</div>
<div class="edit-card-field">
    <br/>
    <div class="row">
    	<input class="txt-name form-control" type="text" name="name" placeholder="Họ Tên" value="" />
    </div>
    <br/>
    <div class="row">
    	<input class="txt-address form-control" type="text" name="address" placeholder="Địa Chỉ" value="" />
    </div>
    <br/>
    <div class="row">
    	<div class="col-xs-4 no-padding-left">
    		<input class="txt-birthday form-control" type="text" name="birthday" placeholder="Ngày Sinh" value="" />
    	</div>
    	<div class="col-xs-4">
    		<select class="txt-gender form-control" name="gender">
    			<option value="1">Nam</option>
    			<option value="2">Nữ</option>
    		</select>
    	</div>
    	<div class="col-xs-4 no-padding-right">
    		<input class="txt-nation form-control" type="text" name="nation" placeholder="Dân Tộc" value="" />
    	</div>
    </div>
</div>