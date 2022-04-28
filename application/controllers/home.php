<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of home
 *
 * @author Nasirul Akbar Khan
 */
class home extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->model('home_model');
        $this->load->helper('json');
    }
    
    public function index(){
        if($this->login_model->is_Logged_In()){
            $data = $this->home_model->load_ViewData();
            $this->load->view('home',$data);
        }else redirect('login','refresh');
    }
    
    public function destroy_FirstTimeLogged(){
        $this->session->unset_userdata('first_time_logged');
    }
    
    public function insert_LateReason(){
        $this->home_model->update_LoginRemarks();
        $this->destroy_FirstTimeLogged();
    }
    
    public function update_Remarks(){
         $input = filter_input_array(INPUT_POST);
//         $input['csrf_test_name'] = $this->security->get_csrf_hash();
         if ($input['action'] === 'edit'){ 
            $array = explode(',',$input['ID']);
            
//         $row = $this->db->distinct()->select('tbl_user_info.User_ID')->join('tbl_user_info','tbl_employee_profile.Employee_ID = tbl_user_info.F_employee_ID','inner')->get_where('tbl_employee_profile',array('Rs_ID' => $input['RSID']))->row();
//            $this->db->update('tbl_login_record',array('Remarks' => $input['Remarks']),array('Login_Record_ID' => $input['ID']));//array('Date' => mdate('%Y-%m-%d', strtotime ($input['Date'])),'F_User_ID' => $row->User_ID)
         
            $row_admin = $this->administrator_model->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => $array[0], 'Date' => $array[1]),'Admin_Remarks,User_Remarks',1); //$row->User_ID  mdate('%Y-%m-%d', strtotime ($input['Date']))
            if($row_admin)
               $this->db->update('tbl_login_remarks',array('User_Remarks' => $input['Remarks'],'Admin_Remarks' => $input['Admin_Remarks']),array('F_User_ID' => $array[0], 'Date' => $array[1]));
            else
               $this->db->insert('tbl_login_remarks',array('User_Remarks' => $input['Remarks'],'Admin_Remarks' => $input['Admin_Remarks'],'Date' => $array[1] ,'F_User_ID' => $array[0])); 
         }
         $this->output->set_output(json_encode($input));
    }

    public function get_User($deptID){
        get_User_Json($deptID);
    }
    
    public function get_User1(){
        $companyID = $this->input->post('company_id');
        $deptID = $this->input->post('dept_id');
        $locationID = $this->input->post('loc_id');
        $supervisorID = $this->input->post('sup_id');
        get_User_Json1($companyID,$deptID,$locationID,$supervisorID);
    }
    
    public function get_Backup_On_Date(){
        $from = $this->input->post('from_date');
        get_Backup_Json($from);
    }
   
    public function get_User_ByCompany($companyID){
        get_UserByCompany_Json($companyID);
    }
    
    public function get_User_ByLocation($locationID){
        get_UserByLocation_Json($locationID);
    }
    
    public function get_SuperVisor_ByCompany($companyID){
        get_SuperVisorByCompany_Json($companyID);
    }


    public function report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_ReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function late_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_LateReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function leave_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_LeaveReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
     public function holiday_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_BasicReport_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
