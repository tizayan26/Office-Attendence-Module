<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
     
/**
 * Description of administrator_model
 *
 * @author Nasirul Akbar Khan
 */
class administrator_model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('login_model');
        $this->load->language(array('dropdown','label'));
        $this->load->helper(array('json','encryption'));
        $this->load->library('menu');
        $this->load->library('javascript');
        $this->load->library('upload');
        $this->load->library('table_to_excel');
        $this->load->library('datatable');
    }
//General Functions
    public function regularDateFormatConverter($date){
        $date = strtotime($date);
        $date_string = '%d-%m-%Y';
        return mdate($date_string,$date);
    }
        
    public function systemDateFormatConverter($date){
        $date = strtotime($date);
        $date_string = '%Y-%m-%d';
        return mdate($date_string,$date);
    }
        
    public function leaveCount($user_id=NUll,$flag=0){
        $result = $this->get_Specific_Info('tbl_leave_record',array('Is_Exist' => 1, 'Is_Processed' => 1,'F_User_ID' => $user_id,$flag==0 ? 'DATE_FORMAT(From_Date,"%Y-%m")' : 'DATE_FORMAT(From_Date,"%Y")' => $flag==0 ? mdate('%Y-%m',now()) : mdate('%Y',now())),'Day,From_Date,To_Date',2);
//        $date_array = array();
        $days = array();
        foreach($result as $row){
//            $from= $row->From_Date;
//            $to = $row->To_Date;
//            $temp_array = $this->showDates($from, $to);
//            $date_array = array_merge($date_array,$temp_array); 
              $days[] =$row->Day; 
        }
//        return count($date_array);
        return array_sum($days);
    }
        
    public function leaveCountNew($user_id=NUll){
        $this->db->where('DATE_FORMAT(From_Date,"%Y-%m-%d") BETWEEN \''.mdate('%Y-%m-25',strtotime("-1 month",now())).'\' AND \''.mdate('%Y-%m-25',now()).'\'');
        $result = $this->get_Specific_Info('tbl_leave_record',array('Is_Exist' => 1, 'Is_Processed' => 1,'F_User_ID' => $user_id),'Day,From_Date,To_Date',2);
        $days = array();
        foreach($result as $row){
              $days[] =$row->Day; 
        }
        return array_sum($days);
    }
        
    public function showDates($from,$to){
        $date_array = array();
        $thisTime = strtotime($from);
        $endTime = strtotime($to);
        while($thisTime <= $endTime){
            $thisDate = mdate('%Y-%m-%d', $thisTime);
            array_push($date_array, $thisDate);
            $thisTime = strtotime('+1 day', $thisTime); // increment for loop
        }
        return $date_array;
    }
        
    public function  load_Menu(){
        $session=$this->session->userdata('logged_in');
        $query = $this->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $session['type']),'User_Permission_Type,Report,All_Report,Leave_Manager,Hierarchy,Leave_Preapproval,Holiday,Company,Location,Department,User_Manager,Recruitment_Manager,Recruitment_Preapproval,Absent_List_IT',1);
        $row=$this->get_Specific_Info('tbl_login_record',array('Date' => $this->login_model->get_Local_Date(),'F_User_ID' => $session['id']),'Out_Time',1);
        $row_profile_pic = $this->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1,'Employee_ID' =>  $session['eid']), 'Photo', 1);
        return $this->menu->load_Menu($session,$query,$row->Out_Time,(isset($row_profile_pic->Photo) ? img(array('src'=>site_url(array('uploaded_images','thumbnails_20',$row_profile_pic->Photo)),'style'=>'border-radius:25%')) : NULL));
    }
        
    public function load_Datepicker($array_id){
        $this->jquery->script(base_url('assets/js/jquery/jquery-ui.min.js'));
        foreach($array_id as $id)
        $this->javascript->datepicker('#'.$id,"dateFormat:'dd-mm-yy',changeMonth: true,changeYear: true");
        $this->javascript->external();
        $this->javascript->compile();
    }
        
    public function load_TableSorter($array_id,$options = ''){
        $this->jquery->script(base_url('assets/js/jquery/jquery.tablesorter.min.js'));
        foreach($array_id as $id)
        $this->jquery->tablesorter('#'.$id,$options);
        $this->javascript->external();
        $this->javascript->compile();
    }
        
    public function get_Specific_Info($table,$where_array,$select=NULL,$limit=1){
        if($select!=NULL)$this->db->select($select);
        if($limit<=1){
            $query=$this->db->get_where($table,$where_array,$limit,0);
            return $query->row();
        }else{
            $query=$this->db->get_where($table,$where_array);
            return $query->result();
        }
        $query->free_result();
    }
    private function numberArray($from,$to,$zero_leading){
        $array = array();
        for($i=$from;$i<=$to;$i++){
            $zero_leading ==1 ? $value = str_pad($i, 2, "0", STR_PAD_LEFT) : $value = $i;
            array_push($array,$value);
        }
        return $array;
    }
    public function delete_Specific_Info($table,$id){
        $this->db->delete($table,$id);
    }
    public function load_User_ValidationConfig($id){
        $config = array(
            array(
                'field'   => 'employee', 
                'label'   => 'Employee', 
                'rules'   => 'trim|required|xss_clean'
            ),
                
            array(
                'field'   => 'full_name', 
                'label'   => 'Full Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
            array(
                'field'   => 'address', 
                'label'   => 'Address', 
                'rules'   => 'trim|max_length[100]|xss_clean' //|required
            ),
            array(
                'field'   => 'date', 
                'label'   => 'Join Date', 
                'rules'   => 'trim|required|max_length[100]|xss_clean'
            ),
            array(
                'field'   => 'email', 
                'label'   => 'Email', 
                'rules'   => 'trim|max_length[80]|xss_clean' //|required|valid_email
            ),
            array(
                'field'   => 'user_name', 
                'label'   => 'User Name', 
                'rules'   => $id>0 ? 'trim|required|min_length[3]|max_length[50]|xss_clean' : 'trim|required|min_length[3]|max_length[50]|is_unique[tbl_login_info.User_Name]|xss_clean',
            ),
            array(
                'field'   => 'password', 
                'label'   => 'Password', 
                'rules'   => 'trim|required|min_length[3]|max_length[100]|xss_clean'
            ),
            array(
                'field'   => 'confirm_password', 
                'label'   => 'Confirm Password', 
                'rules'   => 'trim|required|min_length[3]|max_length[100]|xss_clean|matches[password]'
            ),
            array(
                'field'   => 'permission', 
                'label'   => 'Permission', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'status', 
                'label'   => 'Status', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_UserList_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'ID', 
                'label'   => 'User', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_LeaveCalcultor_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'ID[]', 
                'label'   => 'User', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'Leave[]', 
                'label'   => 'Leave', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
        
        
    public function get_User_Inforamtion($id){
        $this->db->select('tbl_user_info.F_Employee_ID,tbl_user_info.User_ID,tbl_user_info.Full_Name,tbl_user_info.Email,tbl_user_info.Join_Date,tbl_user_info.Address,tbl_user_type.User_Permission_Type,tbl_user_type.User_Permission_ID,tbl_login_info.User_Name,tbl_login_info.`Password`,tbl_user_info.Is_Exist,Sun,Mon,Tue,Wed,Thu,Fri,Sat,In,Out');
        $this->db->from('tbl_login_info');
        $this->db->join('tbl_user_type','tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID','inner');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_login_info.F_User_ID','inner');
        $this->db->join('tbl_work_days','tbl_user_info.F_Employee_ID = tbl_work_days.F_Employee_ID','inner');
        $this->db->join('tbl_office_time','tbl_user_info.F_Employee_ID = tbl_office_time.F_Employee_ID','inner');
        $this->db->where($id);
        $this->db->limit(1, 0);
        $query=$this->db->get();
        return $query->row();
    }
        
    /*User Manager Operations*/
    //Email User Information
    private function email_User_Info(){
        $this->load->library('email');
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['wordwrap'] = TRUE;
        $config['validate'] = TRUE;
        $config['mailtype'] = 'html';
        $config['priority'] = 1;
            
        $this->email->initialize($config);
            
        $this->email->from('Attendance Software', 'Webmaster');
        $this->email->to($this->input->post('email'));
            
        $this->email->subject('Attendance Software Login Information');
        $this->email->message('<html><body><p>Dear '.$this->input->post('full_name').', <br/> Here is youe User Login Information for http://192.168.110.211/AT/ </p>'.br()
        .'<table><tr><td>User</td><td>:</td><td>'.$this->input->post('user_name').'</td></tr>
        <tr><td>Password<td>:</td><td>'.$this->input->post('password').'</td></tr></table></body></html>');
        $this->email->send();
    }
    public function load_UserEntryFromInfo($id){
        
        $encryption = new Encryption;
        $id=$encryption->decrypt($id);
        $this->load_Datepicker(array('join_date'));
            
        $js = "$('#employee1').focusout(function() { 
            
                    var employee_id = $('#employee').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Employee')."/' + employee_id,   
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $('#full_name').val(obj.Full_Name);
                            $('#email').val(obj.Email);
                            $('#address').val(obj.Address);
                            $('#join_date').val(obj.Join_Date);
                            $('#user_name').val(obj.RS_ID.toLowerCase());
                        }
                            
                    });
                        
                });";
//        $js .= $this->autocomplete->load_AutoComplete('employee');
        $this->javascript->ready($js);
        $this->javascript->compile();
            
        if($id>0)$row =$this->get_User_Inforamtion(array('User_ID' => $id));
        if(isset($row)){
            
           $password = $encryption->decrypt($row->Password);
        }
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '640';
        $data['height'] = '460';
        $data['title']=($id>0 ? 'Update' : 'Add New').nbs().'User';
        $data['form']='administrator/user_Entry'.(isset($row) ? '/'.$row->User_ID : '/'); 
        $data['hidden']= isset($row) ? array('id' => $row->User_ID) : NULL;
        $user = $this->session->userdata('logged_in');
        if($user['type']=="Super Administrator")
            $query=$this->db->get_where('tbl_user_type',array('Is_Exist' => 1));
        elseif($user['type']=="Administrator")
            $query=$this->db->get_where('tbl_user_type',array('Is_Exist' => 1,'tbl_user_type.User_Permission_ID <>' => 1));
        else
            $query=$this->db->get_where('tbl_user_type','Is_Exist = 1 AND User_Permission_ID NOT IN (1, 6)');
        $options_permission[NULL] = 'Please Select';
        foreach($query->result() as $rs)
            $options_permission[$rs->User_Permission_ID] = $rs->User_Permission_Type;
        $result_employee = $this->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1),'Employee_ID,Full_Name,RS_ID',2);
        $options_employee[NULL] = 'Please Select';
        foreach($result_employee as $row_employee)
            $options_employee[$row_employee->Employee_ID] = $row_employee->Full_Name.'['.$row_employee->RS_ID.']';
        $data['field']=array(
            'Employee' => array('dropdown' => array('employee',$options_employee,isset($row) ? $row->F_Employee_ID :($this->input->post('employee') ? $this->input->post('employee'): NULL),'id="employee"')),
//            'Employee' => array('input' => array('name'=>'employee','id'=>'category','placeholder'=>'Enter RS ID or Name','onfocus'=>'autoComplete(\''.site_url(array('administrator','employee_Search')).'\',null,\'employee\',\'employee_id\');','onblur'=>'Empty(this, \'employee_id\');','value'=> set_value('employee',(isset($row)  and count($row)) ? $row->Employee_ID : NULL)),
//                                                    'hidden' => array('name'=>'employee_id','id'=>'employee_id','type'=>'hidden','value'=> set_value('employee_id',  isset($row) ? $row->F_Employee_ID : $this->input->post('employee_id'))),
//                                                    'error' => 'employee_id'),
            'Full Name' => array('input' => array('name' => 'full_name','maxlength' => '50','size' => '30','value' =>  set_value('full_name', isset($row) ? $row->Full_Name : NULL), 'id' => 'full_name', 'readonly' => 'readonly')),
            'Email' => array('input' => array('name' => 'email','maxlength' => '80','size' => '30','value' => set_value('email', isset($row) ? $row->Email : NULL),'id' => 'email', 'readonly' => 'readonly')),
            'Contact Address' => array('textarea' => array('name' => 'address','rows' => '2','cols' => '32','value' => set_value('address', isset($row) ? $row->Address : NULL),'id' => 'address', 'readonly' => 'readonly')),
            'Join Date' => array('input' => array('name' => 'date','value' => set_value('date',mdate('%d-%m-%Y',  isset($row) ? strtotime($row->Join_Date) : now())),'id'=> 'join_date', 'readonly' => 'readonly')),       
            'fieldset' => 'Login Information',
            'User Name' => array('input' => array('name' => 'user_name','maxlength' => '50','size' => '30','value' =>  set_value('user_name', isset($row) ? $row->User_Name : NULL),'id'=>'user_name')),
            'Password' => array('password' => array('name' => 'password','maxlength' => '100','size' => '30','value' =>  set_value('password',isset($password) ? $password : NULL))),
            'Confirm Password' => array('password' => array('name' => 'confirm_password','maxlength' => '100','size' => '30','value' =>  set_value('confirm_password',isset($password) ? $password : NULL))),
            'close_fieldset' => '',
            'Permission' =>  array('dropdown' =>  array('permission',$options_permission,isset($row) ? $row->User_Permission_ID :($this->input->post('permission') ? $this->input->post('permission'): NULL))),
            'Status' => array('dropdown' =>  array('status',$this->lang->line('status'),isset($row) ? $row->Is_Exist :($this->input->post('status') ? $this->input->post('status'): NULL))),
            '' => array('submit' => array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert'))
            );
        $data['elements'] = array('employee');
        return $data; 
    }
        
    public function load_UserListViewInfo(){
        $encryption = new Encryption;
//        $this->load_TableSorter(array('grid'));
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/user_List';
        $data['title'] = 'User List';
        $data['width'] = '700';
        $data['height'] = '480';
        $result = $this->get_User_Total_Info();
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$encryption->encrypt($row->User_ID), FALSE);
            $Full_Name[] = $row->Full_Name;
            $Email[] = $row->Email;
            $User_Type[] = $row->User_Permission_Type;
            $User_Name[] = $row->User_Name;
            $visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Name' => $Full_Name,'Email' => $Email,'Permission' => $User_Type,'User' => $User_Name,'Mode' => $visibility);
        $data['width_th'] = array(5,20,20,20,20,5);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));//'Filter' => array('input' => array('id' => 'searchInput','value' => 'Type to filter','placeholder' => 'Type to filter')),
        return $data;
    }
        
    private function get_User_Total_Info(){
        $user = $this->session->userdata('logged_in');
        $this->db->select('tbl_user_info.User_ID,tbl_user_info.Full_Name,tbl_user_info.Email,tbl_user_type.User_Permission_Type,
tbl_login_info.User_Name,tbl_login_info.`Password`,tbl_user_info.Is_Exist,tbl_login_info.Login_ID');
        $this->db->from('tbl_user_info');
        $this->db->join('tbl_login_info','tbl_login_info.F_User_ID = tbl_user_info.User_ID','inner');
        $this->db->join('tbl_user_type','tbl_user_type.User_Permission_ID = tbl_login_info.F_User_Permission_ID','inner');
        if($user['type']!="Super Administrator") $this->db->where('tbl_user_type.User_Permission_ID <>', 1);
        $this->db->order_by('User_ID','desc');
        $query=$this->db->get();
        return $query->result();
    }
        
    public function insertUpdate_User_Info(){
        $encryption = new Encryption;
        $password = $encryption->encrypt($this->input->post('password'));
        $data_user = array(
            'F_Employee_ID' => $this->input->post('employee'),
            'F_User_Permission_ID' => $this->input->post('permission'),
                
            'Full_Name' => $this->input->post('full_name'),
            'Address' => $this->input->post('address'),
            'Email' => $this->input->post('email'),
            'Join_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('date'))),
            'Is_Exist' => $this->input->post('status')
            );
        $data_login = array(
            'F_User_Permission_ID' => $this->input->post('permission'),
            'User_Name' => $this->input->post('user_name'),
            'Password' =>  $password,
            'Is_Exist' => $this->input->post('status')
            );
                
                
        if($this->input->post('insert')){
            $this->db->insert('tbl_user_info',$data_user);
            $data_login['F_User_ID']=$this->db->insert_id();
            $this->db->insert('tbl_login_info',$data_login);
        } 
        if($this->input->post('update')){
            $this->db->where('User_ID',$this->input->post('id'));
            $this->db->update('tbl_user_info',$data_user);  
            $this->db->where('F_User_ID',$this->input->post('id'));
            $this->db->update('tbl_login_info',$data_login);
        }
        $this->email_User_Info();
            
    }
        
/*    public function update_User_Info(){
        if($this->input->post('update')){
            $encryption = new Encryption;
            $password = $encryption->encrypt($this->input->post('password'));
            $data = array(
                'F_Company_ID' => $this->input->post('company'),
                'F_Dept_ID' => $this->input->post('department'),
                'F_User_Permission_ID' => $this->input->post('Permission'),
                'Full_Name' => $this->input->post('full_name'),
                'Address' => $this->input->post('address'),
                'Email' => $this->input->post('email'),
                'Join_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('date'))),
                'Is_Exist' => $this->input->post('Visibility')
                );
            $this->db->where('User_ID',$this->input->post('id'));
            $this->db->update('tbl_user_info',$data);  
            $data = array(
                'F_User_Permission_ID' => $this->input->post('Permission'),
                'User_Name' => $this->input->post('user_name'),
                'Password' => $password,
                'Is_Exist' => $this->input->post('Visibility')
                );
            $this->db->where('F_User_ID',$this->input->post('id'));
            $this->db->update('tbl_login_info',$data);
            $data = array(
                'Sun' => $this->input->post('sunday') ? $this->input->post('sunday') : '0',
                'Mon' => $this->input->post('monday') ? $this->input->post('monday') : '0',
                'Tue' => $this->input->post('tuesday') ? $this->input->post('tuesday') : '0',
                'Wed' => $this->input->post('wednesday') ? $this->input->post('wednesday') : '0',
                'Thu' => $this->input->post('thursday') ? $this->input->post('thursday') : '0',
                'Fri' => $this->input->post('friday') ? $this->input->post('friday') : '0',
                'Sat' => $this->input->post('saturday') ? $this->input->post('saturday') : '0'
               );
            $this->db->where('F_User_ID',$this->input->post('id'));
            $this->db->update('tbl_work_days',$data);
            $query = $this->db->get_where('tbl_login_info',array('User_Name' => $this->input->post('user_name'), 'Password' => $password ));
            $data = array(
                'In' => $this->input->post('in_hr').':'.$this->input->post('in_min'),
                'Out' => $this->input->post('out_hr').':'.$this->input->post('out_min')
                );
            $this->db->where('F_User_ID',$this->input->post('id'));
            $this->db->update('tbl_office_time',$data);
            if(!$query->row()){
            $this->email_User_Info();
            }
        }
    }*/
    /*End of User Operation*/
    public function load_DeptEntryFromInfo($id){
        if($id>0)$row= $this->get_Specific_Info('tbl_dept_info',array('Dept_ID' => $id));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['width'] = '550';
        $data['height'] = '200';
        $data['title'] = ($id>0 ? 'Update' : 'Add').nbs().'Department';
        $data['form'] = 'administrator/dept_Entry'.(isset($row) ? '/'.$row->Dept_ID : '/');
        $data['hidden'] = isset($row) ? array('id' => $row->Dept_ID) : NULL;
        $data['field']=array(
                'Name' => array('input' => array('name' => 'dept_name','maxlength' => '50','size' => '40','value' =>  set_value('dept_name', isset($row) ? $row->Dept_Name : NULL))),
                'Description' => array('textarea' => array('name' => 'description','rows' => '3','cols' => '32','value' => set_value('description',  isset($row) ? $row->Description : NULL))),
                'Company' => array('dropdown' => array('company',$this->globals->getOptionsCompany(),isset($row) ? $row->F_Company_ID :($this->input->post('company') ? $this->input->post('company'): NULL))), 
            );
        $data['select']=array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['selected'] = array(isset($row) ? $row->Is_Exist : NULL);
        $data['submit'] = array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;
    }
        
    public function load_DeptListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/dept_List';
        $data['title'] = 'Department List';
        $data['width'] = '600';
        $data['height'] = '480';
        $this->db->distinct()->select('Dept_ID,Company_Name,Dept_Name,Description,tbl_dept_info.Is_Exist');
        $this->db->join('tbl_company_info','tbl_dept_info.F_Company_ID = tbl_company_info.Company_ID','left');
        $result = $this->db->get('tbl_dept_info')->result();
        $Check_ID = $Dept_Name = $Description = $visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Dept_ID, FALSE);
            $Dept_Name[] = $row->Dept_Name;
            $Description[] = $row->Description;
            $Company_Name[] = $row->Company_Name;
            $visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Department' => $Dept_Name,'Description' => $Description,'Company' => $Company_Name,'Status' => $visibility);
        $data['width_th'] = array(4,22,46,24,8);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
        
    public function load_InsertDept_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'dept_name', 
                'label'   => 'Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
            array(
                'field'   => 'description', 
                'label'   => 'Description', 
                'rules'   => 'trim|required|max_length[100]|xss_clean'
            )
        );
        return $config;
    }
        
    public function insertUpdate_Dept_Info(){
        $set = array(
              'F_Company_ID' => $this->input->post('company'),
              'Dept_Name' => $this->input->post('dept_name'),
              'Description' => $this->input->post('description'),
              'Is_Exist' => $this->input->post('Visibility')
              );
       if($this->input->post('insert'))
           $this->db->insert('tbl_dept_info',$set);
       if($this->input->post('update')){
            $this->db->where('Dept_ID',$this->input->post('id'));
            $this->db->update('tbl_dept_info',$set);      
        }
    }
        
    public function load_UserRole_ValidationCofig(){
        $config = array(
            array(
                'field'   => 'permission_name', 
                'label'   => 'Role Name', 
                'rules'   => 'required|xss_clean'
            )
        );
        return $config;
    }
        
    /*Permission Manager*/
    public function load_UserRoleEntryForm($id){
        if($id>0)$row = $this->get_Specific_Info('tbl_user_type',array('User_Permission_ID' => $id));
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['menu'] = $this->load_Menu();
        $data['width'] = '500';
        $data['height'] = '170';
        $data['title'] = ($id>0 ? 'Update' : 'Insert').nbs().'Role';
        $data['form']='administrator/userRole_Entry'.(isset($row) ? '/'.$row->User_Permission_ID : '/');
        $data['hidden']=isset($row) ? array('id' =>  $row->User_Permission_ID) : NULL;
        $data['field'] = array(
           'Role Name' => array('input' => array('name' => 'permission_name', 'size' => '30','value' =>  set_value('permission_name',isset($row) ? $row->User_Permission_Type : NULL)))
        );
        $data['select'] = array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['selected']=array(isset($row) ? $row->Is_Exist :($this->input->post('Visibility') ? $this->input->post('Visibility') : 1));
        $data['check_caption'] = 'Permission';
        $data['check'] = array('User Manager' => isset($row) ? $row->User_Manager : '0','Report' => isset($row) ? $row->Report : '0','Company' => isset($row) ? $row->Company : '0','Location' => isset($row) ? $row->Location : '0','Department' => isset($row) ? $row->Department : '0','All Report' => isset($row) ? $row->All_Report : '0','Leave Manager' => isset($row) ? $row->Leave_Manager : '0','Leave Pre-approval' => isset($row) ? $row->Leave_Preapproval : '0','Recruitment Manager' => isset($row) ? $row->Recruitment_Manager : '0','Recruitment Pre-approval' => isset($row) ? $row->Recruitment_Preapproval : '0','Holiday' => isset($row) ? $row->Holiday : '0','Hierarchy Manager' => isset($row) ? $row->Hierarchy : '0','Employee Manager'=> isset($row) ? $row->Employee_Manager : '0');  
        $data['submit']=array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;   
    }
        
    public function load_UserRoleListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/userRole_List';
        $data['title'] = 'Role List';
        $data['width'] = '580';
        $data['height'] = '400';
        $this->db->order_by('User_Permission_ID','asc');
        $user = $this->session->userdata('logged_in');
        $result = ($user['type']=="Super Administrator") ? $this->db->get('tbl_user_type')->result() : $this->db->get_where ('tbl_user_type','User_Permission_Type<> "Super Administrator"')->result();
        $Check_ID = $Permission_Type = $Visibility  = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->User_Permission_ID, FALSE);
            $Permission_Type[] = $row->User_Permission_Type;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Permission Type' => $Permission_Type,'Visibility' => $Visibility);
        $data['width_th'] = array(5,10,10);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
    /*End of Permission Manager*/ 
    /*User Permission Operation*/
    public function insertUpdate_User_Role(){
        $set = array(
            'User_Permission_Type' => $this->input->post('permission_name'),
            'User_Manager' => (isset($_POST['usermanager']))? $this->input->post('usermanager'): '0',
            'Holiday' => (isset($_POST['holiday']))? $this->input->post('holiday'): '0',
            'Company' => (isset($_POST['company']))? $this->input->post('company'): '0',
            'Location' => (isset($_POST['location']))? $this->input->post('location'): '0',
            'Department' => (isset($_POST['department']))? $this->input->post('department'): '0',
            'Report' => (isset($_POST['report']))? $this->input->post('report'): '0',
            'All_Report' => isset($_POST['allreport'])? $this->input->post('allreport'): '0',
            'Leave_Preapproval' => isset($_POST['leavepre-approval'])? $this->input->post('leavepre-approval'): '0',
            'Leave_Manager' => isset($_POST['leavemanager'])? $this->input->post('leavemanager'): '0',
            'Recruitment_Preapproval' => isset($_POST['recruitmentpre-approval'])? $this->input->post('leavepre-approval'): '0',
            'Recruitment_Manager' => isset($_POST['recruitmentmanager'])? $this->input->post('recruitmentmanager'): '0',
            'Hierarchy' => isset($_POST['hierarchymanager']) ? $this->input->post('hierarchymanager'): '0',
            'Employee_Manager' => isset($_POST['employeemanager']) ? $this->input->post('employeemanager'): '0',
            'Is_Exist' =>  $this->input->post('Visibility')
          );
        if($this->input->post('insert'))
            $this->db->insert('tbl_user_type',$set);
        if($this->input->post('update')){
            $this->db->where('User_Permission_ID',$this->input->post('id'));
            $this->db->update('tbl_user_type',$set);
        }
    }
    /*End of User Permission Operation*/
