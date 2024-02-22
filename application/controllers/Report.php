<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }

        $this->loadModel(array('rent_model', 'report_model', 'rent_receive_model', 'card_model'));
    }

	public function index()
	{
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        redirect('/auth/admin');
	    }
	    
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    if (!$user->report_enable) {
	        redirect('/auth/admin');
	    }
	    
	    if ($this->input->get()) {
    	    $month = $this->input->get('month');
    	    $year = $this->input->get('year');
	    } else {
	        $month = date("m");
	        $year = date("Y");
	    }
	    $data = $this->rent_model->getReport($year, $month);
	    
		$this->render('report/index', array('data' => $data, 'month' => $month, 'year' => $year));
	}
	
	public function remove($id)
	{
	    $report_item = $this->report_model->findOne(array('id' => $id));
	    if ($report_item) {
	       $this->report_model->update($id, array('remove' => $report_item->remove ? 0 : 1));
	    }
	    
	    echo json_encode(array('status' => 1));
	    exit();
	}
	
	public function change($id)
	{
	    $report_item = $this->report_model->findOne(array('id' => $id));
	    if ($report_item) {
	        $this->load->model(array('rent_model', 'room_model'));
	        $rent = $this->rent_model->findOne(array('id' => $report_item->rent_id));
	        
	        if ($rent) {
	            $room = $this->room_model->findOne(array('id' => $rent->room_id));
    	        $fields['night'] = $report_item->night == 1 ? 0 : 1;
    	        $fields = $this->generatePrice($fields, $room->night_price, $room->hourly_price);
    	        $this->report_model->update($id, $fields);
	        }
	    }
	    
	    echo json_encode(array('status' => 1));
	    exit();
	}
	
	public function rent_list()
	{
        if ($this->input->get()) {
            $date = $this->input->get('date');
            $month = $this->input->get('month');
            $year = $this->input->get('year');
        } else {
            $date = date("d");
            $month = date("m");
            $year = date("Y");
        }
        $data = $this->getRentList($year, $month, $date);
         
        $this->render('report/rent_list', array_merge($data, array('date' => $date, 'month' => $month, 'year' => $year)));
	}
	
	private function getRentList($year, $month, $date)
	{
	    $year = $year ? $year : date('Y');
	    $month = $month ? $month : date('m');
	    if ($date) {
	        $start = strtotime($year . '-' . $month . '-' . $date . ' 00:00:00');
	        $end = strtotime($year . '-' . $month . '-' . $date . ' 23:59:59');
	    } else {
	        $start = strtotime($year . '-' . $month . '-01 00:00:00');
	        $t = date('t', strtotime($year . '-' . $month));
	        $end = strtotime($year . '-' . $month . '-' . $t . ' 23:59:59');
	    }
	    
	    $query = $this->db->query('
	        SELECT rent.id AS rent_id, rent.hourly, room.name AS room_name, room.hourly_price, room.night_price, rent.check_in
            FROM rent
            INNER JOIN room ON room.id = rent.room_id
	        WHERE rent.user_id = ' . $this->session->userdata('user_id') . ' 
                AND ((rent.check_in >= ' . $start . ' AND rent.check_in <= ' . $end . ') OR (rent.check_in <= ' . $end . ' AND rent.check_out IS NULL))
    		ORDER BY rent.check_in'
        );
	    $data = $query->result();
	    
	    if ($data) {
	        foreach ($data as $row) {
                $received_items = $this->rent_receive_model->findAll(array(
                    'rent_id' => $row->rent_id,
                    'type <> "bike"' => null,
                    'card_id IS NOT NULL AND card_id <> ""' => null
                ));
                
                if ($received_items) {
                    $night = $row->hourly ? 0 : 1;
                    foreach ($received_items as $received_item) {
                        $fields = array(
                            'user_id' => $this->session->userdata('user_id'),
                            'rent_id' => $row->rent_id,
                            'room' => $row->room_name,
                            'rent_date' => date('Y-m-d', strtotime($year . '-' . $month . '-' . $date)),
                            'card_id' => $received_item->card_id
                        );
                        
        	            $report_item = $this->report_model->findOne(array(
        	                'rent_id' => $row->rent_id,
        	                'rent_date' => date('Y-m-d', strtotime($year . '-' . $month . '-' . $date)),
        	                'card_id' => $received_item->card_id
        	            ));
        	            if ($report_item) {
        	                if (!$report_item->unit_price || !$report_item->price) {
        	                    $fields['night'] = $report_item->night ? 1 : 0;
        	                    $fields = $this->generatePrice($fields, $row->night_price, $row->hourly_price);
        	                }
        	                $this->report_model->update($report_item->id, $fields);
    	                } else {
    	                    //if (time() >= strtotime($year . '-' . $month . '-' . $date . ' 20:30')) {
        	                    $fields['night'] = $night;
        	                    $fields = $this->generatePrice($fields, $row->night_price, $row->hourly_price);
        	                    $this->report_model->save($fields);
    	                    //}
    	                }
                    }
	            }
	        }
	    }
	    
	    return array(
	        'hourly_list' => $this->getReportList($year, $month, $date, 0),
	        'nightly_list' => $this->getReportList($year, $month, $date, 1)
	    );
	}
	
	private function getReportList($year, $month, $date, $night)
	{
	    $where = array(
		    'report.user_id' => $this->session->userdata('user_id'),
		    'report.rent_date' => date('Y-m-d', strtotime($year . '-' . $month . '-' . $date)),
	        'report.night' => $night
		);
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        $where['remove'] = 0;
	    }
	    
        $this->db->select('
            report.id, report.room, report.rent_date, report.unit, 
            report.unit_price, report.price, report.night, report.remove,
            card.type, card.name, card.number,
            card.nation, card.birthday, card.address, card.gender
        ');
		$this->db->from('report');
		$this->db->join('card', 'card.id = report.card_id', 'INNER');
		$this->db->where($where);
		$query = $this->db->get();
		
        return $query->result();
	}
	
	private function generatePrice($fields, $night_price, $hourly_price)
	{
	    if ($fields['night']) {
	        $fields['price'] = 150000;
	        $fields['unit_price'] = 150000;
	        $fields['unit'] = 1;
	        $fields['tax_report'] = 1;
	    } else {
	        $hour_prices = array(0 => 50000, 1 => 75000, 2 => 100000);
	        $hour_unit = array(0 => 1, 1 => 1.5, 2 => 2);
	        $index = rand(0, 2);
	        $fields['price'] = $hour_prices[$index];
	        $fields['unit_price'] = $hourly_price;
	        $fields['unit'] = $hour_unit[$index];
	        $fields['tax_report'] = 1;
	    }
	    
	    return $fields;
	}
	
	public function tax()
	{
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        redirect('/auth/admin');
	    }
	    
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    if (!$user->report_enable) {
	        redirect('/auth/admin');
	    }
	    
	    $this->render('report/tax');
	}
	
	public function download()
	{
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        redirect('/auth/admin');
	    }
	    
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    if (!$user->report_enable) {
	        redirect('/auth/admin');
	    }
	    
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');
	    
	    $m = $this->input->get('m');
	    $y = $this->input->get('y');
	    if (date('Y-m') == date('Y-m', strtotime($y . '-' . $m))) {
	        $t = date('d');
	    } else {
	        $t = date('t', strtotime($y . '-' . $m));
	    }
        
	    $this->db->select('
            report.id, report.rent_id, report.room, report.rent_date, report.unit,
            report.unit_price, report.price, report.night, report.remove,
            card.type, card.name, card.number,
            card.nation, card.birthday, card.address, card.gender
        ');
	    $this->db->from('report');
	    $this->db->join('card', 'card.id = report.card_id', 'INNER');
        $this->db->where('report.user_id', $this->session->userdata('user_id'));
        $this->db->where('report.remove', 0);
        $this->db->like('report.rent_date', date('Y-m', strtotime($y . '-' . $m)));
        $this->db->order_by('report.rent_date, report.night, report.rent_id');
        $query = $this->db->get();
        $data = $query->result();
	    
        if ($data) {
    	    $this->load->library("Pdf");
            $pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setFontSubsetting(false);
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            
            //$pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle('BÁO CÁO THUẾ');
            
            $date = '';
            $total = 0;
            $rows = array();
            foreach ($data as $row) {
                if ($date == '' || $date <> $row->rent_date) {
                    if (!empty($rows)) {
                        $this->createTaxReportPage($pdf, $date, $rows);
                    }
                    $rows = array();
                    $date = $row->rent_date;
                }
                $rows[] = $row;
                $total += $row->price;
            }
            if (!empty($rows)) {
                $this->createTaxReportPage($pdf, $date, $rows);
            }
            
            $pdf->AddPage();
            $pdf->SetFont('times', '', 15);
            $total_month = '<h1>DOANH THU THÁNG: ' . number_format($total, 0) . '</h1>';
            $pdf->writeHTML($total_month, true, false, true, false, '');
            
            $pdf->Output();
        }
	}
	
	private function createTaxReportPage($pdf, $date, $rows)
	{
	    // Add a page
	    $pdf->AddPage();
	    $pdf->SetFont('times', '', 15);
	    $pdf->writeHTML($this->renderTaxReportHeader(), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 18);
	    $pdf->writeHTML($this->renderTaxReportTitle($date), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 12);
	    $pdf->writeHTML($this->renderTaxReportBody($rows), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 15);
	    $pdf->writeHTML($this->renderTaxReportFooter(), true, false, true, false, '');
	    $pdf->endPage();
	}
	
	private function renderTaxReportFooter()
	{
	    $html = '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="center">Người lập</td>';
	    $html .= '<td align="center"></td>';
	    $html .= '<td align="center">Giám đốc</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    return $html;
	}
	
	private function renderTaxReportBody($rows)
	{
	    $total = 0;
	    
	    $html = '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="center" style="width: 4%; border: 1px solid black;">STT</td>';
	    $html .= '<td style="width: 57%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">';
	    $html .= '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="center" colspan="3" style="border-bottom: 1px solid black;">Thông tin về khách thuê phòng</td>';
	    $html .= '</tr>';
	    $html .= '<tr>';
	    $html .= '<td align="center" style="width: 28%; border-right: 1px solid black; border-bottom: 1px solid black;">Tên khách hàng</td>';
	    $html .= '<td align="center" style="width: 17%; border-right: 1px solid black; border-bottom: 1px solid black;">Số CMND</td>';
	    $html .= '<td align="center" style="width: 55%; border-bottom: 1px solid black;">Địa chỉ</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Phòng</td>';
	    $html .= '<td align="center" style="width: 8%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Hình thức</td>';
	    $html .= '<td align="center" style="width: 7%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Số ngày/giờ</td>';
	    $html .= '<td align="center" style="width: 6%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Đơn giá phòng</td>';
	    $html .= '<td align="center" style="width: 8%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Thành tiền</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">Ghi chú</td>';
	    $html .= '</tr>';
	    
	    $index = 1;
	    $rent_id = null;
	    foreach ($rows as $row) {
	        if ($rent_id && $rent_id == $row->rent_id) {
	            continue;
	        }
	        $total += $row->price;
	        $html .= $this->printTaxReportRow($index, $row);
	        $index++;
	        $rent_id = $row->rent_id;
	    }
	    if ($index < 8) {
	        while ($index <= 8) {
	            $html .= $this->printTaxReportRow($index);
	            $index++;
	        }
	    }
	    
	    $html .= '<tr>';
	    $html .= '<td align="left" colspan="7" style="width: 95%; border: 1px solid black;">';
	    $html .= '<table cellpadding="5">';
	    $html .= '<tr>';
	    $html .= '<td align="left">Tổng tiền</td>';
	    $html .= '<td align="right">' . number_format($total, 0) . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td>';
	    $html .= '</tr>';
	    
	    $html .= '<tr>';
	    $html .= '<td align="left" colspan="7" style="width: 95%; border: 1px solid black;">';
	    $html .= '<table cellpadding="5">';
	    $html .= '<tr>';
	    $html .= '<td align="left">Trong đó</td>';
	    $html .= '<td align="right"></td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td>';
	    $html .= '</tr>';
	    
	    $html .= '<tr>';
	    $html .= '<td align="left" colspan="7" style="width: 95%; border: 1px solid black;">';
	    $html .= '<table cellpadding="5">';
	    $html .= '<tr>';
	    $html .= '<td align="left">Doanh thu</td>';
	    $html .= '<td align="right">' . number_format($total/1.1, 0) . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td>';
	    $html .= '</tr>';
	    
	    $html .= '<tr>';
	    $html .= '<td align="left" colspan="7" style="width: 95%; border: 1px solid black;">';
	    $html .= '<table cellpadding="5">';
	    $html .= '<tr>';
	    $html .= '<td align="left">Thuế GTGT</td>';
	    $html .= '<td align="right">' . number_format($total/11, 0) . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="right" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    return $html;
	}
	
	private function renderTaxReportHeader()
	{
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    
	    $html = '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="left">' . $user->fullname . '</td>';
	    $html .= '<td align="right">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>';
	    $html .= '</tr>';
	    $html .= '<tr>';
	    $html .= '<td align="left">Địa chỉ: ' . $user->address . '</td>';
	    $html .= '<td align="right">Độc lập - Tự do - Hạnh Phúc &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $html .= '</tr>';
	    $html .= '<tr>';
	    $html .= '<td align="left">MST: ' . $user->tax_id . '</td>';
	    $html .= '<td align="right">-----o0o-----  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    return $html;
	}
	
	private function renderTaxReportTitle($date)
	{
	    $html = '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="center" colspan="2">BẢNG KÊ BÁN LẺ HÀNG HÓA, DỊCH VỤ TRỰC TIẾP CHO NGƯỜI TIÊU DÙNG</td>';
	    $html .= '</tr>';
	    $html .= '<tr>';
	    $html .= '<td align="center" colspan="2">Ngày ' . date('d', strtotime($date)) . ' tháng ' . date('m', strtotime($date)) . ' năm ' . date('Y', strtotime($date)) . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    return $html;
	}
	
	private function printTaxReportRow($index, $row=null)
	{
	    $html = '<tr>';
	    $html .= '<td align="center" style="width: 4%; border: 1px solid black;">' . $index . '</td>';
	    $html .= '<td style="width: 57%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">';
	    $html .= '<table>';
	    $html .= '<tr>';
	    $html .= '<td style="width: 28%; border-right: 1px solid black; border-bottom: 1px solid black;">' . ($row ? $row->name : '') . '</td>';
	    $html .= '<td style="width: 17%; border-right: 1px solid black; border-bottom: 1px solid black;">' . ($row ? $row->number : '') . '</td>';
	    $html .= '<td style="width: 55%; border-bottom: 1px solid black;">' . ($row ? $row->address : '') . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    $html .= '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">' . ($row ? $row->room : '') . '</td>';
	    $html .= '<td align="center" style="width: 8%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">' . ($row ? ($row->night == '' ? '' : ($row->night ? 'Qua đêm' : 'Thuê giờ')) : '') . '</td>';
	    $html .= '<td align="center" style="width: 7%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">' . ($row && $row->unit ? number_format($row->unit, 1) : '') . '</td>';
	    $html .= '<td align="center" style="width: 6%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">' . ($row && $row->unit_price ? number_format($row->unit_price, 0) : '') . '</td>';
	    $html .= '<td align="center" style="width: 8%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">' . ($row ? number_format($row->price, 0) : '') . '</td>';
	    $html .= '<td align="center" style="width: 5%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></td>';
	    $html .= '</tr>';
	    return $html;
	}
	
	public function receipt()
	{
	    set_time_limit(0);
	    ini_set('memory_limit', '-1');
	     
	    $rent_id = $this->input->get('rent_id');
	
	    $this->db->select('
            rent.id AS rent_id, room.name AS room, "' . date('d-m-Y') . '" AS rent_date, "" AS unit,
            "" AS unit_price, rent.total_price AS price, "" AS night,
            card.type, card.name, card.number,
            card.nation, card.birthday, card.address, card.gender
        ');
	    $this->db->from('rent');
	    $this->db->join('room', 'room.id= rent.room_id', 'LEFT OUTER');
	    $this->db->join('rent_receive', 'rent_receive.rent_id = rent.id', 'LEFT OUTER');
	    $this->db->join('card', 'card.id = rent_receive.card_id', 'LEFT OUTER');
	    $this->db->where('rent.user_id', $this->session->userdata('user_id'));
	    $this->db->where('rent.id', $rent_id);
	    $query = $this->db->get();
	    $data = $query->result();
	     
	    if ($data) {
	        $this->load->library("Pdf");
	        $pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
	        $pdf->setFontSubsetting(false);
	        $pdf->SetPrintHeader(false);
	        $pdf->SetPrintFooter(false);
	
	        //$pdf->SetCreator(PDF_CREATOR);
	        $pdf->SetTitle('HÓA ĐƠN');
	        
	        if (!empty($data)) {
	            $this->createReceiptPage($pdf, date('d-m-Y'), $data);
	        }
	
	        $pdf->Output();
	    }
	}
	
	private function createReceiptPage($pdf, $date, $rows)
	{
	    // Add a page
	    $pdf->AddPage();
	    $pdf->SetFont('times', '', 15);
	    $pdf->writeHTML($this->renderTaxReportHeader(), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 18);
	    $pdf->writeHTML($this->createReceiptTitle($date), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 12);
	    $pdf->writeHTML($this->renderTaxReportBody($rows), true, false, true, false, '');
	    
	    $pdf->SetFont('times', '', 15);
	    $pdf->writeHTML($this->renderTaxReportFooter(), true, false, true, false, '');
	    $pdf->endPage();
	}
	
	private function createReceiptTitle($date)
	{
	    $html = '<table>';
	    $html .= '<tr>';
	    $html .= '<td align="center" colspan="2">HÓA ĐƠN DỊCH VỤ</td>';
	    $html .= '</tr>';
	    $html .= '<tr>';
	    $html .= '<td align="center" colspan="2">Ngày ' . date('d', strtotime($date)) . ' tháng ' . date('m', strtotime($date)) . ' năm ' . date('Y', strtotime($date)) . '</td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    return $html;
	}
}