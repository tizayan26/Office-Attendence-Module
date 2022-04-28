<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of login
 *
 * @author nasirul
 */
class login_model extends CI_Model{
    public $favicon;
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url','html','form','date'));
//        $this->load->helper('url');
//        $this->load->helper('html');
//        $this->load->helper('form');
//        $this->load->helper('date');
 	$this->load->language(array('label'));
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->favicon =array('href' => $this->config->item('favicon'),'rel' => 'icon','type' => 'image/png');
    }
    
    public function load_loginFormData(){
        echo $this->get_Local_Date_Time();
        $data['link'] = array($this->favicon,$this->config->item('site_css'));
        $data['form'] = 'login';
        $data['title'] = 'Login';
        $data['width'] = '420';
        $data['height'] = '100';
        $data['field'] = array(
            'Username' => array('input' => array('name'=>'username','maxlength'=>'100','value' => set_value('username'),'tabindex'=>1)),
            'Password' => array('password' => array('name'=>'password','maxlength'=>'100','value' => set_value('password'),'tabindex'=>2)),
            '' => array('submit' => array('name'=>'login','value'=>'Login','class'=>'btnLogin','tabindex'=>'3'))
        );
        $data['html'] = '<div style="margin:50px 0;text-align:center;"><img src="http://192.168.110.12/ci_at/assets/images/Fixture-2018.jpg"/></div>';
        return $data;
    }
    
    /*Form Validation Rules*/
    public function load_ValidationConfig(){
         $config = array(
             array(
                    'field'   => 'username', 
                    'label'   => 'Username', 
                    'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
                 ),
              array(
                    'field'   => 'password', 
                    'label'   => 'Password', 
                    'rules'   => 'trim|required|xss_clean|callback_check_Database'
                 )
           );
           return $config;
    }
    /*End of Form Validation Rules*/
    public function check_Login(){
        $this->load->helper('encryption');
        $encryption = new Encryption;
        $password = $encryption->encrypt($this->input->post('password'));
        $this->db->select('tbl_user_info.F_Employee_ID,tbl_user_info.Full_Name,tbl_user_type.User_Permission_Type AS Permission_Type,tbl_login_info.Login_ID AS ID,tbl_login_info.User_Name AS User_Name,tbl_user_info.User_ID AS UID');
        $this->db->from('tbl_login_info');
        $this->db->join('tbl_user_type','tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID','inner');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_login_info.F_User_ID','inner');
//        $this->db->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','left');
        $this->db->where('tbl_login_info.Is_Exist',1); 
        $this->db->where('tbl_user_info.Is_Exist',1);
        $this->db->where('User_Name', $this->input->post('username')); 
        $this->db->where('Password',$password);
        $this->db->limit(1, 0);
        $query=$this->db->get();
        return $query->row();
    }
    
    public function get_Local_Date_Time(){
      $timestamp = now();
      $datestring = "%Y-%m-%d %H:%i:%s";
      $timezone = 'UP6';//UTC
      $daylight_saving = FALSE;
      return mdate($datestring,gmt_to_local($timestamp, $timezone, $daylight_saving));
    }
    
    public function get_Local_Date($datestring=NULL){
      $timestamp = now();
      if($datestring==NULL)
      $datestring = "%Y-%m-%d";
      $timezone = 'UP6';
      $daylight_saving = FALSE;
      return mdate($datestring,gmt_to_local($timestamp, $timezone, $daylight_saving));
    }
    
    public function get_Local_Time(){
      $timestamp = now();
      $datestring = "%H:%i:%s";
      $timezone = 'UP6';//'UTC';
      $daylight_saving = FALSE;
      return mdate($datestring,gmt_to_local($timestamp, $timezone, $daylight_saving));
    }
    
    /*Store Session ifo to db*/
    public function store_Session_to_DB($session){
        $data = array(
            'session_id'=>$session['session_id'],
            'ip_address'=>$session['ip_address'],
            'user_agent'=>$session['user_agent'], 
            'last_activity'=>$session['last_activity'],
            'user_data'=>'Login ID:'.$session['logged_in']['id'].'|Name:'.$session['logged_in']['fullname'].'|Time:'.$session['logged_in']['time'],
        );
        $this->db->insert('tbl_sessions_info',$data);   
    }
    /*End of Store Session info to db*/
    
    public function is_Logged_In(){
          $user = $this->session->userdata('logged_in');
          return $user['logged_in']? TRUE : FALSE;
    }
    
    public function store_LoginRecord_to_DB($user_id,$employee_id){
        $result=$this->db->distinct()->select('Sun,Mon,Tue,Wed,Thu,Fri,Sat')->get_where('tbl_work_days',array('F_Employee_ID' =>  $employee_id))->result();
        foreach($result as $record){
        $offdays=$this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
        }
        $weekcount= mdate('%w',  strtotime(now()));
        $result_ot = $this->db->distinct()->select('In')->get_where('tbl_office_time', array('F_Employee_ID' => $employee_id, 'Weekday'=> $weekcount))->result();
        foreach($result_ot as $row_ot){
            $office_in = $row_ot->In;
        }
        $data = array(
            'F_User_ID'=>$user_id,
            'Date' =>$this->get_Local_Date(),
            'In_Time' =>$this->get_Local_Time(),
            'Offday' => implode(',', $offdays),
            'Office_In' => $office_in
        );
        $this->db->insert('tbl_login_record',$data);  
    }
    
    public function not_Present($user_id){
        $this->db->distinct();
        $this->db->select('In_Time');
        $this->db->limit(1);
        $row=$this->db->get_where('tbl_login_record',array('Date'=>$this->get_Local_Date(),'F_User_ID'=>$user_id))->row();
        return !$row ? TRUE : FALSE;
    }
    
    public function store_LogoutRecord_to_DB(){
        $session=$this->session->userdata('logged_in');
        $set = array(
            'Out_Time'=>$this->get_Local_Time()
        );
        $where = array(
            'F_User_ID' => $session['id'],
            'Date' => $this->get_Local_Date()
        );
        $this->db->update('tbl_login_record', $set, $where);
    }
    
    public function getOffdays($array){
        $offdays_array = array();
        if($array[0]!=1)
        array_push($offdays_array, 0);
        if($array[1]!=1)
        array_push($offdays_array, 1);
        if($array[2]!=1)
        array_push($offdays_array, 2);
        if($array[3]!=1)
        array_push($offdays_array, 3);
        if($array[4]!=1)
        array_push($offdays_array, 4);
        if($array[5]!=1)
        array_push($offdays_array, 5);
        if($array[6]!=1)
        array_push($offdays_array, 6);
        return $offdays_array;
    }
}

?>