//    Leave Manager
     public function load_LeaveTypeEntryForm($id){
        if($id>0)$row = $this->get_Specific_Info('tbl_leave_info',array('Leave_Type_ID' => $id));
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['menu'] = $this->load_Menu();
        $data['width'] = '500';
        $data['height'] = '90';
        $data['title'] = ($id>0 ? 'Update' : 'Insert').nbs().'Leave Type';
        $data['form']='administrator/leaveType_Entry'.(isset($row) ? '/'.$row->Leave_Type_ID : '/');
        $data['hidden']=array('id' =>  isset($row) ? $row->Leave_Type_ID : NULL);
        $data['field'] = array(
           'Name' => array('input' => array('name' => 'name', 'size' => '30','value' =>  set_value('name',isset($row) ? $row->Leave_Type : NULL)))
        );
        $data['select'] = array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['selected']=array(isset($row) ? $row->Is_Exist : ($this->input->post('Visibility') ? $this->input->post('Visibility') : 1));
        $data['submit']=array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;   
    }
        
    public function load_LeaveType_ValidationCofig(){
       $config = array(
           array(
               'field'   => 'name', 
               'label'   => 'Leave Type Name', 
               'rules'   => 'required|xss_clean'
           )
       );
       return $config;
    }
        
    public function insertUpdate_Leave_Type(){
        $set = array(
                'Leave_Type' => $this->input->post('name'),
                'Is_Exist' =>  $this->input->post('Visibility')
            );
        if($this->input->post('insert'))
            $this->db->insert('tbl_leave_info',$set);
        if($this->input->post('update')){
            $this->db->where('Leave_Type_ID',$this->input->post('id'));
            $this->db->update('tbl_leave_info',$set);
        }
    }
        
    public function load_LeaveTypeListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/leaveType_List';
        $data['title'] = 'Leave Type List';
        $data['width'] = '580';
        $data['height'] = '400';
        $result = $this->db->get('tbl_leave_info')->result();
         $Check_ID = $Leave_Type = $Visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Leave_Type_ID, FALSE);
            $Leave_Type[] = $row->Leave_Type;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Leave Type' => $Leave_Type,'Visibility' => $Visibility);
        $data['width_th'] = array(5,10,10);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
        
        //For recommend or deny name array
    private function array_Name($field){
        if($field!=NULL){
            $array_id = explode(',',$field);
            array_pop($array_id);
            foreach($array_id as $id_date){
                $array = explode('#',$id_date);
                $rs=$this->get_Specific_Info('tbl_user_info', array('Is_Exist' => 1, 'User_ID' =>  $array[0]),'Full_Name');
                if(isset($array[1]))
                $array_name[] = '<span title="'.$array[1].'">'.$rs->Full_Name.'</span>';
                else
                $array_name[] = $rs->Full_Name; 
            }
            }else $array_name = NULL;
        return $array_name;
    }
        
    public function load_LeaveRequestListData($flag=0){
        $user = $this->session->userdata('logged_in');
        $this->datatable->load_Script('grid');     
        $js = '$(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $js .= '
            $( "#dialog-message" ).dialog({
                
            modal: true,
            autoOpen: false,
            buttons: {
                
              Close: function() {
                  
                $( this ).dialog( "close" );
              }
                  
            },
            width: "30%",
            });
            $( ".name" ).click(function() {
//            var title = "";
//            title = $( "#dialog-message" ).dialog( "option", "title")+"-"+$(this).html();
            $( "#dialog-message" ).dialog( "option", "title","Leave & Absent History"+" - "+$(this).html());
                $( "#dialog-message" ).dialog( "open" );
                var id = $(this).attr("id");
                //$(".user_id").html();
                $.ajax({
                   type: "POST",
                   url: "'.base_url('administrator/leaveAbsentHistory').'",
                   data: {"user_id":id},
                   success: function(data) 
                       {  
                            $("#dialog-message").html(data);
                       }
                           
               });
                   
            });
            ';
        $this->javascript->ready($js);
        $this->jquery->script(base_url('assets/js/jquery/jquery-ui.min.js'));
        $this->javascript->compile();
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'administrator/leaveRequest_List';
        $data['title'] = $flag>0 ? 'Leave Request List' : 'All Leave List';
        $data['width'] = '1330';
        $data['height'] = '480';
        $where_array = array('tbl_leave_record.Is_Exist' => '1','tbl_employe_list_info.Is_Exist' => '1');//'tbl_leave_info.Is_Exist' => '1',
        if($flag>0){   
            $where_array['tbl_leave_record.Is_Void'] = '0';
            $where_array['tbl_leave_record.Is_Processed'] =   '0' ;
        }
            
//        $this->db->select('Apply_Date,User_ID,tbl_location_info.Location_Name,tbl_employee_profile.Employee_ID,tbl_employee_profile.RS_ID,tbl_leave_record.Day,First_Half,Second_Half,Is_Paid,tbl_user_info.User_ID,tbl_company_info.Company_Name,tbl_employee_profile.`Leave`,tbl_leave_record.Leave_ID,tbl_leave_record.Recommend,tbl_leave_record.Deny,tbl_user_info.Full_Name,tbl_dept_info.Dept_Name,tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_record.Leave_Reason,tbl_leave_record.From_Date,tbl_leave_record.To_Date,(SELECT Full_Name FROM tbl_employee_profile WHERE Employee_ID = Backup_Person_ID) as Backup_Person');//tbl_leave_info.Leave_Type,
        $this->db->select('First_Half,Second_Half,Dept_Name,Company_Name,Apply_Date,Employee_ID,Location_Name,RS_ID,tbl_user_info.User_ID,`Leave`,P_Leave,tbl_hierarchy_info.Supervisor1_ID,tbl_hierarchy_info.Supervisor2_ID,tbl_hierarchy_info.Supervisor3_ID,tbl_leave_record.Leave_ID,tbl_leave_record.Recommend,tbl_leave_record.Deny,tbl_user_info.Full_Name,Dept_Name,tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_record.Leave_Reason,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day,(SELECT Full_Name FROM tbl_employee_profile WHERE Employee_ID = Backup_Person_ID) as Backup_Person,Total_Absent,Leave_Taken,Holiday_Given,Tour_Given,Off_day,tbl_employe_list_info.Join_Date,tbl_employe_list_info.User_ID,tbl_employe_list_info.Employee_ID');
        $this->db->from('tbl_leave_record');
        
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','inner');
        $this->db->join('tbl_employe_list_info','tbl_employe_list_info.Employee_ID = tbl_user_info.F_Employee_ID','right');
        
//        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','left');
//        $this->db->join('tbl_employee_profile', 'tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
//        $this->db->join('tbl_location_info','tbl_location_info.Location_ID = tbl_employee_profile.F_Location_ID','inner');
//        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employee_profile.F_Company_ID','left');
//        $this->db->join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','left');
//        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','left');
        $this->db->join('tbl_hierarchy_info','tbl_leave_record.F_User_ID = tbl_hierarchy_info.F_User_ID','left');
            
        $this->db->where($where_array);
        $this->db->order_by('Leave_ID','desc');
        $result = $this->db->get()->result(); 
        $Check_ID = $Name = $Dept = $Reason = $Leave = $From = $To = $Day = $First_Half = $Second_Half = $Backup =  $Status = $array_name = $array_id = $Remaining = $Recommend = $Location = $Company = $Supervisor = $Deny = NULL;
        foreach($result as $row){
            $record_supervisor = $this->db->distinct()->select('Full_Name,RS_ID')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => $row->Employee_ID))->row();
            $array_name =  $array_name_deny = $array_id = NULL;
            $array_name = $this->array_Name($row->Recommend);
            $array_name_deny = $this->array_Name($row->Deny);
            $Check_ID[] = form_checkbox('ID[]',$row->Leave_ID, FALSE);
            $Name[] = '<div class="name" title="'.mdate('%d-%m-%Y %h:%i %a',strtotime($row->Apply_Date)).'" id="'.$row->User_ID.'">'.$row->Full_Name.'['.$row->RS_ID.']</div>';
            $Company[] = $row->Company_Name;
            $Dept[] = $row->Dept_Name;
            $Location[] = $row->Location_Name;
            $Supervisor[] = $record_supervisor ? ($record_supervisor->Full_Name.'['.$record_supervisor->RS_ID.']') : NULL;
//            $Leave[] = $row->Leave_Type;
            $Reason[] = $row->Leave_Reason;
            $From[] = $this->systemDateFormatConverter($row->From_Date);
            $To[] = $this->systemDateFormatConverter($row->To_Date);
            $Day[] = $row->Day;
            $First_Half[] = $row->First_Half!= NULL ? $this->regularDateFormatConverter($row->First_Half) : '';//($row->Second_Half!= NULL ? '' : 'Full day');
            $Second_Half[] = $row->Second_Half!= NULL ? $this->regularDateFormatConverter($row->Second_Half) : '';//($row->First_Half!= NULL ? '' : 'Full day');
            $Backup[] = $row->Backup_Person;
            $Recommend[] = $array_name!=NULL ?  ('<span style="font:11px/13px tahoma,arial,verdana,sans-serif">'.implode(',', $array_name).'</span>') : nbs();
            $Deny[] = $array_name_deny!=NULL ? ('<span style="font:11px/13px tahoma,arial,verdana,sans-serif;color:#FF0000">'.implode(',', $array_name_deny).'</span>') : nbs();
            
/*            $absent = (int)$row->Total_Absent-(ceil($row->Leave_Taken)+(int)$row->Holiday_Given+(int)$row->Tour_Given+(int)$row->Off_day);   
            
            if(mdate('%Y',strtotime($row->Join_Date))>= mdate('%Y')){
                 $date_diff = $this->db->query('SELECT DATEDIFF(
(SELECT DISTINCT tbl_login_record.Date FROM tbl_login_record WHERE tbl_login_record.F_User_ID = '.$row->User_ID.' ORDER BY tbl_login_record.Date ASC LIMIT 1),
    (SELECT DISTINCT tbl_employee_profile.Join_Date FROM tbl_employee_profile WHERE tbl_employee_profile.Employee_ID = '.$row->Employee_ID.' LIMIT 1)
    ) AS Diff
')->row();
                 $absent = $absent - abs($date_diff->Diff);
            }*/
            
            $Remaining[] = $row->P_Leave-($row->Leave_Taken);//+($absent >0 ? $absent : 0)
//            $Remaining[] = $row->Leave - ($this->leaveCount($row->User_ID,1)+$this->home_model->totalHolidayCount($row->Employee_ID));
//            $Paid[] = ($row->Is_Paid ==1)? NULL : '&#10004;';
            $Status[] = ($row->Is_Processed) ? $this->lang->line('granted') : (($row->Is_Void) ? $this->lang->line('rejected') : $this->lang->line('pending'));
                
        }
        $data['field'] = array(form_checkbox(array('id'=>'select_all')) => $Check_ID,'Name' => $Name,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Supervisor' => $Supervisor/*,'Leave' => $Leave,'Without Pay' => $Paid*/,'Reason' => $Reason,'From' => $From,'To' => $To,'Day' => $Day,'First Half' => $First_Half,'Second Half' => $Second_Half,'Backup Person' => $Backup,'Pre-Approval' => $Recommend,'Deny' => $Deny,'Remaining' => $Remaining,'Status' => $Status);
//        $data['width_th'] = array(3,8,6,6,5,8,3,12,7,7,3,7,7,5,10,10,6,0);//array(3,10,6,6,5,8,3,15,7,7,3,5,10,10,6,0);
        if($flag>0)
        $data['other_fields']=array('Decision' => array('submit' => array('name' => 'accept','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')')),'' => array('submit' => array('name' => 'reject','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')')),'Leave'=>array('dropdown' => array('leave',array(1=>'Paid',0=>'Without pay'),$this->input->post('leave') ? $this->input->post('leave') : 1)));
        else
        $data['other_fields']=array('' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to delete?\')')));
        $data['html'] = '
            <div id="dialog-message" title="Leave & Absent History">
        </div>';
        return $data;
    }
        
    public function load_LeavePreApprovalListData($flag = 0){
         $this->datatable->load_Script('grid');
         $js = '
            $( "#dialog-message" ).dialog({
                
            modal: true,
            autoOpen: false,
            buttons: {
                
              Close: function() {
                  
                $( this ).dialog( "close" );
              }
                  
            },
            width: "30%",
            });
            $( ".name" ).click(function() {
//            var title = "";
//            title = $( "#dialog-message" ).dialog( "option", "title")+"-"+$(this).html();
            $( "#dialog-message" ).dialog( "option", "title","Leave & Absent History"+" - "+$(this).html());
                $( "#dialog-message" ).dialog( "open" );
                var id = $(this).attr("id");
                //$(".user_id").html();
                $.ajax({
                   type: "POST",
                   url: "'.base_url('administrator/leaveAbsentHistory').'",
                   data: {"user_id":id},
                   success: function(data) 
                       {  
                            $("#dialog-message").html(data);
                       }
                           
               });
                   
            });
            ';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $this->jquery->script(base_url('assets/js/jquery/jquery-ui.min.js'));
        $user = $this->session->userdata('logged_in');
        $js = 'waitForMsg();waitForMsgRecruit();';
        if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
        if($flag >1)
         $js .= '$(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        if($flag ==2){
            $data['form'] = 'administrator/approvedLeave_List';
            $data['title'] = 'Approved Leave List';
        }else if($flag ==3){
            $data['form'] = 'administrator/deniedLeave_List';
            $data['title'] = 'Denied Leave List';
        }else{
            $data['form'] = 'administrator/leavePreApproval_List';
            $data['title'] = ($flag>0 ? 'Leave Pre-appoval': 'All Leave').' List';
        }
        $data['width'] = '1300';
        $data['height'] = '460';
//        $this->db->select('Apply_Date,Employee_ID,tbl_location_info.Location_Name,tbl_employe_list_info.RS_ID,tbl_user_info.User_ID,tbl_employe_list_info.`Leave`,tbl_employe_list_info.P_Leave,tbl_hierarchy_info.Supervisor1_ID,tbl_hierarchy_info.Supervisor2_ID,tbl_hierarchy_info.Supervisor3_ID,tbl_leave_record.Leave_ID,tbl_leave_record.Recommend,tbl_leave_record.Deny,tbl_user_info.Full_Name,tbl_dept_info.Dept_Name,tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_record.Leave_Reason,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day,(SELECT Full_Name FROM tbl_employee_profile WHERE Employee_ID = Backup_Person_ID) as Backup_Person,Total_Absent,Leave_Taken,Holiday_Given,Tour_Given,Off_day');//,tbl_leave_info.Leave_Type
        $this->db->select('Apply_Date,Employee_ID,Location_Name,RS_ID,tbl_user_info.User_ID,`Leave`,P_Leave,tbl_hierarchy_info.Supervisor1_ID,tbl_hierarchy_info.Supervisor2_ID,tbl_hierarchy_info.Supervisor3_ID,tbl_leave_record.Leave_ID,tbl_leave_record.Recommend,tbl_leave_record.Deny,tbl_user_info.Full_Name,Dept_Name,tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_record.Leave_Reason,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day,(SELECT Full_Name FROM tbl_employee_profile WHERE Employee_ID = Backup_Person_ID) as Backup_Person,Total_Absent,Leave_Taken,Holiday_Given,Tour_Given,Off_day,tbl_employe_list_info.Join_Date,tbl_employe_list_info.User_ID,tbl_employe_list_info.Employee_ID');
        $this->db->from('tbl_leave_record');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','inner');
//        $this->db->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','right');
        $this->db->join('tbl_employe_list_info','tbl_employe_list_info.Employee_ID = tbl_user_info.F_Employee_ID','right');
//        $this->db->join('tbl_location_info','tbl_location_info.Location_ID = tbl_employe_list_info.F_Location_ID','inner');
//        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employe_list_info.F_Company_ID','inner');
//        $this->db->join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','inner');
//        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employe_list_info.F_Dept_ID','inner');
        $this->db->join('tbl_hierarchy_info','tbl_user_info.F_Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner');
        //tbl_leave_info.Is_Exist = '1' AND
        $this->db->where("tbl_employe_list_info.Is_Exist = '1'");
        if($flag == 2)
            $this->db->where("tbl_leave_record.Is_Exist = '1' AND  tbl_leave_record.Is_Processed = '1' AND tbl_leave_record.Is_Void = '0' AND tbl_leave_record.SV_Seen = '0' AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");
        elseif($flag == 3)
            $this->db->where("tbl_leave_record.Is_Exist = '1' AND  tbl_leave_record.Is_Processed = '0' AND tbl_leave_record.Is_Void = '1' AND tbl_leave_record.SV_Seen = '0' AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");
        else
            $this->db->where("tbl_leave_record.Is_Exist = '1' ".($flag > 0 ? " AND  tbl_leave_record.Is_Processed = '0' AND tbl_leave_record.Is_Void = '0' " : ""). " AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");
        $result = $this->db->get()->result();
            
        $Check_ID = $Name = $Dept = $Location = $Reason = $Leave = $From = $To = $Day = $Backup = $Status = $Recommend = $Deny = $array_name = $array_id = $Remaining = NULL;
        $S1 = $S2 = $S3 = array();
        foreach($result as $row){
            $array_name  = $array_name_deny  = $array_id = NULL;
            $array_name = $this->array_Name($row->Recommend);
            $array_name_deny = $this->array_Name($row->Deny);
//            if($row->Recommend!=NULL){
//            $array_id = explode(',',$row->Recommend);
//            array_pop($array_id);
//            foreach($array_id as $id){
//                $rs=$this->get_Specific_Info('tbl_user_info', array('Is_Exist' => 1, 'User_ID' =>  $id),'Full_Name');
//                $array_name[] = $rs->Full_Name;
//            }
//            }else $array_name = NULL;
            if($flag > 1)
                $Check_ID[] =  form_checkbox('ID[]',$row->Leave_ID, FALSE);
            else
                $Check_ID[] =  form_radio('ID',$row->Leave_ID, FALSE);
            $Name[] = '<div class="name" title="'.mdate('%d-%m-%Y %h:%i %a',strtotime($row->Apply_Date)).'" id="'.$row->User_ID.'">'.$row->Full_Name.'['.$row->RS_ID.']</div>';
            $Dept[] = $row->Dept_Name;
            $Location[] = $row->Location_Name;
//            $Leave[] = $row->Leave_Type;
            $Reason[] = $row->Leave_Reason;
            $From[] = $this->regularDateFormatConverter($row->From_Date);
            $To[] = $this->regularDateFormatConverter($row->To_Date);
            $Day[] = $row->Day;
            $Backup[] = $row->Backup_Person;
            $Recommend[] = $array_name!=NULL ? implode(',', $array_name) : nbs();
            $Deny[] = $array_name_deny!=NULL ? implode(',', $array_name_deny) : nbs();
//            $Remaining[] = $row->Leave - ($this->leaveCount($row->User_ID,1)+$this->home_model->totalHolidayCount($row->Employee_ID)); //'total:'.$row->Leave.'leave:'.$this->leaveCount($row->User_ID,1).'holiday:'.$this->home_model->totalHolidayCount($row->Employee_ID);
/*            $absent = (int)$row->Total_Absent-(ceil($row->Leave_Taken)+(int)$row->Holiday_Given+(int)$row->Tour_Given+(int)$row->Off_day);   
            if(mdate('%Y',strtotime($row->Join_Date))>= mdate('%Y')){
                 $date_diff = $this->db->query('SELECT DATEDIFF(
(SELECT DISTINCT tbl_login_record.Date FROM tbl_login_record WHERE tbl_login_record.F_User_ID = '.$row->User_ID.' ORDER BY tbl_login_record.Date ASC LIMIT 1),
    (SELECT DISTINCT tbl_employee_profile.Join_Date FROM tbl_employee_profile WHERE tbl_employee_profile.Employee_ID = '.$row->Employee_ID.' LIMIT 1)
    ) AS Diff
')->row();
                 $absent = $absent - abs($date_diff->Diff);
            }*/
            $Remaining[] = $row->P_Leave-($row->Leave_Taken);//+($absent >0 ? $absent : 0)
            $Status[] = ($row->Is_Processed) ? $this->lang->line('granted'):(($row->Is_Void)?$this->lang->line('rejected'):$this->lang->line('pending'));
            $S1[] = $row->Supervisor1_ID;
            $S2[] = $row->Supervisor2_ID;
            $S3[] = $row->Supervisor3_ID;  
                
        }
            
        $data['field'] = array($flag > 1 ? form_checkbox(array('id'=>'select_all')) : nbs() => $Check_ID,'Name' => $Name,'Department' => $Dept,'Location' => $Location/*,'Leave' => $Leave*/,'Reason' => $Reason,'From' => $From,'To' => $To,'Day' => $Day,'Backup Person' => $Backup,'Pre-Approval' => $Recommend,'Deny' => $Deny,'Remaining' => $Remaining,'Status' => $Status); 
