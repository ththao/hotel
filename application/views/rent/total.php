<table id="report-table" class="table hotel-table" style="margin-bottom: 0px;">
    <!-- <tr>
        <td colspan="3" align="left">Vào: <?php //echo date('d-m-Y H:i', $data['check_in']); ?> - Hiện tại: <?php //echo date('d-m-Y H:i', $data['check_out']); ?></td>
    </tr> -->
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
    
	<?php if ($room->prepaid): ?>
        <tr>
            <td colspan="2"><b>Tạm tính:</b></td>
            <td align="right"><?php echo number_format($data['total_price'], 0); ?>vnd</td>
        </tr>
        <tr>
            <td colspan="2"><b>Đã trả</b></td>
            <td align="right"><?php echo number_format($room->prepaid, 0); ?>vnd</td>
        </tr>
        <?php if ($data['total_price'] - $room->prepaid > 0): ?>
            <tr>
                <td colspan="2"><h4>Thu thêm:</h4></td>
                <td align="right"><h4><?php echo number_format($data['total_price'] - $room->prepaid, 0); ?>vnd</h4></td>
            </tr>
        <?php elseif ($data['total_price'] - $room->prepaid == 0): ?>
            <tr>
                <td colspan="3"><h4>Đã đủ</h4></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="2"><h4>Trả lại:</h4></td>
                <td align="right"><h4><?php echo number_format($room->prepaid - $data['total_price'], 0); ?>vnd</h4></td>
            </tr>
        <?php endif; ?>
    <?php else: ?>
        <tr>
            <td colspan="2"><h4>Tạm tính:</h4></td>
            <td align="right"><h4><?php echo number_format($data['total_price'], 0); ?>vnd</h4></td>
        </tr>
	<?php endif; ?>
    <?php if (isset($data['notes']) && $data['notes']): ?>
        <tr>
            <td colspan="3" style="color: red;"><h5><b><?php echo $data['notes']; ?></b></h5></td>
        </tr>
	<?php endif; ?>
</table>