//                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
//                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_holidayReportData($companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function absent_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_absentReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function staffUnderSV_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_SUSVReport_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $companyID = $supID = $locID = NULL;
            else{
                $companyID = $this->input->post('company_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_StaffUnderSVReportData($companyID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function basicInfo_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_BasicReport_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $companyID = $deptID = $userID  = $supID = $locID = NULL;
            else{
     
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_BasicInfoReportData($companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function idCardInfo_report(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            if($user['type'] == "Graphics Designer" || $user['eid'] == '508'){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->home_model->load_IDCardReport_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE)
                    $employeeID  = NULL;
                else{
                    $employeeID = $this->input->post('employeeids'); 
                }
                $data = $this->home_model->load_IDCardInfoReportData($employeeID);
                $this->load->view('grid',$data);
            }else redirect('login','refresh');
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function inactiveUser_report(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_InactiveUserReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }


    public function unread_leave_request(){
        $user = $this->session->userdata('logged_in');
        $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Preapproval',1);
        $this->db->from('tbl_leave_record');
        if($user['type']=="Supervisor" || $user['type']=="Co-Supervisor" || $row->Leave_Preapproval == 1){
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','inner');
//        $this->db->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','right');
//        $this->db->join('tbl_location_info','tbl_location_info.Location_ID = tbl_employee_profile.F_Location_ID','inner');
//        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employee_profile.F_Company_ID','inner');
//        $this->db->join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','inner');
//        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','inner');
        $this->db->join('tbl_hierarchy_info','tbl_user_info.F_Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner');
        $this->db->where("Status = 'unread' AND S_Status = 'unread' AND tbl_leave_record.Is_Exist = '1' AND  tbl_leave_record.Is_Processed = '0' AND tbl_leave_record.Is_Void = '0' AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");//AND tbl_leave_info.Is_Exist = '1'
        }else{
            $this->db->where(array('Is_Exist' => 1, 'Status' =>'unread'));
        }
        $this->output->set_output($this->db->count_all_results());
//        $this->output->set_output('<input type="hidden" name="csrf_cookie_name" value="'.$this->security->get_csrf_hash().'"/>');
//        $this->output->set_content_type('application/json');
//        $this->output->set_output(json_encode(array('data' => $this->db->count_all_results(), 'csrf' => $csrf)));//.form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash())
    }
    
    public function processed_leave_request($flag){
        $user = $this->session->userdata('logged_in');
        $this->db->from('tbl_leave_record');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','inner');
//        $this->db->join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','inner');
        $this->db->join('tbl_hierarchy_info','tbl_user_info.F_Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner');
        if($flag == 1){
            $this->db->where("Status = 'read' AND S_Status = 'read' AND tbl_leave_record.Is_Exist = '1' AND  tbl_leave_record.Is_Processed = '1' AND tbl_leave_record.Is_Void = '0' AND tbl_leave_record.SV_Seen = '0' AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");//AND tbl_leave_info.Is_Exist = '1' 
        }else{
             $this->db->where("Status = 'read' AND S_Status = 'read' AND tbl_leave_record.Is_Exist = '1' AND  tbl_leave_record.Is_Processed = '0' AND tbl_leave_record.Is_Void = '1' AND tbl_leave_record.SV_Seen = '0' AND (tbl_hierarchy_info.Supervisor1_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor2_ID = '".$user['eid']."' OR tbl_hierarchy_info.Supervisor3_ID = '".$user['eid']."')");//AND tbl_leave_info.Is_Exist = '1' 
        }
        $this->output->set_output($this->db->count_all_results());
    }
    
    public function unread_recruitment_request(){
        $user = $this->session->userdata('logged_in');
        $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Preapproval',1);
        $this->db->from('tbl_recruitment_record');
        if($user['type']=="Supervisor" || $user['type']=="Co-Supervisor" ){//|| $row->Recruitment_Preapproval == 1
        $this->db->where("S_Status = 'unread'  AND tbl_recruitment_record.Is_Exist = '1' AND F_User_ID = '".$user['id']."'"); //Status = 'unread' AND 
        }else{
            $this->db->where(array('Is_Exist' => 1, 'Status' =>'unread'));
        }
        $this->output->set_output($this->db->count_all_results());
//        $this->output->enable_profiler(TRUE);
    }

    public function leave_application(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_LeaveApply_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
            $data = $this->home_model->load_LeaveApplyFromInfo();
            else{
                $this->home_model->send_LeaveApplication();
                redirect('home/leave_status','refresh');
            }
            $this->load->view('form',$data);
        }else redirect('login','refresh'); 
    }
    
    public function leave_status(){
        if($this->login_model->is_Logged_In()){
            $data = $this->home_model->load_LeaveStatusData();
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
    }
    
    public function leaveBackup_List(){
        if($this->login_model->is_Logged_In()){
            $data = $this->home_model->load_LeaveBackupListData();
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function leave_List(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->home_model->load_Report_ValidationConfig();
            $this->form_validation->set_rules($config);
            if($this->form_validation->run() == FALSE)
                $from = $to = $companyID = $deptID = $userID = $supID = $locID = NULL;
            else{
                $from = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
                $to = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
                $companyID = $this->input->post('company_ID');
                $deptID = $this->input->post('dept_ID');
                $userID = $this->input->post('user_ID');
                $supID = $this->input->post('sup_ID');
                $locID = $this->input->post('loc_ID');
            }
            $data = $this->home_model->load_LeaveListData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
    }
    
    public function recruitment_request(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->home_model->load_RecruitmentRequest_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE)
                $data = $this->home_model->load_RecruitmentRequestFromInfo();
                else{
                    $this->home_model->send_RecruitmentRequest();
                    redirect('home/recruitment_status','refresh');
                }
                $this->load->view('form',$data);
            }else redirect('login','refresh');
        }else redirect('login','refresh');       
    }
    
    public function recruitment_status(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->form_validation->set_message('required', 'You must check atleast one row.');
            $this->form_validation->set_rules('ID', 'User', 'required|xss_clean');
            if($this->form_validation->run() == FALSE){
                $data = $this->home_model->load_RecruitmentRequestStatusData();
                $this->load->view('grid',$data);
            }else{
                if($this->input->post('seen')){
                    $recruiment_id = $this->input->post('ID');
                    foreach($recruiment_id as $id)
                    $this->db->update('tbl_recruitment_record', array('S_Status' => 'read'), array('Recruitment_Request_ID' => $id));
                }
                elseif($this->input->post('cancel')){
                    if(count($this->input->post('ID')<=1))
                        $id = $this->input->post('ID');
                        $this->db->update('tbl_recruitment_record', array('Is_Cancel' => '1'), array('Recruitment_Request_ID' => $id[0]));
                    $this->output->enable_profiler(TRUE);
                }
                redirect('home/recruitment_status','refresh');
            }
          
            
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function cancel_recruitment_request(){
        $array_data = array('Is_Cancel' => '1');
        $this->db->update('tbl_recruitment_record',$array_data,array('Recruitment_Request_ID' => $this->input->post('cacel_request_id')));
    }

    public function logout(){
        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('first_time_logged');
        $this->session->sess_destroy();
        redirect('login','refresh');
    }
    
    public function signout(){
        $this->login_model->store_LogoutRecord_to_DB();
        redirect('home','refresh');
    }
    
    public function date(){
        $now=mdate('%Y-%m-%d',now());
        $this->home_model->showDates('2013-11-1',$now);
    }
    

    public function leaveApplyValidation($remaining){
       return $this->home_model->is_LeaveValid($remaining);
    }
    
    public function leaveExistValidation(){
         return $this->home_model->is_LeaveExist();  
    }
    
    public function is_greaterFromDate(){
         return $this->home_model->is_greaterFromDate();  
    }


    public function recruitmentJoinDateValidation($date){
       return $this->home_model->is_Recruitment_JoinDateValid($date);
    }
    
    public function update_Password(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'User_Manager,Leave_Manager',1);
            if($user['type']!="Super Administrator" || $user['type']!="Administrator"){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->home_model->load_PasswordUpdate_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data = $this->home_model->load_PasswordUpdateFromInfo(); 
                    $this->load->view('form',$data);
                }else{
                    $this->home_model->update_Password_Info();
                    redirect('home','refresh');
                } 
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function my_Profile(){
         if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $js = 'waitForMsg();';
            if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
            $this->javascript->ready($js);
            $encryption = new Encryption;
            $id=$encryption->encrypt($user['eid']);
            $data = $this->administrator_model->load_EmployeeEntryForm_Data($id);
            $this->load->view('form',$data);
      
        }else redirect('login','refresh');   
    }
    
    public function countLeaveDate(){
        $from = $this->input->post('from_date');
        $to = $this->input->post('to_date');
        if($from == NULL || $to == NULL){
            echo 0;
        }else{
            $user = $this->session->userdata('logged_in');
            $record=$this->db->distinct()->select('Sun,Mon,Tue,Wed,Thu,Fri,Sat')->get_where('tbl_work_days',array('F_Employee_ID' =>  $user['eid']))->row();
            $offdays=$this->home_model->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
            //Offday Dates
            $offdays_date_array = array();
            $start = new DateTime(mdate('%Y-%m-%d',strtotime($from)));
            $end   = new DateTime(mdate('%Y-%m-%d',strtotime($to)));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $dt)
            {   foreach($offdays as $days){
                    if($dt->format('w') == $days)
                        array_push ($offdays_date_array, $dt->format('Y-m-d'));
                }
            }
            //Holiday Dates
            $this->db->select('From_Date,To_Date');
            $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$user['eid'].'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'"');
            $result = $query->result();
            $holiday_date_array = array();
            foreach($result as $row){
                $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
                $holiday_date_array = array_merge($holiday_date_array,$temp_date_array);
            }
     
            
            $date_array = array();
            $temp_array = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_array);
            $date_array = array_diff($date_array,$offdays_date_array,$holiday_date_array);
   
            echo  count($date_array);
        }
    }
    
    public function isRecruitment_Accomplished(){
       echo ($this->home_model->is_Recruitment_Accomplished()) ? 0 : 1;  
    }
    
    public function half_time(){
        $this->home_model->test_half_time();
    }
    
    
}

?>