//        $data['width_th'] = array(3,10,6,6,4,29,6,6,4,8,18,12,6,0);
        if($flag == 0){
            unset( $data['field'][nbs()]);
            array_shift($data['width_th']);
        }
        if($flag > 0){
//        $data['other_fields']=array('Filter' => array('input' => array('id' => 'searchInput','value' => 'Type to filter','placeholder' => 'Type to filter')));
        if($flag>1){
            $data['other_fields'] = array(' ' => array('submit' => array('name' => 'seen','value' => 'Seen')));
        }else{
            if(in_array($user['eid'],$S1) || in_array($user['eid'],$S2) || in_array($user['eid'],$S3))
            $data['other_fields'] = array('Decision' => array('submit' => array('name' => 'recommend','value' => 'Recommend','onClick' => 'javascript:return confirm(\'Are you sure to recommend?\')')),'' => array('submit' => array('name' => 'deny','value' => 'Deny','onClick' => 'javascript:return confirm(\'Are you sure to deny?\')')));    //$user['type'] == 'Supervisor' ? 'Approve' :  ,'Leave' => array('dropdown' => array('leave',array(1=>'Paid',0=>'Without pay'),$this->input->post('leave') ? $this->input->post('leave') : 1))
            }
        }
        $data['html'] = '
            <div id="dialog-message" title="Leave & Absent History">
        </div>';
        return $data;
    }
        
    public function execute_LeaveDecision($user_id=0,$flag=NULL){
        $leave_id = $this->input->post('ID');
            
        if($this->input->post('accept')){
            $leave = $this->input->post('leave');
            foreach($leave_id as $id)
            $this->db->update('tbl_leave_record', array('Is_Processed' => 1,'Status' => 'read','S_Status' => 'read','Is_Paid' => $leave), array('Leave_ID' => $id));
            redirect('administrator/leaveRequest_List','refresh');
        }
            
        elseif($this->input->post('reject')){
            foreach($leave_id as $id)
            $this->db->update('tbl_leave_record', array('Is_Void' => 1,'Status' => 'read','S_Status' => 'read'), array('Leave_ID' => $id));
            redirect('administrator/leaveRequest_List','refresh'); 
        }
        elseif($this->input->post('recommend')){
            $leave = $this->input->post('leave');
            $row=$this->get_Specific_Info('tbl_leave_record', array('Is_Exist' => '1','Is_Processed' => '0','Is_Void' => '0','Leave_ID' => $this->input->post('ID')),'Recommend');
            $set= !$row ? $user_id.'#'.mdate('%d-%m-%Y',now()).',' : $row->Recommend.$user_id.'#'.$this->login_model->get_Local_Date('%d-%m-%Y %h:%i %a').',';
            $this->db->update('tbl_leave_record', array('Recommend' => $set,'S_Status' => 'read','Is_Paid' => $leave), array('Leave_ID' => $this->input->post('ID')));
            redirect('administrator/leavePreApproval_List','refresh');
        }elseif($this->input->post('deny')){
            $row=$this->get_Specific_Info('tbl_leave_record', array('Is_Exist' => '1','Is_Processed' => '0','Is_Void' => '0','Leave_ID' => $this->input->post('ID')),'Deny');
            $set= !$row ? $user_id.'#'.mdate('%d-%m-%Y',now()).',' : $row->Deny.$user_id.'#'.$this->login_model->get_Local_Date('%d-%m-%Y %h:%i %a').',';
            $this->db->update('tbl_leave_record', array('Deny' => $set,'S_Status' => 'read'), array('Leave_ID' => $this->input->post('ID')));
            redirect('administrator/leavePreApproval_List','refresh');
        }
            
        if($this->input->post('delete')){
            foreach($leave_id as $id)
            $this->delete_Specific_Info('tbl_leave_record', array('Leave_ID' => $id));
            redirect('administrator/allLeave_List','refresh');
        }
        if($this->input->post('seen')){
            foreach($leave_id as $id)
                $this->db->update('tbl_leave_record', array('SV_Seen' => 1), array('Leave_ID' => $id));
            if($flag ==1) 
                redirect('administrator/approvedLeave_List','refresh');
            else if($flag ==2) 
                redirect('administrator/deniedLeave_List','refresh');
        }
    }
    // Recruitment manager
    public function load_RecruitmentRequestListData($approval=NULL){
        $user = $this->session->userdata('logged_in');
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = is_null($approval) ? 'administrator/recruitmentRequest_List' : 'administrator/recruitmentPreApproval_List';
        $data['title'] = ($approval == 3 ? 'Recruitment Cancellation Request':(($approval ==2)? 'Accomplished':((is_null($approval) ? 'Recruitment Request':'Recruitment Request Status')))).' List';
        $data['width'] = '1340';
        $data['height'] = '400';
            
        if(is_null($approval)) 
            $array_where = array('tbl_recruitment_record.Is_Exist' => '1', 'tbl_recruitment_record.Is_Processed' => '0','tbl_recruitment_record.Is_Void' => '0');
        elseif($approval == '1')
            $array_where = array('tbl_recruitment_record.Is_Exist' => '1','tbl_recruitment_record.Is_Processed' => '0','tbl_recruitment_record.Is_Void' => '1'); 
        elseif($approval == '3')
             $array_where = array('tbl_recruitment_record.Is_Exist' => '1','tbl_recruitment_record.Is_Cancel' => '1'); 
        else
           $array_where = array('tbl_recruitment_record.Is_Processed' => '1'); 
               
        $this->db->select('Education,Stage,tbl_recruitment_record.Join_Date,Reason,tbl_recruitment_record.Designation,Salary_From,Salary_To,Existing_Number,Experience,Location_Name,tbl_recruitment_record.Recruitment_Request_ID,tbl_user_info.Full_Name,tbl_company_info.Company_Name,tbl_dept_info.Dept_Name,tbl_recruitment_record.Number,tbl_recruitment_record.Remarks,tbl_recruitment_record.Date,tbl_recruitment_record.Is_Processed,tbl_recruitment_record.Is_Void,,tbl_recruitment_record.Is_Exist,tbl_recruitment_record.Is_Cancel,Comment,tbl_hierarchy_info.Supervisor1_ID,tbl_hierarchy_info.Supervisor2_ID,tbl_hierarchy_info.Supervisor3_ID');
        $this->db->from('tbl_recruitment_record');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_recruitment_record.F_User_ID','left');
        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_recruitment_record.F_Company_ID','left');
        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_recruitment_record.F_Dept_ID','left');
        $this->db->join('tbl_location_info', 'tbl_location_info.Location_ID = tbl_recruitment_record.F_Location_ID','left');
        $this->db->join('tbl_hierarchy_info','tbl_recruitment_record.F_User_ID = tbl_hierarchy_info.F_User_ID',($user['type']=="Super Administrator" || $user['type']=="Administrator" ||$user['type'] == "HR Manager")?'left':'inner');
        $this->db->where($array_where);
        if($approval == '1')
             $this->db->where("Stage <> 'Accomplish'");
        if($approval == '2'){
            $this->db->or_where ("(tbl_recruitment_record.Is_Accomplish = '1' OR tbl_recruitment_record.Is_Exist = '0' OR tbl_recruitment_record.Is_Processed = '1'  AND Stage IS NOT NULL)");
            $this->db->or_where ("Is_Cancel = '2'");
        }
        if($user['type']!="Super Administrator" && $user['type']!="Administrator" && $user['type'] != "HR Manager")$this->db->where("(tbl_hierarchy_info.Supervisor1_ID = '".$user['id']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['id']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['id']."')");
        $result = $this->db->get()->result(); 
        $Check_ID = $ID = $Name = $Company = $Dept = $Loc = $Exp = $Sal_Rng = $Desig = $Education = $Remarks = $Join_Date = $Date = $Number = $E_Number = $Status = $Reason = $array_name = $array_id  = $Comment = NULL;
        $S1 = $S2 = $S3 = array();
        foreach($result as $row){
            if(!is_null($approval)){
                $array_name = $array_id = NULL;
                if($row->Comment!=NULL){
                $array_comment_id = explode(',',$row->Comment);
                array_pop($array_comment_id);
                foreach($array_comment_id as $comment_id){
                    $id =  explode('~',$comment_id);
                    $rs=$this->get_Specific_Info('tbl_user_info', array('Is_Exist' => 1, 'User_ID' =>  $id[0]),'Full_Name');
                    $array_name[] = $id[1].nbs().'-'.nbs().$rs->Full_Name;
                }
                }else $array_name = NULL;
            }
            $Check_ID[] =  form_radio('ID',$row->Recruitment_Request_ID, FALSE);
            $ID[] = $row->Recruitment_Request_ID;
            $Name[] = $row->Full_Name;
            $Company[] = $row->Company_Name;
            $Dept[] = $row->Dept_Name;
            $Loc[] = $row->Location_Name;
            $Desig[] = $row->Designation;
            $Education[] = $row->Education;
            $Exp[] = $row->Experience.nbs().'year';
            $Sal_Rng[] = $row->Salary_From.'-'.$row->Salary_To; 
            $Remarks[] = $row->Remarks;
            $Number[] = $row->Number;
            $E_Number[] = $row->Existing_Number;
            $Reason[] = $row->Reason;
            $Join_Date[] = $this->regularDateFormatConverter($row->Join_Date);
            $Date[] = $this->regularDateFormatConverter($row->Date);
            if($approval!=NULL){$Comment[] = $array_name!=NULL ? implode(br(2), $array_name) : nbs();}
            if($row->Is_Cancel==2)
                 $Status[] = 'Canceled';
            else
            $Status[] = ($row->Is_Exist==0) ? $this->lang->line('rejected') :(($row->Is_Processed)? $this->lang->line('granted'):($row->Is_Void)? ($row->Stage != NULL ? $row->Stage : $this->lang->line('processing')): $this->lang->line('pending'));
            $S1[] = $row->Supervisor1_ID;
            $S2[] = $row->Supervisor2_ID;
            $S3[] = $row->Supervisor3_ID;       
        }
            
        $data['field'] = array(nbs() => $Check_ID,'ID' => $ID,'Name' => $Name,'Company For' => $Company,'Department For' => $Dept ,'Location For' => $Loc,'Designation For' => $Desig,'Employee Needed' => $Number,'Existing Employee' => $E_Number,'Required Experience' => $Exp,'Salary Range' => $Sal_Rng,'Education' => $Education,'Reason' => $Reason,'Remarks' => $Remarks,'Tentative Join Date' => $Join_Date,'Date of Request' => $Date,'Status' => $Status);
        if($approval==2) 
            unset($data['field'][nbs()]);
        if(!is_null($approval))$data['field']['Comment'] = $Comment;
        $data['width_th'] = array(3,2,10,10,12,12,12,10,8,10,16);
        if(in_array($user['eid'],$S1) || in_array($user['eid'],$S2) || in_array($user['eid'],$S3) || $user['type']=="Super Administrator" || $user['type']=="Administrator")
        $data['other_fields'] = is_null($approval) ? array('Decision' => array('submit' => array('name' => 'recommend','value' => 'Approve','onClick' => 'javascript:return confirm(\'Are you sure to Approve?\')')),'' => array('submit' => array('name' => 'reject','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')'))) : array('Comment' => array('textarea' => array('name' => 'comment','rows' => '2')),'' => array('submit' => array('name' => 'save','value' => 'Save')),'Change Status' => array('dropdown' => array('status',array('Short listed'=>'Short listed','Primary interview' => 'Primary interview', 'Final interview' => 'Final interview','Selection' => 'Selection','Accomplish' => 'Accomplish'),$this->input->post('status') ? $this->input->post('status') : NULL,'style="width:12em"')),'&nbsp;&nbsp;' => array('submit' => array('name' => 'update','value' => 'Update')));//,'&nbsp;' => array('submit' => array('name' => 'accept','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')'))
        if($user['type']== 'HR Manager' && !is_null($approval)){
            $data['other_fields']['Decision'] = array('submit' => array('name' => 'accept','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')'));
            $data['other_fields'][' '] = array('submit' => array('name' => 'reject','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')'));
        }
        if($approval==2)
            unset($data['other_fields']);
        if($approval==3){
            unset($data['other_fields']);
            $data['other_fields'][''] = array('submit' => array('name' => 'accept_cancel_req','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')'));
            $data['other_fields'][' '] = array('submit' => array('name' => 'reject_cancel_req','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')'));
        }
            
        $data['html'] = '
            <div id="dialog-message" title="Leave & Absent History">
        </div>';
        return $data;
    }
    public function execute_RecruitmentDecision($user_id=0){
        if($this->input->post('accept')){
            $this->db->update('tbl_recruitment_record', array('Is_Processed' => 1), array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentRequest_List','refresh');
        }
            
        elseif($this->input->post('reject')){
            $this->db->update('tbl_recruitment_record', array('Is_Exist' => '0'), array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentRequest_List','refresh'); 
        }
        elseif($this->input->post('recommend')){
            $this->db->update('tbl_recruitment_record', array('Is_Void' => 1,'Status' => 'read','S_Status' => 'unread'), array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentPreApproval_List','refresh');
        }
        elseif($this->input->post('save')){
            $row=$this->get_Specific_Info('tbl_recruitment_record', array('Is_Exist' => '1','Is_Processed' => '0','Is_Void' => '1','Recruitment_Request_ID' => $this->input->post('ID')),'Comment');
            $set= (!$row ? ($user_id.'~'.$this->input->post('comment')) : ($row->Comment.$user_id.'~'.$this->input->post('comment'))).',';
            $this->db->update('tbl_recruitment_record', array('Comment' => $set), array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentPreApproval_List','refresh');
        }
        elseif($this->input->post('update')){
            $array_data = array('Stage' => $this->input->post('status'),'S_Status'=>'unread');
            if($this->input->post('status') == 'Accomplish')
                    $array_data['Is_Accomplish'] = 1;
            $this->db->update('tbl_recruitment_record',$array_data,array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentPreApproval_List','refresh');
        }
        elseif($this->input->post('accept_cancel_req')){
            $array_data = array('Is_Cancel' => '2');
            $this->db->update('tbl_recruitment_record',$array_data,array('Recruitment_Request_ID' => $this->input->post('ID')));
            redirect('administrator/recruitmentCancelRequest_List','refresh');
        }
    }
        
    //end of Recruitment manager
    public function load_EmployeeHierarchyEntryForm($id){
        $this->jquery->script(base_url('assets/js/jquery/jquery-ui.min.js'));
             $js = "function change_dept() { 
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value= ''>All Dept</option>\");
                    var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_AllDept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                        }
                            
                    });
                        
                }";
        $js .= "$('#company').change(change_dept);";
        $js .= "$('#dept').change(function() { 
                    $('#desig > option').remove(); 
                    $('#desig').append(\"<option value=NULL>Please Select</option>\");
                    var dept_id = $('#dept').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Designation')."/' + dept_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Designation_Name);//Designation_ID
                                opt.text(obj.Designation_Name);
                                $('#desig').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $this->javascript->ready($js);
        $this->javascript->compile();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['menu'] = $this->load_Menu();
        $data['width'] = '510';
        $data['height'] = '340';
        $data['title'] = ($id>0 ? 'Update' : 'Insert').nbs().'Hierarchy';
        if($id>0)$rs=$this->get_Specific_Info('tbl_hierarchy_info', array('H_ID' => $id));
        $data['form']='administrator/employeeHierarchy_Entry'.(isset($rs) ? '/'.$rs->H_ID : '/'); 
        $data['hidden']=$id>0 ? array('id' => $rs->H_ID) : NULL;
//        $result = $this->get_Specific_Info('tbl_user_info', array('Is_Exist' => 1,'F_User_Permission_ID <>' => 1),'User_ID,Full_Name',2);
        $result = $this->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1),'Employee_ID,Full_Name,RS_ID',2);
        $options[NULL] = 'Please Select';
        foreach($result as $row)
            $options[$row->Employee_ID] = $row->Full_Name.'['.$row->RS_ID.']';
        $data['field'] = array(
            'Employee' => array('dropdown' => array('employee_id',$options, isset($rs) ? $rs->F_Employee_ID:($this->input->post('employee_id') ? $this->input->post('employee_id') : NULL),'id= "employee"')),
            'Company' => array('dropdown' => array('company',$this->globals->getOptionsCompany(),$this->input->post('company') ? $this->input->post('company') : NULL,'id="company" style="width:12em"')),
            'Department' => array('dropdown' => array('dept',array(NULL => 'Please Select'),$this->input->post('dept') ? $this->input->post('dept') : NULL,'id="dept" style="width:12em"')),
            'Designation' => array('dropdown' => array('designation',$this->globals->getOptionsDesignation(),$this->input->post('designation') ? $this->input->post('designation') : NULL,'id="desig" style="width:12em"')),//array('input' => array('name' => 'designation','maxlength' => '50','size' => '30','value' =>  set_value('designation', isset($rs) ? $rs->Designation : NULL))),
            'fieldset' => 'Hierarchy Information',
            'Supervisor' => array('dropdown' => array('supervisor1_id',$options,isset($rs) ? $rs->Supervisor1_ID : ($this->input->post('supervisor1_id') ? $this->input->post('supervisor1_id') : NULL),'id= "supervisor1"')),
            'Co-Supervisor #1' => array('dropdown' => array('supervisor2_id',$options,isset($rs) ? $rs->Supervisor2_ID : ($this->input->post('supervisor2_id') ? $this->input->post('supervisor1_id') : NULL),'id= "supervisor2"')),
            'Co-Supervisor #2' => array('dropdown' => array('supervisor3_id',$options,isset($rs) ? $rs->Supervisor3_ID : ($this->input->post('supervisor3_id') ? $this->input->post('supervisor3_id') : NULL),'id= "supervisor3"')),
            'close_fieldset' => ''
        );
        $data['elements'] = array('employee','supervisor1','supervisor2','supervisor3');
        $data['selected']=array($id>0 ? $rs->Is_Exist : 1);
        $data['select'] = array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['submit']=array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;   
    }
        
    public function load_EmployeeHierarchy_ValidationConfig($id){
        $config = array(
            array(
                'field'   => 'employee_id', 
                'label'   => 'Employee', 
                'rules'   => 'trim|required|xss_clean'.($id==0 ? '|is_unique[tbl_hierarchy_info.F_Employee_ID]' : '')
            ),
            array(
                'field'   => 'supervisor1_id', 
                'label'   => 'Supervisor', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'supervisor2_id', 
                'label'   => 'Co-Supervisor #1', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field'   => 'supervisor3_id', 
                'label'   => 'Co-Supervisor #2', 
                'rules'   => 'trim|xss_clean'//|required
            )
        );
        return $config;
    }
        
    public function insertUpdate_Hierarchy_Info(){
        $set = array(
//                'F_User_ID' => $this->input->post('user_id'),
                'F_Employee_ID' => $this->input->post('employee_id'),
                'Designation' => $this->input->post('designation'),
                'Supervisor1_ID' => $this->input->post('supervisor1_id'),
                'Supervisor2_ID' => $this->input->post('supervisor2_id'),
                'Supervisor3_ID' => $this->input->post('supervisor3_id'),
                'Is_Exist' => $this->input->post('Visibility')
            );
        if($this->input->post('insert'))
            $this->db->insert('tbl_hierarchy_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('H_ID' => $this->input->post('id')));
            $this->db->update('tbl_hierarchy_info', $set);
        }
    }
        
    public function load_EmployeeHierarchyListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/employeeHierarchy_List';
        $data['title'] = 'Employee Hierarchy List';
        $data['width'] = '1280';
        $data['height'] = '480';
        $this->db->select('Designation,tbl_location_info.Location_Name,tbl_company_info.Company_Name,tbl_dept_info.Dept_Name,tbl_hierarchy_info.H_ID,tbl_employee_profile.Full_Name,tbl_employee_profile.RS_ID,(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS S,
(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor2_ID) AS S1,
(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor3_ID) AS S2,tbl_hierarchy_info.Is_Exist')->from('tbl_hierarchy_info');
        $this->db->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID');
        $this->db->join('tbl_location_info','tbl_location_info.Location_ID = tbl_employee_profile.F_Location_ID','left');
        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employee_profile.F_Company_ID','left');
        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','left');
        $this->db->where(array('tbl_employee_profile.Is_Exist' => 1, 'tbl_hierarchy_info.Is_Exist'  => 1));
        $result = $this->db->get()->result();
        $Check_ID = $Empoyee_Name = $Designation = $Location = $Company = $Dept = $Supervisor = $Co_Supervisor1 = $Co_Supervisor2 = $Visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->H_ID, FALSE);
            $Empoyee_Name[] = $row->Full_Name.'['.$row->RS_ID.']';
            $Designation[] = $row->Designation;
            $Location[] = $row->Location_Name;
            $Company[] = $row->Company_Name;
            $Dept[] = $row->Dept_Name;
            $Supervisor[] = $row->S;
            $Co_Supervisor1[]= $row->S1;
            $Co_Supervisor2[]= $row->S2;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Employee Name' => $Empoyee_Name,'Designation' => $Designation,'Location' => $Location,'Company' => $Company,'Department' => $Dept,'Supervisor' => $Supervisor,'Co-Supervisor#1' => $Co_Supervisor1,'Co-Supervisor#2' => $Co_Supervisor2,'Visibility' => $Visibility);
        $data['width_th'] = array(3,18,10,10,10,10,10,10,4);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
        
    public function load_HolidayEntryFromInfo($id){
        $this->load_Datepicker(array('from_date','to_date'));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '550';
        $data['height'] = '180';
        $data['title']= ($id>0 ? 'Update': 'Add').nbs().'Holiday';
        if($id>0)$row = $this->get_Specific_Info('tbl_holiday_info',array('Holiday_ID' => $id));
        $data['form']='administrator/holiday_Entry'.(isset($row) ? '/'.$row->Holiday_ID : '/'); 
        $data['hidden']= $id>0 ? array('id' => $row->Holiday_ID) : NULL;
        $holiday_type_array = array(NULL => 'Please Select','Public Holiday' => 'Public Holiday','Government Holiday' => 'Government Holiday','Optional Holiday' => 'Optional Holiday');
        $data['field']=array(
            'Name' => array('input' => array('name' => 'holiday_name','maxlength' => '50','size' => '40','value' =>  set_value('holiday_name', isset($row) ? $row->Holiday_Name : NULL))),
            'Holiday Type' => array('dropdown' => array('holiday_type',$holiday_type_array, isset($row) ? $row->Holiday_Type : ($this->input->post('holiday_type') ? $this->input->post('holiday_type') : NULL))),
            'From' => array('input' => array('name' => 'from_date','id' => 'from_date','value' => set_value('from_date',isset($row) ? $this->regularDateFormatConverter($row->From_Date) : NULL))),
            'To' => array('input' => array('name' => 'to_date','id' => 'to_date','value' => set_value('to_date',  isset($row) ? $this->regularDateFormatConverter($row->To_Date) : NULL))),
            'Status' => array('dropdown' => array('status',$this->lang->line('status'),$id>0?$row->Is_Exist : NULL)),
            '' => array('submit' => array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert'))
            );
        return $data;
    }
        
    public function insertUpdate_Holiday_Info(){
         $set = array(
                'Holiday_Name' => $this->input->post('holiday_name'),
                'Holiday_Type' => $this->input->post('holiday_type'),
                'From_Date' => $this->systemDateFormatConverter($this->input->post('from_date')),
                'To_Date' => $this->systemDateFormatConverter($this->input->post('to_date')),
                'Is_Exist' => $this->input->post('status')
            );
        if($this->input->post('insert'))
           $this->db->insert('tbl_holiday_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('Holiday_ID' => $this->input->post('id')));
            $this->db->update('tbl_holiday_info', $set);
        }
    }
        
    public function load_HolidayEntry_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'holiday_name', 
                'label'   => 'Holiday Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
            array(
                'field'   => 'holiday_type', 
                'label'   => 'Holiday Type', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'from_date', 
                'label'   => 'From', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'to_date', 
                'label'   => 'To', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_EmployeeHolidayListViewInfo(){
            $js = "
                function calculateSum() {
                var sum = 0;
                //iterate through each td based on class and add the values
                $('#grid').find('tbody tr').each(function() {
                    //add only if the value is number
                    var value = $($(this).find('td')[5]).text();
                    if(!isNaN(value) && value.length!=0) {
                        sum += parseFloat(value);
                    }
                        
                });
                return sum;
                }
                $('#total').text('Total given holiday:'+calculateSum());";
        $this->javascript->ready($js);
        $this->javascript->compile();
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/holiday_List';
        $data['title'] = 'Holiday List';
        $data['width'] = '800';
        $data['height'] = '480';
        $this->db->distinct()->select(array('Holiday_ID,Holiday_Name,Holiday_Type,From_Date,To_Date,Is_Exist,(DATEDIFF(To_Date,From_Date)+1) AS Day'));
        $result = $this->db->get('tbl_holiday_info')->result();
        $Check_ID = $Holiday_Name = $Type = $From = $To = $Visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Holiday_ID, FALSE);
            $Holiday_Name[] = $row->Holiday_Name;
            $Type[] = $row->Holiday_Type;
            $From[]= $this->systemDateFormatConverter($row->From_Date);
            $To[]= $this->systemDateFormatConverter($row->To_Date);
            $Day[] = $row->Day;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Holiday Name' => $Holiday_Name,'Holiday Type' => $Type,'From' => $From,'To' => $To,'Day' => $Day,'Visibility' => $Visibility);
        $data['width_th'] = array(5,10,10,10,10,10);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        $data['footer'] = array('<spand id="total"></span>');
        return $data;
    }
    /*Company Manager*/
    public function load_CompanyEntryFromInfo($id){
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['width'] = '620';
        $data['height'] = '120';
        $data['title']= ($id>0 ? 'Update': 'Add').nbs().'Company';
        if($id>0)$row = $this->get_Specific_Info('tbl_company_info',array('Company_ID' => $id));
        $data['form']='administrator/company_Entry'.(isset($row) ? '/'.$row->Company_ID : '/'); 
        $data['hidden']= $id>0 ? array('id' => $row->Company_ID) : NULL;
        $data['field']=array(
            'Name' => array('input' => array('name' => 'company_name','maxlength' => '50','size' => '40','value' =>  set_value('company_name', isset($row) ? $row->Company_Name : NULL))),
            'Total Leave Count(Days)' => array('input' => array('name' => 'leave_count','maxlength' => '10','size' => '8','value' =>  set_value('leave_count', isset($row) ? $row->Leave : NULL))),
            );
        $data['selected']=array($id>0?$row->Is_Exist : 1);
        $data['select']=array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['submit']=array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;
    }
        
    public function insertUpdate_Company_Info(){
         $set = array(
                'Company_Name' => $this->input->post('company_name'),
                'Leave' => $this->input->post('leave_count'),
                'Is_Exist' => $this->input->post('Visibility')
            );
        if($this->input->post('insert'))
           $this->db->insert('tbl_company_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('Company_ID' => $this->input->post('id')));
            $this->db->update('tbl_company_info', $set);
        }
    }
        
    public function load_ComapanyEntry_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'company_name', 
                'label'   => 'Company Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[40]|xss_clean'
            ),
            array(
                'field'   => 'leave_count', 
                'label'   => 'Leave Count', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_CompanyListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/company_List';
        $data['title'] = 'Company List';
        $data['width'] = '580';
        $data['height'] = '480';
        $result = $this->db->get('tbl_company_info')->result();
        $Check_ID = $Company_Name = $Leave_Count = $Visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Company_ID, FALSE);
            $Company_Name[] = $row->Company_Name;
            $Leave_Count[] = $row->Leave;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Company Name' => $Company_Name,'Leave Count' => $Leave_Count,'Visibility' => $Visibility);
        $data['width_th'] = array(5,10,10,10);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
    /*Candidate Manager*/
    /*Location Manager*/
    public function load_LocationEntryFromInfo($id){
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['width'] = '620';
        $data['height'] = '120';
        $data['title']= ($id>0 ? 'Update': 'Add').nbs().'Location';
        if($id>0)$row = $this->get_Specific_Info('tbl_location_info',array('Location_ID' => $id));
        $data['form']='administrator/location_Entry'.(isset($row) ? '/'.$row->Location_ID : '/'); 
        $data['hidden']= $id>0 ? array('id' => $row->Location_ID) : NULL;
        $data['field']=array(
            'Name' => array('input' => array('name' => 'location_name','maxlength' => '50','size' => '40','value' =>  set_value('location_name', isset($row) ? $row->Location_Name : NULL))),
//            'Total Leave Count(Days)' => array('input' => array('name' => 'leave_count','maxlength' => '10','size' => '8','value' =>  set_value('leave_count', isset($row) ? $row->Leave : NULL))),
            'Status' => array('dropdown' => array('status',$this->lang->line('status'),$id>0?$row->Is_Exist : NULL)),
            '' => array('submit' => array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert'))
            );
        return $data;
    }
        
    public function insertUpdate_Location_Info(){
         $set = array(
                'Location_Name' => $this->input->post('location_name'),
//                'Leave' => $this->input->post('leave_count'),
                'Is_Exist' => $this->input->post('status')
            );
        if($this->input->post('insert'))
           $this->db->insert('tbl_location_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('Location_ID' => $this->input->post('id')));
            $this->db->update('tbl_location_info', $set);
        }
    }
        
    public function load_LocationEntry_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'location_name', 
                'label'   => 'Location Name', 
                'rules'   => 'trim|required|min_length[2]|max_length[40]|xss_clean'
            ),
            array(
                'field'   => 'status', 
                'label'   => 'Status', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_LocationListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/location_List';
        $data['title'] = 'Location List';
        $data['width'] = '580';
        $data['height'] = '480';
        $result = $this->db->get('tbl_location_info')->result();
        $Check_ID = $Location_Name = $Status = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Location_ID, FALSE);
            $Location_Name[] = $row->Location_Name;
//            $Leave_Count[] = $row->Leave;
            $Status[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Location Name' => $Location_Name,'Status' => $Status);
        $data['width_th'] = array(5,10,10);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
    /*Location Manager*/
    public function load_Candidate_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'full_name', 
                'label'   => 'Full Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
            array(
                'field'   => 'contact_number', 
                'label'   => 'Contact Number', 
                'rules'   => 'trim|max_length[100]|xss_clean'
            ),
            array(
                'field'   => 'recruitment_id', 
                'label'   => 'Recruitment ID', 
                'rules'   => 'trim|required|max_length[100]|xss_clean'
            )
        );
        return $config;
    }
    public function load_CandidateEntryFromInfo($id){
        $encryption = new Encryption;
        $id=$encryption->decrypt($id);
        if($id>0)$row =$this->get_Specific_Info('tbl_candidate_info',array('Candidate_ID' => $id));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['width'] = '520';
        $data['height'] = '180';
        $data['title'] =($id>0 ? 'Update' : 'Add New').nbs().'Candidate';
        $data['form'] ='administrator/candidate_Entry'.(isset($row) ? '/'.$row->Candidate_ID : '/'); 
        $data['hidden'] = isset($row) ? array('id' => $row->Candidate_ID) : NULL;
        $query = $this->db->select('Recruitment_Request_ID')->get_where('tbl_recruitment_record',array('Is_Exist' => 1,'Is_Processed' => 1));
        $options_rr_id[NULL] = 'Please Select';
        foreach($query->result() as $rs)
            $options_rr_id[$rs->Recruitment_Request_ID] = $rs->Recruitment_Request_ID;
        $data['field']=array(
            'Full Name' => array('input' => array('name' => 'full_name','maxlength' => '50','size' => '30','value' =>  set_value('full_name', isset($row) ? $row->Name : NULL))),
            'Contact Number' => array('input' => array('name' => 'contact_number','maxlength' => '80','size' => '30','value' => set_value('contact_number', isset($row) ? $row->Phone : NULL))),
            'Recruitment ID' => array('dropdown' => array('recruitment_id',$options_rr_id,isset($row) ? $row->F_Recruitment_Request_ID : ($this->input->post('recruitment_id') ? $this->input->post('recruitment_id'): NULL))),
            'Resume/CV' => array('upload' => array('name' => 'file','size' => '30','value' => set_value('file')))
            );
                
        $data['submit']=array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data; 
    }
        
     public function upload_CV($file,$path,$validation_flag=FALSE){
        if(!file_exists($path))mkdir($path, 0777, true);
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'doc|docx|pdf|jpeg|jpg|gif|png';
        $config['overwrite'] = TRUE;
        $this->upload->initialize($config);
        $files = array();
        
        $no_of_files = count($_FILES['adoc']['size']);
        
        //foreach ($_FILES['adoc'] as $key => $value) {
          for($i=0;$i<$no_of_files;$i++){ 
            $_FILES['userfile']['name']     = $_FILES['adoc']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['adoc']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['adoc']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['adoc']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['adoc']['size'][$i];

//            $_FILES['images']['name']= $files['name'][$key];
//            $_FILES['images']['type']= $files['type'][$key];
//            $_FILES['images']['tmp_name']= $files['tmp_name'][$key];
//            $_FILES['images']['error']= $files['error'][$key];
//            $_FILES['images']['size']= $files['size'][$key];   
        if(!$this->upload->do_upload('userfile'))
            return $validation_flag ? $this->upload->display_errors('', '') : FALSE;
        
        }       
    }
        
    public function load_CandidateListViewInfo($approval=NULL){
        $user = $this->session->userdata('logged_in');
        $encryption = new Encryption;
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/candidate_List';
        $data['title'] = 'Candidate Pre-selection List';
        $data['width'] = '600';
        $data['height'] = '400';
        $result = $this->db->get_where('tbl_candidate_info',array('Is_Processed' => (!is_null($approval)? '0' : '1')))->result();
        $Recruitment_id = $Name = $Contact = $Check_ID = $Comment = $Resume = NULL;
        foreach($result as $row){
            if(!is_null($approval)){
                $array_name = $array_id = NULL;
                if($row->Comment!=NULL){
                $array_comment_id = explode(',',$row->Comment);
                array_pop($array_comment_id);
                foreach($array_comment_id as $comment_id){
                    $id =  explode('~',$comment_id);
                    $rs=$this->get_Specific_Info('tbl_user_info', array('Is_Exist' => 1, 'User_ID' =>  $id[0]),'Full_Name');
                    $array_name[] = $id[1].nbs().'-'.nbs().$rs->Full_Name;
                }
                }else $array_name = NULL;
            }
            $Check_ID[] =  form_radio('ID',$encryption->encrypt($row->Candidate_ID), FALSE);
            $Recruitment_id[] = $row->F_Recruitment_Request_ID;
            $Name[] = $row->Name;
            $Contact[] = $row->Phone;
            $Resume[] = $row->Resume? anchor_popup(base_url('uploaded_cv/'.$row->Resume),'Download') : 'N/Ai';
            if($approval!=NULL){$Comment[] = $array_name!=NULL ? implode(br(2), $array_name) : nbs();}
        }
        $data['field'] = array('&nbsp;' => $Check_ID,'Recruitment ID' => $Recruitment_id,'Name' => $Name,'Contact' => $Contact,'Resume' => $Resume);
         if(!is_null($approval))$data['field']['Comment'] = $Comment;
        $data['width_th'] = array(5,15,18,18,10,30);
        $data['other_fields']= ($user['type']== 'HR Manager') ? array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')),'Decision'  => array('submit' => array('name' => 'selected','value' => 'Select')),'Comment' => array('textarea' => array('name' => 'comment','rows' => '2')),'&nbsp' => array('submit' => array('name' => 'save','value' => 'Save'))) : array('Comment' => array('textarea' => array('name' => 'comment','rows' => '2')),'' => array('submit' => array('name' => 'save','value' => 'Save')));
            
        return $data;
    }
        
    public function insertUpdate_Candidate_Info(){
        $set = array(
                'Name' => $this->input->post('full_name'),
                'Phone' => $this->input->post('contact_number'),
                'F_Recruitment_Request_ID' => $this->input->post('recruitment_id'),
                'Resume' => $this->upload->file_name
            );
        if($this->input->post('insert'))
           $this->db->insert('tbl_candidate_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('Candidate_ID' => $this->input->post('id')));
            $this->db->update('tbl_candidate_info', $set);
        }
    }
        
    public function execute_CadidateDecision($user_id=0){
        $encryption = new Encryption;
        if($this->input->post('edit'))
            redirect('administrator/candidate_Entry/'.$this->input->post('ID'),'refresh');         
        else if($this->input->post('save')){
            $row=$this->get_Specific_Info('tbl_candidate_info', array('Is_Exist' => '1','Candidate_ID' => $this->input->post('ID')),'Comment');
            $set= (!$row ? ($user_id.'~'.$this->input->post('comment')) : ($row->Comment.$user_id.'~'.$this->input->post('comment'))).',';
            $this->db->update('tbl_candidate_info', array('Comment' => $set), array('Candidate_ID' => $encryption->decrypt($this->input->post('ID'))));
            redirect('administrator/candidate_List','refresh');
        }elseif($this->input->post('selected')){
            $this->db->update('tbl_candidate_info', array('Is_Processed' => 1), array('Candidate_ID' => $encryption->decrypt($this->input->post('ID'))));
            redirect('administrator/candidate_List','refresh');
        }
    }
        
    /*End of Candidate Manager*/
        
     //Employee Profile
     public function load_EmployeeEntry_ValidationConfig($id,$photo=NULL){
        $encryption = new Encryption;
        $id=$encryption->decrypt($id);
        if(is_null($photo))
        $config = array(
            array(
                'field'   => 'birth_date', 
                'label'   => 'Birth Date', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field'   => 'full_name', 
                'label'   => 'Full Name', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                   'field'   => 'f_name', 
                   'label'   => 'Father\'s Name', 
                   'rules'   => 'trim|xss_clean'//|required
            ),
             array(
                   'field'   => 'm_name', 
                   'label'   => 'Mother\'s Name', 
                   'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'e_contact', 
                'label'   => 'Emergency Contact', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'blood_group', 
                'label'   => 'Blood Group', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field' => 'height', 
                'label'   => 'Height', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'weight', 
                'label'   => 'Weight', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'gender', 
                'label'   => 'Gender', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field' => 'marital_status', 
                'label'   => 'Marital Status', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field' => 'id_mark', 
                'label'   => 'Identification Mark', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'training', 
                'label'   => 'Training', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'nid', 
                'label'   => 'NID Number', 
                'rules'   => 'trim|xss_clean'//|required.($id==0 ? '|is_unique[tbl_employee_profile.NID]' : '')
            ),
            array(
                'field' => 'passport', 
                'label'   => 'Passport Number', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'passport_issue_date', 
                'label'   => 'Passport Issue Date', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'passport_expiry_date', 
                'label'   => 'Passport Expiry Date', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'p_address', 
                'label'   => 'Parmanent Address', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'p_org', 
                'label'   => 'Previous Organization', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'email', 
                'label'   => 'Email', 
                'rules'   => 'trim|xss_clean'
            ),
            array(
                'field' => 'employee_id', 
                'label'   => 'Employee ID', 
                'rules'   => 'trim|required|xss_clean'.($id==0 ? '|is_unique[tbl_employee_profile.RS_ID]' : '')
            ),
            array(
                'field' => 'leave_count', 
                'label'   => 'Leave Count', 
                'rules'   => 'trim|numeric|xss_clean'//|required
            ),
            array(
                'field' => 'department', 
                'label'   => 'Department', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'location', 
                'label'   => 'Location', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'company', 
                'label'   => 'Company', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'user_name', 
                'label'   => 'User Name', 
                'rules'   => $id>0 ? 'trim|required|min_length[3]|max_length[50]|xss_clean' : 'trim|required|min_length[3]|max_length[50]|is_unique[tbl_login_info.User_Name]|xss_clean',
            ),
            array(
                'field'   => 'password', 
                'label'   => 'Password', 
                'rules'   => 'trim|required|min_length[3]|max_length[100]|xss_clean'
            ),
            array(
                'field'   => 'confirm_password', 
                'label'   => 'Confirm Password', 
                'rules'   => 'trim|required|min_length[3]|max_length[100]|xss_clean|matches[password]'
            ),
            array(
                'field'   => 'permission', 
                'label'   => 'Permission', 
                'rules'   => 'trim|required|xss_clean'
            ),
//            array(
//                'field'   => 'employee_id', 
//                'label'   => 'Employee', 
//                'rules'   => 'trim|required|xss_clean'.($id==0 ? '|is_unique[tbl_hierarchy_info.F_Employee_ID]' : '')
//            ),
            array(
                'field'   => 'supervisor1_id', 
                'label'   => 'Supervisor', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'supervisor2_id', 
                'label'   => 'Co-Supervisor #1', 
                'rules'   => 'trim|xss_clean'//|required
            ),
            array(
                'field'   => 'supervisor3_id', 
                'label'   => 'Co-Supervisor #2', 
                'rules'   => 'trim|xss_clean'//|required
            )
                
        );
        else
            $config = array(
               array(
                   'field'   => 'full_name', 
                   'label'   => 'Full Name', 
                   'rules'   => 'trim|required|xss_clean'
                   )  
            );
        return $config;
    }
        
    public function load_EmployeeEntryForm_Data($id,$photo=NULL){
        $encryption = new Encryption;
        $id=$encryption->decrypt($id);
        $data['session'] = $this->session->userdata('logged_in');  
        $js = "
            $('.add_more').click(function(e){
                e.preventDefault();
                $(this).before(\"<input name='adoc[]' size='30' accept='.pdf,.jpg,.jpeg,.gif,.doc,.docx' multiple='multiple' type='file'>\");
            });

            $('#employee_id').keyup(function(){
                $('#user_name').val($('#employee_id').val());
            });
            function get_dept(){
                    var dept_id = $('#dept').val();
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value=NULL>Please Select</option>\");
                    var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Dept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                            if(dept_id != '')
                            $('#dept').val(dept_id);";
                            if($id>0)$js .= "get_desig();";
                        $js .="}
                            
                    });
                        
                }";
                if($id>0)$js .= "get_dept();";    
        $js .= "function days_between(date1, date2) {
            
    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24
        
    // Convert both dates to milliseconds
    var date1_ms = date1.getTime()
    var date2_ms = date2.getTime()
        
    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms)
        
    // Convert back to days and return
    return Math.abs(difference_ms/ONE_DAY)
        
}";
$js .= 'function strtoDate(dateStr) {
    var parts = dateStr.split("-");
    return new Date(parts[2], parts[1] - 1, parts[0]);
}';
        $js .= "function calculate_leave(){
                    var from_date = strtoDate($('#join_date').val());
                    var to_date =  strtoDate('31-12-'+(new Date()).getFullYear());
                    var count = Math.round((days_between(from_date,to_date)*32)/365);
  
                    if(count>32)
                        count = 32;
                    $('#leave_count').val(count);   
                   
                }";
        
        $js .= "function calculate_pleave(){
                    var from_date = strtoDate($('#join_date').val());
                    var to_date =  strtoDate('31-12-'+(new Date()).getFullYear());
                    var count = Math.round((days_between(from_date,to_date))*0.041);
                    if(count>15)
                        count = 15;
                    $('#pleave_count').val(count);   
                   
                }";
       
       
           
        $js .= "$('#company').change(get_dept);";
        $js .= "
                $('#dept').change(get_desig);      
              
                //calculate_leave();
                //calculate_pleave()
                //$('#join_date').change(calculate_leave);
                //$('#join_date').change(calculate_pleave);
                "; 
         $js .= "function get_desig() { 
                    var dept_id = $('#dept').val();   
                    var desig_id = $('#desig').val();
                    $('#desig > option').remove(); 
                    $('#desig').append(\"<option value=NULL>Please Select</option>\");
               
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Designation')."/' + dept_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Designation_ID);
                                opt.text(obj.Designation_Name);
                                $('#desig').append(opt);
                            });
                            if(desig_id != '')
                            $('#desig').val(desig_id);
                        }
              
                    });
                }";
