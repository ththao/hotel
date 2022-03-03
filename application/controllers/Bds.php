<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bds extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->layout('layout/main_bds');
    }
    
    public function input()
    {
        if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        if ($this->session->userdata('user_id') != 1) {
            $this->session->unset_userdata('logged_in');
        }
        
        if ($_POST) {
            if (isset($_POST['ward_id']) && $_POST['ward_id'] && isset($_POST['map_id']) && $_POST['map_id'] && isset($_POST['latitude']) && $_POST['latitude'] && isset($_POST['longitude']) && $_POST['longitude'] && isset($_POST['price']) && $_POST['price'] && isset($_POST['acreage']) && $_POST['acreage']) {
                if (isset($_POST['map_id']) && $_POST['map_id']) {
                    $query = $this->db->select('id')->from('properties')->where('map_id', $_POST['map_id'])->get();
                    $row = $query->row();
                }
                
                if (!$row) {
                    $query = $this->db->select('id')->from('properties')->where('latitude', $_POST['latitude'])->where('longitude', $_POST['longitude'])->get();
                    $row = $query->row();
                }
                
                if (isset($row) && $row) {
                    $id = $row->id;
                } else {
                    $this->db->insert('properties', [
                        'ward_id' => $_POST['ward_id'],
                        'latitude' => $_POST['latitude'],
                        'longitude' => $_POST['longitude'],
                        'street' => isset($_POST['street']) ? $_POST['street'] : '',
                        'acreage' => $_POST['acreage'],
                        'map_id' => $_POST['map_id']
                    ]);
                    $id = $this->db->insert_id();
                }
                
                if ($id) {
                    $price_row = null;
                    
                    if (isset($_POST['source_url']) && $_POST['source_url']) {
                        $query = $this->db->select('id')->from('property_prices')->where('property_id', $id)->where('source_url', $_POST['source_url'])->get();
                        $price_row = $query->row();
                    }
                    
                    if ($price_row) {
                        $this->db->where('id', $price_row->id);
                        $this->db->update('property_prices', [
                            'price' => $_POST['price'],
                            'price_per_m' => intval($_POST['price']) / floatval($_POST['acreage']),
                            'value_date' => isset($_POST['value_date']) ? date('Y-m-d', strtotime($_POST['value_date'])) : date('Y-m-d'),
                            'source_url' => $_POST['source_url'],
                            'details' => $_POST['details']
                        ]);
                    } else {
                        $this->db->insert('property_prices', [
                            'property_id' => $id,
                            'price' => $_POST['price'],
                            'price_per_m' => intval($_POST['price']) / floatval($_POST['acreage']),
                            'value_date' => isset($_POST['value_date']) ? date('Y-m-d', strtotime($_POST['value_date'])) : date('Y-m-d'),
                            'source_url' => $_POST['source_url'],
                            'details' => $_POST['details'],
                            'created_by' => $this->session->userdata('user_id')
                        ]);
                    }
                }
                
                $query = $this->db->select('properties.id, properties.latitude, properties.longitude, properties.acreage, properties.map_id, property_prices.value_date, property_prices.price_per_m, districts.name AS district, wards.name AS ward')
                ->from('properties')->join('wards', 'wards.id = properties.ward_id', 'INNER')->join('districts', 'wards.district_id = districts.id', 'INNER')
                ->join('property_prices', 'property_prices.property_id = properties.id', 'INNER')
                ->where('properties.map_id', $_POST['map_id'])->order_by('value_date DESC')->get();
                $properties = $query->result();
                
                $markers = $this->propertiesToMarkers($properties);
                $marker = $markers[0];
                
                echo json_encode(array('status' => 1, 'title' => $marker['title'], 'latitude' => $marker['latitude'], 'longitude' => $marker['longitude']));
                exit();
                
            } else {
                echo json_encode(array('status' => 0, 'message' => 'Thiếu dữ liệu'));
                exit();
            }
        }
    }
    
    public function check_source_url()
    {
        if (isset($_POST['source_url']) && $_POST['source_url']) {
            $query = $this->db->select('id')->from('property_prices')->where('source_url', $_POST['source_url'])->get();
            $price_row = $query->row();
            
            if ($price_row) {
                echo json_encode(array('status' => 1, 'duplicated' => 1));
                exit();
            }
        }
        
        echo json_encode(array('status' => 1, 'duplicated' => 0));
        exit();
    }

	public function index()
	{
	    $params = array();
	    
	    $markers = '';
	    
	    if (!isset($_GET['w']) || !$_GET['w']) {
	        $_GET['w'] = 60009002;
	    }
	    
        $query = $this->db->select('id, district_id, latitude, longitude')->from('wards')->where('id', $_GET['w'])->get();
        $ward = $query->row();
        
        if ($ward) {
	        $params['district_id'] = $ward->district_id;
	        $params['ward_id'] = $ward->id;
	        $params['latitude'] = $ward->latitude;
	        $params['longitude'] = $ward->longitude;
	        
	        $query = $this->db->select('id, province_id')->from('districts')->where('id', $ward->district_id)->get();
	        $district = $query->row();
	        
	        $params['province_id'] = $district->province_id;
	        
	        $query = $this->db->select('id, name')->from('districts')->where('active', 1)->where('province_id', $district->province_id)->get();
	        $districts = $query->result();
	        $params['districts'] = $districts;
	        
	        $query = $this->db->select('id, name')->from('wards')->where('active', 1)->where('district_id', $ward->district_id)->get();
	        $wards = $query->result();
	        $params['wards'] = $wards;
        }
        
        $query = $this->db->select('properties.id, properties.latitude, properties.longitude, properties.acreage, properties.map_id, property_prices.value_date, property_prices.price_per_m, districts.name AS district, wards.name AS ward')
        ->from('properties')->join('wards', 'wards.id = properties.ward_id', 'INNER')->join('districts', 'wards.district_id = districts.id', 'INNER')
        ->join('property_prices', 'property_prices.property_id = properties.id', 'INNER')->order_by('properties.id, value_date DESC')->get();
        $properties = $query->result();
        
        $params['markers'] = $this->propertiesToMarkers($properties);
	    
	    $query = $this->db->select('id, name')->from('provinces')->where('active', 1)->get();
	    $provinces = $query->result();
	    $params['provinces'] = $provinces;
	    
	    $this->render('bds/index', $params);
	}
	
	private function propertiesToMarkers($properties)
	{
	    $markers = [];
	    if ($properties) {
	        foreach ($properties as $property) {
	            if (!isset($markers[$property->map_id])) {
	                $markers[$property->map_id] = [
	                    'map_id' => $property->map_id,
	                    'latitude' => $property->latitude,
	                    'longitude' => $property->longitude,
	                    'acreage' => $property->acreage,
	                    'district' => $property->district,
	                    'ward' => $property->ward,
	                    'prices' => []
	                ];
	            }
	            
	            $markers[$property->map_id]['prices'][] = $property->value_date . ": " . number_format($property->price_per_m) . " đ/m";
	        }
	    }
	    
	    $mrks = [];
	    if ($markers) {
	        foreach ($markers as $marker) {
	            $title = $marker['district'] . ' - ' . $marker['ward'] . '\nSố tờ/Số thửa: ' . $marker['map_id'] .  '\nDiện tích: ' . $marker['acreage'] . '\n';
	            foreach ($marker['prices'] as $price) {
	                $title .= $price . '\n';
	            }
	            $mrks[] = [
	                'latitude' => $marker['latitude'],
	                'longitude' => $marker['longitude'],
	                'title' => $title
	            ];
	        }
	    }
	    
	    return $mrks;
	}
	
	private function nl4br($str)
	{
	    return str_replace("\n", '<br />', $str);
	}
	
	public function load_districts()
	{
	    $query = $this->db->select('id, name')->from('districts')->where('active', 1)->where('province_id', $_POST['province_id'])->get();
	    $districts = $query->result();
	    
	    $html = '<option value="">Chọn Huyện/Quận</option>';
	    if ($districts) {
	        foreach ($districts as $district) {
	            $html .= '<option value="' . $district->id . '">' . $district->name . '</option>';
	        }
	    }
	    
	    echo json_encode(array('status' => 1, 'html' => $html));
	    exit();
	}
	
	public function load_wards()
	{
	    $query = $this->db->select('id, name')->from('wards')->where('active', 1)->where('district_id', $_POST['district_id'])->get();
	    $wards = $query->result();
	    
	    $html = '<option value="">Chọn Xã/Phường</option>';
	    if ($wards) {
	        foreach ($wards as $ward) {
	            $html .= '<option value="' . $ward->id . '">' . $ward->name . '</option>';
	        }
	    }
	    
	    echo json_encode(array('status' => 1, 'html' => $html));
	    exit();
	}
}