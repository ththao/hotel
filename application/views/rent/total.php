<table id="report-table" class="table hotel-table" style="margin-bottom: 0px;">
    <?php if (!isset($data['negotiate_price']) || !$data['negotiate_price']): ?>
        <tr>
            <td colspan="2" style="border-top: none;"><b>Số giờ</b></td>
            <td align="right" style="border-top: none;"><?php echo number_format(($data['check_out'] - $data['check_in'])/3600, 1); ?></td>
        </tr>
        <?php
            $note = $data['note'];
            $notes = explode(';', $note);
            foreach ($notes as $note_item) {
                $note_item = explode('=', $note_item);
        ?>
            <tr>
                <td colspan="2"><b><?php echo isset($note_item[0]) ? $note_item[0] : ''; ?></b></td>
                <td align="right"><?php echo isset($note_item[1]) ? $note_item[1] : ''; ?></td>
            </tr>
        <?php
            } 
        ?>
    <?php endif; ?>
    
    <?php $total_price = (isset($data['negotiate_price']) && $data['negotiate_price']) ? $data['negotiate_price'] : $data['total_price']; ?>
    
    <tr>
        <td colspan="2"><b><?php echo (isset($data['negotiate_price']) && $data['negotiate_price']) ? 'Giá thỏa thuận:' : 'Tạm tính:'; ?></b></td>
        <td align="right"><?php echo number_format($total_price, 0); ?>vnd</td>
    </tr>
	<?php if ($room->prepaid): ?>
        <tr>
            <td colspan="2"><b>Đã trả</b></td>
            <td align="right"><?php echo number_format($room->prepaid, 0); ?>vnd</td>
        </tr>
        <?php if ($total_price - $room->prepaid > 0): ?>
            <tr>
                <td colspan="2" style="color: green;"><h4>Thu thêm:</h4></td>
                <td align="right" style="color: green;"><h4><?php echo number_format($total_price - $room->prepaid, 0); ?>vnd</h4></td>
            </tr>
        <?php elseif ($total_price - $room->prepaid == 0): ?>
            <tr>
                <td colspan="3"><h4>Đã đủ</h4></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="2" style="color: red;"><h4>Trả lại:</h4></td>
                <td align="right" style="color: red;"><h4><?php echo number_format($room->prepaid - $total_price, 0); ?>vnd</h4></td>
            </tr>
        <?php endif; ?>
	<?php endif; ?>
    <?php if (isset($data['notes']) && $data['notes']): ?>
        <tr>
            <td colspan="3" style="color: red;"><h5><b><?php echo ((isset($data['negotiate_price']) && $data['negotiate_price']) ? ('Thuê phòng thỏa thuận ' . number_format($total_price, 0) . '; ') : '') . $data['notes']; ?></b></h5></td>
        </tr>
	<?php endif; ?>
</table>