//        if($id>0)$js .= "get_desig();";
        if($data['session']['type']!="Super Administrator" && $data['session']['type']!="Administrator" && is_null($photo))//&& $data['session']['type']!="Photographer"
            $js.= "$('.window').find('input, textarea, button, select').attr('disabled','disabled');";    
           
        $this->javascript->ready($js);    
        $this->load_Datepicker(array('birth_date','join_date','passport_issue_date','passport_expiry_date','exit_date'));
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '700';
        $data['height'] = is_null($photo)? NULL : '600';
            
        $data['title']=($data['session']['type']!="Super Administrator" && $data['session']['type']!="Administrator")? (is_null($photo) ? 'My Profile' : 'Photo Entry'):(($id>0 ? 'Update' : 'Add New').nbs().'Employee');
        if($id>0)$row =$this->get_Employee_Information(array('Employee_ID' => $id));
        if(isset($row)){
            
           $password = $encryption->decrypt($row->Password);
        }
        $data['form']='administrator/'.(is_null($photo)?'employee_Entry':'photo_Entry').(isset($row) ? '/'.$encryption->encrypt($row->Employee_ID) : '/'); 
        $data['hidden']= isset($row) ? array('id' => $row->Employee_ID,'user_id' => $row->User_ID) : NULL;
        if($id>0){
            $result = $this->get_Specific_Info('tbl_office_time', array('F_Employee_ID'=>$id), 'Weekday,`In`,`Out`',2);
                
            if(isset($result)){
                foreach($result as $row_time){
                    $in_time[] =  explode(':',$row_time->In);
                    $out_time[] = explode(':',$row_time->Out);
                }      
            }
        }
        $result_company = $this->get_Specific_Info('tbl_company_info', array('Is_Exist' => 1),'Company_ID,Company_Name',2);
        $options_company[NULL] = 'Please Select';
        foreach($result_company as $row_company)
            $options_company[$row_company->Company_ID] = $row_company->Company_Name;
                
        $result = $this->get_Specific_Info('tbl_location_info', array('Is_Exist' => 1),'Location_ID,Location_Name',2);
        $options_loc[NULL] = 'Please Select'; 
        foreach($result as $rs)
            $options_loc[$rs->Location_ID]=$rs->Location_Name;
                
        $result = $this->get_Specific_Info('tbl_dept_info', array('Is_Exist' => 1),'Dept_ID,Dept_Name',2);
        $options_dept[NULL] = 'Please Select'; 
        foreach($result as $rs)
            $options_dept[$rs->Dept_ID]=$rs->Dept_Name;
                
        $options_hr = array_combine($this->numberArray(0, 23, 1),$this->numberArray(0, 23, 1));
        $options_min = array_combine($this->numberArray(0, 59, 1),$this->numberArray(0, 59, 1));
        $array_weekdays = $this->lang->line('weekdays');
        $str = '<table>';
        $i=0;
        while($i<7){
            $str.= '<tr><td><label>'.$array_weekdays[$i].'</label></td><td><label>From</label></td><td>'.nbs(4).form_hidden('weekday'.$i,$i).form_dropdown('in_hr'.$i,$options_hr,  isset($in_time) ? $in_time[$i][0] :($this->input->post('in_hr'.$i) ? $this->input->post('in_hr'.$i) : '10')).nbs().':'.nbs().form_dropdown('in_min'.$i,$options_min,isset($in_time[$i]) ? $in_time[$i][1] : ($this->input->post('in_min') ? $this->input->post('in_min') : '00')).nbs(4).'</td><td><label>To</label></td><td>'.nbs(4).form_dropdown('out_hr'.$i,$options_hr,  isset($out_time[$i]) ? $out_time[$i][0] : ($this->input->post('out_hr'.$i) ? $this->input->post('out_hr'.$i) : '20')).nbs().':'.nbs().form_dropdown('out_min'.$i,$options_min,isset($out_time[$i]) ? $out_time[$i][1] : ($this->input->post('out_min'.$i) ? $this->input->post('out_min'.$i) : '00')).'</td></tr>';
            $i++;             
        }
        $str.= '</table>'; 
        if($id>0){
            $document_link = '<ul style="margin: 0;padding: 0;list-style: none;">';
            $doc_array = explode(',', $row->Document);
            foreach($doc_array as $doc){
//                print_r($doc);
            $document_link .= (($doc!=NULL) ? '<li><a style="text-decoration:none"  href="'.base_url('uploaded_document/'.$doc).'" target="_blank">'.$doc.'</a></li>':NULL);
            }
            $document_link .= '</ul>';
        }else $document_link = NULL;
        $data['field']=array(
            'Photograph(Max File Size 2MB)' => array('upload' => array('name' => 'file','size' => '30','accept' => '.gif,.png,.jpg,.jpeg','value' => set_value('file',isset($row) ? $row->Photo : NULL))),
            'Employee ID<span class="required"></span>' => array('input' => array('name' => 'employee_id','id' => 'employee_id','value' => set_value('employee_id',isset($row) ? $row->RS_ID : NULL))),
                
            'fieldset' => 'Personal Information',
            'Full Name<span class="required"></span>' => array('input' => array('name' => 'full_name','value' => set_value('full_name',isset($row) ? $row->Full_Name : NULL))),
            'Nick Name' => array('input' => array('name' => 'nick_name','value' => set_value('nick_name',isset($row) ? $row->Nick_Name : NULL))),
            'Birth Date' => array('input' => array('name' => 'birth_date','id' => 'birth_date','value' => set_value('birth_date',isset($row) ? $row->DOB : NULL))),
            'Father\'s Name' => array('input' => array('name' => 'f_name','value' => set_value('f_name',isset($row) ? $row->Father : NULL))),
            'Mother\'s Name' => array('input' => array('name' => 'm_name','value' => set_value('m_name',isset($row) ? $row->Mother : NULL))),
            'Blood Group' => array('dropdown' => array('blood_group',$this->lang->line('blood_group'),isset($row) ? $row->Blood_Group : ($this->input->post('blood_group') ? $this->input->post('blood_group') : NULL))),
            'Height' => array('input' => array('name' => 'height','value' => set_value('height',isset($row) ? $row->Height : NULL))),
            'Weight' => array('input' => array('name' => 'weight','value' => set_value('weight',isset($row) ? $row->Weight : NULL))),
            'Gender' => array('dropdown' => array('gender',$this->lang->line('gender'),isset($row) ? $row->Gender :($this->input->post('gender') ? $this->input->post('gender') : NULL))),
            'Marital Status' => array('dropdown' => array('marital_status',$this->lang->line('marital_status'),isset($row) ? $row->Marital_Status :($this->input->post('marital_status') ? $this->input->post('marital_status') : NULL))),
            'Identification Mark' => array('textarea' => array('name' => 'id_mark','rows' => '3','cols' => '32','value' => set_value('id_mark',isset($row) ? $row->Identification_Mark : NULL))),
            'close_fieldset_' => '',
                
            'fieldset1' => 'Official Information',
            'Email' => array('input' => array('name' => 'email','maxlength' => '80','size' => '30','value' => set_value('email', isset($row) ? $row->Email : NULL))),
            'Emergency Contact Number' => array('input' => array('name' => 'e_contact','value' => set_value('e_contact',isset($row) ? $row->Emergency_Contact : NULL))),
            'Official Contact Number' => array('input' => array('name' => 'o_contact','value' => set_value('o_contact',isset($row) ? $row->Office_Contact : NULL))),
            'Contact Address' => array('textarea' => array('name' => 'address','rows' => '2','cols' => '32','value' => set_value('address', isset($row) ? $row->Address : NULL))),
            'Permanent Address<span class="required"></span>' => array('textarea' => array('name' => 'p_address','rows' => '3','cols' => '32','value' => set_value('p_address',isset($row) ? $row->Permanent_Address : NULL))),
            'Special Qualification/Training' => array('textarea' => array('name' => 'training','rows' => '3','cols' => '32','value' => set_value('training',isset($row) ? $row->Training : NULL))),
            'NID Number' => array('input' => array('name' => 'nid','value' => set_value('nid',isset($row) ? $row->NID : NULL))),
                
            'Passport Number' => array('input' => array('name' => 'passport','value' => set_value('passport',isset($row) ? $row->Passport : NULL))),
            'Passport Issue Date' => array('input' => array('name' => 'passport_issue_date','id' => 'passport_issue_date','value' => set_value('passport_issue_date',isset($row) ? mdate('%d-%m-%Y',  strtotime($row->Passport_Issue_Date)) : NULL))),
            'Passport Expiry Date' => array('input' => array('name' => 'passport_expiry_date','id' => 'passport_expiry_date','value' => set_value('passport_expiry_date',isset($row) ? mdate('%d-%m-%Y',strtotime($row->Passport_Expiry_Date)) : NULL))),
                
            'Previous Organization' => array('textarea' => array('name' => 'p_org','rows' => '3','cols' => '32','value' => set_value('p_org',isset($row) ? $row->Previous_Organization : NULL))),
            'Join Date' => array('input' => array('name' => 'join_date','id' => 'join_date','value' => set_value('join_date',mdate('%d-%m-%Y',  isset($row) ? strtotime($row->Join_Date) : now())))),
            'Company<span class="required"></span>' => array('dropdown' => array('company',$options_company,isset($row) ? $row->F_Company_ID :($this->input->post('company') ? $this->input->post('company'): NULL),'id="company"')),
            'Department<span class="required"></span>' => array('dropdown' => array('department',$options_dept,isset($row) ? $row->F_Dept_ID :($this->input->post('department') ? $this->input->post('department'): NULL),'id="dept"')),
            'Location<span class="required"></span>' => array('dropdown' => array('location',$options_loc,isset($row) ? $row->F_Location_ID :($this->input->post('location') ? $this->input->post('location'): NULL))),
            'Designation' => array('dropdown' => array('designation',$this->globals->getOptionsDesignation(),isset($row) ? $row->F_Designation_ID : ($this->input->post('designation') ? $this->input->post('designation') : NULL),'id="desig" style="width:12em"')),
            'Total Leave Count(Days)' => array('input' => array('name' => 'leave_count','id' => 'leave_count','maxlength' => '10','size' => '8','value' =>  set_value('leave_count', isset($row) ? $row->Leave : 32))), 
            'Personal Leave Count(Days)' => array('input' => array('name' => 'pleave_count','id' => 'pleave_count','maxlength' => '10','size' => '8','value' =>  set_value('pleave_count', isset($row) ? $row->P_Leave : NULL))), 
            'Grace Time' => array('input' => array('name' => 'grace_time','maxlength' => '10','size' => '8','value' =>  set_value('grace_timegrace_time', isset($row) ? $row->Grace_Time : NULL))), 
            'Job Description' => array('textarea' => array('name' => 'jobdesc','rows' => '3','cols' => '32','value' => set_value('jobdesc',isset($row) ? $row->Job_Desc : NULL))),
            'Additional Document' => array('upload' => array('name' => 'adoc[]','size' => '30','accept' => '.pdf,.jpg,.jpeg,.gif,.doc,.docx','value' => set_value('adoc'),'multiple' => 'multiple')),
            $document_link => array('button' => array('name' => 'addmore', 'type'=>'button', 'class' => 'add_more', 'content' => 'Add More Files')),
            'close_fieldset1' => '',
                 
                
            'fieldset2' => 'Login Information',
            'User Name<span class="required"></span>' => array('input' => array('name' => 'user_name','id'=> 'user_name','maxlength' => '50','size' => '30','value' =>  set_value('user_name', isset($row) ? $row->User_Name : NULL),'id'=>'user_name')),
            'Password<span class="required"></span>' => array('password' => array('name' => 'password','maxlength' => '100','size' => '30','value' =>  set_value('password',isset($password) ? $password : NULL))),
            'Confirm Password<span class="required"></span>' => array('password' => array('name' => 'confirm_password','maxlength' => '100','size' => '30','value' =>  set_value('confirm_password',isset($password) ? $password : NULL))),
            'Permission<span class="required"></span>' =>  array('dropdown' =>  array('permission',$this->globals->getOptionsPermission(),isset($row) ? $row->User_Permission_ID :($this->input->post('permission') ? $this->input->post('permission'): NULL))),
            'close_fieldset2' => '',
                
            'fieldset3' => 'Hierarchy Information',
            'Supervisor<span class="required">' => array('dropdown' => array('supervisor1_id',$this->globals->getOptionsEmployee(),isset($row) ? $row->Supervisor1_ID : ($this->input->post('supervisor1_id') ? $this->input->post('supervisor1_id') : NULL),'id= "supervisor1"')),
            'Co-Supervisor #1' => array('dropdown' => array('supervisor2_id',$this->globals->getOptionsEmployee(),isset($row) ? $row->Supervisor2_ID : ($this->input->post('supervisor2_id') ? $this->input->post('supervisor1_id') : NULL),'id= "supervisor2"')),
            'Co-Supervisor #2' => array('dropdown' => array('supervisor3_id',$this->globals->getOptionsEmployee(),isset($row) ? $row->Supervisor3_ID : ($this->input->post('supervisor3_id') ? $this->input->post('supervisor3_id') : NULL),'id= "supervisor3"')),
            'close_fieldset3' => '',
                
            'Status' => array('dropdown' => array('status',$this->lang->line('status'),isset($row) ? $row->Is_Exist :($this->input->post('status') ? $this->input->post('status') : NULL))),
            'Exit Date' => array('input' => array('name' => 'exit_date','id' => 'exit_date','value' => set_value('exit_date',isset($row) ? ($row->Inactive_Date!= NULL ? mdate('%d-%m-%Y',  strtotime($row->Inactive_Date)) : NULL ):NULL)))
            );
            $data['elements'] = array('supervisor1','supervisor2','supervisor3');
            if($id > 0)$record_supervisor = $this->db->distinct()->select('Full_Name,Designation')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => ($id)? $id :$data['session']['eid']))->row();
