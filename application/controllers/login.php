<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of login
 *
 * @author nasirul
 */
class login extends CI_Controller{
//    public  $logged_flag = FALSE;
    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('administrator_model');
        $this->load->model('home_model');
    }
    
    public function index(){
        if($this->login_model->is_Logged_In())
            redirect('home','refresh');
        else{
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $config=$this->login_model->load_ValidationConfig();
            $this->form_validation->set_rules($config);
             if($this->form_validation->run() == FALSE){
                $data = $this->login_model->load_loginFormData();
                $this->load->view('form',$data);
             }else{ 
//                if($this->logged_flag)
//                    redirect('home#openModal','refresh');
//                else 
//                    redirect('home','refresh');
                 redirect('home','refresh');
             }
        }
    }
    
    public function check_Database(){
        $row = $this->login_model->check_Login();
        $date = $this->login_model->get_Local_Date_Time();
        if($row){
            if($row->Permission_Type == 'Super Administrator' || $row->Permission_Type =='Administrator'){
                $session_data = array('eid' => $row->F_Employee_ID, 'id' => $row->UID,'lid' => $row->ID,'user' => $row->User_Name,'type' => $row->Permission_Type,'fullname' => $row->Full_Name,'time'=>$date,'logged_in'=>TRUE);
                  $this->session->set_userdata('logged_in',$session_data);
                  $this->login_model->store_Session_to_DB($this->session->all_userdata());
                  if($this->login_model->not_present($row->UID)){
                      $this->login_model->store_LoginRecord_to_DB($row->UID,$row->F_Employee_ID);
      //                $this->logged_flag = TRUE;
                      $this->session->set_userdata('first_time_logged',TRUE);
                  }
                  return TRUE;
            }else{
                //Offday Dates
                $weekcount= mdate('%w',  strtotime(now()));    
                $record = $this->administrator_model->get_User_Inforamtion(array('User_ID' => $row->UID,'Weekday' => $weekcount));
                $offdays = $this->home_model->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
                $offdays_date_array = $this->home_model->getoffdaysDate($offdays); 
                
                //Holiday Dates
                $this->db->select('From_Date,To_Date');
                $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$row->F_Employee_ID.'",Employee)  AND (From_Date = "'.mdate('%Y-%m-%d').'" OR To_Date = "'.mdate('%Y-%m-%d').'")');
                $result = $query->result();
                $holiday_date_array = array();
                foreach($result as $row_off){
                    $temp_date_array = $this->administrator_model->showDates($row_off->From_Date, $row_off->To_Date);
                    $holiday_date_array = array_merge($holiday_date_array,$temp_date_array);
                }
            
                //Tour Dates
                $this->db->select('From_Date,To_Date');
                $query = $this->db->get_where('tbl_tour_info','Is_Exist = 1 AND FIND_IN_SET ("'.$row->F_Employee_ID.'",Employee) AND (From_Date = "'.mdate('%Y-%m-%d').'" OR To_Date = "'.mdate('%Y-%m-%d').'")');
                $result = $query->result();
                $tour_date_array = array();
                foreach($result as $row_tour){
                    $temp_date_array = $this->administrator_model->showDates($row_tour->From_Date, $row_tour->To_Date);
                    $tour_date_array = array_merge($tour_date_array,$temp_date_array);
                }
                
                 //Leave Dates      
                $query = $this->db->distinct()->select('From_Date,To_Date,First_Half,Second_Half')->where('(From_Date BETWEEN "'.mdate('%Y-%m-01').'" AND "'.mdate('%Y-%m-%d').'" OR To_Date BETWEEN "'.mdate('%Y-%m-01').'" AND "'.mdate('%Y-%m-%d').'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$row->UID));
                if($query->num_rows() > 0){
                    $result = $query->result();
                }else{
                    $result = $this->db->distinct()->select('From_Date,To_Date,First_Half,Second_Half')->where('(To_Date BETWEEN "'.mdate('%Y-%m-%d').'" AND "'.mdate('%Y-%m-31').'" OR From_Date BETWEEN "'.mdate('%Y-%m-%d').'" AND "'.mdate('%Y-%m-31').'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$row->UID))->result();
                }
                $leave_array = array();
                foreach($result as $row_leave){
                    $leave_date_array = $this->administrator_model->showDates($row_leave->From_Date,$row_leave->To_Date);
                    $leave_array = array_merge($leave_array,$leave_date_array);
                }
                
                if(in_array($this->login_model->get_Local_Date(), $offdays_date_array) || in_array(mdate('%Y-%m-%d'), $holiday_date_array) || in_array(mdate('%Y-%m-%d'), $tour_date_array) || in_array(mdate('%Y-%m-%d'), $leave_array)){
                    
                    $weekcount= mdate('%w',  strtotime(now()));
                    $row_ot = $this->db->distinct()->select('In,Out')->get_where('tbl_office_time', array('F_Employee_ID' => $user['eid'], 'Weekday'=> $weekcount))->row();
                    $dateDiff = intval((strtotime($row_ot->Out)-strtotime($row_ot->In))/60);
                    $office_hours = intval($dateDiff/60);
                    $half_office_hour = $office_hours/2;
                    $half_office_min = $half_office_hour * 60;
                    $half_time =  mdate('%H:%i:%s',strtotime("+ $half_office_min minutes",strtotime($row_ot->In)));
                    $row_half = $this->db->distinct()->select('First_Half,Second_Half')->where('(To_Date = "'.mdate('%Y-%m-%d').'" OR From_Date = "'.mdate('%Y-%m-%d').'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$row->UID))->row();
                    if(!is_null($row_half)){
                        if(is_null($row_half->First_Half) && mdate('%H:%i:%s')<$half_time){
                            $session_data = array('eid' => $row->F_Employee_ID, 'id' => $row->UID,'lid' => $row->ID,'user' => $row->User_Name,'type' => $row->Permission_Type,'fullname' => $row->Full_Name,'time'=>$date,'logged_in'=>TRUE);
                            $this->session->set_userdata('logged_in',$session_data);
                            $this->login_model->store_Session_to_DB($this->session->all_userdata());
                            if($this->login_model->not_present($row->UID)){
                                $this->login_model->store_LoginRecord_to_DB($row->UID,$row->F_Employee_ID); 
                                $this->session->set_userdata('first_time_logged',TRUE);
                            }
                            return TRUE;
                        }
                        elseif(is_null($row_half->Second_Half) && mdate('%H:%i:%s')>$half_time){
                            $session_data = array('eid' => $row->F_Employee_ID, 'id' => $row->UID,'lid' => $row->ID,'user' => $row->User_Name,'type' => $row->Permission_Type,'fullname' => $row->Full_Name,'time'=>$date,'logged_in'=>TRUE);
                            $this->session->set_userdata('logged_in',$session_data);
                            $this->login_model->store_Session_to_DB($this->session->all_userdata());
                            if($this->login_model->not_present($row->UID)){
                                $this->login_model->store_LoginRecord_to_DB($row->UID,$row->F_Employee_ID); 
                                $this->session->set_userdata('first_time_logged',TRUE);
                            }
                            return TRUE;
                        }
                        else{
                            $this->form_validation->set_message('check_Database','Access denied');
                            return FALSE;
                        }
                    }else{
                        $this->form_validation->set_message('check_Database','Access denied');
                        return FALSE;
                    }   
                }else{
                    $session_data = array('eid' => $row->F_Employee_ID, 'id' => $row->UID,'lid' => $row->ID,'user' => $row->User_Name,'type' => $row->Permission_Type,'fullname' => $row->Full_Name,'time'=>$date,'logged_in'=>TRUE);
                    $this->session->set_userdata('logged_in',$session_data);
                    $this->login_model->store_Session_to_DB($this->session->all_userdata());
                    if($this->login_model->not_present($row->UID)){
                        $this->login_model->store_LoginRecord_to_DB($row->UID,$row->F_Employee_ID);
        //                $this->logged_flag = TRUE;
                        $this->session->set_userdata('first_time_logged',TRUE);
                    }
                    return TRUE;
                }
            }
        }else{
            $this->form_validation->set_message('check_Database','Invalid username or password');
            return FALSE;
        }  
        
        $this->output->enable_profiler(TRUE);
    }
}

?>
