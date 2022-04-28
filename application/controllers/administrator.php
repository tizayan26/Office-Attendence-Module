<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of administrator
 *
 * @author Nasirul Akbar Khan
 */
class administrator extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('administrator_model');
        $this->load->model('home_model');
    }
    
    public function get_Dept($companyID){
        get_Dept_Json($companyID);
    }
    
    public function get_AllDept($companyID){
        get_All_Dept_Json($companyID);
    }
    
    public function get_Employee($employeeID){
        get_Employee_Json($employeeID);
    }
    
    public function get_Designation($deptID){
        get_DesignationByDept_Json($deptID);
    }

    public function index(){
        redirect('login','refresh');
    }
    
    public function user_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'User_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
//                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->User_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_User_ValidationConfig($id);
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_UserEntryFromInfo($id); 
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_User_Info();//$id>0 ? $this->administrator_model->update_User_Info() : 
                    redirect('administrator/user_List','refresh');
                } 
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
     public function user_List($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'User_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->User_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one user.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_UserListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/user_Entry/'.$this->input->post('ID'),'refresh');
                    if($this->input->post('delete'))
                        redirect('administrator/delete_User_Entry/'.$this->input->post('ID'),'refresh');
                }
            }   
        }else
            redirect('login','refresh');
    }
    
    public function delete_User_Entry($id){
        $encryption = new Encryption;
        $id = $encryption->decrypt($id);
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            if($user['type']=="Super Administrator" || $user['type']=="Administrator"){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_login_info',array('F_User_ID'=>$id));
                    $this->administrator_model->delete_Specific_Info('tbl_user_info',array('User_ID'=>$id));
                    $this->administrator_model->delete_Specific_Info('tbl_work_days',array('F_User_ID'=>$id));
                    redirect('administrator/user_List','refresh');
                }
            }else
                 redirect('login','refresh');            
        }else
            redirect('login','refresh');
    }
    
    public function dept_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Department==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_InsertDept_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_DeptEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Dept_Info();
                    redirect('administrator/dept_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function dept_List($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Department==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one dept.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_DeptListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/dept_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Dept_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Dept_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department',1);
            if($user['type']=="Super Administrator" || $row->Department){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_dept_info',array('Dept_ID'=>$id));
                    redirect('administrator/dept_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function userRole_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            if($user['type']=="Super Administrator"){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserRole_ValidationCofig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data = $this->administrator_model->load_UserRoleEntryForm($id);
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_User_Role();
                    redirect('administrator/userRole_List','refresh');
                }
            }else redirect('administrator','refresh');
        }else redirect('administrator','refresh');
    }
    
    public function userRole_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            if($user['type']=="Super Administrator"){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one dept.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_UserRoleListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/userRole_Entry/'.$this->input->post('ID'),'refresh');
                    if($this->input->post('delete'))
                        redirect('administrator/delete_userRole_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_userRole_Entry($id){
       if($this->login_model->is_Logged_In()){
           $user = $this->session->userdata('logged_in');
           if($user['type']=="Super Administrator"){
               if((int)$id > 0){
                   $this->administrator_model->delete_Specific_Info('tbl_user_type',array('User_Permission_ID'=>$id));
                   redirect('administrator/userRole_List','refresh');
               }
           }else
               redirect('login','refresh');
       }else
           redirect('login','refresh');
    }
    
    public function leaveType_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $row->Leave_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_LeaveType_ValidationCofig();
                $this->form_validation->set_rules($config);

                if($this->form_validation->run() == FALSE){
                    $data = $this->administrator_model->load_LeaveTypeEntryForm($id);
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Leave_Type();
                    redirect('administrator/leaveType_List','refresh');
                }
            }else redirect('administrator','refresh');
        }else redirect('administrator','refresh');
    }
    
    public function leaveType_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Manager',1);
            if($user['type']=="Super Administrator" || $row->Leave_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeaveTypeListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/leaveType_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_leaveType_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_leaveType_Entry($id){
       if($this->login_model->is_Logged_In()){
           $user = $this->session->userdata('logged_in');
           $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Manager',1);
           if($user['type']=="Super Administrator" || $row->Leave_Manager==1){
               if((int)$id > 0){
                   $this->administrator_model->delete_Specific_Info('tbl_leave_info',array('Leave_Type_ID'=>$id));
                   redirect('administrator/leaveType_List','refresh');
               }
           }else
               redirect('login','refresh');
       }else
           redirect('login','refresh');
    }
    
    public function leaveRequest_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = array(
                            array(
                                'field'   => 'ID', 
                                'label'   => 'User', 
                                'rules'   => 'required|xss_clean'
                            )
                    );
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeaveRequestListData(1);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision();
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
     public function allLeave_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = array(
                            array(
                                'field'   => 'ID', 
                                'label'   => 'User', 
                                'rules'   => 'required|xss_clean'
                            )
                    );
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeaveRequestListData(0);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision();
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function check_Recomendation(){
        $user = $this->session->userdata('logged_in');
        $row=$this->administrator_model->get_Specific_Info('tbl_leave_record', array('Is_Exist' => '1','Is_Processed' => '0','Is_Void' => '0','Leave_ID' => $this->input->post('ID')),'From_Date,Recommend,Deny');
        $recommend_array = explode(',', $row->Recommend);
        foreach($recommend_array as $recommend){
            $tmp = explode('#', $recommend);
            $recommend_array[] = $tmp[0];
        }
        $deny_array = explode(',', $row->Deny);
        foreach($deny_array as $deny){
           $tmp = explode('#', $deny);
           $deny_array[] = $tmp[0];
        }

        if(in_array($user['id'],$recommend_array) || in_array($user['id'],$deny_array)){
            $this->form_validation->set_message('check_Recomendation','You have already given your opinion!');
            return FALSE;
        }

        if(mdate('%Y-%m-%d') == mdate('%Y-%m-%d',strtotime($row->From_Date))){
            $this->form_validation->set_message('check_Recomendation','You could not allow to approve on From date!');
            return FALSE;
        }
        else
            return TRUE;
    }
    
    
    public function leavePreApproval_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Preapproval,Leave_Manager',1);
//            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1 || $row->Leave_Preapproval == 1){
//                $this->javascript->ready('waitForMsg();');
//                $this->javascript->compile();
//            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
//                $config=$this->administrator_model->load_UserList_ValidationConfig();
              
                $this->form_validation->set_message('required', 'You must check atleast one row.');
//                $this->form_validation->set_rules($config);
                $this->form_validation->set_rules('ID', 'User', 'trim|required|xss_clean|callback_check_Recomendation');
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeavePreApprovalListData(1);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function supAllLeave_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Preapproval,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1 || $row->Leave_Preapproval == 1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
//                $config=$this->administrator_model->load_UserList_ValidationConfig();
              
                $this->form_validation->set_message('required', 'You must check atleast one row.');
//                $this->form_validation->set_rules($config);
                $this->form_validation->set_rules('ID', 'User', 'trim|required|xss_clean|callback_check_Recomendation');
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeavePreApprovalListData(0);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function approvedLeave_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Preapproval,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1 || $row->Leave_Preapproval == 1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
//                $config=$this->administrator_model->load_UserList_ValidationConfig();
              
                $this->form_validation->set_message('required', 'You must check atleast one row.');
//                $this->form_validation->set_rules($config);
                $this->form_validation->set_rules('ID', 'User', 'required|xss_clean');
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeavePreApprovalListData(2);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision($user['id'],1);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    public function deniedLeave_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Leave_Preapproval,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1 || $row->Leave_Preapproval == 1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
//                $config=$this->administrator_model->load_UserList_ValidationConfig();
              
                $this->form_validation->set_message('required', 'You must check atleast one row.');
//                $this->form_validation->set_rules($config);
                $this->form_validation->set_rules('ID', 'User', 'required|xss_clean');
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LeavePreApprovalListData(3);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_LeaveDecision($user['id'],2);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function recruitmentRequest_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_RecruitmentRequestListData();
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_RecruitmentDecision();
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function recruitmentPreApproval_List(){
    if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Preapproval,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_RecruitmentRequestListData(1);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_RecruitmentDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function allRecruitment_List(){
    if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Preapproval,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Preapproval == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_RecruitmentRequestListData(2);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_RecruitmentDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function recruitmentCancelRequest_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager == 1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_RecruitmentRequestListData(3);
                    $this->load->view('grid',$data);
                }else
                    $this->administrator_model->execute_RecruitmentDecision();
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function employeeHierarchy_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Hierarchy,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Hierarchy==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_EmployeeHierarchy_ValidationConfig($id);
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeHierarchyEntryForm($id);
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Hierarchy_Info();
                    redirect('administrator/employeeHierarchy_List/','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function employeeHierarchy_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Hierarchy,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator"  || $row->Hierarchy==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one Employee.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeHierarchyListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/employeeHierarchy_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_employeeHierarchy_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_employeeHierarchy_Entry($id){
        if($this->login_model->is_Logged_In()){
           $user = $this->session->userdata('logged_in');
           $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Hierarchy',1);
           if($user['type']=="Super Administrator" || $row->Hierarchy==1){
               if((int)$id > 0){
                   $this->administrator_model->delete_Specific_Info('tbl_hierarchy_info',array('H_ID'=>$id));
                   redirect('administrator/employeeHierarchy_List','refresh');
               }
           }else
               redirect('login','refresh');
       }else
           redirect('login','refresh');
    }
    
    public function holiday_Entry($id=0) {
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Holiday==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_HolidayEntry_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){     
                    $data=$this->administrator_model->load_HolidayEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Holiday_Info();
                    redirect('administrator/holiday_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh'); 
    }
    
    public function holiday_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Holiday==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeHolidayListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/holiday_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Holiday_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Holiday_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday',1);
            if($user['type']=="Super Administrator" || $row->Holiday==1){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_holiday_info',array('Holiday_ID'=>$id));
                    redirect('administrator/holiday_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }

    public function assign_Holiday(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
               
                $config= array(
                    array(
                        'field'   => 'ID', 
                        'label'   => 'User', 
                        'rules'   => 'xss_clean' //($this->input->post('assign'))? 'required|xss_clean' :
                    )
                );

                $this->form_validation->set_message('required', 'You must check atleast one employee.');
                $this->form_validation->set_rules($config);
                    $companyID = $this->input->post('company_ID');
                    $deptID = $this->input->post('dept_ID');
                    $userID = $this->input->post('user_ID');
                    $locID = $this->input->post('loc_ID');
                    if($this->input->post('search')){
                        $this->session->set_userdata('company_ID',$companyID);
                        $this->session->set_userdata('dept_ID',$deptID);
                        $this->session->set_userdata('user_ID',$userID);
                        $this->session->set_userdata('loc_ID',$locID);
                    }
                if($this->form_validation->run() == FALSE){
                    $companyID = $this->session->userdata('company_ID') ? $this->session->userdata('company_ID') : $this->input->post('company_ID');
                    $deptID = $this->session->userdata('dept_ID') ? $this->session->userdata('dept_ID') : $this->input->post('dept_ID');
                    $userID = $this->session->userdata('user_ID') ? $this->session->userdata('user_ID') : $this->input->post('user_ID');
                    $locID = $this->session->userdata('loc_ID') ? $this->session->userdata('loc_ID') : $this->input->post('loc_ID');
                }else{
                    if($this->input->post('assign')){
                 
                        if($companyID!=NULL)
                            $where['F_Company_ID'] = $companyID;
                        if($deptID!=NULL)
                            $where['F_Dept_ID'] = $deptID;
                        if($locID!=NULL)
                            $where['F_Location_ID'] = $locID;
                        $result1 = $this->db->select('Employee_ID')->get_where('tbl_employee_profile',$where)->result();
                        $post = isset($_POST['ID']) ? $this->input->post('ID') : array();
                        foreach($result1 as $row1)
                            $array_id_test[] =  $row1->Employee_ID;
                        $array_id_test = array_diff($array_id_test,$post);
                    
                        $row = $this->db->distinct()->select('Employee')->get_where('tbl_holiday_info',array('Holiday_ID' => $this->input->post('holiday')))->row();
                        $employee_array = explode(',',$row->Employee);
//                        if(!empty(array_intersect($employee_array, $post)))
//                                echo 'test';
//                        else
//                            echo 'test1';
//                        exit();
                        if($row->Employee!=NULL){
                            foreach($post as $p){
                                if(!in_array($p, $employee_array)){
                                    array_push($employee_array,$p);
                                }
                            }
                            $employee = (implode(',',array_diff($employee_array, $array_id_test)));
                        }else{
                            $employee = implode(',',$this->input->post('ID')); 
                        }
                        
                        $this->session->set_userdata('holiday',$this->input->post('holiday'));
                        
                        $this->db->where(array('Holiday_ID' => $this->input->post('holiday')));
                        $this->db->update('tbl_holiday_info', array('Employee' => $employee ));
                        redirect('administrator/assign_Holiday','refresh');
                    }
//                    if($this->input->post('cancel'))
//                        redirect('administrator/update_Holiday/'.$this->input->post('ID'),'refresh');
                }
                $data=$this->administrator_model->load_EmployeeListFor_AssignHoliday($companyID,$deptID,$userID,$locID);
                $this->load->view('grid',$data);
            }
            else redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function get_Holiday_Employee(){
        echo get_Holiday_Employee_Json($this->input->post('holiday'));
    }
    public function company_Entry($id=0) {
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Company==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_ComapanyEntry_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){     
                    $data=$this->administrator_model->load_CompanyEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Company_Info();
                    redirect('administrator/company_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh'); 
    }
    
    public function company_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Company==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one company.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_CompanyListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/company_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Company_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function location_Entry($id=0) {
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Location==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_LocationEntry_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){     
                    $data=$this->administrator_model->load_LocationEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Location_Info();
                    redirect('administrator/location_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh'); 
    }
    
    public function location_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Location==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one company.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_LocationListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/location_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Location_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Location_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company',1);
            if($user['type']=="Super Administrator" || $row->Location==1){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_location_info',array('Location_ID'=>$id));
                    redirect('administrator/location_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Company_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company',1);
            if($user['type']=="Super Administrator" || $row->Company==1){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_company_info',array('Company_ID'=>$id));
                    redirect('administrator/company_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function candidate_Entry($id=0){
         if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_Candidate_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_CandidateEntryFromInfo($id); 
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->upload_CV('file','./uploaded_cv/');
                    $this->administrator_model->insertUpdate_Candidate_Info();
                    redirect('administrator/candidate_List','refresh');
                } 
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function candidate_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one company.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_CandidateListViewInfo(1);
                    $this->load->view('grid',$data);
                }else
                     $this->administrator_model->execute_CadidateDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function candidate_Selected_List(){
          if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Recruitment_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Recruitment_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one company.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_CandidateListViewInfo();
                    $this->load->view('grid',$data);
                }else
                     $this->administrator_model->execute_CadidateDecision($user['id']);
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
   
    public  function test(){
        $this->load->helper('phpword');
        word_generate();
    }
    
    public function employee_Entry($id=0){
         if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_EmployeeEntry_ValidationConfig($id);
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE)
                $data = $this->administrator_model->load_EmployeeEntryForm_Data($id);
                else{
                    if($_FILES){
                        $path='./uploaded_images/';
                        $this->administrator_model->upload_Image('file',$path,80,100,array(20,100));
                        $this->administrator_model->upload_CV('adoc','./uploaded_document/');
                    }
                    $this->administrator_model->insertUpdate_EmployeeInfo();
                    redirect('administrator/employee_Entry/'.$id,'refresh');
//                    redirect('administrator/employee_List','refresh');
                }
                $this->load->view('form',$data);
            }else redirect('login','refresh');
        }else redirect('login','refresh');   
    }
    
    public function employee_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one employee.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/employee_Entry/'.$this->input->post('ID'),'refresh');
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Employee_Entry/'.$this->input->post('ID'),'refresh');
                }
            }
            else redirect('login','refresh');   
        }else
            redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function photo_Entry($id=0){
         if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_EmployeeEntry_ValidationConfig($id,1);
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE)
                $data = $this->administrator_model->load_EmployeeEntryForm_Data($id,1);
                else{
                    if($_FILES){
                        $path='./uploaded_images/';
                        $this->administrator_model->upload_Image('file',$path,80,100,array(20,100));
                        $this->administrator_model->upload_CV('adoc','./uploaded_document/');
                    }
                    $this->administrator_model->insert_Photo();
                    redirect('administrator/photo_Entry/'.$id,'refresh');
//                    redirect('administrator/photo_List','refresh');
                }
                $this->load->view('form',$data);
            }else redirect('login','refresh');
        }else redirect('login','refresh');   
    }
    
    public function photo_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one employee.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeListViewInfo(1);
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/photo_Entry/'.$this->input->post('ID'),'refresh');
//                    if($this->input->post('delete'))
//                        redirect('administrator/delete_Employee_Entry/'.$this->input->post('ID'),'refresh');
                }
            }
            else redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function delete_Employee_Entry($id){
        $encryption = new Encryption;
        $id=$encryption->decrypt($id);
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager',1);
            if($user['type']=="Super Administrator" || $row->Employee_Manager==1){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_employee_profile',array('Employee_ID'=>$id));
                    redirect('administrator/employee_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function absentLeave_List($flag){
        if($flag == 1)
            $company_name =  array('Ryans IT Limited');
        elseif($flag == 2)
            $company_name =  array('Ryans Archives Ltd.');
        elseif($flag == 3)
            $company_name =  array('Ryans Services Limited');
        
        $data = $this->administrator_model->load_AbsentLeaveListViewInfo($company_name);
        $this->load->view('form',$data);
//        $this->output->enable_profiler(TRUE);
    }
    
    public function sv_List($companyID){
        $data = $this->administrator_model->load_SupervisorListViewInfo($companyID);
        $this->load->view('form',$data);
    }
    
    public function leaveAbsentHistory(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        echo $this->administrator_model->load_leaveHistory_Table().br();
        echo $this->administrator_model->load_absentHistory_Table($from,$to);
        echo ' <script>
      $( function() {
        $("#grid1").DataTable({
            "bLengthChange": false,
            "bPaginate": false,
            "ordering": true,
            "bFilter": false,
            "bInfo": false,
            "scrollY": "100px",
            "scrollCollapse": true
                
	});
        $("#grid2").DataTable({
            "bLengthChange": false,
            "bPaginate": false,
            "ordering": true,
            "bFilter": false,
            "bInfo": false,
            "scrollY": "100px",
            "scrollCollapse": true
                
	});
     
      } );
    </script>';
//        $this->output->enable_profiler(TRUE);
    }
    
    public function designation_Entry($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Department==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_InsertDesignation_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_DesignationEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Designation_Info();
                    redirect('administrator/designation_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function designation_List($id=0){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Department==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one dept.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_DesignationListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/designation_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Designation_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Designation_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Department',1);
            if($user['type']=="Super Administrator" || $row->Department){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_designation_info',array('designation_List'=>$id));
                    redirect('administrator/designation_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function tour_Entry($id=0) {
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Holiday==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_TourEntry_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){     
                    $data=$this->administrator_model->load_TourEntryFromInfo($id);    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->insertUpdate_Tour_Info();
                    redirect('administrator/tour_List','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh'); 
    }
    
    public function tour_List(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Holiday==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_UserList_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one row.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeTourListViewInfo();
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('edit'))
                        redirect('administrator/tour_Entry/'.$this->input->post('ID'),'refresh');
                    
                    if($this->input->post('delete'))
                        redirect('administrator/delete_Tour_Entry/'.$this->input->post('ID'),'refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }
    
    public function delete_Tour_Entry($id){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Holiday',1);
            if($user['type']=="Super Administrator" || $row->Holiday==1){
                if((int)$id > 0){
                    $this->administrator_model->delete_Specific_Info('tbl_tour_info',array('Tour_ID'=>$id));
                    redirect('administrator/tour_List','refresh');
                }
            }else
                redirect('login','refresh');
        }else
            redirect('login','refresh');
    }

    public function assign_Tour(){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
               
                $config= array(
                    array(
                        'field'   => 'ID', 
                        'label'   => 'User', 
                        'rules'   =>  ($this->input->post('assign'))? 'required|xss_clean' : 'xss_clean'
                    )
                );

                $this->form_validation->set_message('required', 'You must check atleast one employee.');
                $this->form_validation->set_rules($config);
                    $companyID = $this->input->post('company_ID');
                    $deptID = $this->input->post('dept_ID');
                    $userID = $this->input->post('user_ID');
                if($this->form_validation->run() == FALSE){
                    $companyID = $this->input->post('company_ID');
                    $deptID = $this->input->post('dept_ID');
                    $userID = $this->input->post('user_ID');
                   
                }else{
                    if($this->input->post('assign')){
                        $row = $this->db->distinct()->select('Employee')->get_where('tbl_tour_info',array('Tour_ID' => $this->input->post('tour')))->row();
                        $employee_array = explode(',',$row->Employee);
//                        $employee_str = implode(',',array_merge($employee_array,$this->input->post('ID')))
                        $this->db->where(array('Tour_ID' => $this->input->post('tour')));
                        $employee_str = implode(',',array_diff($employee_array,$this->input->post('ID')));
                        if(count(array_intersect($this->input->post('ID'), $employee_array)) != count($this->input->post('ID')))
                            $employee = $row->Employee!=NULL ? $row->Employee.','.implode(',',$this->input->post('ID')) : implode(',',$this->input->post('ID'));//($employee_str == NULL)? implode(',',$this->input->post('ID')) : $employee_str
                        else{
                            $employee = implode(',',$this->input->post('ID'));
                        }
//                        echo count(array_intersect($this->input->post('ID'), $employee_array)).'===='.count($this->input->post('ID'));
//                        echo $employee;
                        $this->db->update('tbl_tour_info', array('Employee' => $employee ));
                        redirect('administrator/assign_Tour','refresh');
                    }
//                    if($this->input->post('cancel'))
//                        redirect('administrator/update_Holiday/'.$this->input->post('ID'),'refresh');
                }
                $data=$this->administrator_model->load_EmployeeListFor_AssignTour($companyID,$deptID,$userID);
                $this->load->view('grid',$data);
            }
            else redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function get_Tour_Employee(){
        echo get_Tour_Employee_Json($this->input->post('tour'));
    }
    
    public function user_Inactive_Form(){
        if($this->login_model->is_Logged_In()){
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->administrator_model->load_InactiveUser_ValidationConfig();
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
                if($this->input->post('inactive')){
                    $employee_id = $this->input->post('ID');
                    foreach($employee_id as $id){
                        $row = $this->administrator_model->get_Specific_Info('tbl_user_info', array('F_Employee_ID' => $id),'User_ID');
         
                        $this->db->update('tbl_user_info',array('Is_Exist'=>0),array('User_ID' => $row->User_ID));
                        $this->db->update('tbl_login_info',array('Is_Exist'=>0),array('F_User_ID' => $row->User_ID));
                        $this->db->update('tbl_employee_profile',array('Is_Exist'=>0) , array('Employee_ID' => $id));
                    }
                }
            }
            $data = $this->administrator_model->load_InactiveFormData($from,$to,$companyID,$deptID,$userID,$supID,$locID);
            $this->load->view('grid',$data);
        }else redirect('login','refresh');
//        $this->output->enable_profiler(TRUE);
    }
    
    public function force_Present() {
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Company,Leave_Manager',1);
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
                $this->javascript->ready('waitForMsg();');
                $this->javascript->compile();
            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Company==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config = $this->administrator_model->load_ForcePresent_ValidationConfig();
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){     
                    $data=$this->administrator_model->load_ForcePresentEntryFromInfo();    
                    $this->load->view('form',$data);
                }else{
                    $this->administrator_model->execute_ForcePresent();
                    redirect('administrator/force_Present','refresh');
                }
            }else
                redirect('login','refresh');   
        }else
            redirect('login','refresh'); 
    }
    
    public function leave_Calculator($F_Company_ID){
        if($this->login_model->is_Logged_In()){
            $user = $this->session->userdata('logged_in');
            $row = $this->administrator_model->get_Specific_Info('tbl_user_type', array('tbl_user_type.Is_Exist' => 1, 'tbl_user_type.User_Permission_Type' => $user['type']),'Employee_Manager,Leave_Manager',1);
//            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Leave_Manager==1){
//                $this->javascript->ready('waitForMsg();');
//                $this->javascript->compile();
//            }
            if($user['type']=="Super Administrator" || $user['type']=="Administrator" || $row->Employee_Manager==1){
                $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
                $config=$this->administrator_model->load_LeaveCalcultor_ValidationConfig();
                $this->form_validation->set_message('required', 'You must check atleast one employee.');
                $this->form_validation->set_rules($config);
                if($this->form_validation->run() == FALSE){
                    $data=$this->administrator_model->load_EmployeeLeaveListViewInfo($F_Company_ID);
                    $this->load->view('grid',$data);
                }else{
                    if($this->input->post('apply')){
                        $id = $this->input->post('ID');
                        $leave = $this->input->post('Leave');
                        for($i=0;$i<sizeof($id);$i++){
                            $this->db->where(array('Employee_ID' => $id[$i]));
                            $this->db->update('tbl_employee_profile',array('Leave' =>$leave[$i]));
                        }
                        redirect('administrator/leave_Calculator','refresh');
                    }
                }
            }
            else redirect('login','refresh');   
        }else
            redirect('login','refresh');
    }
    
    public function update_employee_db(){
        if($this->db->query('TRUNCATE TABLE tbl_employe_list_info')){
            if($this->db->query("INSERT INTO `tbl_employe_list_info` (SELECT  DISTINCT '',tbl_user_info.User_ID,(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS Supervisor,Office_Contact,Employee_ID,Company_Name,Location_Name,Dept_Name,RS_ID,tbl_employee_profile.Full_Name,Nick_Name,tbl_employee_profile.Email,tbl_employee_profile.Join_Date,
        tbl_employee_profile.`Leave`,tbl_employee_profile.P_Leave,
        Designation, IF(tbl_employee_profile.Is_Exist=1,(SELECT DISTINCT COUNT(selected_date) from 
(select adddate('2010-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) z
where selected_date BETWEEN IF(DATE_FORMAT(tbl_employee_profile.Join_Date,'%Y-%m-%d')>DATE_FORMAT(NOW(),'%Y-01-01'),DATE_FORMAT(tbl_employee_profile.Join_Date,'%Y-%m-%d'),DATE_FORMAT(NOW(),'%Y-01-01')) and DATE_FORMAT(NOW(),'%Y-%m-%d') AND selected_date NOT IN (
SELECT DISTINCT
tbl_login_record.Date
FROM
tbl_login_record
WHERE
tbl_login_record.Date BETWEEN IF(DATE_FORMAT(tbl_employee_profile.Join_Date,'%Y-%m-%d')>DATE_FORMAT(NOW(),'%Y-01-01'),DATE_FORMAT(tbl_employee_profile.Join_Date,'%Y-%m-%d'),DATE_FORMAT(NOW(),'%Y-01-01')) AND DATE_FORMAT(NOW(),'%Y-%m-%d') AND tbl_login_record.F_User_ID = tbl_user_info.User_ID)),0)
AS Total_Absent,
IF(tbl_employee_profile.Is_Exist=1,(SELECT SUM( tbl_leave_record.`Day` )
FROM tbl_leave_record
WHERE tbl_leave_record.F_User_ID = tbl_user_info.User_ID
AND tbl_leave_record.Is_Exist = '1'
AND tbl_leave_record.Is_Processed = '1'
AND tbl_leave_record.Is_Void = '0'
AND ((From_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' ))
OR (To_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' )))
),0) AS Leave_Taken
,
IF(tbl_employee_profile.Is_Exist=1,(SELECT DISTINCT SUM(
DATEDIFF(
tbl_holiday_info.To_Date,tbl_holiday_info.From_Date
)+1 ) FROM
tbl_holiday_info

WHERE
FIND_IN_SET(tbl_user_info.F_Employee_ID,tbl_holiday_info.Employee)
AND
(From_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' )
OR To_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' ))
AND tbl_holiday_info.Is_Exist = 1),0) AS Holiday_Given,

IF(tbl_employee_profile.Is_Exist=1,(SELECT DISTINCT SUM(
DATEDIFF(
tbl_tour_info.To_Date,tbl_tour_info.From_Date
)+1 ) FROM
tbl_tour_info

WHERE
FIND_IN_SET(tbl_user_info.F_Employee_ID,tbl_tour_info.Employee)
AND
(From_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' )
OR To_Date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' ))
AND tbl_tour_info.Is_Exist = 1),0) AS Tour_Given,

(
select COUNT(selected_date)
from (select adddate('2010-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) z
where 
selected_date
BETWEEN DATE_FORMAT( NOW( ) , '%Y-01-01' )
AND DATE_FORMAT( NOW( ) , '%Y-%m-%d' )
  and DAYOFWEEK(selected_date) in (
SELECT 
(CASE
WHEN
tbl_work_days.Sun = '0' THEN 1
WHEN
tbl_work_days.Mon = '0' THEN 2
WHEN
tbl_work_days.Tue = '0' THEN 3
WHEN
tbl_work_days.Wed = '0' THEN 4
WHEN
tbl_work_days.Thu = '0' THEN 5
WHEN
tbl_work_days.Fri = '0' THEN 6
WHEN
tbl_work_days.Sat = '0' THEN 7
END) as Weekend
FROM
tbl_work_days
WHERE
tbl_work_days.F_Employee_ID = tbl_user_info.F_Employee_ID
)

)AS Off_day,

'',
tbl_employee_profile.Is_Exist

FROM
`tbl_user_info`
RIGHT JOIN `tbl_employee_profile` ON `tbl_user_info`.`F_Employee_ID` = `tbl_employee_profile`.`Employee_ID`
LEFT JOIN `tbl_company_info` ON `tbl_employee_profile`.`F_Company_ID` = `tbl_company_info`.`Company_ID`
LEFT JOIN `tbl_location_info` ON `tbl_employee_profile`.`F_Location_ID` = `tbl_location_info`.`Location_ID`
LEFT JOIN `tbl_dept_info` ON `tbl_employee_profile`.`F_Dept_ID` = `tbl_dept_info`.`Dept_ID`
LEFT JOIN `tbl_hierarchy_info` ON `tbl_hierarchy_info`.`F_Employee_ID` = `tbl_employee_profile`.`Employee_ID`
LEFT JOIN `tbl_office_time` ON `tbl_employee_profile`.`Employee_ID` = `tbl_office_time`.`F_Employee_ID`

)
")) echo "Successfully Updated";
                    
        }
        
    }
    
    public function test123(){
        $this->db->_protect_identifiers=false;
        $result = $this->db->distinct()->select('tbl_hierarchy_info.F_Employee_ID,tbl_designation_info.Designation_ID')
                ->from('tbl_designation_info')
                ->join('tbl_hierarchy_info', 'tbl_designation_info.Designation_Name LIKE tbl_hierarchy_info.Designation','inner')->get()->result();
        $final_array = array();
        foreach($result as $row){
            $data['F_Designation_ID']= $row->Designation_ID;
            $data['F_Employee_ID'] = $row->F_Employee_ID;
            array_push($final_array, $data);
        }    
    
        $this->db->update_batch('tbl_hierarchy_info',$final_array,'F_Employee_ID');
    }
    
        public function make_offday(){
        $res = $this->db->distinct()->select('User_ID,Employee_ID')->get_where('tbl_employe_list_info',array('Is_Exist'=>'1'))->result();
        foreach($res as $row){
         if($this->db->query("UPDATE tbl_login_record SET Offday = (SELECT 
            (CASE
            WHEN
            tbl_work_days.Sun = '0' THEN 0
            WHEN
            tbl_work_days.Mon = '0' THEN 1
            WHEN
            tbl_work_days.Tue = '0' THEN 2
            WHEN
            tbl_work_days.Wed = '0' THEN 3
            WHEN
            tbl_work_days.Thu = '0' THEN 4
            WHEN
            tbl_work_days.Fri = '0' THEN 5
            WHEN
            tbl_work_days.Sat = '0' THEN 6
            END) 
            FROM
            tbl_work_days
            WHERE 
            tbl_work_days.F_Employee_ID='".$row->Employee_ID."')
            WHERE
            tbl_login_record.F_User_ID='".$row->User_ID."'
            AND tbl_login_record.Remarks = 'Auto' AND tbl_login_record.Force_Present = '1'
           
            ")) echo "success for ".$row->Employee_ID;
        }
    }

}
?>