//            $data['field']['Supervisor1'] = array('input' => array('readonly' => 'readonly','name' => 'supervisor','value' => set_value('supervisor',isset($record_supervisor->Full_Name) ? $record_supervisor->Full_Name : NULL)));
//            $data['field']['Designation1'] = array('input' => array('readonly' => 'readonly','name' => 'designation','value' => set_value('designation',isset($record_supervisor->Designation) ? $record_supervisor->Designation : NULL)));
            $data['check_caption'] = 'Working Days';
            $data['check'] = array($array_weekdays[0] => isset($row) ? $row->Sun : ($this->input->post('sunday') ? $this->input->post('sunday') : '0'),$array_weekdays[1] => isset($row) ? $row->Mon : ($this->input->post('monday') ? $this->input->post('monday') : '0'),$array_weekdays[2] => isset($row) ? $row->Tue : ($this->input->post('tuesday') ? $this->input->post('tuesday') : '0'),$array_weekdays[3] => isset($row) ? $row->Wed : ($this->input->post('wednesday') ? $this->input->post('wednesday') : '0'),$array_weekdays[4] => isset($row) ? $row->Thu : ($this->input->post('thursday') ? $this->input->post('thursday') : '0'),$array_weekdays[5] => isset($row) ? $row->Fri : ($this->input->post('friday') ? $this->input->post('friday') : '0'),$array_weekdays[6] => isset($row) ? $row->Sat : ($this->input->post('saturday') ? $this->input->post('saturday') : '0'));  
        if($data['session']['type']!="Super Administrator" && $data['session']['type']!="Administrator"){
            unset($data['field']['Photograph(Max File Size 2MB)']);
            unset($data['field']['Grace Time']);
            unset($data['field']['Total Leave Count(Days)']);
             unset($data['field']['Additional Document']);
            unset($data['field']['Status']);
        }
        if(!is_null($photo)){//$data['session']['type'] = "Photographer"
            
            unset($data['field']['fieldset']);
            unset($data['field']['Nick Name']);
            unset($data['field']['Birth Date']);
                
            unset($data['field']['Father\'s Name']);
            unset($data['field']['Mother\'s Name']);
            unset($data['field']['Blood Group']);
            unset($data['field']['Height']);
            unset($data['field']['Weight']);
            unset($data['field']['Gender']);
            unset($data['field']['Marital Status']);
            unset($data['field']['Identification Mark']);
            unset($data['field']['close_fieldset_']);
                
            unset($data['field']['fieldset1']);
            unset($data['field']['Email']);
            unset($data['field']['Emergency Contact Number']);
            unset($data['field']['Official Contact Number']);
            unset($data['field']['Contact Address']);
            unset($data['field']['Permanent Address<span class="required"></span>']);
            unset($data['field']['Special Qualification/Training']);
            unset($data['field']['NID Number']);
            unset($data['field']['Passport Number']);
            unset($data['field']['Passport Issue Date']);
            unset($data['field']['Passport Expiry Date']);
            unset($data['field']['Previous Organization']);
            unset($data['field']['Join Date']);
            unset($data['field']['Company<span class="required"></span>']);
            unset($data['field']['Department<span class="required"></span>']);
            unset($data['field']['Location<span class="required"></span>']);
            unset($data['field']['Designation']);
            unset($data['field']['Total Leave Count(Days)']);
            unset($data['field']['Grace Time']);
            unset($data['field']['Job Description']);
            unset($data['field']['Additional Document']);
            unset($data['field']['close_fieldset1']);
                
            unset($data['field']['fieldset2']);
            unset($data['field']['User Name<span class="required"></span>']);
            unset($data['field']['Password<span class="required"></span>']);
            unset($data['field']['Confirm Password<span class="required"></span>']);
            unset($data['field']['Permission<span class="required"></span>']);
            unset($data['field']['close_fieldset2']);
                
            unset($data['field']['fieldset3']);
            unset($data['field']['Supervisor<span class="required">']);
            unset($data['field']['Co-Supervisor #1']);
            unset($data['field']['Co-Supervisor #2']);
            unset($data['field']['close_fieldset3']);
            unset($data['field']['Exit Date']);
                
            unset($data['check_caption']);
            unset($data['check']);
                
            unset($data['field']['Status']);
        }
            
        $data['others'] = '<div style="position: absolute;top: 300px;left: 60%;">'.form_fieldset('Photograph').img((isset($row->Photo) ? site_url(array('uploaded_images','thumbnails_100',$row->Photo)) : site_url(array('assets','images','default-user.png')))).form_fieldset_close().'</div>';
//        if($data['session']['type'] != "Photographer")
        if(is_null($photo))
            $data['others'] .= form_fieldset('Office Time').$str.form_fieldset_close();
        if($id>0){
//            $doc_array = explode(',', $row->Document);
//            foreach($doc_array as $doc){
//            $data['others'] .= (($doc!=NULL) ? '<a class="paginate_button" style="text-decoration:none;position: absolute;top: 200px;left: 60%;"  href="'.base_url('uploaded_document/'.$doc).'" target="_blank">Document</a>':NULL);
//            }
            $data['others'] .= '<input type="button" onclick="window.print()" style="position: absolute;top: 200px;left: 55%;" class="print_button" title="Click here for print"/>';
        }
        if($data['session']['type']=="Super Administrator" || $data['session']['type']=="Administrator" || !is_null($photo))//|| $data['session']['type']=="Photographer"
        $data['submit'] = array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        if($this->session->flashdata('msg'))
        $data['html'] = '<div id="dialog-message" title="'.($id>0 ? 'Update' : 'Insert').' complete">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    '.$this->session->flashdata('msg').' successfully.
  </p>
  <p>
    <b>Do you want to continue?</b>.
  </p>
</div>
<script>

  $( function() {
    $( "#dialog-message" ).dialog({
      modal: true,
      buttons: {
        Yes: function() {
          $( this ).dialog( "close" );
        },
        No: function() {
            $(location).attr("href", "../employee_List");    
        }
      }
    });
  } );
  </script>';
        return $data; 
    }
    public function insert_Photo(){
        if($_FILES['file']['name']){
            $data_employee = array('Photo' => str_replace(' ','_',$_FILES['file']['name']));
            $this->db->where('Employee_ID',$this->input->post('id'));
            $this->db->update('tbl_employee_profile',$data_employee);
        }
    }    
    public function insertUpdate_EmployeeInfo(){
        $data_employee = array(
            'F_Company_ID' => $this->input->post('company'),
            'F_Location_ID' => $this->input->post('location'),
            'F_Dept_ID' => $this->input->post('department'),
            'Full_Name' => $this->input->post('full_name'),
            'Nick_Name' => $this->input->post('nick_name'),
            'DOB' => mdate('%Y-%m-%d',strtotime($this->input->post('birth_date'))),
            'RS_ID' => $this->input->post('employee_id'),
            'Father' => $this->input->post('f_name'),
            'Mother' => $this->input->post('m_name'),
            'Email' => $this->input->post('email'),
            'Join_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('join_date'))),
            'Emergency_Contact' => $this->input->post('e_contact'),
            'Office_Contact' => $this->input->post('o_contact'),
            'Email' => $this->input->post('email'),
            'Blood_Group' => $this->input->post('blood_group'),
            'Height' => $this->input->post('height'),
            'Weight' => $this->input->post('weight'),
            'Marital_Status' => $this->input->post('marital_status'),
            'Identification_Mark' => $this->input->post('id_mark'),
            'Training' => $this->input->post('training'),
            'Gender'  => $this->input->post('gender'),
            'NID'  => $this->input->post('nid'),
            'Passport'  => $this->input->post('passport'),
            'Passport_Issue_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('passport_issue_date'))),
            'Passport_Expiry_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('passport_expiry_date'))),
            'Permanent_Address'  => $this->input->post('p_address'),
            'Address' => $this->input->post('address'),
            'Previous_Organization'  => $this->input->post('p_org'),
            'Job_Desc' => $this->input->post('jobdesc'),
            'Leave'  => $this->input->post('leave_count'),
            'P_Leave'  => $this->input->post('pleave_count'),
            'Grace_Time' => $this->input->post('grace_time'),
            'Is_Exist' => $this->input->post('status')
                
        );
        if($_FILES['file']['name'])$data_employee['Photo'] = str_replace(' ','_',$_FILES['file']['name']);//this->upload->file_name;
       
        $data_days = array(
         //'F_User_ID' => $rand_id,
            'Sun' => $this->input->post('sunday') ? $this->input->post('sunday') : '0',
            'Mon' => $this->input->post('monday') ? $this->input->post('monday') : '0',
            'Tue' => $this->input->post('tuesday') ? $this->input->post('tuesday') : '0',
            'Wed' => $this->input->post('wednesday') ? $this->input->post('wednesday') : '0',
            'Thu' => $this->input->post('thursday') ? $this->input->post('thursday') : '0',
            'Fri' => $this->input->post('friday') ? $this->input->post('friday') : '0',
            'Sat' => $this->input->post('saturday') ? $this->input->post('saturday') : '0'
        );
            
        $data_user = array(
//            'F_Employee_ID' => $this->input->post('employee'),
            'F_User_Permission_ID' => $this->input->post('permission'),
                
            'Full_Name' => $this->input->post('full_name'),
            'Address' => $this->input->post('address'),
            'Email' => $this->input->post('email'),
            'Join_Date' => mdate('%Y-%m-%d',strtotime($this->input->post('join_date'))),
            'Is_Absent' => '0',
            'Is_Exist' => $this->input->post('status')
            );
        $encryption = new Encryption;
        $password = $encryption->encrypt($this->input->post('password'));
        $data_login = array(
            'F_User_Permission_ID' => $this->input->post('permission'),
            'User_Name' => $this->input->post('user_name'),
            'Password' =>  $password,
            'Is_Exist' => $this->input->post('status')
            );
                
        $data_hierarchy = array(
                'F_Designation_ID' => $this->input->post('designation'),
                'Supervisor1_ID' => $this->input->post('supervisor1_id'),
                'Supervisor2_ID' => $this->input->post('supervisor2_id'),
                'Supervisor3_ID' => $this->input->post('supervisor3_id'),
                'Is_Exist' => $this->input->post('status')
            );
                
        if($this->input->post('insert')){
            if($_FILES['adoc']['name'])$data_employee['Document'] = str_replace(' ','_',  implode (',', $_FILES['adoc']['name']));
            $this->db->insert('tbl_employee_profile',$data_employee);
            $data_days['F_Employee_ID']=$this->db->insert_id();
            $data_user['F_Employee_ID']=$this->db->insert_id();
            $data_hierarchy['F_Employee_ID']=$this->db->insert_id();
            $i=0; 
            $office_time_array = array();
            while($i<7){
                $data_time['F_Employee_ID'] = $this->db->insert_id();
                $data_time['Weekday'] = $this->input->post('weekday'.$i); ;
                $data_time['In'] = $this->input->post('in_hr'.$i).':'.$this->input->post('in_min'.$i) ;
                $data_time['Out'] = $this->input->post('out_hr'.$i).':'.$this->input->post('out_min'.$i);
                array_push($office_time_array, $data_time);
                $i++;
            }
            $this->db->insert('tbl_work_days',$data_days);
            $this->db->insert_batch('tbl_office_time',$office_time_array);
                
            $this->db->insert('tbl_user_info',$data_user);
            $data_login['F_User_ID']=$this->db->insert_id();
            $this->db->insert('tbl_login_info',$data_login);
                
            $this->db->insert('tbl_hierarchy_info', $data_hierarchy);
                
            $this->session->set_flashdata('msg', 'Employee information added');
        } 
            
        if($this->input->post('update')){
            $row_document = $this->get_Specific_Info('tbl_employee_profile',array('Employee_ID' => $this->input->post('id')),'Document',1);
            if($row_document)
            if($_FILES['adoc']['name'])$data_employee['Document'] = $row_document->Document.','.str_replace(' ','_',implode (',', $_FILES['adoc']['name']));
//            if($this->input->post('status')==0){
                if($this->input->post('exit_date'))
                    $data_employee['Inactive_Date'] = mdate('%Y-%m-%d',strtotime($this->input->post('exit_date')));
//                else
//                    $data_employee['Inactive_Date'] = $this->login_model->get_Local_Date();
//            }
//            if($this->input->post('status')==1)
//                  $data_employee['Inactive_Date'] = NULL;
            $this->db->where('Employee_ID',$this->input->post('id'));
            $this->db->update('tbl_employee_profile',$data_employee);  
                
            $this->db->where('F_Employee_ID',$this->input->post('id'));
            $this->db->update('tbl_work_days',$data_days);
                
            $i=0; 
            $office_time_array = array();
            while($i<7){
                $data_time['Weekday'] = $this->input->post('weekday'.$i); ;
                $data_time['In'] = $this->input->post('in_hr'.$i).':'.$this->input->post('in_min'.$i) ;
                $data_time['Out'] = $this->input->post('out_hr'.$i).':'.$this->input->post('out_min'.$i);
                array_push($office_time_array, $data_time);
                $i++;
            }
            $this->db->where('F_Employee_ID',$this->input->post('id'));
            $this->db->update_batch('tbl_office_time',$office_time_array,'Weekday');
                
            $this->db->where('F_Employee_ID',$this->input->post('id'));
            $this->db->update('tbl_user_info',array('Is_Exist' => $this->input->post('status')));
                
            $this->db->where('User_ID',$this->input->post('user_id'));
            $this->db->update('tbl_user_info',$data_user);  
            $this->db->where('F_User_ID',$this->input->post('user_id'));
            $this->db->update('tbl_login_info',$data_login);
                
            $this->db->where(array('F_Employee_ID' => $this->input->post('id')));
            $this->db->update('tbl_hierarchy_info', $data_hierarchy);
            $this->session->set_flashdata('msg', 'Employee information updated');
        }    
    }
        
    public function get_Employee_Information($id){
        $this->db->distinct();
        $this->db->select('P_Leave,Inactive_Date,User_ID,Document,Office_Contact,Job_Desc,Photo,Grace_Time,Employee_ID,tbl_employee_profile.F_Company_ID,Company_Name,F_Location_ID,Location_Name,F_Dept_ID,Dept_Name,RS_ID,tbl_employee_profile.Full_Name,Nick_Name,Gender,DOB,Father,Mother,tbl_employee_profile.Email,tbl_employee_profile.Join_Date,Emergency_Contact,Blood_Group,Height,Weight,Marital_Status,
        Identification_Mark,Training,NID,Passport,Passport_Issue_Date,Passport_Expiry_Date,Permanent_Address,tbl_employee_profile.Address,Previous_Organization,tbl_employee_profile.`Leave`,tbl_employee_profile.Is_Exist,
        tbl_work_days.Sun,tbl_work_days.Mon,tbl_work_days.Tue,tbl_work_days.Wed,tbl_work_days.Thu,tbl_work_days.Fri,tbl_work_days.Sat,User_Name,Password,User_Permission_ID,Designation,F_Designation_ID,Supervisor1_ID,Supervisor2_ID,Supervisor3_ID');
        $this->db->join('tbl_work_days','tbl_employee_profile.Employee_ID = tbl_work_days.F_Employee_ID','left');
        $this->db->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left');
        $this->db->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left');
        $this->db->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left');
        $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
        $this->db->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
        $this->db->join('tbl_login_info','tbl_login_info.F_User_ID = tbl_user_info.User_ID','left');
        $this->db->join('tbl_user_type','tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID','left');
        if($id!=NULL){
            $this->db->where($id);
            $this->db->limit(1, 0);
        }
        $query=$this->db->get('tbl_employee_profile');
        return   $id!=NULL ? $query->row() : $query->result();
    }
    
    public function get_absentDates($user_id,$from,$to){
       $this->db->query("SELECT * from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date BETWEEN DATE_FORMAT(NOW(),'%Y-01-01') and DATE_FORMAT(NOW(),'%Y-%m-%d') AND selected_date NOT IN (
SELECT
tbl_login_record.Date
FROM
tbl_login_record
WHERE
tbl_login_record.Date BETWEEN DATE_FORMAT(NOW(),'%Y-01-01') AND DATE_FORMAT(NOW(),'%Y-%m-%d') AND
tbl_login_record.F_User_ID = 5)");      $this->db->query("SELECT * from 
(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date BETWEEN DATE_FORMAT(NOW(),'%Y-01-01') and DATE_FORMAT(NOW(),'%Y-%m-%d') AND selected_date NOT IN (
SELECT
tbl_login_record.Date
FROM
tbl_login_record
WHERE
tbl_login_record.Date BETWEEN DATE_FORMAT(NOW(),'%Y-01-01') AND DATE_FORMAT(NOW(),'%Y-%m-%d') AND
tbl_login_record.F_User_ID = 5)");
        
    }


    public function load_EmployeeListViewInfo($photo = NULL){
        $user = $this->session->userdata('logged_in');
        $columnDef='{"orderable": false , "width": \'1%\', "targets": 0 },{"width": \'15%\', "targets": [1,4] },{"width": \'10%\', "targets": [3,8,9] },{"width": \'8%\', "targets": [2,6,10] },{"width": \'12%\', "targets": [5,11] },{"width": \'2%\', "targets": [12] }';
        $this->datatable->load_Script('grid',NULL,$columnDef);
        $js = '   
        counter = function(textMatch,col){
                count=0;
                //loop through all <tr> s
                $(\'#grid\').find(\'tbody tr\').each(function( index ) {
                    //if second <td> contains matching text update counter
                    if($($(this).find(\'td\')[col]).text() == textMatch){
                        count++
                    }
                });
                return count;
            }
        var data = $(\'#count_com\').text().split(",");
        $(\'#count_com\').text("");
        for (var d = 0; d < data.length; ++d) {
        $(\'#count_com\').append(data[d]+":"+counter(data[d],4)+"&nbsp;");
        }
        var data = $(\'#count_dept\').text().split(",");
        $(\'#count_dept\').text("");
        for (var d = 0; d < data.length; ++d) {
        $(\'#count_dept\').append(data[d]+":"+counter(data[d],5)+"&nbsp;");
        }
        ';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $encryption = new Encryption;
//        $options = '{headers: {0: {sorter: false}}}';
//        $this->load_TableSorter(array('grid'),$options);
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = is_null($photo) ? 'administrator/employee_List' : 'administrator/photo_List';
        $data['title'] = 'Employee List';
        $data['width'] = '1300';
        $data['height'] = '480';
        /*if($user['type']=="Super Administrator" )
        $this->db->like('bl_company_info.Company_Name','Ryans Archives Ltd.');
        $this->db->select('(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS S');
        $result = $this->get_Employee_Information(NULL);*/
        $this->db->select('view_employee.User_ID,view_employee.Employee_ID,view_employee.RS_ID,view_employee.Full_Name,view_employee.Nick_Name,view_employee.Company_Name,view_employee.Location_Name,view_employee.Dept_Name,view_employee.Email,view_employee.Join_Date,view_employee.Emergency_Contact,view_employee.Office_Contact,view_employee.Grace_Time,view_employee.Designation,view_employee.`Leave`,view_employee.P_Leave,view_employee.Supervisor,view_employee.Is_Exist,'
                . 'tbl_employe_list_info.Total_Absent,tbl_employe_list_info.Leave_Taken,tbl_employe_list_info.Holiday_Given,tbl_employe_list_info.Tour_Given,tbl_employe_list_info.Off_day');
        $this->db->from('view_employee');
        $this->db->join('tbl_employe_list_info','tbl_employe_list_info.Employee_ID = view_employee.Employee_ID','left');
//        $this->db->where(array('tbl_employe_list_info.RS_ID' => 'RS270'));
        $result = $this->db->get()->result();  



            
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$encryption->encrypt($row->Employee_ID), FALSE);
            $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
            $Email[] = $row->Email;
//            $Emergency_Contact[] = $row->Emergency_Contact;
            $Office_Contact[] = $row->Office_Contact;
            $Company[] = $row->Company_Name;
            $Location[] = $row->Location_Name;
            $Dept[] = $row->Dept_Name;
            $Employee_ID[] = $row->RS_ID;
            $Join_Date[] = $this->systemDateFormatConverter($row->Join_Date);
            $NID[] = $row->Supervisor;
        
            if(!is_null($row->User_ID)){ 
                $from = mdate('%Y-01-01');
                $to = mdate('%Y-%m-%d');
                /*$row_offday=$this->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row->Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));          
                $offdays_date_array = $this->home_model->getoffdaysDate($offdays, $from, $to); 
                $absent_array = $this->home_model->absentReport($row->User_ID, $offdays_date_array, $from, $to);    
                
                $weekcount= mdate('%w',  strtotime(now()));    
                $record = $this->get_User_Inforamtion(array('User_ID' => $row->User_ID,'Weekday' => $weekcount));
                $offdays_row = $this->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row->User_ID, 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('- day',now()))),'F_User_ID,Offday',1);
                $offdays = $this->home_model->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
                $offdays_date_array = $this->home_model->getoffdaysDate($offdays,$from,$to); 
                $absent_array = $this->home_model->absentReport($row->User_ID, $offdays_date_array, $from, $to);
                
                $Absent_Count[] = (count($absent_array)>0 ? '<font color="blue">'.count($absent_array).'</font>':count($absent_array));*/

                
//                $this->db->select('From_Date,To_Date');
//                        $query_tour = $this->db->get_where('tbl_tour_info','Is_Exist = 1 AND FIND_IN_SET ("'.$row->Employee_ID.'",Employee)  AND (From_Date BETWEEN DATE_FORMAT(NOW(),"%Y-01-01") and DATE_FORMAT(NOW(),"%Y-%m-%d") OR To_Date BETWEEN DATE_FORMAT(NOW(),"%Y-01-01") and DATE_FORMAT(NOW(),"%Y-%m-%d"))');
//                        $result_tour = $query_tour->result();
//                        $tour_date_array = array();
//                        foreach($result_tour as $row_tour){
//                            $temp_tour_date_array = $this->administrator_model->showDates($row_tour->From_Date, $row_tour->To_Date);
//                            $tour_date_array = array_merge($tour_date_array,$temp_tour_date_array);
//                        }
               
//                echo $this->leaveCountNew($row->User_ID).br();
                $this->db->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                    ->join('tbl_office_time','tbl_employee_profile.Employee_ID = tbl_office_time.F_Employee_ID','inner')
                    ->join('tbl_login_record','tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner');
                $Late_Count[] = $this->db->where('tbl_login_record.Date BETWEEN \''.$from.'\' AND \''.$to.'\' AND tbl_office_time.Weekday = DATE_FORMAT(tbl_login_record.Date,"%w") AND ADDTIME(tbl_office_time.`In`,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60)) < tbl_login_record.`In_Time`')
                    ->where(array('User_ID' => $row->User_ID))->count_all_results('tbl_user_info');
                 $absent = (int)$row->Total_Absent-(ceil($row->Leave_Taken)+(int)$row->Holiday_Given+(int)$row->Tour_Given+(int)$row->Off_day);        
            if(mdate('%Y',strtotime($row->Join_Date))>= mdate('%Y')){
                 $date_diff = $this->db->query('SELECT DATEDIFF(
(SELECT DISTINCT tbl_login_record.Date FROM tbl_login_record WHERE tbl_login_record.F_User_ID = '.$row->User_ID.' ORDER BY tbl_login_record.Date ASC LIMIT 1),
    (SELECT DISTINCT tbl_employee_profile.Join_Date FROM tbl_employee_profile WHERE tbl_employee_profile.Employee_ID = '.$row->Employee_ID.' LIMIT 1)
    ) AS Diff
')->row();
                 $absent = $absent - ($date_diff ? abs($date_diff->Diff) : 0);
            }
/*            if($row->Employee_ID == 55)
                echo  ($date_diff ? abs($date_diff->Diff):0).' total:'.$row->Total_Absent.' Leave:'.ceil($row->Leave_Taken).' Holiday:'.$row->Holiday_Given.' Tour:'.$row->Tour_Given.' Offday:'.$row->Off_day;
*/ 
           }

           
           
          
            $Absent_Count[] = $absent<0 ? 0 : ($absent>0 ? '<font color="blue">'.$absent.'</font>': $absent);        
//               $Absent_Count[] = 'total absent'.$row->Total_Absent.'-'.'(leave taken'.ceil($row->Leave_Taken).'+ holiday given'.(int)$row->Holiday_Given.'+ tour given'.(int)$row->Tour_Given.'+ offday'.(int)$row->Off_day.')'; 
            $Leave[] = $row->P_Leave;//(mdate('%Y',strtotime($row->Join_Date))== mdate('%Y',now())) ? round(dateDifference($this->systemDateFormatConverter($row->Join_Date),mdate('%Y-12-12',now()))*(42/365)) :  
            $Leave_Taken[] = $row->Leave_Taken;
            $Remaining[] = $row->P_Leave-($row->Leave_Taken);//+($absent >0 ? $absent : 0). 'pleave:'.$row->P_Leave.'-'.'leave taken:'.$row->Leave_Taken.'-'.'absent:'.$absent.'=';
            $Status[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Name' => $Full_Name,'Employee ID' => $Employee_ID,'Supervisor' => $NID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Email' => $Email,'Official Contact' => $Office_Contact,'Join Date' => $Join_Date,'Late' => $Late_Count,'Absent' => $Absent_Count,'Personal Leave' => $Leave,'Leave Taken' => $Leave_Taken,'Remaining' => $Remaining,'Mode' => $Status);//'Emergency Contact' => $Emergency_Contact
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));//'Filter' => array('input' => array('id' => 'searchInput','value' => 'Type to filter','placeholder' => 'Type to filter')),
        $result_com = $this->get_Specific_Info('tbl_company_info', array('Is_Exist' => 1), 'Company_Name',2);
        foreach($result_com as $row_com)
            $company_array[] = $row_com->Company_Name;
        $result_dept = $this->get_Specific_Info('tbl_dept_info', array('Is_Exist' => 1), 'Dept_Name',2);
        foreach($result_dept as $row_dept)
            $dept_array[] = $row_dept->Dept_Name;
        $data['footer'] =  array('<span id="count_com">'.implode(',', $company_array).'</span>');//,'Department[<span id="count_dept">'.implode(',', $dept_array).'</span>]' ,img(array('title' => 'Export to excel','id' => 'download_button','src' => base_url('assets/images/export_excel10.png')))
        return $data;
    }
        
        
    public function load_EmployeeListFor_AssignHoliday($companyID=NULL,$deptID=NULL,$userID=NULL,$locID=NULL){
        $Check_ID = $Full_Name = $Employee_ID = $Company = $Location = $Dept = $Leave = $Status = NULL;//= $Emergency_Contact 
        $this->datatable->load_Script('grid');
            $js = "function change_dept() { 
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value= ''>All Dept</option>\");
                    var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Dept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                        }
                            
                    });
                        
                }";
//        $js .= "change_dept();";
        $js .= "$('#company').change(change_dept);";
        $js .= "$('#company').change(function() { 
                    $('#users > option').remove(); 
                    $('#users').append(\"<option value= ''>All User</option>\");
                    var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.User_ID);
                                opt.text(obj.Full_Name);
                                $('#users').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $js .= "$('#dept').change(function() { 
                    $('#users > option').remove(); 
                    $('#users').append(\"<option value= ''>All User</option>\");
                    var dept_id = $('#dept').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_User')."/' + dept_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.User_ID);
                                opt.text(obj.Full_Name);
                                $('#users').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $js .= '
            
        counter = function(textMatch,col){
                count=0;
                //loop through all <tr> s
                $(\'#grid\').find(\'tbody tr\').each(function( index ) {
                    //if second <td> contains matching text update counter
                    if($($(this).find(\'td\')[col]).text() == textMatch){
                        count++
                    }
                });
                return count;
            }
                
        $(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $js .= "function getAssignEmp() {
                    if($('#holiday').val()!='')
                    $('#loader').addClass('loader');
                    $('#grid').attr('disabled', 'disabled');
                    $.ajax({
                        type: 'POST',
                        url: '".base_url('/administrator/get_Holiday_Employee')."', 
                        data: {'holiday': $('#holiday').val()},
                            
                        success: function(data) 
                        {   
                            $(':checkbox').prop('checked', false); 
                            data = jQuery.parseJSON(data);
                            if(data.length<=1){
                            $('#loader').removeClass('loader');
                            }
                            $.each(data, function(i, data)
                            {   
                                $(':checkbox[value='+data+']').prop('checked','true');
                            });
                        },
                        complete: function () {
                            $('#loader').removeClass('loader');
                            $('#grid').removeAttr('disabled', 'disabled');
                        }
                            
                    });
                        
                }";
        $js .= "$('#holiday').change(getAssignEmp);";
        $js .= 'getAssignEmp();';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $encryption = new Encryption;
            
        $result_holiday = $this->get_Specific_Info('tbl_holiday_info', array('Is_Exist' => 1),'Holiday_Name,Holiday_ID',2);
        $options_holiday[NULL] = 'Please Select'; 
        foreach($result_holiday as $row_holiday){
            $options_holiday[$row_holiday->Holiday_ID] = $row_holiday->Holiday_Name;
        }
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/assign_Holiday';
        $data['title'] = 'Holiday Assign';
        $data['width'] = '1250';
        $data['height'] = '480';
        $array_where['tbl_employee_profile.Is_Exist'] = 1 ;
        if($companyID !=NULL){
        if($deptID == NULL){
            $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
            if($userID != NULL)
                $array_where['User_ID'] = $userID;
        }else if($userID == NULL)
            $array_where['F_Dept_ID'] = $deptID;
        else
            $array_where['User_ID'] = $userID;
        }
        if($locID != NULL){
            $array_where['Location_ID'] = $locID; 
        }
        $this->db->where($array_where);
        $result = $this->get_Employee_Information(NULL);
      
        foreach($result as $row){
//            $this->get_Specific_Info('tbl_holiday_info', array('Holiday_ID' =>))
            $Check_ID[] = form_checkbox('ID[]',$row->Employee_ID, FALSE);
            $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
            $Employee_ID[] = $row->RS_ID;
//            $Emergency_Contact[] = $row->Emergency_Contact;
            $Company[] = $row->Company_Name;
            $Location[] = $row->Location_Name;
            $Dept[] = $row->Dept_Name;
            $Leave[] = $row->Leave - ($this->leaveCount($row->User_ID,1)+$this->home_model->totalHolidayCount($row->Employee_ID));
            $Status[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array(form_checkbox(array('id'=>'select_all')) => $Check_ID,'Name' => $Full_Name,'Employee ID' => $Employee_ID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Remaining Leave' => $Leave,'Mode' => $Status);//,'Emergency Contact' => $Emergency_Contact
        $data['width_th'] = array(3,15,6,8,10,8,10,6,4);
            
        $result_company = $this->get_Specific_Info('tbl_company_info',array('Is_Exist'=>1),'Company_ID,Company_Name',2);
        $options_company[NULL] = 'Please Select';
        foreach($result_company as $row_company)
            $options_company[$row_company->Company_ID] = $row_company->Company_Name;
                
        $result_dept = $this->get_Specific_Info('tbl_dept_info',array('Is_Exist'=>1),'Dept_ID,Dept_Name',2);
        $options_dept[NULL] = $companyID == NULL ? 'Please Select' : 'All Dept';
        foreach($result_dept as $row_dept)
            $options_dept[$row_dept->Dept_ID] = $row_dept->Dept_Name;
        $result_usr = ($deptID == NULL) ? $this->get_Specific_Info('tbl_user_info',array('Is_Exist'=>1,'F_User_Permission_ID <> '=>1),'User_ID,Full_Name',2) : $this->db->distinct()->select('User_ID,tbl_user_info.Full_Name')->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')->get_where('tbl_user_info',array('tbl_user_info.Is_Exist'=>1,'F_User_Permission_ID <> '=>1,'F_Dept_ID' => $deptID))->result();
        $options_usr[NULL]= ($deptID == NULL) ? 'Please Select' : 'All User';
        foreach($result_usr as $row_usr)
            $options_usr[$row_usr->User_ID]=$row_usr->Full_Name;
                
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$options_company,$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:12em;"')),'Department' => array('dropdown' => array('dept_ID',$options_dept,$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:12em;"')),'User' => array('dropdown' => array('user_ID',$options_usr,$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:12em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')), 'Holiday' => array('dropdown' => array('holiday',$options_holiday,$this->session->userdata('holiday') ? $this->session->userdata('holiday') : ($this->input->post('holiday') ? $this->input->post('holiday') : NULL),'id="holiday" style="width: 110px;"')) , ' ' => array('submit' => array('name' => 'assign','value' => 'Assign', 'onClick' => 'javascript:return confirm(\'Are you sure to assign?\')'),'label'=> '<div id="loader"></div>'));//,'' => array('submit' => array('name' => 'cancel','value' => 'Cancel','onClick' => 'javascript:return confirm(\'Are you sure to cancel?\')'))
        $data['footer'] =  array(img(array('title' => 'Export to excel','id' => 'download_button','src' => base_url('assets/images/export_excel10.png'))));
        return $data;
    }
        
    public function load_AbsentLeaveListViewInfo($company_names){
        if(count($company_names)<=1){
        $this->db->like('Company_Name',$company_names[0]);
        }else{
            $this->db->like('Company_Name',$company_names[0]);
            $this->db->or_like('Company_Name',$company_names[1]);
        }
        $this->db->order_by('Location_ID','asc');
        $this->db->order_by('Dept_ID','asc');
        $result = $this->db->distinct()->select('Dept_Name,Location_Name,F_Employee_ID,User_ID,tbl_user_info.Full_Name,tbl_employee_profile.Grace_Time,RS_ID,tbl_employee_profile.Join_Date')->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left')->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left')->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left')->get_where('tbl_user_info',array('tbl_user_info.Is_exist' => 1,'tbl_employee_profile.Is_exist' => 1, 'tbl_user_info.User_ID <>' => 1,'tbl_employee_profile.Join_Date <=' => mdate('%Y-%m-%d') ))->result();
        $list = NULL;    
        foreach($result as $row_user){
                    $date = mdate('%Y-%m-%d');
                    $weekcount=mdate('%w',  strtotime($date));  
                        
                    $row_offday=$this->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                    $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                    $result_leave = $this->get_Specific_Info('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=> $row_user->User_ID,'DATE_FORMAT(tbl_leave_record.From_Date,"%Y")' => mdate('%Y', now())),'From_Date,To_Date',2);
                        
                    $row = $this->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Remarks',1);
                        
                    $res_presesnt = $this->db->distinct()->select('Date')->from('tbl_login_record')->where(array('F_User_ID' => $row_user->User_ID))->order_by('Date', 'desc')->limit(1)->get()->result();
                    foreach($res_presesnt as $last_present_date)
                    $from = $last_present_date->Date;// ? $last_present_date->Date : $row_user->Join_Date
                    $to = $this->systemDateFormatConverter(now());
                    $record = $this->administrator_model->get_User_Inforamtion(array('User_ID' => $row_user->User_ID,'Weekday' => $weekcount));
                    $offdays = $this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
                    $offdays_date_array = $this->home_model->getoffdaysDate($offdays,$from,$to); 
                    $absent_array = $this->home_model->absentReport($row_user->User_ID, $offdays_date_array, $from, $to);
                        
                    if($row || $this->home_model->is_Holiday($date,$row_user->F_Employee_ID)){
                    }
                    elseif($this->is_OnLeave($row_user->User_ID,$date,$result_leave)){
                         if(in_array($weekcount,$offdays)){
                             
                         }else{
                            $list.= $row_user->Full_Name.'.'.$row_user->Dept_Name.'/'.$row_user->Location_Name.'[Leave]'.'('.$this->count_OnLeave($row_user->User_ID,$date).')'."\n";
                         }    
                    }
//                    elseif($this->home_model->is_Holiday($date,$row_user->F_Employee_ID)){
//                         if(in_array($weekcount,$offdays)){
//                             
//                         }else
//                         $list.= $row_user->Full_Name.'.'.$row_user->Dept_Name.'/'.$row_user->Location_Name.'['.$this->home_model->is_Holiday($date,$row_user->F_Employee_ID).']'."\n";
//                     
//                    }
                    elseif($this->home_model->is_OnTour($date,$row_user->F_Employee_ID)){
                         if(in_array($weekcount,$offdays)){
                             
                         }else
                         $list.= $row_user->Full_Name.'.'.$row_user->Dept_Name.'/'.$row_user->Location_Name.'[Tour]'."\n";//$this->home_model->is_OnTour($date,$row_user->F_Employee_ID)
                             
                    }else{
                        if(in_array($weekcount,$offdays)){
                            
                        }else
                        $list .= $row_user->Full_Name.'('.count($absent_array).')'.'.'.$row_user->Dept_Name.'/'.$row_user->Location_Name."\n";
                            
                    }
        }
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '380';
        $data['height'] = '540';
        $data['title']='Today\'s Absent List';
        $data['form']='administrator/absentLeave_List'; 
        $data['field']=array(
            'Copy' => array('textarea' => array('name' => 'address','rows' => '30','cols' => '40','value' => set_value('address',$list)))
        );
        return $data; 
    }
        
        public function load_SupervisorListViewInfo($companyID){
            $result = $this->db->distinct()->select('Full_Name,Employee_ID')
                    ->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_hierarchy_info.Supervisor1_ID OR tbl_employee_profile.Employee_ID = tbl_hierarchy_info.Supervisor2_ID OR tbl_employee_profile.Employee_ID = tbl_hierarchy_info.Supervisor3_ID','inner')
                    ->get_where('tbl_hierarchy_info',array('tbl_hierarchy_info.Is_Exist'=>1,'tbl_employee_profile.Is_Exist'=>1,'tbl_employee_profile.F_Company_ID' => $companyID))->result();
//            $result = $this->db->distinct()->select('Dept_Name,Location_Name,F_Employee_ID,User_ID,tbl_user_info.Full_Name,tbl_employee_profile.Grace_Time,RS_ID')->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left')->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left')->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left')->get_where('tbl_user_info',array('tbl_user_info.Is_exist' => 1,'tbl_employee_profile.Is_exist' => 1, 'tbl_user_info.User_ID <>' => 1))->result();
            $list = NULL;    
            foreach($result as $row_user)
                $list.= $row_user->Full_Name."\n";
            $data['menu'] = $this->load_Menu();
            $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
            $data['width'] = '380';
            $data['height'] = '540';
            $data['title']='Supervisor List';
            $data['form']='administrator/sv_List'; 
            $data['field']=array(
                'Copy' => array('textarea' => array('name' => 'address','rows' => '30','cols' => '40','value' => set_value('address',$list)))
            );
            return $data; 
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
        
        
        
    public function is_OnLeave($user_id=NUll,$date,$result){
        $date_array = array();
        foreach($result as $row){
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_array = $this->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_array); 
        }
        return in_array($date,$date_array)?  TRUE : FALSE;
    }
        
    public function count_OnLeave($user_id=NUll,$date){
        
        $row = $this->db->distinct()->select('From_Date,To_Date,Day')->order_by('From_Date','desc')->get_where('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=> $user_id,'From_Date'=>$date),1)->row(); 
        if(!$row)
        $row = $this->db->distinct()->select('From_Date,To_Date,Day')->order_by('From_Date','desc')->get_where('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=> $user_id),1)->row();                  
       
        $from= $row->From_Date;
        $to = $row->To_Date;

        if(($from == $to) &&($from == $date) && ($to == $date)){
            return $row->Day;   
        }else{
            $date_array = array();
            $temp_array = $this->showDates($from, $date);
            $date_array = array_merge($date_array,$temp_array); 
            return count($date_array);  
        }
                    

    }
    
//        foreach($result as $row){
//            $from= $row->From_Date;
//            $to = $row->To_Date;
//            $temp_array = $this->showDates($from, $date);
//            $date_array = array_merge($date_array,$temp_array); 
//        }
        
    public function load_leaveHistory_Table(){
        $result = $this->get_Specific_Info('tbl_leave_record', array('F_User_ID'=>$this->input->post('user_id')),'From_Date,To_Date,Day,Leave_Reason,Is_Processed,Is_Void',2);
        $str = '<fieldset>
<legend>Leave History</legend>
        <div class="grid">
        <table align="center" width="90%" id="grid1">
            <thead>
                <tr><th width="20%">From Date</th><th width="20%">To Date</th><th>Day</th><th width="36%">Reason</th><th width="10%">Status</th></tr>
            </thead>
            <tbody style="max-height: 150px">';
          foreach($result as $row){
            $str .='<tr><td width="20%">'.$row->From_Date.'</td><td width="20%">'.$row->To_Date.'</td><td>'.$row->Day.'</td><td width="36%">'.$row->Leave_Reason.'</td><td width="10%">'.(($row->Is_Processed) ? $this->lang->line('granted') : (($row->Is_Void) ? $this->lang->line('rejected') : $this->lang->line('pending'))).'</td></tr>';
          }
          $str .= '</tbody>
        </table>
        </div>
        </fieldset>';
            
          return $str;      
              
    }
        
    public function load_absentHistory_Table($from=NULL,$to=NULL){
        $record=$this->get_User_Inforamtion(array('User_ID' => $this->input->post('user_id')));
        $offdays=$this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
        $offdays_date_array = $this->home_model->getoffdaysDate($offdays,$from,$to); 
        $array_absent= $this->home_model->absentReport($this->input->post('user_id'),$offdays_date_array,  is_null($from) ? $this->login_model->get_Local_Date('%Y-%m-01') : $from, is_null($to) ? $this->login_model->get_Local_Date('%Y-%m-%d') : $to);
        $str = '<fieldset>
<legend>Absent History</legend>
        <div class="grid">
        <table align="center" width="90%" id="grid2">
            <thead>
                <tr><th width="30%">Date</th><th width="30%">User Remarks</th><th width="30%">Admin Remarks</th></tr>
            </thead>
            <tbody style="max-height: 150px">';
          for($i=0; $i<sizeof($array_absent);$i++){
            $row_admn_remark = $this->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => $this->input->post('user_id'), 'tbl_login_remarks.Date' => $array_absent[$i]),'User_Remarks,Admin_Remarks',1);
            $str .='<tr><td width="30%">'.$array_absent[$i].'</td><td width="30%">'.(($row_admn_remark) ? $row_admn_remark->User_Remarks : NULL).'</td><td width="30%">'.(($row_admn_remark) ? $row_admn_remark->Admin_Remarks : NULL).'</td></tr>';
                
          }
          $str .= '</tbody>
        </table>
        </div>
        </fieldset>';
            
          return $str;      
              
    }
        
        
    public function upload_Image($image,$path,$width=NULL,$height=NULL,$thumb=NULL,$validation_flag=FALSE){
        if(!file_exists($path))mkdir($path, 0777, true);
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']  = 2048;
        $config['max_width']  = '2288';
        $config['max_height']  = '2860';
        $config['image_width'] = $width;
        $config['image_height'] = $height;
        $config['overwrite'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $this->load->library('upload');
        $this->upload->initialize($config);
            
            
        if(!$this->upload->do_upload($image)){
            return $validation_flag ? $this->upload->display_errors('', '') : FALSE;
        }else{
            $this->output->set_output($image_path = $this->upload->upload_path.$this->upload->file_name);
            return $this->resize_Image($image_path,$width,$height,$thumb,$validation_flag);
        }  
    }
        
    private function resize_Image($image_path,$width=NULL,$height=NULL,$thumb=NULL,$validation_flag){
        $this->load->library('image_lib');
        $config['image_library'] = 'gd2';
        $config['source_image'] = $image_path;
        $config['width'] = $width;
        $config['height'] =$height; 
        $config['overwrite'] = TRUE;
        if($thumb!=NULL){
            if(is_array($thumb)){
                foreach($thumb as $thumb_width){
                    if(!is_dir($this->upload->upload_path.'/thumbnails_'.$thumb_width))mkdir($this->upload->upload_path.'/thumbnails_'.$thumb_width, 0777, true);
                    $config['new_image'] = $this->upload->upload_path.'/thumbnails_'.$thumb_width.'/'.$this->upload->file_name;
                    $config['width'] = $thumb_width;
                    $config['height'] = $thumb_width;
                    $config['maintain_ratio'] = TRUE;
                    $this->image_lib->initialize($config);
                    if(!$this->image_lib->resize()){
                        $this->delete_File($this->upload->file_name, $this->upload->upload_path);
                        $this->image_lib->clear();
                        return $validation_flag ? $this->image_lib->display_errors() : FALSE;
                    }
                }
            }else{
                if(!is_dir($this->upload->upload_path.'/thumbnails'))mkdir($this->upload->upload_path.'/thumbnails_'.$thumb_width, 0777, true);
                $config['new_image'] = $this->upload->upload_path.'/thumbnails/'.$this->upload->file_name;
                list($width_thumb, $height_thumb, $type, $attr) =  getimagesize($image_path);
                if($height!=NULL)
                        $config['height'] = ($height_thumb<=100 ? (($height_thumb>=50) ? $thumb+5 : $thumb) : $thumb+10);
                $config['maintain_ratio'] = TRUE;
                $config['overwrite'] = TRUE;
                $this->image_lib->initialize($config);
                if(!$this->image_lib->resize()){
                    $this->delete_File($this->upload->file_name, $this->upload->upload_path.'/thumbnails_'.$thumb_width);
                    $this->delete_File($this->upload->file_name, $image_path);
                    $this->image_lib->clear();
                    return $validation_flag ? $this->image_lib->display_errors() : FALSE;
                }
                    
            }
       }
   }
       
public function load_DesignationEntryFromInfo($id){
        $js = "function change_dept() { 
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value= ''>All Dept</option>\");";
                        
                    $js .= "var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Dept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                        }
                            
                    });
                        
                }";
//        if($id>0) $js .= 'change_dept();';
        $js .= "$('#company').change(change_dept);";
        $this->javascript->ready($js); 
        $this->javascript->compile();
        if($id>0)$row= $this->get_Specific_Info('tbl_designation_info',array('Designation_ID' => $id));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['width'] = '550';
        $data['height'] = '300';
        $data['title'] = ($id>0 ? 'Update' : 'Add').nbs().'Designation';
        $data['form'] = 'administrator/designation_Entry'.(isset($row) ? '/'.$row->Designation_ID : '/');
        $data['hidden'] = isset($row) ? array('id' => $row->Designation_ID) : NULL;
        $data['field']=array(
                'Designation Name' => array('input' => array('name' => 'desig_name','maxlength' => '50','size' => '40','value' =>  set_value('desig_name', isset($row) ? $row->Designation_Name : NULL))),
                'Company' => array('dropdown' => array('company',$this->globals->getOptionsCompany(),isset($row) ? $row->F_Company_ID :($this->input->post('company') ? $this->input->post('company'): NULL),'id="company" ')),
                'Dept' => array('dropdown' => array('dept[]',isset($row->F_Company_ID) ? $this->globals->getOptionsDept($row->F_Company_ID) : array('NULL' => 'Please Select'),isset($row) ? explode(',', $row->Dept_IDs) :($this->input->post('dept[]') ? $this->input->post('dept[]'): NULL),'id="dept" multiple size="10"')), 
            );
        $data['select']=array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['selected'] = array(isset($row) ? $row->Is_Exist : NULL);
        $data['submit'] = array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert');
        return $data;
    }
        
    public function load_DesignationListViewInfo(){
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/designation_List';
        $data['title'] = 'Designation List';
        $data['width'] = '600';
        $data['height'] = '480';
        $this->db->distinct()->select('Company_Name,Designation_ID,Designation_Name,tbl_designation_info.Is_Exist,GROUP_CONCAT(tbl_dept_info.Dept_Name ORDER BY tbl_dept_info.Dept_ID) Dept_Names');
        $this->db->join('tbl_company_info','tbl_designation_info.F_Company_ID = tbl_company_info.Company_ID','inner');
        $this->db->_protect_identifiers=false;
        $this->db->join('tbl_dept_info','FIND_IN_SET(Dept_ID, Dept_IDs) > 0','inner');
        $this->db->group_by('Designation_ID');
        $result = $this->db->get('tbl_designation_info')->result();
        $Check_ID = $Designation_Name = $Company_Name = $Dept_Names = $Visibility = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Designation_ID, FALSE);
            $Designation_Name[] = $row->Designation_Name;
            $Company_Name[] = $row->Company_Name;
            $Dept_Names[] = $row->Dept_Names;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Designation' => $Designation_Name,'Company' => $Company_Name,'Deparments' => $Dept_Names,'Status' => $Visibility);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        return $data;
    }
        
    public function load_InsertDesignation_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'desig_name', 
                'label'   => 'Designation Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
            array(
                'field'   => 'company', 
                'label'   => 'Company', 
                'rules'   => 'trim|required|max_length[100]|xss_clean'
            ),
            array(
                'field'   => 'dept[]', 
                'label'   => 'Dept', 
                'rules'   => 'trim|required|max_length[100]|xss_clean'
            )
                
        );
        return $config;
    }
        
    public function insertUpdate_Designation_Info(){
        $set = array(
              'F_Company_ID' => $this->input->post('company'),
              'Dept_IDs' => implode(',', $this->input->post('dept')),
              'Designation_Name' => $this->input->post('desig_name'),
              'Is_Exist' => $this->input->post('Visibility')
              );
       if($this->input->post('insert'))
           $this->db->insert('tbl_designation_info',$set);
       if($this->input->post('update')){
            $this->db->where('Designation_ID',$this->input->post('id'));
            $this->db->update('tbl_designation_info',$set);      
        }
    }
        
/*    Tour Manager  */
    public function load_TourEntryFromInfo($id){
        $this->load_Datepicker(array('from_date','to_date'));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '550';
        $data['height'] = '180';
        $data['title']= ($id>0 ? 'Update': 'Add').nbs().'Tour';
        if($id>0)$row = $this->get_Specific_Info('tbl_tour_info',array('Tour_ID' => $id));
        $data['form']='administrator/tour_Entry'.(isset($row) ? '/'.$row->Tour_ID : '/'); 
        $data['hidden']= $id>0 ? array('id' => $row->Tour_ID) : NULL;
//        $holiday_type_array = array(NULL => 'Please Select','Public Holiday' => 'Public Holiday','Government Holiday' => 'Government Holiday','Optional Holiday' => 'Optional Holiday');
        $data['field']=array(
            'Name' => array('input' => array('name' => 'tour_name','maxlength' => '50','size' => '40','value' =>  set_value('holiday_name', isset($row) ? $row->Tour_Name : NULL))),
//            'Holiday Type' => array('dropdown' => array('holiday_type',$holiday_type_array, isset($row) ? $row->Holiday_Type : ($this->input->post('holiday_type') ? $this->input->post('holiday_type') : NULL))),
            'From' => array('input' => array('name' => 'from_date','id' => 'from_date','value' => set_value('from_date',isset($row) ? $this->regularDateFormatConverter($row->From_Date) : NULL))),
            'To' => array('input' => array('name' => 'to_date','id' => 'to_date','value' => set_value('to_date',  isset($row) ? $this->regularDateFormatConverter($row->To_Date) : NULL))),
            'Status' => array('dropdown' => array('status',$this->lang->line('status'),$id>0?$row->Is_Exist : NULL)),
            '' => array('submit' => array('name' =>  $id>0 ? 'update' : 'insert','value' =>  $id>0 ? 'Update' : 'Insert'))
            );
        return $data;
    }
        
    public function insertUpdate_Tour_Info(){
         $set = array(
                'Tour_Name' => $this->input->post('tour_name'),
//                'Holiday_Type' => $this->input->post('holiday_type'),
                'From_Date' => $this->systemDateFormatConverter($this->input->post('from_date')),
                'To_Date' => $this->systemDateFormatConverter($this->input->post('to_date')),
                'Is_Exist' => $this->input->post('status')
            );
        if($this->input->post('insert'))
           $this->db->insert('tbl_tour_info', $set);
        if($this->input->post('update')){
            $this->db->where(array('Tour_ID' => $this->input->post('id')));
            $this->db->update('tbl_tour_info', $set);
        }
    }
        
    public function load_TourEntry_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'tour_name', 
                'label'   => 'Tour Name', 
                'rules'   => 'trim|required|min_length[4]|max_length[50]|xss_clean'
            ),
//            array(
//                'field'   => 'holiday_type', 
//                'label'   => 'Holiday Type', 
//                'rules'   => 'trim|required|xss_clean'
//            ),
            array(
                'field'   => 'from_date', 
                'label'   => 'From', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'to_date', 
                'label'   => 'To', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function load_EmployeeTourListViewInfo(){
            $js = "
                function calculateSum() {
                var sum = 0;
                //iterate through each td based on class and add the values
                $('#grid').find('tbody tr').each(function() {
                    //add only if the value is number
                    var value = $($(this).find('td')[5]).text();
                    if(!isNaN(value) && value.length!=0) {
                        sum += parseFloat(value);
                    }
                        
                });
                return sum;
                }
                $('#total').text('Total given holiday:'+calculateSum());";
        $this->javascript->ready($js);
        $this->javascript->compile();
        $this->datatable->load_Script('grid');
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/tour_List';
        $data['title'] = 'Tour List';
        $data['width'] = '800';
        $data['height'] = '480';
        $this->db->distinct()->select(array('Tour_ID,Tour_Name,From_Date,To_Date,tbl_tour_info.Is_Exist,(DATEDIFF(To_Date,From_Date)+1) AS Day,GROUP_CONCAT(tbl_employee_profile.Full_Name ORDER BY tbl_employee_profile.Employee_ID) Employee_Names'));
        $this->db->_protect_identifiers=false;
        $this->db->join('tbl_employee_profile','FIND_IN_SET(Employee_ID, Employee) > 0','left');
        $this->db->group_by('Tour_ID');
        $result = $this->db->get('tbl_tour_info')->result();
        $Check_ID = $Tour_Name = $From = $To = $Visibility = $Day = $Employees = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_radio('ID',$row->Tour_ID, FALSE);
            $Tour_Name[] = $row->Tour_Name;
            $From[]= $this->systemDateFormatConverter($row->From_Date);
            $To[]= $this->systemDateFormatConverter($row->To_Date);
            $Day[] = $row->Day;
            $Employees[] = $row->Employee_Names;
            $Visibility[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';;
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Tour Name' => $Tour_Name,'From' => $From,'To' => $To,'Day' => $Day,'Employee' => $Employees,'Visibility' => $Visibility);
        $data['other_fields']=array(' ' => array('submit' => array('name' => 'edit','value' => 'Edit')),'' => array('submit' => array('name' => 'delete','value' => 'Delete','onClick' => 'javascript:return confirm(\'Are you sure to Delete?\')')));
        $data['footer'] = array('<spand id="total"></span>');
        return $data;
    }
        
    public function load_EmployeeListFor_AssignTour($companyID=NULL,$deptID=NULL,$userID=NULL){
        $Check_ID = $Full_Name = $Employee_ID = $Emergency_Contact = $Company = $Location = $Dept = $Leave = $Status = NULL;
        $this->datatable->load_Script('grid');
            $js = "function change_dept() { 
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value= ''>All Dept</option>\");
                    var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Dept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                        }
                            
                    });
                        
                }";
//        $js .= "change_dept();";
        $js .= "$('#company').change(change_dept);";
        $js .= "$('#company').change(function() { 
                    $('#users > option').remove(); 
                    $('#users').append(\"<option value= ''>All User</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.User_ID);
                                opt.text(obj.Full_Name);
                                $('#users').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $js .= "$('#dept').change(function() { 
                    $('#users > option').remove(); 
                    $('#users').append(\"<option value= ''>All User</option>\");
                    var dept_id = $('#dept').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_User')."/' + dept_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.User_ID);
                                opt.text(obj.Full_Name);
                                $('#users').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $js .= '
            
        counter = function(textMatch,col){
                count=0;
                //loop through all <tr> s
                $(\'#grid\').find(\'tbody tr\').each(function( index ) {
                    //if second <td> contains matching text update counter
                    if($($(this).find(\'td\')[col]).text() == textMatch){
                        count++
                    }
                });
                return count;
            }
                
        $(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $js .= "$('#tour').change(function() {
                    $('#loader').addClass('loader');
                    $('#grid').attr('disabled', 'disabled');
                    $.ajax({
                        type: 'POST',
                        url: '".base_url('/administrator/get_Tour_Employee')."', 
                        data: {'tour': $(this).val()},
                            
                        success: function(data) 
                        {   
                            $(':checkbox').prop('checked', false); 
                            data = jQuery.parseJSON(data);
                            if(data.length<=1){
                            $('#loader').removeClass('loader');
                            }
                            $.each(data, function(i, data)
                            {   
                                $(':checkbox[value='+data+']').prop('checked','true');
                            });
                        },
                        complete: function () {
                            $('#loader').removeClass('loader');
                            $('#grid').removeAttr('disabled', 'disabled');
                        }
                            
                    });
                        
                });";
        $this->javascript->ready($js);
        $this->javascript->compile();
        $encryption = new Encryption;
            
        $result_tour = $this->get_Specific_Info('tbl_tour_info', array('Is_Exist' => 1),'Tour_Name,Tour_ID',2);
        $options_tour[NULL] = 'Please Select'; 
        foreach($result_tour as $row_tour){
            $options_tour[$row_tour->Tour_ID] = $row_tour->Tour_Name;
        }
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/assign_Tour';
        $data['title'] = 'Tour Assign';
        $data['width'] = '1250';
        $data['height'] = '480';
        $array_where['tbl_employee_profile.Is_Exist'] = 1 ;
        if($companyID !=NULL){
        if($deptID == NULL){
            $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
            if($userID != NULL)
                $array_where['User_ID'] = $userID;
        }else if($userID == NULL)
            $array_where['F_Dept_ID'] = $deptID;
        else
            $array_where['User_ID'] = $userID;
        }
        $this->db->where($array_where);
        $result = $this->get_Employee_Information(NULL);
        foreach($result as $row){
//            $this->get_Specific_Info('tbl_holiday_info', array('Holiday_ID' =>))
            $Check_ID[] = form_checkbox('ID[]',$row->Employee_ID, FALSE);
            $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
            $Employee_ID[] = $row->RS_ID;
            $Emergency_Contact[] = $row->Emergency_Contact;
            $Company[] = $row->Company_Name;
            $Location[] = $row->Location_Name;
            $Dept[] = $row->Dept_Name;
            $Leave[] = $row->Leave - ($this->leaveCount($row->User_ID,1)+$this->home_model->totalHolidayCount($row->Employee_ID));
            $Status[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            }
        $data['field'] = array(form_checkbox(array('id'=>'select_all')) => $Check_ID,'Name' => $Full_Name,'Employee ID' => $Employee_ID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Emergency Contact' => $Emergency_Contact,'Remaining Leave' => $Leave,'Mode' => $Status);
        $data['width_th'] = array(3,15,6,8,10,8,10,6,4);
            
        $result_company = $this->get_Specific_Info('tbl_company_info',array('Is_Exist'=>1),'Company_ID,Company_Name',2);
        $options_company[NULL] = 'Please Select';
        foreach($result_company as $row_company)
            $options_company[$row_company->Company_ID] = $row_company->Company_Name;
                
        $result_dept = $this->get_Specific_Info('tbl_dept_info',array('Is_Exist'=>1),'Dept_ID,Dept_Name',2);
        $options_dept[NULL] = $companyID == NULL ? 'Please Select' : 'All Dept';
        foreach($result_dept as $row_dept)
            $options_dept[$row_dept->Dept_ID] = $row_dept->Dept_Name;
        $result_usr = ($deptID == NULL) ? $this->get_Specific_Info('tbl_user_info',array('Is_Exist'=>1,'F_User_Permission_ID <> '=>1),'User_ID,Full_Name',2) : $this->db->distinct()->select('User_ID,tbl_user_info.Full_Name')->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')->get_where('tbl_user_info',array('tbl_user_info.Is_Exist'=>1,'F_User_Permission_ID <> '=>1,'F_Dept_ID' => $deptID))->result();
        $options_usr[NULL]= ($deptID == NULL) ? 'Please Select' : 'All User';
        foreach($result_usr as $row_usr)
            $options_usr[$row_usr->User_ID]=$row_usr->Full_Name;
                
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$options_company,$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:12em;"')),'Department' => array('dropdown' => array('dept_ID',$options_dept,$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:12em;"')),'User' => array('dropdown' => array('user_ID',$options_usr,$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:12em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')), 'Holiday' => array('dropdown' => array('tour',$options_tour,$this->input->post('tour') ? $this->input->post('tour') : NULL,'id="tour" style="width: 110px;"')) , ' ' => array('submit' => array('name' => 'assign','value' => 'Assign', 'onClick' => 'javascript:return confirm(\'Are you sure to assign?\')'),'label'=> '<div id="loader"></div>'));//,'' => array('submit' => array('name' => 'cancel','value' => 'Cancel','onClick' => 'javascript:return confirm(\'Are you sure to cancel?\')'))
        $data['footer'] =  array(img(array('title' => 'Export to excel','id' => 'download_button','src' => base_url('assets/images/export_excel10.png'))));
        return $data;
    }
        
    public function load_InactiveFormData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        $data['session'] = $this->session->userdata('logged_in');
        $Check_ID = $Absent_Count = $Leave_Count = $Offdays_Count = $Name = $Holiday_Count = $Tour_Count = $RS = $Dept = $Office_Time = $Location = $Supervisor = NULL;
            
        $columnDef = ($companyID == NULL) ? '{"targets": 0, "width":\'1%\'}' : '{"visible": true , "targets": 0, "width":\'0%\' },{"width": \'6%\', "targets": [1]},{"width": \'7.5%\', "targets": [2,4,7,8] },{"width": \'13%\', "targets": [5,6,11,12]},{"width": \'6%\', "targets": [3,9,10]}';
        $this->datatable->load_Script('grid',NULL,NULL,'"order": [[ 1, "asc" ]]');
        $js = 'waitForMsg();waitForMsgRecruit();';
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
           $js .= '$(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $js = "function change_dept() {
                    var dept_id = $('#dept').val();
                    $('#dept > option').remove(); 
                    $('#dept').append(\"<option value= ''>All Dept</option>\");";
                        
                    $js .= "var company_id = $('#company').val(); 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/administrator/get_Dept')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Dept_ID);
                                opt.text(obj.Dept_Name);
                                $('#dept').append(opt);
                            });
                            if(dept_id != '')
                            $('#dept').val(dept_id);
                        }
                            
                    });
                        
                }";
                    
            $js .= "function change_user() { 
                    $('#users > option').remove(); 
                    $('#users').append(\"<option value= ''>All User</option>\");
                    var company_id = $('#company').val();
                    var dept_id = $('#dept').val(); 
                    var loc_id = $('#location').val(); 
                    var sup_id = $('#supervisor').val();
                    $.ajax({
                        type: 'POST',
                        data: {'company_id':company_id,'dept_id':dept_id,'loc_id':loc_id,'sup_id':sup_id},
                        url: '".base_url('/home/get_User1')."', 
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.User_ID);
                                opt.text(obj.Full_Name);
                                $('#users').append(opt);
                            });
                        }
                            
                    });
                        
                }";
                    
        if($this->input->post('company_ID'))
        $js .= "change_user();change_dept();";
        $js .= "$('#company').change(change_dept);";
        $js .= "$('#company').change(change_user);";
        $js .= "$('#dept').change(change_user);";
        $js .= "$('#location').change(change_user);";
        $js .= "$('#supervisor').change(change_user);";
            
         $js .= '
            $( "#dialog-message" ).dialog({
                
            modal: true,
            autoOpen: false,
            buttons: {
                
              Close: function() {
                  
                $( this ).dialog( "close" );
              }
                  
            },
            width: "30%",
            });
            $( ".name" ).click(function() {
            $( "#dialog-message" ).dialog( "option", "title","Leave & Absent History");//+" - "+$(this).html()
                $( "#dialog-message" ).dialog( "open" );
                var id = $(this).attr("id");
                //$(".user_id").html();
                $.ajax({
                   type: "POST",
                   url: "'.base_url('administrator/leaveAbsentHistory').'",
                   data: {"user_id":id,"from":"'.$from.'","to":"'.$to.'"},
                   success: function(data) 
                       {  
                            $("#dialog-message").html(data);
                       }
                           
               });
                   
            });
            ';
                
        $this->javascript->ready($js);
            
            
        $this->load_Datepicker(array('from_date','to_date'));
            
            
            
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'administrator/user_Inactive_Form';
        $data['title'] = 'Inactive User Form';
        $data['width'] = '1300';
        $data['height'] = '480';
        $data['elements'] = array('users');
        $row_employee = $this->get_User_Inforamtion(array('tbl_user_info.Is_Exist' => '1','User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID));
          
          
                $array_where['tbl_user_info.Is_Exist'] = '1' ;
                $array_where['tbl_employee_profile.Is_Exist'] = '1' ;
                if($deptID == NULL){
                    $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    if($userID != NULL)
                        $array_where['User_ID'] = $userID;
                }else if($userID == NULL){
                    if($companyID != NULL)
                        $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    $array_where['F_Dept_ID'] = $deptID;
                }else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
                    
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
                $this->db->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
                if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor"){
                    $condition = 'tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID ';
                    if($companyID != NULL) $condition .= ' AND tbl_employee_profile.F_Company_ID = '.$companyID;
                    if($deptID != NULL) $condition .= ' AND tbl_employee_profile.F_Dept_ID = '.$deptID;
                    if($userID != NULL)  $condition .= ' AND tbl_user_info.User_ID = '.$userID;
                    $this->db->join('tbl_hierarchy_info',$condition,'left');
                    $this->db->where("(`Supervisor1_ID` = ".$data['session']['eid']." OR `Supervisor2_ID` = ".$data['session']['eid']." OR `Supervisor3_ID` = ".$data['session']['eid'].")");
                }
                if($data['session']['type'] == "Administrator" || $data['session']['type'] == "Super Administrator"){
                     $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
                }
                $array_where['tbl_employee_profile.Join_Date <'] =  mdate('%Y-%m-%d');
                $result = $this->db->distinct()->select('Dept_Name,Location_Name,tbl_user_info.F_Employee_ID,User_ID,tbl_user_info.Full_Name,tbl_employee_profile.Grace_Time,RS_ID,tbl_employee_profile.Join_Date')->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left')->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left')->get_where('tbl_user_info',$array_where)->result();//$this->get_Specific_Info('tbl_user_info',$array_where,'User_ID,Full_Name',2);
                    
                foreach($result as $row_user){
                        $record_supervisor = $this->db->distinct()->select('Full_Name')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => $row_user->F_Employee_ID))->row();
                        $Supervisor[] = isset($record_supervisor->Full_Name) ? $record_supervisor->Full_Name : NULL;
                        $Check_ID[] = form_checkbox('ID[]',$row_user->F_Employee_ID, FALSE);
                        $RS[] = $row_user->RS_ID;
                        $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                        $Location[] = $row_user->Location_Name;
                        $Dept[] = $row_user->Dept_Name;
//                        $weekcount=mdate('%w',  now());  
//                        $record = $this->administrator_model->get_User_Inforamtion(array('User_ID' => $row_user->User_ID,'Weekday' => $weekcount));
//                        $offdays = $this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
    
                        $row = $this->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => $this->systemDateFormatConverter(now())),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                        if($row){
                            if(is_null($row->Offday)){
                                $row_offday=$this->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                                $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                            }else{
                                $offdays =  explode(',',$row->Offday);
                            }
                        }else{
                            $row_offday=$this->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                            $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                        }
                            
                            
                        $offdays_date_array = $this->home_model->getoffdaysDate($offdays,$from,$to); 
//                       
                        $Offdays_Count [] = count($offdays_date_array);
                            
                            
                        $absent_array = $this->home_model->absentReport($row_user->User_ID, $offdays_date_array, $from, $to);    
//                       print_r($absent_array);
                        $Absent_Count[] = '<div class="name" id="'.$row_user->User_ID.'">'.(count($absent_array)>0 ? '<font color="blue">'.count($absent_array).'</font>':count($absent_array)).'</div>';
                            
                        $result_leave = $this->get_Specific_Info('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=> $row_user->User_ID,'DATE_FORMAT(tbl_leave_record.From_Date,"%Y")' => $from==NULL? mdate('%Y', now()) : mdate('%Y', strtotime($from))),'From_Date,To_Date',2);
                            
                            
                        //Leave Dates
                            
                        $query = $this->db->distinct()->select('From_Date,To_Date')->where('(From_Date BETWEEN "'.mdate('%Y-%m-01', strtotime($from)).'" AND "'.$to.'" OR To_Date BETWEEN "'.mdate('%Y-%m-01', strtotime($from)).'" AND "'.$to.'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$row_user->User_ID));
                            
                        if($query->num_rows() > 0){
                            $result = $query->result();
                        }else{
                            $result = $this->db->distinct()->select('From_Date,To_Date')->where('(To_Date BETWEEN "'.$from.'" AND "'.mdate('%Y-%m-31', strtotime($to)).'" OR From_Date  BETWEEN "'.$from.'" AND "'.mdate('%Y-%m-31', strtotime($to)).'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$row_user->User_ID))->result();
                        }
                        $leave_array = array();
                        foreach($result as $row){
                            $leave_date_array = $this->administrator_model->showDates($row->From_Date,$row->To_Date);
                            $leave_array = array_merge($leave_array,$leave_date_array);
                        }
                        $Leave_Count[] = count($leave_array); 
                            
                        //Holiday Dates
                        $this->db->select('From_Date,To_Date');
                        $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$row_user->F_Employee_ID.'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'"');
                        $result = $query->result();
                        $holiday_date_array = array();
                        foreach($result as $row){
                            $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
                            $holiday_date_array = array_merge($holiday_date_array,$temp_date_array);
                        }
                        $Holiday_Count[] = count($holiday_date_array);
                            
                       //Tour Dates
                        $this->db->select('From_Date,To_Date');
                        $query = $this->db->get_where('tbl_tour_info','Is_Exist = 1 AND FIND_IN_SET ("'.$row_user->F_Employee_ID.'",Employee)  AND (From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'" OR To_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'")');
                        $result = $query->result();
                        $tour_date_array = array();
                        foreach($result as $row){
                            $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
                            $tour_date_array = array_merge($tour_date_array,$temp_date_array);
                        }
                        $Tour_Count[] = count($tour_date_array);
                            
                }
                    
                    
        $data['field'] = array(form_checkbox(array('id'=>'select_all')) => $Check_ID,'Name' => $Name,'Employee ID' => $RS,'Location' => $Location, 'Department' => $Dept,'Supervisor' => $Supervisor,'Absent' => $Absent_Count,'Leave' => $Leave_Count,'Offdays' => $Offdays_Count,'Holiday'=>$Holiday_Count,'Tour'=>$Tour_Count);//'Leave' => $leave,
            
            
        $row = $this->db->distinct()->select('All_Report')->from('tbl_user_type')->join('tbl_login_info', 'tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID')->where(array('tbl_login_info.Is_Exist' => 1, 'tbl_user_type.Is_Exist' => 1, 'tbl_login_info.F_User_ID' => $data['session']['id']))->limit(1)->get()->row();
            
        $data['other_fields']= array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',  strtotime("-10 day",now()))),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')),'  ' => array('submit' => array('name'=>'inactive','value'=>'Inactive')));
        $data['html'] = '
            <div id="dialog-message" title="Leave & Absent History">
        </div>';
    return $data;
        
    }
        
    public function load_InactiveUser_ValidationConfig(){
       $config = array(
           array(
               'field' => 'company_ID', 
               'label' => 'Company ID', 
               'rules' => 'trim|xss_clean'
           ),
           array(
               'field' => 'dept_ID', 
               'label' => 'Department ID', 
               'rules' => 'trim|xss_clean'
           ),
           array(
               'field' => 'user_ID', 
               'label' => 'User ID', 
               'rules' => 'trim|xss_clean'
           ),
           array(
               'field' => 'from_date', 
               'label' => 'From', 
               'rules' => 'trim|required|xss_clean'
           ),
           array(
               'field' => 'to_date', 
               'label' => 'To', 
               'rules' => 'trim|required|xss_clean'
           )
       );
       return $config;
    }
        
    /*Force Present*/
    public function load_ForcePresentEntryFromInfo(){
        $this->load_Datepicker(array('date'));
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '620';
        $data['height'] = '220';
        $data['title']= 'Make Present';
            
        $data['form']='administrator/force_Present'; 
        $options_hr = array_combine($this->numberArray(0, 23, 1),$this->numberArray(0, 23, 1));
        $options_min = array_combine($this->numberArray(0, 59, 1),$this->numberArray(0, 59, 1));
//        $data['hidden']= $id>0 ? array('id' => $row->Company_ID) : NULL;
        $data['field']=array(
            'Employee' =>array('dropdown' => array('employee',$this->globals->getOptionsUser(),$this->input->post('employee') ? $this->input->post('employee'): NULL,'id="employee" style="width:9em;"')),
            'Date' => array('input' => array('name'=>'date','value'=> set_value('date',mdate('%d-%m-%Y',now())),'id'=>'date','size'=>12)),
            'fieldset' => 'Office Time',
            'Hour' => array('dropdown' => array('in_hr',$options_hr,$this->input->post('in_hr') ? $this->input->post('in_hr') : '10')),
            'Min' => array('dropdown' => array('in_min',$options_min,$this->input->post('in_min') ? $this->input->post('in_min') : '00')),
            'close_fieldset_' => ''
            );
//        $data['selected']=array($id>0?$row->Is_Exist : 1);
//        $data['select']=array('Visibility' => array('1' => 'Published', '0' => 'Unpublished'));
        $data['submit']=array('name' => 'insert','value' => 'Insert');
        $data['elements'] = array('employee');
        return $data;
    }
        
    public function load_ForcePresent_ValidationConfig(){
        $config = array(
            array(
                'field'   => 'employee', 
                'label'   => 'Employee Name', 
                'rules'   => 'trim|required|xss_clean'
            ),
            array(
                'field'   => 'date', 
                'label'   => 'Date', 
                'rules'   => 'trim|required|xss_clean'
            )
        );
        return $config;
    }
        
    public function execute_ForcePresent(){
        $employee_id = $this->get_Specific_Info('tbl_user_info', array('User_ID' => $this->input->post('employee')), 'F_Employee_ID', 1);
        $record=$this->db->distinct()->select('Sun,Mon,Tue,Wed,Thu,Fri,Sat')->get_where('tbl_work_days',array('F_Employee_ID' =>  $employee_id->F_Employee_ID))->row();
        $offdays=$this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
        $weekcount= mdate('%w',  strtotime(now()));
        $row_ot = $this->db->distinct()->select('In')->get_where('tbl_office_time', array('F_Employee_ID' => $employee_id->F_Employee_ID, 'Weekday'=> $weekcount))->row();
            
        $set = array(
            'F_User_ID' => $this->input->post('employee'),
            'Date' => $this->systemDateFormatConverter($this->input->post('date')),
            'In_Time' => $this->input->post('in_hr').':'.$this->input->post('in_min'),
            'Offday' => trim(implode(',', $offdays)),
            'Office_In' => $row_ot->In,
            'Force_Present' => 1
        );
        if($this->input->post('insert'))
           $this->db->insert('tbl_login_record', $set);
               
    }
    /*Force Present*/
        
    public function load_EmployeeLeaveListViewInfo($F_Company_ID=1){
        
        $user = $this->session->userdata('logged_in');
        $columnDef='{"orderable": false , "width": \'1%\', "targets": 0 },{"width": \'15%\', "targets": [1,4] },{"width": \'10%\', "targets": [3] },{"width": \'8%\', "targets": [2] },{"width": \'12%\', "targets": [5] }';
        $this->datatable->load_Script('grid',NULL,$columnDef);
            
        $this->javascript->compile();
        $encryption = new Encryption;
        $data['menu'] = $this->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'administrator/leave_Calculator';
        $data['title'] = 'Employee List';
        $data['width'] = '1300';
        $data['height'] = '480';
        $this->db->where(array('tbl_employee_profile.Is_Exist' => 1,'tbl_employee_profile.F_Company_ID' => $F_Company_ID));
        $result = $this->get_Employee_Information(NULL);
        foreach($result as $row){
            $Check_ID[] = form_hidden('ID[]',$row->Employee_ID);
            $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
                
            $Employee_ID[] = $row->RS_ID;
            $Join_Date[] = $this->systemDateFormatConverter($row->Join_Date);
            $Suggested_Leave[] = (mdate('%Y',strtotime($row->Join_Date))== mdate('%Y',now())) ? round(dateDifference($this->systemDateFormatConverter($row->Join_Date),mdate('%Y-12-31',now()))*(.115)) :  $row->Leave;
            $Leave[] = form_input('Leave[]',(mdate('%Y',strtotime($row->Join_Date))== mdate('%Y',now())) ? round(dateDifference($this->systemDateFormatConverter($row->Join_Date),mdate('%Y-12-31',now()))*(.115)) :  $row->Leave);
            }
        $data['field'] = array('&nbsp;' => $Check_ID,'Name' => $Full_Name,'Employee ID' => $Employee_ID,'Join Date' => $Join_Date,'Suggested Leave' => $Suggested_Leave,'Leave' => $Leave);
        $data['other_fields']=array('' => array('submit' => array('name' => 'apply','value' => 'Apply','onClick' => 'javascript:return confirm(\'Are you sure to Apply?\')')));//'Filter' => array('input' => array('id' => 'searchInput','value' => 'Type to filter','placeholder' => 'Type to filter')),
        $result_com = $this->get_Specific_Info('tbl_company_info', array('Is_Exist' => 1), 'Company_Name',2);
        foreach($result_com as $row_com)
            $company_array[] = $row_com->Company_Name;
        $result_dept = $this->get_Specific_Info('tbl_dept_info', array('Is_Exist' => 1), 'Dept_Name',2);
        foreach($result_dept as $row_dept)
            $dept_array[] = $row_dept->Dept_Name;   
        return $data;
    }
}
?>