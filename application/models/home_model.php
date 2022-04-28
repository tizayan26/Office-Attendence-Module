<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
     
/**
 * Description of home_model
 *
 * @author Nasirul Akbar Khan
 */
class home_model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('administrator_model');
        $this->load->library('table_edit');
    }
        
        
        
    public function update_LoginRemarks(){
        $user = $this->session->userdata('logged_in');
        $this->db->update('tbl_login_record',array('Remarks' => 'Late:'.nbs().$this->input->post('reason')),array('F_User_ID' => $user['id'], 'Date' => $this->login_model->get_Local_Date()));
    }
        
        
    public function load_Report_ValidationConfig(){
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
        
    public function load_BasicReport_ValidationConfig(){
       $config = array(
           array(
               'field' => 'company_ID', 
               'label' => 'Company ID', 
               'rules' => 'trim|required|xss_clean'
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
           )
               
       );
       return $config;
    }
    
    public function load_IDCardReport_ValidationConfig(){
       $config = array(
           array(
               'field' => 'employeeids', 
               'label' => 'Employee ID', 
               'rules' => 'trim|required|xss_clean'
           )
               
       );
       return $config;
    }
        
    public function load_SUSVReport_ValidationConfig(){
       $config = array(
           array(
               'field' => 'sup_ID', 
               'label' => 'Supervisor', 
               'rules' => 'trim|xss_clean'//required
           )
       );
       return $config;
    }
        
    public function load_ViewData(){
        
        //echo $this->countDays(2013,11,array(5));
        $data['session']=$this->session->userdata('logged_in');
        if($data['session']['type'] == 'Administrator'){
            $result = $this->db->distinct()->select('Count(tbl_employee_profile.RS_ID) AS Count_emp,tbl_company_info.Company_Name')->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employee_profile.F_Company_ID', 'inner')->group_by('tbl_employee_profile.F_Company_ID')->get_where('tbl_employee_profile',array('tbl_employee_profile.Is_Exist' => 1))->result();
            $data['pie'] = NULL;
            foreach($result as $row){
                $data['pie'] .= '[\''.$row->Company_Name.'\','.$row->Count_emp.'],';
            }
        }else{
//            $data_time_array = explode(' ', $data['session']['time']);
            $grace_time = $this->administrator_model->get_Specific_Info('tbl_employee_profile',array('Employee_ID' => $data['session']['eid']),'Grace_Time,Photo',1);
//          
            $record=$this->administrator_model->get_Specific_Info('tbl_office_time',array('F_Employee_ID' => $data['session']['eid'],'Weekday' => mdate('%w')),'In',1);
//            echo  mdate('%h:%i %a',strtotime("+".$grace_time->Grace_Time+1." minutes", strtotime($record->In))).'='.mdate('%h:%i %a',strtotime($data['session']['time']));
//            if(mdate('%h:%i %a',strtotime("+".$grace_time->Grace_Time+1." minutes", strtotime($record->In)) <  mdate('%h:%i %a',strtotime($data['session']['time'])))){ 
//                $data['html'] = 'Late Reason'.form_textarea(array('name' => 'reason', 'id' => 'reason', 'rows' => '4', 'cols' => '60'));
//            }
        }
        $this->jquery->script(base_url('assets/js/jquery/jquery-ui.min.js'));
        $js = 'waitForMsg();waitForMsgRecruit();';
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
        $this->javascript->ready($js);
        $this->javascript->compile();
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['title'] = 'Welcome'.nbs().$data['session']['fullname'];
        $data['width'] = '700';
        $data['height'] = '540';
        if($data['session']['type']!="Administrator"){
        $this->db->distinct();
        $this->db->select('In_Time,Out_Time,Offday,Office_In');
        $this->db->limit(1);
        $row=$this->db->get_where('tbl_login_record',array('Date'=>$this->login_model->get_Local_Date(),'F_User_ID'=>$data['session']['id']))->row();
            
        $weekcount= mdate('%w',  strtotime(now()));
            
        $record=$this->administrator_model->get_User_Inforamtion(array('User_ID' => $data['session']['id'],'Weekday' => $weekcount));
        $record_supervisor = $this->db->distinct()->select('Full_Name')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => $data['session']['eid']))->row();
        $data['profile_pic'] = img(site_url(isset($grace_time->Photo)? array('uploaded_images','thumbnails_100',$grace_time->Photo) : array('assets','images','default-user.png')));
        $data['date']='Date:'.nbs().$this->administrator_model->regularDateFormatConverter($this->login_model->get_Local_Date());
        $data['in_time'] =  mdate('%h:%i %a',  strtotime($row->In_Time));
        if($row->Out_Time!='00:00:00')
        $data['out_time'] = mdate('%h:%i %a',  strtotime($row->Out_Time));
        $data['office_hours'] = mdate('%h:%i %a',strtotime($record->In)).' - '.mdate('%h:%i %a',strtotime($record->Out));
        $data['supervisor'] = $record_supervisor->Full_Name;
        if(is_null($row->Offday)){
            $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' => $data['session']['eid']),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID
            $offdays =  $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
        }
        else{
            $offdays =  explode(',',$row->Offday);
        }
//        $offdays=$this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
        $offdays_date_array = $this->getoffdaysDate($offdays,mdate('%Y-%m-25',strtotime("-1 month",now())),mdate('%Y-%m-%d',now())); 
//        $data['total_working_days'] = $this->countDays($this->login_model->get_Local_Date('%Y'), $this->login_model->get_Local_Date('%m'),$offdays);
        $data['total_working_days'] = $this->countDaysNew(mdate('%Y-%m-25',strtotime("-1 month",now())),mdate('%Y-%m-25',now()), $offdays_date_array);
//        $data['current_working_days'] = ($this->login_model->get_Local_Date('%d')-$this->countoffDays($offdays))-$this->holidayCount();
        $data['current_working_days'] = ($this->countDaysNew(mdate('%Y-%m-25',strtotime("-1 month",now())),mdate('%Y-%m-%d',now()), $offdays_date_array))-$this->holidayCount();
        $data['present'] = $this->presentCount($data['session']['eid'],mdate('%Y-%m-25',strtotime("-1 month",now())),mdate('%Y-%m-%d',now()));
        $data['late'] = $this->lateCount($data['session']['eid'],NULL);
        $data['on_time'] = $this->lateCount($data['session']['eid'],NULL,1);
        $data['offdays'] = $this->countoffDays($offdays);
        $data['holiday'] = $this->holidayCount($data['session']['eid']);
        $data['leave'] = $this->administrator_model->leaveCountNew($data['session']['id']);
        $data['late_report'] = $this->lateReport($data['session']['id'],$weekcount);
        $data['absent_report'] = $this->absentReport($data['session']['id'],$offdays_date_array,mdate('%Y-%m-25',strtotime("-1 month",now())),mdate('%Y-%m-%d',now()));
        if($this->session->userdata('first_time_logged')){
            $absent = $this->absentReport($data['session']['id'],$offdays_date_array,mdate('%Y-%m-%d',strtotime("-1 day",now())),mdate('%Y-%m-%d',now()));

            $from = mdate('%Y-%m-%d',strtotime("-1 day",now()));
            $to = mdate('%Y-%m-%d',now());
            $user_id = $data['session']['id'];

            $row_remarks = $this->administrator_model->get_Specific_Info('tbl_login_remarks', array('Date' => $from,'F_User_ID' => $user_id), 'Admin_Remarks',1);

            //Holiday Dates
            $this->db->select('From_Date,To_Date');
            $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$data['session']['eid'].'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'"');
            $result = $query->result();
            $holiday_date_array = array();
            foreach($result as $row){
                $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
                $holiday_date_array = array_merge($holiday_date_array,$temp_date_array);
            }

            //Tour Dates
            $this->db->select('From_Date,To_Date');
            $query = $this->db->get_where('tbl_tour_info','Is_Exist = 1 AND FIND_IN_SET ("'.$data['session']['eid'].'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'"');
            $result = $query->result();
            $tour_date_array = array();
            foreach($result as $row){
                $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
                $tour_date_array = array_merge($tour_date_array,$temp_date_array);
            }

            //Leave Dates

            $query = $this->db->distinct()->select('From_Date,To_Date')->where('From_Date BETWEEN "'.mdate('%Y-%m-01', strtotime($from)).'" AND "'.$to.'"')->get_where('tbl_leave_record',array('Is_Exist' =>'1','F_User_ID'=>$user_id));//'Is_Processed'=>'1'

            if($query->num_rows() > 0){
                $result = $query->result();
            }else{
                $result = $this->db->distinct()->select('From_Date,To_Date')->where('To_Date BETWEEN "'.$from.'" AND "'.mdate('%Y-%m-31', strtotime($to)).'"')->get_where('tbl_leave_record',array('Is_Exist' =>'1','F_User_ID'=>$user_id))->result();//,'Is_Processed'=>'1'
            }
            $leave_array = array();
            foreach($result as $row){
                $leave_date_array = $this->administrator_model->showDates($row->From_Date,$row->To_Date);
                $leave_array = array_merge($leave_array,$leave_date_array);
            }

//            $yesterday = mdate('%Y-%m-%d',strtotime("-1 day",now()));
//            if(!in_array($yesterday,$offdays_date_array) || !in_array($yesterday,$holiday_date_array) || !in_array($yesterday,$leave_array) || !in_array($yesterday,$tour_date_array)|| $row_remarks->Admin_Remarks != NULL){   
    //            echo $yesterday;
//                    $this->db->where('User_ID',$data['session']['id']);
//                    $this->db->update('tbl_user_info',array('Is_Exist'=>0));
//
//                    $this->db->where('F_User_ID',$data['session']['id']);
//                    $this->db->update('tbl_login_info',array('Is_Exist'=>0));
//
//                    $this->db->where('Employee_ID',$data['session']['eid']);
//                    $this->db->update('tbl_employee_profile',array('Is_Exist'=>0));
//                $row = $this->db->distinct()->select('In_Time')->get_where('tbl_login_record',array('F_User_ID' =>$data['session']['id'],'Date'=> $yesterday))->row();
//                if(!$row || is_null($row->In_Time)){ 
//                    $this->db->where('User_ID',$data['session']['id']);
//                    $this->db->update('tbl_user_info',array('Is_Absent'=>1)); 
//                }
//                $row = $this->db->distinct()->select('Is_Absent')->get_where('tbl_user_info',array('User_ID' =>$data['session']['id']))->row();
//                if($row->Is_Absent>=1){
//                    $this->db->where('User_ID',$data['session']['id']);
//                    $this->db->update('tbl_user_info',array('Is_Absent'=>$row->Is_Absent+1));
//                }
//                if($row->Is_Absent>=7){
//                    $this->db->where('User_ID',$data['session']['id']);
//                    $this->db->update('tbl_user_info',array('Is_Exist'=>0));
//
//                    $this->db->where('F_User_ID',$data['session']['id']);
//                    $this->db->update('tbl_login_info',array('Is_Exist'=>0));
//
//                    $this->db->where('Employee_ID',$data['session']['eid']);
//                    $this->db->update('tbl_employee_profile',array('Is_Exist'=>0));
//
//                }

//            }   
    
        }
        }
        return $data;
    }
        
    public function countDaysNew($from,$to,$holidays){
        $start = new DateTime($from);
        $end = new DateTime($to);
        // otherwise the  end date is excluded (bug?)
        $end->modify('+1 day');
            
        $interval = $end->diff($start);
            
        // total days
        $days = $interval->days;
            
        // create an iterateable period of date (P1D equates to 1 day)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
            
        // best stored as array, so you can add more than one
            
            
        foreach($period as $dt) {
            $curr = $dt->format('D');
                
            // substract if Saturday or Sunday
//            if ($curr == 'Sat' || $curr == 'Sun') {
//                $days--;
//            }
    
            // (optional) for the updated question
            if (in_array($dt->format('Y-m-d'), $holidays)) {
                $days--;
            }
        }
        return $days;
    }
        
        
    public function countDays($year, $month, $ignore){
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month){
            if (in_array(date("w", $counter), $ignore) == false)
                $count++;
            $counter = strtotime("+1 day", $counter);
        }
        return $count;
    }
        
    public function countoffDays($offdays){
        $no = 0;
        $start = new DateTime(mdate('%Y-%m-25',strtotime("-1 month",now())));
        $end   = new DateTime(mdate('%Y-%m-25',now()));
//        $start = new DateTime($this->login_model->get_Local_Date('%Y-%m').'-01');
//        $end   = new DateTime($this->login_model->get_Local_Date());
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $dt)
        {   foreach($offdays as $days){
                if($dt->format('w') == $days)
                    $no++;
            }
        }
        return $no;
    }
        
    public function getoffdaysDate($offdays,$from=NULL,$to=NULL){
        $offdays_date_array = array();
        $start =  new DateTime(is_null($from) ? $this->login_model->get_Local_Date('%Y-%m').'-01':$from) ;// mdate('%Y-%m-01',strtotime($from)
        $end   =  new DateTime(is_null($to) ? $this->login_model->get_Local_Date() :$to);// mdate('%Y-%m-%d',strtotime($to))
        $end->setTime(0,0,1);
//        $interval = DateInterval::createFromDateString('1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
            
        foreach ($period as $dt)
        {   foreach($offdays as $days){
                if($dt->format('w') == $days)
                    array_push ($offdays_date_array, $dt->format('Y-m-d'));
            }
        }
        return $offdays_date_array;
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
        
    public function presentCount($employee_id,$date_from=NULL,$date_to=NULL){
        if($date_from==NULL && $date_to== NULL)
            $this->db->where(array('F_Employee_ID' => $employee_id,'DATE_FORMAT(tbl_login_record.Date,"%m")' => $this->login_model->get_Local_Date('%m')));
        else
            $this->db->where('F_Employee_ID ='.$employee_id.' AND tbl_login_record.Date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner');
        return $this->db->count_all_results('tbl_login_record');
    }
        
    public function lateCount($employee_id,$weekcount,$ontime=0){
        $this->db->select('COUNT(tbl_login_record.In_Time) AS late_count');
        $this->db->from('tbl_login_record');
        $this->db->join('tbl_user_info','tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner');
        $this->db->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner');
        $this->db->join('tbl_office_time', 'tbl_office_time.F_Employee_ID = tbl_user_info.F_Employee_ID','inner');
        $where = 'tbl_user_info.F_Employee_ID = '.$employee_id.' AND DATE_FORMAT(tbl_login_record.Date,"%m") = '.$this->login_model->get_Local_Date('%m').' AND tbl_office_time.Weekday = '.($weekcount!= NULL? $weekcount : 'DATE_FORMAT(tbl_login_record.Date,"%w")').' AND ADDTIME(tbl_office_time.`In`,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60))';
        $where .=($ontime==0) ? ' < ' : ' > ';
        $where .=' tbl_login_record.`In_Time`';
        $this->db->where($where);
//        if($ontime==0)
//            $this->db->where('tbl_login_record.F_User_ID = '.$user_id.' AND DATE_FORMAT(tbl_login_record.Date,"%m") = '.$this->login_model->get_Local_Date('%m').' AND tbl_office_time.`In` < tbl_login_record.In_Time');
//        else
//            $this->db->where('tbl_login_record.F_User_ID = '.$user_id.' AND DATE_FORMAT(tbl_login_record.Date,"%m") = '.$this->login_model->get_Local_Date('%m').' AND tbl_office_time.`In` > tbl_login_record.In_Time');
        $row=$this->db->get()->row();
        return  $row->late_count;
    }
        
    public function lateReport($user_id,$weekcount,$ontime=0){
        $this->db->select('tbl_login_record.In_Time,tbl_login_record.Out_Time,tbl_login_record.Date');
        $this->db->from('tbl_login_record');
        $this->db->join('tbl_user_info', 'tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner');
        $this->db->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner');
        $this->db->join('tbl_office_time', 'tbl_office_time.F_Employee_ID = tbl_user_info.F_Employee_ID','inner');
        if($ontime==0)
//            tbl_office_time.Weekday = '.$weekcount.'
            $this->db->where('tbl_office_time.Weekday = DATE_FORMAT(tbl_login_record.Date,"%w") AND tbl_login_record.F_User_ID = '.$user_id.' AND ADDTIME(tbl_office_time.`In`,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60)) < tbl_login_record.`In_Time` AND tbl_login_record.Date BETWEEN \''.mdate('%Y-%m-25',strtotime("-1 month",now())).'\' AND \''.mdate('%Y-%m-%d',now()).'\'');//DATE_FORMAT(tbl_login_record.Date,"%m")= '.$this->login_model->get_Local_Date('%m')
        else
//            tbl_office_time.Weekday = '.$weekcount.'
            $this->db->where('tbl_office_time.Weekday = DATE_FORMAT(tbl_login_record.Date,"%w") AND tbl_login_record.F_User_ID = '.$user_id.' AND ADDTIME(tbl_office_time.`In`,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60)) > tbl_login_record.`In_Time`');
        $this->db->order_by('tbl_login_record.Date','asc');
        return $this->db->get()->result();
    }
        
    public function absentReport($user_id,$offdays_date_array,$from,$to){
        
        $first_login_row = $this->db->select('Date')->order_by('Date','asc')->get_where('tbl_login_record','F_User_ID ='.$user_id, 1)->row();
        if($first_login_row){
            $first_login = $first_login_row->Date;
            if (strtotime($first_login) > strtotime($from)) {
                $from = $first_login;
            }
        }
        $date_array=$this->administrator_model->showDates($from,$to);
        $emp_id = $this->administrator_model->get_Specific_Info('tbl_user_info',array('Is_Exist'=>1,'User_ID' => $user_id),'F_Employee_ID',1);
        $offdays_date_array1 = array();
        foreach($date_array as $date){
            $weekcount= mdate('%w',  strtotime($date));   
            if($date>mdate('%Y-%m-%d')){
                $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $emp_id->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
            }else{
                $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $user_id, 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                if($row){
//                    echo $date.nbs().'Same date offday found in login'.br();
                    $offdays = explode(',',$row->Offday);
                }else{
                    $offdays_row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $user_id, 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('-1 day', strtotime($date)))),'F_User_ID,Offday',1);
                    if($offdays_row){
//                        echo mdate('%Y-%m-%d',strtotime('-3 day', strtotime($date))).nbs().'offday found in login<br>';
                        $offdays = explode(',',$offdays_row->Offday);
                    }else{
//                        echo $date.nbs().'offday default from config<br>';
                        $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $emp_id->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                        $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                    }
                }
            }
            if(in_array($weekcount,$offdays))
                array_push($offdays_date_array1, $date) ;
        }
        //Holiday Dates
        $this->db->select('From_Date,To_Date');
        $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$emp_id->F_Employee_ID.'",Employee)  AND DATE_FORMAT(From_Date,"%Y-%m") BETWEEN "'.mdate('%Y-%m',strtotime($from)).'" AND "'.mdate('%Y-%m',strtotime($to)).'"');
        $result = $query->result();
        $holiday_date_array = array();
        foreach($result as $row){
            $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
            $holiday_date_array = array_merge($holiday_date_array,$temp_date_array);
        }
            
        //Tour Dates
        $this->db->select('From_Date,To_Date');
        $query = $this->db->get_where('tbl_tour_info','Is_Exist = 1 AND FIND_IN_SET ("'.$emp_id->F_Employee_ID.'",Employee) AND (From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'" OR To_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'")');
        $result = $query->result();
        $tour_date_array = array();
        foreach($result as $row){
            $temp_date_array = $this->administrator_model->showDates($row->From_Date, $row->To_Date);
            $tour_date_array = array_merge($tour_date_array,$temp_date_array);
        }
//        print_r($tour_date_array);
            
        //Leave Dates
            
        $query = $this->db->distinct()->select('From_Date,To_Date')->where('(From_Date BETWEEN "'.mdate('%Y-%m-01', strtotime($from)).'" AND "'.mdate('%Y-%m-%d', strtotime($to)).'" OR To_Date BETWEEN "'.mdate('%Y-%m-01', strtotime($from)).'" AND "'.mdate('%Y-%m-%d', strtotime($to)).'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$user_id));
            
        if($query->num_rows() > 0){
            $result = $query->result();
        }else{
            $result = $this->db->distinct()->select('From_Date,To_Date')->where('(To_Date BETWEEN "'.mdate('%Y-%m-%d', strtotime($from)).'" AND "'.mdate('%Y-%m-31', strtotime($to)).'" OR From_Date BETWEEN "'.mdate('%Y-%m-%d', strtotime($from)).'" AND "'.mdate('%Y-%m-31', strtotime($to)).'")')->get_where('tbl_leave_record',array('Is_Exist' =>'1','Is_Processed'=>'1','F_User_ID'=>$user_id))->result();
        }
        $leave_array = array();
        foreach($result as $row){
            $leave_date_array = $this->administrator_model->showDates($row->From_Date,$row->To_Date);
            $leave_array = array_merge($leave_array,$leave_date_array);
        }
//        print_r($leave_array);
        //Present Dates
        $this->db->select('tbl_login_record.In_Time,tbl_login_record.Out_Time,tbl_login_record.Date');
        $this->db->from('tbl_login_record');
        $this->db->where('tbl_login_record.Date BETWEEN "'.$from.'" AND "'.$to.'" AND tbl_login_record.F_User_ID = '.$user_id);
        $this->db->order_by('tbl_login_record.Date','asc');
        $result = $this->db->get()->result();
        $date_array_present = array();
        foreach($result as $row){
            array_push($date_array_present, $row->Date);
        }
//        echo 'Dates';
//        print_r($date_array);
//        echo 'Present';
//        print_r($date_array_present);
//        echo 'Leave';
//        print_r($leave_array);
//        echo br().'Holiday';
//        print_r($holiday_date_array);
//        echo br().'$offdays';
//        print_r($offdays_date_array);
//        echo br().'Diff';
//        print_r(array_values(array_diff($date_array, $date_array_present,$offdays_date_array,$leave_array,$holiday_date_array)));

        
//        if (strtotime($first_login) < strtotime($from)) {
//            $date_before_login = $this->administrator_model->showDates($first_login->Date, $from);
//        } else {
//            $date_before_login = $this->administrator_model->showDates($first_login, $to);
//        }
//        print_r($date_array_present);
//        print_r($date_array);
//        print_r(array_values(array_diff($date_array, $date_array_present,$offdays_date_array,$leave_array,$holiday_date_array,$tour_date_array)));
        return array_values(array_diff($date_array, $date_array_present,$offdays_date_array,$leave_array,$holiday_date_array,$tour_date_array,$offdays_date_array1));//,$date_before_login
    }
        
    public function load_ReportData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        $data['session'] = $this->session->userdata('logged_in');
        $ID = $Date = $Weekdays = $InTime = $OutTime = $Name = $Remarks = $Admin_Remarks = $RS = $Dept = $Office_Time = $Location = $Supervisor = NULL;
//        $this->table_to_excel->load_Script('download_button','grid','noExl','Report','[1,2,3,4,5,6,7,8,9]');
//        $columnDef = ($companyID == NULL) ? '{"visible": true , "targets": 0, "width":\'1%\'}' : '{"visible": true , "targets": 0, "width":\'1%\' },{"width": \'6%\', "targets": [2,4,7,8] },{"width": \'10%\', "targets": [5,6,11,12]},{"width": \'28%\', "targets": 1},{"width": \'4%\', "targets": [3,9,10]}';
        $columnDef = ($companyID == NULL) ? '{"targets": 0, "width":\'1%\'}' : '{"visible": true , "targets": 0, "width":\'0%\' },{"width": \'6%\', "targets": [1]},{"width": \'7.5%\', "targets": [2,4,7,8] },{"width": \'13%\', "targets": [5,6,11,12]},{"width": \'6%\', "targets": [3,9,10]},{"type": \'hh:mm A\',"targets":[9] }';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 1, "asc" ]]');
        $js = 'waitForMsg();waitForMsgRecruit();';
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
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
//        $js .= "function change_user() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var company_id = $('#company').val();
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                }";
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
//        $js .= "$('#dept').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var dept_id = $('#dept').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User')."/' + dept_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
//        
//         $js .= "$('#location').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var loc_id = $('#location').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByLocation')."/' + loc_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
    
        $this->javascript->ready($js);
        $ontime = $late =  $offday = $leave = $holiday = $absent = 0;
            
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
            
            
        if($data['session']['type']=="Super Administrator" || $data['session']['type']=="Administrator" || $data['session']['type']=="Supervisor")
        $this->table_edit->load_Script('grid',$companyID == NULL ? '[6,"Remarks"],[7,"Admin_Remarks"]' : '[11,"Remarks"],[12,"Admin_Remarks"]','[0,"ID"]','home/update_Remarks'); //,[1,"RSID"]    ,[2,"RSID"]
        else
        $this->table_edit->load_Script('grid',$companyID == NULL ? '[5,"Remarks"]' : '[10,"Remarks"]','[0,"ID"]','home/update_Remarks'); //,[1,"RSID"]  ,[2,"RSID"]     
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/report';
        $data['title'] = 'Report';
        $data['width'] = '1300';
        $data['height'] = '480';
        $data['elements'] = array('users');
        $row_employee = $this->administrator_model->get_User_Inforamtion(array('tbl_user_info.Is_Exist' => '1','User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID));
        if($from == NULL && $to == NULL){
            $from = mdate('%Y-%m').'-1';
            $to = mdate('%Y-%m-%d',now());
        }
        $date_array=$this->administrator_model->showDates($from,$to);
            
        foreach($date_array as $date){
            if($companyID == NULL){
                $join = strtotime($row_employee->Join_Date);
                $now = strtotime($date);
                if($now >= $join){
                $weekcount=mdate('%w',  strtotime($date));
                if($userID==NULL && $data['session']['type']!="Administrator")
                $record_array_where['F_Employee_ID'] = $row_employee->F_Employee_ID;
                $record_array_where['Weekday'] = $weekcount;
                $record=$this->administrator_model->get_Specific_Info('tbl_office_time',$record_array_where,'In',1);
                $Date[] = $this->administrator_model->regularDateFormatConverter($date);
                $weekday = getdate(strtotime($date));
                $Weekdays[]= $weekday['weekday'];
                    
                    
                    
                $result_leave = $this->administrator_model->get_Specific_Info('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=>($userID==NULL) ? $data['session']['id'] : $userID,'DATE_FORMAT(tbl_leave_record.From_Date,"%Y")' => $from==NULL? mdate('%Y', now()) : mdate('%Y', strtotime($from))),'From_Date,To_Date',2);
                $grace_time = $this->administrator_model->get_Specific_Info('tbl_employee_profile',array('Employee_ID' => $row_employee->F_Employee_ID,'Is_Exist' => '1'),'Grace_Time',1);
//                $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID, 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
//                if($row){                  
//                if(is_null($row->Offday)){
//                    $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' => $row_employee->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID
//                    $offdays =  $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
//                }
//                else{
//                    $offdays =  explode(',',$row->Offday);
//                }
//                }else{
//                    $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' => $row_employee->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID
//                    $offdays =  $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
//                }
                $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $data['session']['id'], 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                if($date>mdate('%Y-%m-%d')){
                     $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $data['session']['eid']),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                     $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                 }else{
                     
                     if($row){
                         $offdays = explode(',',$row->Offday);
                     }else{
                         $offdays_row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $data['session']['id'], 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('-1 day', strtotime($date)))),'F_User_ID,Offday',1);
                         if($offdays_row)
                             $offdays = explode(',',$offdays_row->Offday);
                         else{
                             $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $data['session']['eid']),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                             $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                         }
                            
                     }
                 }
                $row_admn_remark = $this->administrator_model->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => ($userID==NULL) ? $data['session']['id'] : $userID, 'tbl_login_remarks.Date' => $date),'User_Remarks,Admin_Remarks',1);
                $rs = $this->administrator_model->get_Specific_Info('tbl_employee_profile',array('Employee_ID' => $data['session']['eid'],'Is_Exist' => '1'),'RS_ID,Employee_ID',1);
                $RS[] = isset($rs->RS_ID) ? $rs->RS_ID : NULL;
                $ID[] = ($userID==NULL ? $data['session']['id'] : $userID).','.$date;
                $Remarks[] =  isset($row_admn_remark->User_Remarks) ? $row_admn_remark->User_Remarks : NULL;
                $Admin_Remarks[] = isset($row_admn_remark->Admin_Remarks) ? $row_admn_remark->Admin_Remarks : NULL;
                if($row){
                    mdate('%H:%i',strtotime("+".(isset($grace_time->Grace_Time)? $grace_time->Grace_Time+1 : "15")." minutes",strtotime(($row->Office_In!=NULL)? $row->Office_In : $record->In))) <  mdate('%H:%i',strtotime($row->In_Time)) ? $late++ : $ontime++;
                    $InTime[] = mdate('%H:%i',strtotime("+".(isset($grace_time->Grace_Time)? $grace_time->Grace_Time+1 : "15")." minutes",strtotime(($row->Office_In!=NULL)? $row->Office_In : $record->In)))<  mdate('%H:%i',strtotime($row->In_Time)) ? '<font color="#FF0000">'.mdate('%h:%i %a',  strtotime($row->In_Time)).'</font>':mdate('%h:%i %a',  strtotime($row->In_Time));
                    $OutTime[] = $row->Out_Time;
                }elseif($this->is_Holiday($date,isset($rs->Employee_ID) ? $rs->Employee_ID : NULL)){
                    $holiday++;
                    $InTime[] =  '<font color="#5500FF">'.(isset($rs->Employee_ID) ? $this->is_Holiday($date,$rs->Employee_ID) : NULL).'</font>';
                    $OutTime[] = '<font color="#5500FF">Holiday</font>';
                }elseif($this->is_OnLeave($userID==NULL ? $data['session']['id'] :$userID,$date,$result_leave)){
                    $leave++;
                    $InTime[] =  '<font color="#FF7F00">Leave</font>';
                    $OutTime[] = '<font color="#FF7F00">Leave</font>';
                }elseif($this->is_OnTour($date,isset($rs->Employee_ID) ? $rs->Employee_ID : NULL)){
//                    $holiday++;
//                    echo $this->is_OnTour($date,$rs->Employee_ID);
                    $InTime[] =  '<font color="#5500FF">'.(isset($rs->Employee_ID) ? $this->is_OnTour($date,$rs->Employee_ID) : NULL).'</font>';
                    $OutTime[] = '<font color="#5500FF">Official Tour</font>';
                }else{
                    if(in_array($weekcount,$offdays))
                        $offday++;
                    else
                        $absent++;
                    $InTime[] = (in_array($weekcount,$offdays))?'<font color="#009900">Off Day</font>' : '<font color="#0045a8">Absent</font>';
                    $OutTime[] = (in_array($weekcount,$offdays))?'<font color="#009900">Off Day</font>': '<font color="#0045a8">Absent</font>';
                }
                }
                    
            }else{
                
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
                $result = $this->db->distinct()->select('Dept_Name,Location_Name,tbl_user_info.F_Employee_ID,User_ID,tbl_user_info.Full_Name,tbl_employee_profile.Grace_Time,RS_ID,tbl_employee_profile.Join_Date')->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left')->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left')->get_where('tbl_user_info',$array_where)->result();//$this->administrator_model->get_Specific_Info('tbl_user_info',$array_where,'User_ID,Full_Name',2);
                    
                foreach($result as $row_user){
                    $join = strtotime($row_user->Join_Date);
                    $now = strtotime($date);
                    $result_first_login = $this->db->select('Date')->order_by('Date','asc')->get_where('tbl_login_record','F_User_ID ='.$row_user->User_ID, 1)->result();
                    foreach($result_first_login as $first_login_date)
                    $first_login =  strtotime($first_login_date->Date);
                    if($now >= $join){
                    $record_supervisor = $this->db->distinct()->select('Full_Name')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => $row_user->F_Employee_ID))->row();
                    $Supervisor[] = isset($record_supervisor->Full_Name) ? $record_supervisor->Full_Name : NULL;
                    $weekcount=mdate('%w',  strtotime($date));  
                    $record=$this->administrator_model->get_Specific_Info('tbl_office_time',array('F_Employee_ID' =>$row_user->F_Employee_ID,'Weekday'=> $weekcount),'In,Out',1);
                    $Date[] = $this->administrator_model->systemDateFormatConverter($date);
                    
                    $weekday = getdate(strtotime($date));
                    $Weekdays[]= $weekday['weekday'];
                    $RS[] = $row_user->RS_ID;
                    $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                    $Location[] = $row_user->Location_Name;
                    $Dept[] = $row_user->Dept_Name;
                        
                        
                    $result_leave = $this->administrator_model->get_Specific_Info('tbl_leave_record',array('Is_Exist'=>1,'Is_Processed' => 1,'F_User_ID'=> $row_user->User_ID,'DATE_FORMAT(tbl_leave_record.From_Date,"%Y")' => $from==NULL? mdate('%Y', now()) : mdate('%Y', strtotime($from))),'From_Date,To_Date',2);
                        
                    $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                    $Office_Time[] = mdate('%h:%i %a',strtotime(($row)? $row->Office_In : $record->In)/*strtotime($record->In)*/).' - '.mdate('%h:%i %a',strtotime($record->Out));
                    /*                    if($row){
//                        echo 'row found';
                        if(is_null($row->Offday)){
//                            echo 'offday mission';
                            $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                            $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                        }else{
//                            echo 'offday found';
                            $buffer = $offdays =  explode(',',$row->Offday);
                        }
                    }else{
//                        echo 'defalut';
//                        if(isset($buffer)){
//                        $offdays = $buffer;
//                        }else{
                        $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                        $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                    
//                        }
                        }*/
                    if($date>mdate('%Y-%m-%d')){
                        $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                        $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                    }else{
                        $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => $date),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                        if($row){
                            $offdays = explode(',',$row->Offday);
                        }else{
                            $offdays_row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('-1 day', strtotime($date)))),'F_User_ID,Offday',1);
                            if($offdays_row)
                                $offdays = explode(',',$offdays_row->Offday);
                            else{
                                $row_offday=$this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $row_user->F_Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                                $offdays=$this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                            }
//                            $offdays = $this->globals->getOffday($date,$row_user->User_ID,1);
                        }
                    }
                    $row_admn_remark = $this->administrator_model->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => $row_user->User_ID, 'tbl_login_remarks.Date' => $date),'User_Remarks,Admin_Remarks',1);
                    $ID[] = isset($row_user->User_ID) && isset($date) ? ($row_user->User_ID.','.$date) : NULL;
                    $Remarks[] = isset($row_admn_remark->User_Remarks) ? $row_admn_remark->User_Remarks : NULL;
                    $Admin_Remarks[] = isset($row_admn_remark->Admin_Remarks) ? $row_admn_remark->Admin_Remarks : NULL;
                    if($row){
                        mdate('%H:%i',strtotime("+".(isset($row_user->Grace_Time)? $row_user->Grace_Time+1 : "15")." minutes",strtotime(($row->Office_In!=NULL)? $row->Office_In : $record->In))) <  mdate('%H:%i',strtotime($row->In_Time)) ? $late++ : $ontime++;
                        $InTime[] = mdate('%H:%i',strtotime("+".(isset($row_user->Grace_Time)? $row_user->Grace_Time+1 : "15")." minutes",strtotime(($row->Office_In!=NULL)? $row->Office_In : $record->In)))<  mdate('%H:%i',strtotime($row->In_Time)) ? '<font color="#FF0000">'.mdate('%h:%i %a',  strtotime($row->In_Time)).'</font>':mdate('%h:%i %a',  strtotime($row->In_Time));
                        $OutTime[] = $row->Out_Time;
                    }elseif($this->is_Holiday($date,$row_user->F_Employee_ID ? $row_user->F_Employee_ID : NULL)){
                        $holiday++;
                        $InTime[] =  '<font color="5500FF">'.$this->is_Holiday($date,$row_user->F_Employee_ID).'</font>';
                        $OutTime[] = '<font color="5500FF">Holiday</font>';
                    }elseif($this->is_OnLeave($row_user->User_ID,$date,$result_leave)){
                        $leave++;
                        $InTime[] =  '<font color="FF7F00">Leave</font>';
                        $OutTime[] = '<font color="FF7F00">Leave</font>';
                    }elseif($this->is_OnTour($date,isset($row_user->F_Employee_ID) ? $row_user->F_Employee_ID : NULL)){
//                    $holiday++;
                    $InTime[] =  '<font color="#ff3b00">'.(isset($row_user->F_Employee_ID) ? $this->is_OnTour($date,$row_user->F_Employee_ID) : NULL).'</font>';
                    $OutTime[] = '<font color="#ff3b00">Official Tour</font>';
                    }else{
                        if(in_array($weekcount,$offdays))
                            $offday++;
                        else{
                            if($now >= $first_login)$absent++;
                        }
                        $InTime[] = (in_array($weekcount,$offdays))?'<font color="#009900">Off Day</font>' : (($now >= $first_login) ? '<font color="#003bff">Absent</font>' : 'No record');
                        $OutTime[] = (in_array($weekcount,$offdays))?'<font color="#009900">Off Day</font>': (($now >= $first_login) ? '<font color="#003bff">Absent</font>' : 'No record');
                    }
                }
                }
            }
        }
            
        $data['field'] = array('ID' => $ID,'Date' => $Date,'Weekdays' => $Weekdays,'In Time' => $InTime,'Out Time' => $OutTime,'User Remarks' => $Remarks,'Admin Remarks' => $Admin_Remarks);
        if($companyID != NULL){
            unset($data['field']['Employee ID']);
            $data['field'] = array_slice($data['field'], 0, 1, true) +
            array('Name' => $Name,'Employee ID' => $RS,'Location' => $Location, 'Department' => $Dept,'Office Time' => $Office_Time,'Supervisor' => $Supervisor) +
            array_slice($data['field'], 1, count($data['field']) - 1, true) ;
        }
            
        $row = $this->db->distinct()->select('All_Report')->from('tbl_user_type')->join('tbl_login_info', 'tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID')->where(array('tbl_login_info.Is_Exist' => 1, 'tbl_user_type.Is_Exist' => 1, 'tbl_login_info.F_User_ID' => $data['session']['id']))->limit(1)->get()->row();
            
        $data['other_fields']=($data['session']['type']=="Super Administrator" || $data['session']['type']=="Administrator" || $row->All_Report == 1)?array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search'))):array('From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date'),'id'=>'from_date')),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date'),'id'=>'to_date')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset( $data['other_fields']['Supervisor']);
                
        $data['footer'] = array('Ontime:'.$ontime,'Late:'.$late,'Absent:'.$absent,'Offday:'.$offday,'Leave:'.$leave,'Holiday:'.$holiday);
        return $data;
    }
        
    public function load_LateReportData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        
        $Date = $Weekdays = $InTime = $Name = $Remarks = $Admin_Remarks = $RS = $Dept = $Office_Time = $Location = $Supervisor = NULL;
        $this->javascript->ready('waitForMsg();waitForMsgRecruit();');
        $this->javascript->compile();
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
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
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
//        $js .= "$('#dept').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var dept_id = $('#dept').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User')."/' + dept_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
//          $js .= "$('#location').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var loc_id = $('#location').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByLocation')."/' + loc_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
        $this->javascript->ready($js);
        $ontime = $late =  $offday = $leave = $holiday = $absent = 0;
        $columnDef='{"orderable": true, "targets": 0 }';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 1, "asc" ]]');
//        $this->administrator_model->load_TableSorter(array('grid'));
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/late_report';
        $data['title'] = 'Late Report';
        $data['width'] = '1280';
        $data['height'] = '480';
        $data['elements'] = array('users');
        if($from!=NULL || $to!=NUll || $companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
            
            $array_where['tbl_user_info.Is_Exist'] = '1' ;
            $array_where['tbl_employee_profile.Is_Exist'] = '1' ;
            if($data['session']['type'] == "Administrator" || $data['session']['type'] == "Super Administrator"){
//                $companyID =1;
                if($deptID == NULL){
                    $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    if($userID != NULL)
                        $array_where['User_ID'] = $userID;
                }else if($userID == NULL){
                    if($companyID != NULL)
                        $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    $array_where['F_Dept_ID'] = $deptID;
                }
                else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
                    
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
            }
                $this->db->distinct()->select('tbl_dept_info.Dept_Name,tbl_location_info.Location_Name,tbl_user_info.Full_Name,tbl_employee_profile.Grace_Time,tbl_employee_profile.RS_ID,tbl_user_info.User_ID,tbl_login_record.In_Time,tbl_login_record.Date,tbl_office_time.`In`,tbl_office_time.`Out`,(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS Supervisor')
                ->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','inner')
                ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','inner')
                ->join('tbl_login_record','tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner')
                ->join('tbl_office_time','tbl_employee_profile.Employee_ID = tbl_office_time.F_Employee_ID','inner');
                if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor"){
                    $condition = 'tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID ';
                    if($companyID != NULL) $condition .= ' AND tbl_employee_profile.F_Company_ID = '.$companyID;
                    if($deptID != NULL) $condition .= ' AND tbl_employee_profile.F_Dept_ID = '.$deptID;
                    if($userID != NULL)  $condition .= ' AND tbl_user_info.User_ID = '.$userID;
                    $this->db->join('tbl_hierarchy_info',$condition,'left');
                    $this->db->where("(`Supervisor1_ID` = ".$data['session']['eid']." OR `Supervisor2_ID` = ".$data['session']['eid']." OR `Supervisor3_ID` = ".$data['session']['eid'].")");                
                }else{
                 $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner');
                }        
                    
                $result = $this->db->where('tbl_login_record.Date BETWEEN \''.$from.'\' AND \''.$to.'\' AND tbl_office_time.Weekday = DATE_FORMAT(tbl_login_record.Date,"%w") AND IF(tbl_login_record.Office_In <> NULL,ADDTIME(tbl_office_time.`In`,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60)),ADDTIME(tbl_login_record.Office_In,SEC_TO_TIME((tbl_employee_profile.Grace_Time*60)+60))) < tbl_login_record.`In_Time`')
                ->get_where('tbl_user_info',$array_where)->result();
                foreach($result as $row_user){
                    $weekcount=mdate('%w',  strtotime($row_user->Date));  
                    $Supervisor[] = isset($row_user->Supervisor) ? $row_user->Supervisor : NULL;
                    $Date[] = $this->administrator_model->regularDateFormatConverter($row_user->Date);
                    $Office_Time[] = mdate('%h:%i %a',strtotime($row_user->In)).' - '.mdate('%h:%i %a',strtotime($row_user->Out));
                    $weekday = getdate(strtotime($row_user->Date));
                    $Weekdays[]= $weekday['weekday'];
                    $RS[] = $row_user->RS_ID;
                    $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                    $Location[] = $row_user->Location_Name;
                    $Dept[] = $row_user->Dept_Name;
                        
                    $row_admn_remark = $this->administrator_model->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => $row_user->User_ID, 'tbl_login_remarks.Date' => $row_user->Date),'User_Remarks,Admin_Remarks',1);
                    $Remarks[] = isset($row_admn_remark->User_Remarks) ? $row_admn_remark->User_Remarks : NULL;
                    $Admin_Remarks[] = isset($row_admn_remark->Admin_Remarks) ? $row_admn_remark->Admin_Remarks : NULL;
                        
                        
                        
                    $InTime[] = mdate('%h:%i %a',  strtotime($row_user->In_Time));
                        
                }
       }
           
        $data['field'] = array('Date' => $Date,'Employee ID' => $RS,'Weekdays' => $Weekdays,'In Time' => $InTime,'User Remarks' => $Remarks,'Admin Remarks' => $Admin_Remarks);
            
        if($companyID != NULL){
            unset($data['field']['Employee ID']);
            $data['field'] = array_slice($data['field'], 0, 1, true) +
            array('Name' => $Name,'Employee ID' => $RS,'Location' => $Location, 'Department' => $Dept,'Office Time' => $Office_Time,'Supervisor' => $Supervisor) +
            array_slice($data['field'], 1, count($data['field']) - 1, true) ;
                
        }
            
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:12em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset( $data['other_fields']['Supervisor']);
        return $data;
    }
        
    public function load_HolidayReportData($companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        
       $Name = $Holiday_List = $RS = $Dept = $Location = $Total = NULL;
        $this->javascript->ready('waitForMsg();waitForMsgRecruit();');
        $this->javascript->compile();
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
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
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
        $this->javascript->ready($js);
        $ontime = $late =  $offday = $leave = $holiday = $absent = 0;
        $columnDef='{"orderable": true, "targets": 0 }';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 1, "asc" ]]');
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/holiday_report';
        $data['title'] = 'Holiday Report';
        $data['width'] = '1280';
        $data['height'] = '480';
        $data['elements'] = array('users');
        if($companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
            
//            $array_where['tbl_holiday_info.Is_Exist'] = '1' ;
            $array_where['tbl_employee_profile.Is_Exist'] = '1' ;
            if($data['session']['type'] == "Administrator" || $data['session']['type'] == "Super Administrator"){
//                $companyID =1;
                if($deptID == NULL){
                    $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    if($userID != NULL)
                        $array_where['User_ID'] = $userID;
                }else if($userID == NULL){
                    if($companyID != NULL)
                        $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    $array_where['F_Dept_ID'] = $deptID;
                }
                else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
                    
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
            }
                $this->db->_protect_identifiers=false;
                $this->db->distinct()->select('tbl_dept_info.Dept_Name,tbl_location_info.Location_Name,tbl_employee_profile.Full_Name,tbl_employee_profile.RS_ID,(SELECT GROUP_CONCAT(CONCAT(`Holiday_Name`,"(",DATEDIFF(To_Date,From_Date)+1),")") FROM tbl_holiday_info WHERE FIND_IN_SET(tbl_employee_profile.Employee_ID,Employee)) AS Names,(SELECT SUM(DATEDIFF(To_Date,From_Date)+1)  FROM `tbl_holiday_info` WHERE FIND_IN_SET(tbl_employee_profile.Employee_ID,Employee)) AS Total')
//                ->join('tbl_holiday_info','FIND_IN_SET(tbl_employee_profile.Employee_ID,Employee)','left',FALSE)
                ->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','inner')
                ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','inner');
                if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor"){
                    $condition = 'tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID ';
                    if($companyID != NULL) $condition .= ' AND tbl_employee_profile.F_Company_ID = '.$companyID;
                    if($deptID != NULL) $condition .= ' AND tbl_employee_profile.F_Dept_ID = '.$deptID;
                    if($userID != NULL)  $condition .= ' AND tbl_user_info.User_ID = '.$userID;
                    $this->db->join('tbl_hierarchy_info',$condition,'left');
                    $this->db->where("(`Supervisor1_ID` = ".$data['session']['eid']." OR `Supervisor2_ID` = ".$data['session']['eid']." OR `Supervisor3_ID` = ".$data['session']['eid'].")");                
                }else{
                 $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner');
                }        
                    
                $result = $this->db->get_where('tbl_user_info',$array_where)->result();
                foreach($result as $row_user){
//                    $weekcount=mdate('%w',  strtotime($row_user->Date));  
//                    $Supervisor[] = isset($row_user->Supervisor) ? $row_user->Supervisor : NULL;
//                    $Date[] = $this->administrator_model->regularDateFormatConverter($row_user->Date);
//                    $Office_Time[] = mdate('%h:%i %a',strtotime($row_user->In)).' - '.mdate('%h:%i %a',strtotime($row_user->Out));
//                    $weekday = getdate(strtotime($row_user->Date));
//                    $Weekdays[]= $weekday['weekday'];
                    $RS[] = $row_user->RS_ID;
                    $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                    $Location[] = $row_user->Location_Name;
                    $Dept[] = $row_user->Dept_Name;
                    $Holiday_List[] = $row_user->Names;
                    $Total[] = $row_user->Total;
                        
                }
       }
           
        $data['field'] = array('Name' => $Name,'Employee ID' => $RS,'Location' => $Location, 'Department' => $Dept,'Holidays' => $Holiday_List,'Total Days' => $Total);
            
            
            
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:12em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        //,'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12))
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset( $data['other_fields']['Supervisor']);
        return $data;
    }
        
    public function load_LeaveReportData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        $Date = $Name = $RS = $Dept = $Location = $Supervisor = $Reason = $From_Date = $To_Date = $Day = $Status = NULL;
        $columnDef='{"orderable": true, "targets": 0},{"width": \'8%\', "targets": [1,2,3,6,7]} , {"width": \'12%\', "targets": [0,4]} , {"width": \'20%\', "targets": 6}';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 1, "asc" ]]');
        $this->javascript->ready('waitForMsg();waitForMsgRecruit();');
        $this->javascript->compile();
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
//        $js .= "$('#company').change(change_dept);";
//        $js .= "$('#company').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var company_id = $('#company').val();; 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
//        $js .= "$('#dept').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var dept_id = $('#dept').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User')."/' + dept_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
//         $js .= "$('#location').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var loc_id = $('#location').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByLocation')."/' + loc_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
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
        $this->javascript->ready($js);
        $ontime = $late =  $offday = $leave = $holiday = $absent = 0;
            
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/leave_report';
        $data['title'] = 'Leave Report';
        $data['width'] = '1280';
        $data['height'] = '480';
        $data['elements'] = array('users');
        if($from!=NULL || $to!=NUll || $companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
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
                }
                else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }else{
                    if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
                        $array_where['Supervisor1_ID'] = $data['session']['eid'];
                }
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
                    
                $result = $this->db->distinct()->select('Dept_Name,Location_Name,tbl_user_info.Full_Name,RS_ID,User_ID,(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS Supervisor,From_Date,To_Date,Leave_Reason,`Day`,tbl_leave_record.Is_Void,tbl_leave_record.Is_Processed')
                ->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')
                ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left')
                ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left')
                ->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                ->join('tbl_leave_record','tbl_leave_record.F_User_ID = tbl_user_info.User_ID','inner')
//                ->where('(DATE_FORMAT("%Y",tbl_leave_record.From_Date) BETWEEN \''.mdate('%Y',  strtotime($from)).'\' AND \''.mdate('%Y',  strtotime($to)).'\' OR DATE_FORMAT("%Y",tbl_leave_record.To_Date) BETWEEN \''.mdate('%Y',  strtotime($from)).'\' AND \''.mdate('%Y',  strtotime($to)).'\')')
                ->get_where('tbl_user_info',$array_where)->result();
                $date_array = $this->administrator_model->showDates($from, $to);
                foreach($result as $row_user){
                    //$date_array = $this->administrator_model->showDates($row_user->From_Date, $row_user->To_Date);
                 
                    //if(in_array($from, $date_array) || in_array($to, $date_array)){
                    if(in_array($row_user->From_Date, $date_array) || in_array($row_user->To_Date, $date_array)){
                        $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                        $RS[] = $row_user->RS_ID;
                        $Dept[] = $row_user->Dept_Name;
                        $Location[] = $row_user->Location_Name;
                        $Supervisor[] = isset($row_user->Supervisor) ? $row_user->Supervisor : NULL;
                        $Reason[] = $row_user->Leave_Reason;
                        $From_Date[] = $row_user->From_Date;
                        $To_Date[] = $row_user->To_Date;
                        $Day[] = $row_user->Day;  
                        $Status[] = ($row_user->Is_Processed) ? $this->lang->line('granted'):(($row_user->Is_Void)?$this->lang->line('rejected'):$this->lang->line('pending'));
                    }
                }
        }
            
        $data['field'] = array('Name' => $Name,'Employee ID' => $RS,'Department' => $Dept,'Location' => $Location,'Supervisor' => $Supervisor,'Reason' => $Reason,'From' => $From_Date, 'To' => $To_Date,'Day' => $Day,'Status' => $Status);
            
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset( $data['other_fields']['Supervisor']);
        return $data;
    }
        
    public function load_AbsentReportData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
        
        $Date = $Weekdays = $InTime = $Name = $Remarks = $Admin_Remarks = $RS = $Dept = $Office_Time = $Location = $Supervisor = $Remarks = $Admin_Remarks =NULL;
        $this->table_to_excel->load_Script('download_button','grid','noExl','Report','[1,2,3,4,5,6,7,8,9]');
        $this->javascript->ready('waitForMsg();waitForMsgRecruit();');
        $this->javascript->compile();
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
//        $js .= "$('#company').change(change_dept);";
//        $js .= "$('#company').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var company_id = $('#company').val();; 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
//        $js .= "$('#dept').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var dept_id = $('#dept').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User')."/' + dept_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
//          $js .= "$('#location').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var loc_id = $('#location').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByLocation')."/' + loc_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
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
        $this->javascript->ready($js);
        $ontime = $late =  $offday = $leave = $holiday = $absent = 0;
        $columnDef='{"orderable": true, "targets": 0 }';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 1, "asc" ]]');
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/absent_report';
        $data['title'] = 'Absent Report';
        $data['width'] = '1280';
        $data['height'] = '480';
        $data['elements'] = array('users');
        if($from!=NULL || $to!=NUll || $companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
            
            $array_where['tbl_user_info.Is_Exist'] = '1' ;
            $array_where['tbl_employee_profile.Is_Exist'] = '1' ;
            if($data['session']['type'] == "Administrator" || $data['session']['type'] == "Super Administrator"){
//                $companyID =1;
                if($deptID == NULL){
                    $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    if($userID != NULL)
                        $array_where['User_ID'] = $userID;
                }else if($userID == NULL){
                    if($companyID != NULL)
                        $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;
                    $array_where['F_Dept_ID'] = $deptID;
                }
                else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
            }
            if($locID != NULL){
                $array_where['Location_ID'] = $locID; 
            }
                $this->db->distinct()->select('tbl_employee_profile.Join_Date,Dept_Name,Location_Name,tbl_user_info.Full_Name,RS_ID,User_ID,(SELECT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS Supervisor')
                ->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','inner')
                ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','inner');
//                ->join('tbl_login_record','tbl_user_info.User_ID = tbl_login_record.F_User_ID','inner')
//                ->join('tbl_office_time','tbl_employee_profile.Employee_ID = tbl_office_time.F_Employee_ID','inner');
                if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor"){
                    $condition = 'tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID ';
                    if($companyID != NULL) $condition .= ' AND tbl_employee_profile.F_Company_ID = '.$companyID;
                    if($deptID != NULL) $condition .= ' AND tbl_employee_profile.F_Dept_ID = '.$deptID;
                    if($userID != NULL)  $condition .= ' AND tbl_user_info.User_ID = '.$userID;
                    $this->db->join('tbl_hierarchy_info',$condition,'left');
                    $this->db->where("(`Supervisor1_ID` = ".$data['session']['eid']." OR `Supervisor2_ID` = ".$data['session']['eid']." OR `Supervisor3_ID` = ".$data['session']['eid'].")");
//                  
                }else{
                 $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
                }        
//                ->join('tbl_login_remarks','tbl_user_info.User_ID = tbl_login_remarks.F_User_ID','inner')
                //tbl_login_record.Date = tbl_login_remarks.Date AND
//                $this->db->where('DATE_FORMAT(tbl_employee_profile.Join_Date,"%Y-%m-%d") < DATE_FORMAT(CURDATE(),"%Y-%m-%d")');
                $result = $this->db->get_where('tbl_user_info',$array_where)->result();
                foreach($result as $row_user){
                    $weekcount= mdate('%w',  strtotime(now()));    
                    $record = $this->administrator_model->get_User_Inforamtion(array('User_ID' => $row_user->User_ID,'Weekday' => $weekcount));
                    $offdays_row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('- day',now()))),'F_User_ID,Offday',1);
                  
                    $offdays = $this->getOffdays(array($record->Sun,$record->Mon,$record->Tue,$record->Wed,$record->Thu,$record->Fri,$record->Sat));
                    
                    $offdays_date_array = $this->getoffdaysDate($offdays,$from,$to); 
                    $absent_array = $this->absentReport($row_user->User_ID, $offdays_date_array, $from, $to);
                        
                    $result_first_login = $this->db->select('Date')->order_by('Date','asc')->get_where('tbl_login_record','F_User_ID ='.$row_user->User_ID, 1)->result();
                    foreach($result_first_login as $first_login_date)
                    $first_login =  strtotime($first_login_date->Date);
                        
                    if(!is_null($absent_array))
                    foreach ($absent_array as $absent){
                        if(!is_null($absent) && strtotime($row_user->Join_Date) <  strtotime($absent)){  
                            if(strtotime($absent) >= $first_login){
                                $Date[] = $absent;
                                $RS[] = $row_user->RS_ID;
                                $Supervisor[] = isset($row_user->Supervisor) ? $row_user->Supervisor : NULL;
                                $Name[] = $row_user->Full_Name;//.'['.$row_user->RS_ID.']'
                                $Location[] = $row_user->Location_Name;
                                $Dept[] = $row_user->Dept_Name;
                                $row_admn_remark = $this->administrator_model->get_Specific_Info('tbl_login_remarks',array('F_User_ID' => $row_user->User_ID, 'tbl_login_remarks.Date' => $absent),'User_Remarks,Admin_Remarks',1);
                                $Remarks[] =  isset($row_admn_remark->User_Remarks) ? $row_admn_remark->User_Remarks : NULL;
                                $Admin_Remarks[] = isset($row_admn_remark->Admin_Remarks) ? $row_admn_remark->Admin_Remarks : NULL;
                            }
                        }
                    }
                        
                }
       }
           
        $data['field'] = array('Date' => $Date,'Name' => $Name,'Employee ID' => $RS,'Location' => $Location, 'Department' => $Dept,'Supervisor' => $Supervisor,'User Remarks' => $Remarks, 'Admin Remarks' => $Admin_Remarks); 
            
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser(),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset( $data['other_fields']['Supervisor']);
        return $data;
    }
        
    public function load_StaffUnderSVReportData($companyID,$supID=NULL,$locID=NULL){
        $Name = $Company = $Dept = $Designation = $Location = $Join_Date = $Email = $Office_Contact = $Emergency_Contact = $Address = $Permanent_Address = $Leave = NULL;
        $js = 'waitForMsg();waitForMsgRecruit();';
        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
        $this->javascript->ready($js);
        $this->javascript->compile();
        $columnDef='{"orderable": true, "targets": 0 }';
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 0, "asc" ]]');
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/staffUnderSV_report';
        $data['title'] = 'Staffs Under Supervision Report';
        $data['width'] = '1280';
        $data['height'] = '480';
        if($data['session']['type'] == "Supervisor")
           $supID = $data['session']['eid']; 
        if($data['session']['type'] == "Administrator"){
            if($locID != NULL){
                $array_where['Location_ID'] = $locID; 
            }   
        }
        //if($supID !=NULL){
//            if($companyID !=NULL)
//            $array_where['tbl_employee_profile.F_Company_ID'] = $companyID;    
                
            $array_where['tbl_employe_list_info.Is_Exist'] = '1' ;
            if($data['session']['type'] == "Administrator"){
            if($supID !=NULL)
            $array_where['Supervisor1_ID'] = $supID; 
            }    
            $this->db->distinct()->select('Leave_Taken,tbl_employe_list_info.Employee_ID,tbl_employe_list_info.User_ID,view_employee.Full_Name,view_employee.Nick_Name,view_employee.RS_ID,view_employee.Company_Name,view_employee.Dept_Name,view_employee.Designation,view_employee.Location_Name,view_employee.Join_Date,view_employee.Email,view_employee.Emergency_Contact,view_employee.Office_Contact,tbl_employe_list_info.Leave,tbl_employe_list_info.P_Leave,tbl_employe_list_info.Designation');
//            ->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','inner')
//            ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','inner')
//            ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','inner')          
//            ->join('tbl_user_info','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner')
            $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = view_employee.Employee_ID','left');
//            $this->db->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_employe_list_info.Employee_ID','left');
//            ->join('tbl_designation_info','tbl_designation_info.Designation_ID = tbl_hierarchy_info.F_Designation_ID','left');
//            $this->db->from('view_employee');
            $this->db->join('tbl_employe_list_info','tbl_employe_list_info.Employee_ID = view_employee.Employee_ID','left');
                    if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            $this->db->where("(`Supervisor1_ID` = ".$supID." OR `Supervisor2_ID` = ".$supID." OR `Supervisor3_ID` = ".$supID.")");  
            $result = $this->db->get_where('view_employee',$array_where)->result();//tbl_employee_profile
            foreach($result as $row_user){
                
                $Name[] = $row_user->Full_Name.'('.$row_user->Nick_Name.')'.'['.$row_user->RS_ID.']';
                $Company[] = $row_user->Company_Name;
                $Dept[] = $row_user->Dept_Name;
                $Designation[] = $row_user->Designation;
                $Location[] = $row_user->Location_Name;
                $Join_Date[] = $row_user->Join_Date;
                $Email[] = $row_user->Email;
                $Office_Contact[] = $row_user->Office_Contact;
                $Emergency_Contact[] = $row_user->Emergency_Contact;
//                $Leave[] = $row_user->Leave - ($this->administrator_model->leaveCount($row_user->User_ID,1)+$this->totalHolidayCount($row_user->Employee_ID));
/*                
                $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $row_user->User_ID, 'tbl_login_record.Date' => $this->administrator_model->systemDateFormatConverter(now())),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
                if($row){
                    if(is_null($row->Offday)){
                        $row_offday = $this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' => $row_user->Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                        $offdays = $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                    }else{
                        $offdays =  explode(',',$row->Offday);
                    }
                }else{
                    $row_offday = $this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' => $row_user->Employee_ID),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                    $offdays = $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
                }
               $from = mdate('%Y-01-01');
                $to = mdate('%Y-%m-%d');
                $offdays_date_array = $this->getoffdaysDate($offdays,$from,$to);            
                $absent_array = $this->absentReport($row_user->User_ID, $offdays_date_array, $from, $to);    
*/
//                $Leave[] = $row_user->P_Leave - ($this->administrator_model->leaveCount($row_user->User_ID,1)+count($absent_array));//+$this->totalHolidayCount($row_user->Employee_ID)
            
                $Leave[] = $row_user->P_Leave - $row_user->Leave_Taken;
            }                        
        //}
            
        $data['field'] = array('Name' => $Name,'Company' => $Company,'Department' => $Dept,'Designation' => $Designation, 'Location' => $Location, 'Join Date' => $Join_Date,'Email' => $Email,'Official Contact' => $Office_Contact,'Emergency Contact' => $Emergency_Contact,'Remaining Leave' => $Leave);
            
        if($data['session']['type'] == "Administrator" || $data['session']['type'] == "Super Administrator")
            $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:12em;"')),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:12em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        return $data;
    }
        
    public function load_BasicInfoReportData($companyID,$deptID,$userID,$supID,$locID){
        $Full_Name= $Email = $Emergency_Contact = $Office_Contact = $Company = $Location = $Dept = $Employee_ID = $Join_Date = $NID = $Leave = $Status = $Gender = $DOB = $Blood_Group = $Height = $Weight = $Present_Address = $Permamemt_Address = $Passport = $Passport_Issue = $Passport_Expiry = NULL;
        $data['session'] = $this->session->userdata('logged_in');
        $columnDef='{"width": \'15%\', "targets": [0,3,17,18] },{"width": \'10%\', "targets": [2,7,8] },{"width": \'8%\', "targets": [1,5,9,14] },{"width": \'12%\', "targets": [4,10] },{"width": \'2%\', "targets": [11,12,15,16] },{"visible": false, "targets": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21]}';
        $others = "  initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=\"\"></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                            
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
                        
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value=\"'+d+'\">'+d+'</option>' )
                } );
            } );
        }";
            
        $this->datatable->load_Script('grid',',\'columnsToggle\'',$columnDef,$others);
            
        $js = 'waitForMsg();waitForMsgRecruit();';
        $js .= "function change_dept() { 
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
            
        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
                    
        $this->javascript->ready($js);
        $this->javascript->compile(); 
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'home/basicInfo_report';
        $data['title'] = 'Basic Information Report';
        $data['width'] = '1280';
        $data['height'] = '500';
            
        if($data['session']['type'] == "Supervisor")
        $supID = $data['session']['eid']; 
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
                if($data['session']['type'] == "Administrator"){      
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
                }  
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
                
                $this->db->distinct();
                $this->db->select('User_ID,Document,Office_Contact,Job_Desc,Photo,Grace_Time,Employee_ID,tbl_employee_profile.F_Company_ID,Company_Name,F_Location_ID,Location_Name,F_Dept_ID,Dept_Name,RS_ID,tbl_employee_profile.Full_Name,Nick_Name,Gender,DOB,Father,Mother,tbl_employee_profile.Email,tbl_employee_profile.Join_Date,Emergency_Contact,Blood_Group,Height,Weight,Marital_Status,
                Identification_Mark,Training,NID,Passport,Passport_Issue_Date,Passport_Expiry_Date,Permanent_Address,tbl_employee_profile.Address,Previous_Organization,tbl_employee_profile.`Leave`,tbl_employee_profile.Is_Exist,
                tbl_work_days.Sun,tbl_work_days.Mon,tbl_work_days.Tue,tbl_work_days.Wed,tbl_work_days.Thu,tbl_work_days.Fri,tbl_work_days.Sat');
                $this->db->join('tbl_work_days','tbl_employee_profile.Employee_ID = tbl_work_days.F_Employee_ID','left');
                $this->db->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left');
                $this->db->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left');
                $this->db->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left');
                $this->db->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
        

    
                if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor"){
                    $condition = 'tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID ';
                    if($companyID != NULL) $condition .= ' AND tbl_employee_profile.F_Company_ID = '.$companyID;
                    if($deptID != NULL) $condition .= ' AND tbl_employee_profile.F_Dept_ID = '.$deptID;
                    if($userID != NULL)  $condition .= ' AND tbl_user_info.User_ID = '.$userID;
                    $this->db->join('tbl_hierarchy_info',$condition,'left');
                    $this->db->where("(`Supervisor1_ID` = ".$data['session']['eid']." OR `Supervisor2_ID` = ".$data['session']['eid']." OR `Supervisor3_ID` = ".$data['session']['eid'].")");
                }else
                    $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
//                    $this->db->join('tbl_designation_info','tbl_hierarchy_info.F_Designation_ID = tbl_designation_info.Designation_ID','inner');
                $this->db->where($array_where);
                $query=$this->db->get('tbl_employee_profile');
                $result =  $query->result();
//        $result = $this->get_Employee_Information($array_where);
        foreach($result as $row){
            $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
            $Email[] = $row->Email;
            $Emergency_Contact[] = $row->Emergency_Contact;
            $Office_Contact[] = $row->Office_Contact;
            $Company[] = $row->Company_Name;
            $Location[] = $row->Location_Name;
            $Dept[] = $row->Dept_Name;
            $Employee_ID[] = $row->RS_ID;
            $Join_Date[] = mdate('%d-%m-%Y',strtotime($row->Join_Date));
            $NID[] = $row->NID;
            $Leave[] = $row->Leave;
            $Status[] = $row->Is_Exist ? '<font color="#009900">Active</font>' : '<font color="#FF0000">Inactive</font>';
            $Gender[] = $row->Gender;
            $DOB[] = $row->DOB;
            $Blood_Group[] = $row->Blood_Group; 
            $Height[] = $row->Height;
            $Weight[] = $row->Weight;
            $Present_Address[] = $row->Address;
            $Permamemt_Address[] = $row->Permanent_Address;
            $Passport[] = $row->Passport;
            $Passport_Issue[] =  $row->Passport_Issue_Date!='0000-00-00' ? mdate('%d-%m-%Y',strtotime($row->Passport_Issue_Date)) : NULL;
            $Passport_Expiry[] = $row->Passport_Expiry_Date!='0000-00-00' ? mdate('%d-%m-%Y',strtotime($row->Passport_Expiry_Date)) : NULL;
        }
        $data['field'] = array('Name' => $Full_Name,'Employee ID' => $Employee_ID,'National ID' => $NID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Email' => $Email,'Emergency Contact' => $Emergency_Contact,'Official Contact' => $Office_Contact,'Join Date' => $Join_Date,'Leave' => $Leave,'Mode' => $Status,'Gender' => $Gender,'Date of Birth' => $DOB,'Blood Group' => $Blood_Group,'Height' => $Height, 'Weight' => $Weight,'Present Address' => $Present_Address, 'Permanent Address' => $Permamemt_Address,'Passport No' => $Passport,'Passport Issue Date' => $Passport_Issue,'Pasport Expiry Date' => $Passport_Expiry);
        $row = $this->db->distinct()->select('All_Report')->from('tbl_user_type')->join('tbl_login_info', 'tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID')->where(array('tbl_login_info.Is_Exist' => 1, 'tbl_user_type.Is_Exist' => 1, 'tbl_login_info.F_User_ID' => $data['session']['id']))->limit(1)->get()->row();
        if($data['session']['type']=="Super Administrator" || $data['session']['type']=="Administrator" || $row->All_Report == 1)
        $data['other_fields'] = array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:12em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:12em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:12em;"')),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset($data['other_fields']['Supervisor']);
        return $data;
    }
        
    public function load_InactiveUserReportData($from,$to,$companyID,$deptID,$userID,$supID,$locID){
        $in = $out = 0;
        $Full_Name = $Company = $Location = $Dept = $Employee_ID = $Join_Date = $Inactive_Date = NULL;
        $data['session'] = $this->session->userdata('logged_in');
        $columnDef='{"width": \'15%\', "targets": [0] },{"width": \'10%\', "targets": [2,3] },{"width": \'8%\', "targets": [1,4,5,6] }';
        $this->datatable->load_Script('grid',NULL,$columnDef,NULL);
            
        $js = 'waitForMsg();waitForMsgRecruit();';
        $js .= "function change_dept() { 
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
//        $js .= "function change_user() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var company_id = $('#company').val();; 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User_ByCompany')."/' + company_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                }";
//        if($this->input->post('company_ID'))
//        $js .= "change_user();";//change_dept();
//        $js .= "$('#company').change(change_dept);";
//        $js .= "$('#company').change(change_user);";
//
//        $js .= "$('#dept').change(function() { 
//                    $('#users > option').remove(); 
//                    $('#users').append(\"<option value= ''>All User</option>\");
//                    var dept_id = $('#dept').val(); 
//                    $.ajax({
//                        type: 'GET',
//                        url: '".base_url('/home/get_User')."/' + dept_id, 
//
//                        success: function(data) 
//                        {  
//                            var obj = jQuery.parseJSON(data);
//                            $.each(obj, function(i, obj)
//                            {   
//                                var opt = $('<option />'); 
//                                opt.val(obj.User_ID);
//                                opt.text(obj.Full_Name);
//                                $('#users').append(opt);
//                            });
//                        }
//
//                    });
//
//                });";
//        
         $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
                    
        $this->javascript->ready($js);
        $this->javascript->compile(); 
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/inactiveUser_report';
        $data['title'] = 'Inactive User Report';
        $data['width'] = '1280';
        $data['height'] = '500';
        if($from!=NULL || $to!=NUll || $companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
            if($data['session']['type'] == "Supervisor")
            $supID = $data['session']['eid']; 
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
            $result = $this->db->distinct()->select('tbl_employee_profile.Full_Name,Nick_Name,RS_ID,Company_Name,Dept_Name,Location_Name,tbl_employee_profile.Join_Date,Inactive_Date')
                    ->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')
                    ->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','inner')
                    ->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','inner')
                    ->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','inner')
                    ->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')
                    ->where('((`tbl_employee_profile`.`Join_Date` BETWEEN \''.$from.'\' AND \''.$to.'\') OR (`Inactive_Date` BETWEEN \''.$from.'\' AND \''.$to.'\'))')
                    ->get_where('tbl_employee_profile',$array_where)->result();
            $date_array = $this->administrator_model->showDates($from, $to);
            foreach($result as $row){
                if($row->Join_Date)
                    if(in_array($row->Join_Date, $date_array))
                        $in++;
                            
                if($row->Inactive_Date)
                    if(in_array($row->Inactive_Date, $date_array))
                        $out++;
                            
                $Full_Name[] = $row->Full_Name.($row->Nick_Name ? '('.$row->Nick_Name.')' : NULL);
                    
                $Company[] = $row->Company_Name;
                $Location[] = $row->Location_Name;
                $Dept[] = $row->Dept_Name;
                $Employee_ID[] = $row->RS_ID;
                $Join_Date[] = $row->Join_Date ? (in_array($row->Join_Date, $date_array) ? '<font color="blue">'.mdate('%d-%m-%Y',strtotime($row->Join_Date)).'</font>' : mdate('%d-%m-%Y',strtotime($row->Join_Date))) : NULL;
                $Inactive_Date[] =  $row->Inactive_Date ? (in_array($row->Inactive_Date, $date_array) ? '<font color="blue">'.mdate('%d-%m-%Y',strtotime($row->Inactive_Date)).'</font>' : mdate('%d-%m-%Y',strtotime($row->Inactive_Date))) : NULL;
            }
        }
        $data['field'] = array('Name' => $Full_Name,'Employee ID' => $Employee_ID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Join Date' => $Join_Date,'Exit Date' => $Inactive_Date);
            
        $row = $this->db->distinct()->select('All_Report')->from('tbl_user_type')->join('tbl_login_info', 'tbl_login_info.F_User_Permission_ID = tbl_user_type.User_Permission_ID')->where(array('tbl_login_info.Is_Exist' => 1, 'tbl_user_type.Is_Exist' => 1, 'tbl_login_info.F_User_ID' => $data['session']['id']))->limit(1)->get()->row();
        if($data['session']['type']=="Super Administrator" || $data['session']['type']=="Administrator" || $row->All_Report == 1)
        $data['other_fields'] = array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),'' => array('submit' => array('name'=>'search','value'=>'Search')));
        if($data['session']['type'] == "Supervisor" || $data['session']['type'] == "Co-Supervisor")
            unset($data['other_fields']['Supervisor']);
        $data['footer'] = array('Total In: '.$in.', Total Out: '.$out);
        return $data;
    }
        
//    Leave Management
    public function load_LeaveApplyFromInfo(){
        $js = 'function getDateCount() {
                var from = $("#from_date").val();
                var to = $("#to_date").val();
                if(from == ""){
                $("#error").html("From date is required");
//                $("#from_date").focus();
                }
                else if(to == ""){
                $("#error").html("To date is required");
//                $("#to_date").focus();
                }
                else if( (new Date(from).getTime() > new Date(to).getTime()))
                $("#error").html("Invalid date range");
                else{
                    $.ajax({
                            type: "POST",
                            url: "'.base_url('/home/countLeaveDate').'", 
                            data: {from_date:from,to_date:to},
                            success: function(data) 
                            {  
                                if(from == "" || to == "")
                                    $("#day").val("");
                                else
                                    $("#day").val(data);
                            }
                                
                        });
                    }
                }
            $( "#to_date" ).change(getDateCount);
            $( "#from_date" ).change(getDateCount);
            if($( "#from_date" ).val()== "" || $( "#to_date" ).val() == "")
            var day = 0;
            $(\'#fullday\').change(function() {
                if($(this).is(\':checked\')) {
                    $("#firsthalf").prop(\'disabled\', true);
                    $("#secondhalf").prop(\'disabled\', true);
                }else{
                    $("#firsthalf").prop(\'disabled\', false);
                    $("#secondhalf").prop(\'disabled\', false);
                }
            })
            $(\'#firsthalf\').change(function() {
               if($(this).is(\':checked\')) {
                    $("#secondhalf").prop(\'disabled\', true);
                    $("#fullday").prop(\'disabled\', true);
                    var day =  parseFloat($("#day").val())-0.5;
                    $("#day").val(day);
                    $("#error").html("First half of "+$("#to_date").val());
               }else{
                    $("#secondhalf").prop(\'disabled\', false);
                    $("#fullday").prop(\'disabled\', false);
                    var day =  parseFloat($("#day").val())+0.5;
                    $("#day").val(day);
                    $("#error").html("");
               }
            })
            $(\'#secondhalf\').change(function() {
                if($(this).is(\':checked\')) {
                    $("#firsthalf").prop(\'disabled\', true);
                    $("#fullday").prop(\'disabled\', true);
                    day =  parseFloat($("#day").val())-0.5;
                    $("#day").val(day);
                    $("#error").html("Second half of "+$("#from_date").val());
                }else{
                    $("#firsthalf").prop(\'disabled\', false);
                    $("#fullday").prop(\'disabled\', false);
                    day =  parseFloat($("#day").val())+0.5;
                    $("#day").val(day);
                    $("#error").html("");
                }
           })
        ';
        $js .="function change_backup() { 
                    $('#backup > option').remove(); 
                    var from_date = $('#from_date').val();
                   
                    $.ajax({
                        type: 'POST',
                        data: {'from_date':from_date},
                        url: '".base_url('/home/get_Backup_On_Date')."', 
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#backup').append(opt);
                            });
                        }
                            
                    });
                        
                }
                $( '#from_date' ).change(change_backup);
                ";
        $this->javascript->ready($js);
        $data['session'] = $this->session->userdata('logged_in');
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '640';
        $data['height'] = '500';
            
        $data['form']='home/leave_application'; 
        $data['submit']=array('name'=>'apply','value'=>'Apply');
        $data['title']='Leave Application';
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['check_caption'] = 'Leave Length';
        $data['check'] = array('Fullday' => 0,'First Half' => 0,'Second Half' => 0); 
        $row_total_leave = $this->db->distinct()->select('tbl_employee_profile.`Leave`,P_Leave')->from('tbl_user_info')->join('tbl_employee_profile', ' tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner')
                ->where(array('tbl_user_info.Is_Exist' => 1, 'tbl_employee_profile.Is_Exist' => 1, 'tbl_user_info.User_ID' => $data['session']['id']))->limit(1)->get()->row();    
        $result = $this->administrator_model->get_Specific_Info('tbl_leave_info', array('Is_Exist' => 1),'Leave_Type_ID,Leave_Type',2);
        $options_leavetype[NULL] = 'Please Select'; 
        $record_supervisor = $this->db->distinct()->select('Full_Name')->join('tbl_employee_profile','tbl_hierarchy_info.Supervisor1_ID = tbl_employee_profile.Employee_ID','inner')->get_where('tbl_hierarchy_info', array('F_Employee_ID' => $data['session']['eid']))->row();
        foreach($result as $row)
            $options_leavetype[$row->Leave_Type_ID]=$row->Leave_Type;
        $result_backup = $this->administrator_model->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1,'Employee_ID <>' => $data['session']['eid']),'Employee_ID,Full_Name,RS_ID',2);
        $options_backup[NULL] = 'Please Select';
        foreach($result_backup as $row_backup)
            $options_backup[$row_backup->Employee_ID] = $row_backup->Full_Name.'['.$row_backup->RS_ID.']';
        $row_jdate = $this->administrator_model->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1,'Employee_ID' => $data['session']['eid']),'Join_Date',1);
        
        $row = $this->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $data['session']['id'], 'tbl_login_record.Date' => $this->administrator_model->systemDateFormatConverter(now())),'F_User_ID,In_Time,Out_Time,Offday,Office_In',1);
        if($row){
            if(is_null($row->Offday)){
                $row_offday = $this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $data['session']['eid']),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
                $offdays = $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
            }else{
                $offdays =  explode(',',$row->Offday);
            }
        }else{
            $row_offday = $this->administrator_model->get_Specific_Info('tbl_work_days',array('F_Employee_ID' =>  $data['session']['eid']),'Sun,Mon,Tue,Wed,Thu,Fri,Sat',1);//'F_User_ID' =>  $row_user->User_ID
            $offdays = $this->getOffdays(array($row_offday->Sun,$row_offday->Mon,$row_offday->Tue,$row_offday->Wed,$row_offday->Thu,$row_offday->Fri,$row_offday->Sat));
        }
//        $from = mdate('%Y-01-01');
//        $to = mdate('%Y-%m-%d');
//        $offdays_date_array = $this->getoffdaysDate($offdays,$from,$to);            
//        $absent_array = $this->absentReport($data['session']['id'], $offdays_date_array, $from, $to);    
      
        
        $data['field']=array(
//            'Leave Type' => array('dropdown' => array('leave_type',$options_leavetype,$this->input->post('leave_type') ? $this->input->post('leave_type') : NULL)),
            'From' => array('input' => array('name'=>'from_date','id'=>'from_date','value'=>set_value('from_date'))),
            'To' => array('input' => array('name'=>'to_date','id'=>'to_date','value'=>set_value('to_date'))),
            'Reason' => array('textarea' => array('name'=>'reason','rows'=>'3','cols'=>'32','value'=>set_value('reason'))),
            'Leave Length(Day)' => array('input' => array('name'=>'day','id'=>'day','value'=>set_value('day',$this->input->post('day') ? $this->input->post('day') : NULL),'readonly'=>'true')),
            'Backup Person' => array('dropdown' => array('backup',$options_backup,$this->input->post('backup') ? $this->input->post('backup') : NULL,'id= "backup"')),
            'fieldset' => 'Leave Balance',
            'Total' =>  array('input' => array('name'=>'total','value'=>$row_total_leave->Leave-elapsed_leave_calculation($row_jdate->Join_Date),'size'=>4,'readonly'=>'true')),
            'Official/Holiday given' => array('input' => array('name'=>'holiday','value'=>$this->totalHolidayCount($data['session']['eid']),'size'=>4,'readonly'=>'true')),
            'Personal leave given' => array('input' => array('name'=>'pleave','value'=>$row_total_leave->P_Leave,'size'=>4,'readonly'=>'true')),
            'Personal leave taken' => array('input' => array('name'=>'leave','value'=>$this->administrator_model->leaveCount($data['session']['id'],1),'size'=>4,'readonly'=>'true')),
//            'Absent' => array('input' => array('name'=>'absent','value'=>count($absent_array),'size'=>4,'readonly'=>'true')),
//            'Remaining' => array('input' => array('name'=>'remaining','value'=>($row_total_leave->Leave-elapsed_leave_calculation($row_jdate->Join_Date)) - ($this->administrator_model->leaveCount($data['session']['id'],1)+$this->totalHolidayCount($data['session']['eid'])+count($absent_array)),'size'=>4,'readonly'=>'true')),
            'Remaining' => array('input' => array('name'=>'remaining','value'=>($row_total_leave->P_Leave-elapsed_leave_calculation($row_jdate->Join_Date)) - ($this->administrator_model->leaveCount($data['session']['id'],1)),'size'=>4,'readonly'=>'true')),//+count($absent_array)
            'close_fieldset' => '',
//            'Leave' => array('dropdown' => array('leave',array(1=>'Paid',0=>'Without pay'),$this->input->post('leave') ? $this->input->post('leave') : 1)),
            'Supervisor' => array('input' => array('name'=>'supervisor','value'=>$record_supervisor->Full_Name,'readonly'=>'true')),
            '' => array('label' => '<div id="error" class="error"></div>')
            );
        $data['elements'] = array('backup');
            
        return $data; 
    }
        
    public function load_LeaveApply_ValidationConfig(){
        $config = array(
            array(
                'field' => 'from_date', 
                'label' => 'From', 
                'rules' => 'trim|required|xss_clean|callback_is_greaterFromDate'
            ),
            array(
                'field' => 'to_date', 
                'label' => 'To', 
                'rules' => 'trim|required|xss_clean|callback_leaveExistValidation'
            ),
            array(
                'field' => 'day', 
                'label' => 'Day', 
                'rules' => 'trim|required|xss_clean|greater_than[0]'
            ),
            /*array(
                   'field' => 'leave_type', 
                   'label' => 'Leave Type', 
                   'rules' => 'trim|required|xss_clean'
            ),*/
            array(
                'field' => 'reason', 
                'label' => 'Leave Reason', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                   'field' => 'backup', 
                   'label' => 'Backup Person', 
                   'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'remaining', 
                'label' => 'Remaining leave balance', 
                'rules' => 'trim|required|xss_clean|callback_leaveApplyValidation[remaining]' //|is_natural_no_zero
            )
                
        );
        return $config;
    }
        
    public function send_LeaveApplication(){
        if($this->input->post('apply')){
            $user = $this->session->userdata('logged_in');
            $data = array(
                'F_User_ID' => $user['id'],
//                'Leave_Type_ID' => $this->input->post('leave_type'),
                'From_Date' => $this->administrator_model->systemDateFormatConverter($this->input->post('from_date')),
                'To_Date' =>  $this->administrator_model->systemDateFormatConverter($this->input->post('to_date')),
                'Leave_Reason' => $this->input->post('reason'),
                'Day' => (float) filter_var( $this->input->post('day'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) ,
                'Backup_Person_ID' => $this->input->post('backup'),
//                'Is_Paid' => $this->input->post('leave'),
                'Status' => 'unread',
                'Apply_Date' => $this->login_model->get_Local_Date('%Y-%m-%d %H:%i:%s')
            );
            if(isset($_POST['firsthalf'])) $data['First_Half'] = $this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
            if(isset($_POST['secondhalf']))$data['Second_Half'] = $this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
            $this->db->insert('tbl_leave_record',$data);
                
            $row = $this->administrator_model->get_Specific_Info('tbl_employee_profile',array('Is_Exist' => 1,'Employee_ID' => $this->input->post('backup')),',Full_Name,Email',1);
                
            $this->load->library('email');
            $this->email->from('mostafiz@ryansplus.com', 'Mostafizur Rahman');
            $this->email->to(trim($row->Email));
            $this->email->subject('You will be working as a backup Person');
            $this->email->message('Dear '.$row->Full_Name.', you will be working as a backup person for '.$user['fullname'].', from '.$this->input->post('from_date').' to '.$this->input->post('to_date'));
            $this->email->send();
        }
    }
        
        
        
    public function load_LeaveStatusData(){
        $this->datatable->load_Script('grid');
        $data['session'] = $this->session->userdata('logged_in');
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'home/leave_status';
        $data['title'] = 'Leave Status';
        $data['width'] = '800';
        $data['height'] = '400';
        //tbl_leave_info.Leave_Type,
        $result = $this->db->select('tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day')->from('tbl_leave_record')->/*join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','inner')->*/where(array(/*'tbl_leave_info.Is_Exist' => 1,*/'tbl_leave_record.Is_Exist' => 1, 'tbl_leave_record.F_User_ID' => $data['session']['id']))->get()->result();
        $row_total_leave = $this->db->distinct()->select('tbl_employee_profile.`Leave`')->from('tbl_user_info')->join('tbl_employee_profile', ' tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner')
                ->where(array('tbl_user_info.Is_Exist' => 1, 'tbl_employee_profile.Is_Exist' => 1, 'tbl_user_info.User_ID' => $data['session']['id']))->limit(1)->get()->row();
        $Leave = $From = $To = $Status = $Day = NULL;
        foreach($result as $row){
//           $Leave[] = $row->Leave_Type;
           $From[] = $this->administrator_model->regularDateFormatConverter($row->From_Date);
           $To[] = $this->administrator_model->regularDateFormatConverter($row->To_Date);
           $Day[] = $row->Day;
           $Status[] = $row->Is_Processed ? $this->lang->line('granted'):($row->Is_Void ? $this->lang->line('rejected') : $this->lang->line('pending'));
        }
        $data['field'] = array(/*'Leave'=>$Leave,*/'From'=>$From,'To'=>$To,'Day' => $Day,'Status'=>$Status);
        $data['width_th'] = array(20,20,20,20,8);
        $data['other_fields']=array('Total Leave' => array('input' => array('name'=>'edit','value'=>$row_total_leave->Leave,'size'=>4,'readonly'=>'true')),'Total Leave Counted' => array('input' => array('name'=>'edit','value'=>$this->administrator_model->leaveCount($data['session']['id'],1),'size'=>4,'readonly'=>'true')),'Yearly Total Holiday Counted' => array('input' => array('name'=>'edit','value'=>$this->totalHolidayCount($data['session']['eid']),'size'=>4,'readonly'=>'true')),'Leave Balance' => array('input' => array('name'=>'edit','value'=>$row_total_leave->Leave - ($this->administrator_model->leaveCount($data['session']['id'],1)+$this->totalHolidayCount($data['session']['eid'])),'size'=>4,'readonly'=>'true')));
        return $data;
    }
        
    public function load_LeaveBackupListData(){
        /*$js = '$(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
        $this->javascript->ready($js);
        $this->javascript->compile();*/
        $this->datatable->load_Script('grid');
        $data['session'] = $this->session->userdata('logged_in');
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'home/leaveBackup_List';
        $data['title'] = 'Backup Duty List';
        $data['width'] = '800';
        $data['height'] = '400';
        $result = $this->db->select('Leave_ID,Full_Name,tbl_leave_record.Is_Processed,tbl_leave_record.Is_Void,tbl_leave_info.Leave_Type,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day')->from('tbl_leave_record')->join('tbl_leave_info','tbl_leave_record.Leave_Type_ID = tbl_leave_info.Leave_Type_ID','left')
                ->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','left')
                ->where(array('tbl_leave_record.Is_Exist' => 1, 'tbl_leave_record.Backup_Person_ID' => $data['session']['eid']))->get()->result();
                    
        $Name = $Leave = $From = $To = $Status = $Day = NULL;
        foreach($result as $row){
        $Check_ID[] = form_checkbox('ID[]',$row->Leave_ID, FALSE);
           $Name[] = $row->Full_Name; 
           $Leave[] = $row->Leave_Type;
           $From[] = $this->administrator_model->regularDateFormatConverter($row->From_Date);
           $To[] = $this->administrator_model->regularDateFormatConverter($row->To_Date);
           $Day[] = $row->Day;
           $Status[] = $row->Is_Processed ? $this->lang->line('granted'):($row->Is_Void ? $this->lang->line('rejected') : $this->lang->line('pending'));
        }
        $data['field'] = array(/*form_checkbox(array('id'=>'select_all')) => $Check_ID,*/'Backup For' => $Name,'Leave'=>$Leave,'From'=>$From,'To'=>$To,'Day' => $Day,'Status'=>$Status);
//        $data['other_fields']=array('Decision' => array('submit' => array('name' => 'accept','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')')),'' => array('submit' => array('name' => 'reject','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')')));
        return $data;
    }
    
     public function load_LeaveListData($from=NULL,$to=NUll,$companyID=NULL,$deptID=NULL,$userID=NULL,$supID=NULL,$locID=NULL){
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

        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";

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
        $this->javascript->ready($js);
     
            
        $this->administrator_model->load_Datepicker(array('from_date','to_date'));
        $data['session'] = $this->session->userdata('logged_in');
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['form'] = 'home/leave_List';
        $data['title'] = 'All Staff Leave List';
        $data['width'] = '1300';
        $data['height'] = '480';
        $data['elements'] = array('users');
        $this->db->_protect_identifiers=false;
        if($from!=NULL || $to!=NUll || $companyID!=NULL || $deptID!=NULL ||$userID!=NULL || $supID !=NULL || $locID !=NULL){
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
                }
                else
                    $array_where['User_ID'] = $userID;
                if($supID != NULL){
                    $array_where['Supervisor1_ID'] = $supID; 
                }
                if($locID != NULL){
                    $array_where['Location_ID'] = $locID; 
                }
                $this->db->where($array_where);
        }
        $result = $this->db->distinct()
                ->select('Leave_ID,tbl_employee_profile.Full_Name,RS_ID,tbl_leave_record.From_Date,tbl_leave_record.To_Date,tbl_leave_record.Day,Location_Name,Company_Name,Dept_Name,
 (SELECT DISTINCT GROUP_CONCAT(Full_Name) FROM tbl_user_info WHERE FIND_IN_SET(User_ID,LEFT( Recommend, INSTR( Recommend, "#" ) -1 ) )) AS Recommend,
 (SELECT DISTINCT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_hierarchy_info.Supervisor1_ID) AS Supervisor,
 (SELECT DISTINCT tbl_employee_profile.Full_Name FROM tbl_employee_profile WHERE Employee_ID = tbl_leave_record.Backup_Person_ID) AS Backup_Person')
                ->from('tbl_leave_record')
             
                ->join('tbl_user_info','tbl_user_info.User_ID = tbl_leave_record.F_User_ID','inner')
                ->join('tbl_employee_profile', 'tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')
                ->join('tbl_location_info','tbl_location_info.Location_ID = tbl_employee_profile.F_Location_ID','inner')
                ->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_employee_profile.F_Company_ID','left')
                ->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','left')
                ->join('tbl_hierarchy_info','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')
                ->where(array('tbl_leave_record.Is_Exist' => '1','tbl_leave_record.Is_Processed' =>'1','tbl_leave_record.Is_Void' => '0','tbl_employee_profile.Is_Exist' => '1'))
                ->get()
                ->result();
                    
        $Name = $RS = $Location = $Company = $Dept = $Supervisor = $From = $To = $Day = $Backup =  $Approval =  NULL;
        foreach($result as $row){
            $date_array = $this->administrator_model->showDates($row->From_Date,$row->To_Date);
            if(in_array($from, $date_array) || in_array($to, $date_array)){
//        $Check_ID[] = form_checkbox('ID[]',$row->Leave_ID, FALSE);
                $Name[] = $row->Full_Name; 
                $RS[] = $row->RS_ID;
                $Location[] = $row->Location_Name;
                $Company[] = $row->Company_Name;
                $Dept[] = $row->Dept_Name;
                $Supervisor[]= $row->Supervisor;
                $From[] = $this->administrator_model->regularDateFormatConverter($row->From_Date);
                $To[] = $this->administrator_model->regularDateFormatConverter($row->To_Date);
                $Day[] = $row->Day;
                $Backup[] = $row->Backup_Person;
                $Approval[] = $row->Recommend;
            }
           
        }
        $data['field'] = array(/*form_checkbox(array('id'=>'select_all')) => $Check_ID,*/'Name' => $Name,'RS ID' => $RS ,'Supervisor'=>$Supervisor,'Company' => $Company, 'Department' => $Dept, 'Location' => $Location,'From'=>$From,'To'=>$To,'Day' => $Day, 'Backup Person' => $Backup, 'Approval' => $Approval);
        $data['other_fields']=array('Company' => array('dropdown' => array('company_ID',$this->globals->getOptionsCompany(),$this->input->post('company_ID') ? $this->input->post('company_ID') : $companyID,'id="company" style="width:9em;"')),'Department' => array('dropdown' => array('dept_ID',$this->globals->getOptionsDept($companyID),$this->input->post('dept_ID') ? $this->input->post('dept_ID') : $deptID,'id="dept" style="width:9em;"')),'User' => array('dropdown' => array('user_ID',$this->globals->getOptionsUser($deptID),$this->input->post('user_ID') ? $this->input->post('user_ID'): $userID,'id="users" style="width:9em;"')),'From' => array('input' => array('name'=>'from_date','value'=> set_value('from_date',mdate('%d-%m-%Y',now())),'id'=>'from_date','size'=>12)),'To' => array('input' => array('name'=>'to_date','value'=> set_value('to_date',mdate('%d-%m-%Y',now())),'id'=>'to_date','size'=>12)),/*'Supervisor' => array('dropdown' => array('sup_ID',$this->globals->getOptionsSV(),$this->input->post('sup_ID') ? $this->input->post('sup_ID') : $supID,'id="supervisor" style="width:9em;"')),*/'Location' => array('dropdown' => array('loc_ID',$this->globals->getOptionsLocation(),$this->input->post('loc_ID') ? $this->input->post('loc_ID') : $locID,'id="location" style="width:9em;"')),'' => array('submit' => array('name'=>'search','value'=>'Search')));
//        $data['other_fields']=array('Decision' => array('submit' => array('name' => 'accept','value' => 'Accept','onClick' => 'javascript:return confirm(\'Are you sure to accept?\')')),'' => array('submit' => array('name' => 'reject','value' => 'Reject','onClick' => 'javascript:return confirm(\'Are you sure to reject?\')')));
        return $data;
    }
        
    public function is_OnLeave($user_id=NUll,$date,$result){
        $date_array = array();
        foreach($result as $row){
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_array = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_array); 
        }
        return in_array($date,$date_array)?  TRUE : FALSE;
    }
        
    public function is_Holiday($date,$employeeID=NULL){
        $date = $this->administrator_model->systemDateFormatConverter($date);
        if($employeeID != NULL)
//        $this->db->where_in('Employee',$employeeID);
        $this->db->where('FIND_IN_SET("'.$employeeID.'",Employee) !=', 0);
        $result = $this->administrator_model->get_Specific_Info('tbl_holiday_info',array('Is_Exist'=>1),'Holiday_Name,From_Date,To_Date,Employee',2);
        $date_array = array();
        $name_array = array();
        foreach($result as $row){
            
            $name = $row->Holiday_Name;
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_date_array[$row->Holiday_Name] = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_date_array);
        }
        foreach($date_array as $key => $value){
            if(in_array($date,$value))
            return  $key;
        }
    }
        
    public function is_OnTour($date,$employeeID=NULL){
        $date = $this->administrator_model->systemDateFormatConverter($date);
        if($employeeID != NULL)
//        $this->db->where_in('Employee',$employeeID);
        $this->db->where('FIND_IN_SET("'.$employeeID.'",Employee) !=', 0);
        $result = $this->administrator_model->get_Specific_Info('tbl_tour_info',array('Is_Exist'=>1),'Tour_Name,From_Date,To_Date,Employee',2);
        $date_array = array();
        $name_array = array();
        foreach($result as $row){
            
            $name = $row->Tour_Name;
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_date_array[$row->Tour_Name] = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_date_array);
        }
        foreach($date_array as $key => $value){
            if(in_array($date,$value))
            return  $key;
        }
    }
        
    public function holidayCount($employeeID=NULL){
//        $result = $this->administrator_model->get_Specific_Info('tbl_holiday_info',array('Is_Exist'=>1,'DATE_FORMAT(From_Date,"%Y-%m")' => mdate('%Y-%m',now())),'From_Date,To_Date',2);
        $this->db->select('From_Date,To_Date');
        $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$employeeID.'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-25',strtotime("-1 month",now())).'" AND "'.$this->administrator_model->systemDateFormatConverter(now()).'"');
        $result = $query->result();
        $date_array = array();
        foreach($result as $row){
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_date_array = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_date_array);
        }
        return count($date_array);
    }
    
     public function specificHolidayCount($employeeID=NULL,$from,$to){
        $this->db->select('From_Date,To_Date');
        $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$employeeID.'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-%m-%d',strtotime($from)).'" AND "'.mdate('%Y-%m-%d',strtotime($to)).'"');
        $result = $query->result();
        $date_array = array();
        foreach($result as $row){
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_date_array = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_date_array);
        }
        return count($date_array);
    }
        
    public function totalHolidayCount($employeeID=NULL){
        $this->db->select('From_Date,To_Date');
        $query = $this->db->get_where('tbl_holiday_info','Is_Exist = 1 AND FIND_IN_SET ("'.$employeeID.'",Employee)  AND From_Date BETWEEN "'.mdate('%Y-01-01',now()).'" AND "'.mdate('%Y-12-31',now()).'"');
        $result = $query->result();
        $date_array = array();
        foreach($result as $row){
            $from= $row->From_Date;
            $to = $row->To_Date;
            $temp_date_array = $this->administrator_model->showDates($from, $to);
            $date_array = array_merge($date_array,$temp_date_array);
        }
        return count($date_array);
    }
        
    public function is_LeaveValid($remaining){
        //$date_array = $this->administrator_model->showDates($from,$to);
            
        if($this->input->post('day')>$remaining){//count($date_array)
            $this->form_validation->set_message('leaveApplyValidation','Not enough leave remain');
            return FALSE;
        }else
            return TRUE;
    }
    
    public function is_greaterFromDate(){
        $From_Date = strtotime($this->input->post('from_date'));
        $To_Date = strtotime($this->input->post('to_date'));
        if($From_Date>$To_Date){
            $this->form_validation->set_message('is_greaterFromDate','From date is greter than To date');
            return FALSE;
        }else
            return TRUE;
    }
    
        
    public function is_LeaveExist(){
        $session = $this->session->userdata('logged_in');
        $from=$this->administrator_model->systemDateFormatConverter($this->input->post('from_date'));
        $to=$this->administrator_model->systemDateFormatConverter($this->input->post('to_date'));
        $rowcount = $this->administrator_model->get_Specific_Info('tbl_leave_record', array('Is_Exist' => 1,/*'Is_Void' => 0,*/'F_User_ID' => $session['id'],'From_Date' => $from, 'To_Date' => $to),'COUNT(Leave_ID) AS Count',1);
        if($rowcount->Count > 0){
            $this->form_validation->set_message('leaveExistValidation','You have already applied this leave');
            return FALSE;
        }else
            return TRUE;
    }
        
    public function is_Recruitment_JoinDateValid($date){
        $date2 = date_create($this->administrator_model->systemDateFormatConverter($date));
        $date1 = date_create($this->login_model->get_Local_Date());
        $diff=date_diff($date1,$date2);
            
        if($diff->format("%a")<=30){//"%R"
            $this->form_validation->set_message('recruitmentJoinDateValidation','Too close for joining!');
            return FALSE;
        }else
            return TRUE;
    }
        
    public function is_Recruitment_Accomplished(){
        if(is_array($this->input->post('ID'))){
            return FALSE;
        }else{
            $row = $this->administrator_model->get_Specific_Info('tbl_recruitment_record', array('Is_Exist' => 1,'Recruitment_Request_ID' => $this->input->post('ID')),'Is_Accomplish',1);
            return ($row->Is_Accomplish == 1) ? TRUE : FALSE;
        }
    }
        
    public function load_RecruitmentRequestStatusData(){
        $js = '$(\'#select_all\').change(function() {
            var checkboxes = $(this).closest(\'form\').find(\':checkbox\');
            if($(this).is(\':checked\')) {
                checkboxes.prop(\'checked\', true);
            } else {
                checkboxes.prop(\'checked\', false);
            }
        });
        ';
//        $js .= "cancel_request(id){
//             alert('test!');
//                 $.ajax({
//                        type: 'POST',
//                        data:{'cacel_request_id':id},
//                        url: '".base_url('/administrator/get_AllDept')."', 
//
//                        success: function(data) 
//                        {  
//                           alert('Success!');
//                        }
//
//                    });
//                }";
        $js .= '
            $(\'#cancel\').hide();
            $(\'#fbody\').closest(\'form\').find(\':checkbox\').change(function() {
            if($(this).is(\':checked\')) {
            var cid = $(this).val();
             $.ajax({
                        type: \'POST\',
                        data:{\'ID\':cid},
                        url: \''.base_url('/home/isRecruitment_Accomplished').'\', 
                            
                        success: function(data) 
                        {  
                           if(data==1)
                           $(\'#cancel\').show();
                           else
                           $(\'#cancel\').hide();
                        }
                            
                    });
            } else {
                $(\'#cancel\').hide();
            }
        });';
        $this->javascript->ready($js);
        $this->javascript->compile();
        $this->datatable->load_Script('grid');
        $data['session'] = $this->session->userdata('logged_in');
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'home/recruitment_status';
        $data['title'] = 'Recruitment Request Status';
        $data['width'] = '1340';
        $data['height'] = '400';
        $this->db->select('Recruitment_Request_ID,Stage,tbl_recruitment_record.Join_Date,Reason,tbl_recruitment_record.Designation,Salary_From,Salary_To,Existing_Number,Experience,Company_Name,Dept_Name,Location_Name,tbl_recruitment_record.Number,tbl_recruitment_record.Remarks,tbl_recruitment_record.Date,tbl_recruitment_record.Is_Exist,tbl_recruitment_record.Is_Processed,tbl_recruitment_record.Is_Void,Is_Cancel');
        $this->db->from('tbl_recruitment_record');
        $this->db->join('tbl_company_info', 'tbl_company_info.Company_ID = tbl_recruitment_record.F_Company_ID','left');
        $this->db->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_recruitment_record.F_Dept_ID','left');
        $this->db->join('tbl_location_info', 'tbl_location_info.Location_ID = tbl_recruitment_record.F_Location_ID','left');
        $this->db->where(array('tbl_recruitment_record.F_User_ID' => $data['session']['id']));
        $result = $this->db->get()->result();
            
        $Check_ID = $Company = $Dept = $Loc  = $Exp = $Sal_Rng = $Desig = $Remarks = $Join_Date = $Date = $Number = $E_Number = $Status = $Reason  = NULL;
        foreach($result as $row){
            $Check_ID[] =  form_checkbox('ID[]',$row->Recruitment_Request_ID, FALSE);
            $Company[] = $row->Company_Name;
            $Dept[] = $row->Dept_Name;
            $Loc[] = $row->Location_Name;
            $Desig[] =$row->Designation;
            $Exp[] = $row->Experience.nbs().'year';
            $Sal_Rng[] = $row->Salary_From.'-'.$row->Salary_To; 
            $Remarks[] = $row->Remarks;
            $Number[] = $row->Number;
            $E_Number[] = $row->Existing_Number;
            $Reason[] = $row->Reason;
            $Join_Date[] = $this->administrator_model->regularDateFormatConverter($row->Join_Date);
            $Date[] = $this->administrator_model->regularDateFormatConverter($row->Date);
            if($row->Is_Cancel==1)
                $Status[] = 'Request for Cancel';
            elseif ($row->Is_Cancel==2) 
                $Status[] = 'Canceled';
            else
                $Status[] = ($row->Is_Exist==0) ? $this->lang->line('rejected') :($row->Is_Processed ? $this->lang->line('granted') : ($row->Is_Void? ($row->Stage != NULL ? ($row->Stage != 'Accomplish' ? $row->Stage : $row->Stage) : $this->lang->line('processing')) : $this->lang->line('pending')));
        }
        $data['field'] = array(form_checkbox(array('id'=>'select_all')) => $Check_ID,'Company For'=>$Company,'Department For'=>$Dept,'Location For' => $Loc,'Designation For' => $Desig,'Employee Needed' => $Number,'Existing Employee' => $E_Number,'Required Experience' => $Exp,'Salary Range' => $Sal_Rng,'Reason' => $Reason,'Remarks' => $Remarks,'Tentative Join Date' => $Join_Date,'Date of Request' => $Date,'Status'=>$Status);
        $data['other_fields'] = array(' ' => array('submit' => array('name' => 'seen','value' => 'Seen')),'' => array('submit' => array('name' => 'cancel','value' => 'Cancel','id' => 'cancel')));
        return $data;
    }
    //    Recruitment Management
    public function load_RecruitmentRequestFromInfo(){
        $data['session'] = $this->session->userdata('logged_in');
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
        $js .= numeric(array('experience','recruitment','exsisting','sal_from','sal_to'));
        $js.= "$('#sal_from').attr('disabled','disabled');$('#sal_to').attr('disabled','disabled');";
        $this->javascript->ready($js);
        $this->administrator_model->load_Datepicker(array('join_date'));
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '640';
        $data['height'] = '570';
        $data['form']='home/recruitment_request'; 
        $data['title']='Recruitment Request Application for a single employee';
            
        $this->db->order_by('tbl_hierarchy_info.Designation');
        $result_designation = $this->administrator_model->get_Specific_Info('tbl_hierarchy_info','tbl_hierarchy_info.Designation IS NOT NULL','tbl_hierarchy_info.Designation',2);
        $options_designation[NULL] = 'Please Select';
        foreach($result_designation as $row)
            $options_designation[$row->Designation]=$row->Designation;
        $result_dept = $this->administrator_model->get_Specific_Info('tbl_dept_info',array('Is_Exist'=>1),'Dept_ID,Dept_Name',2);
        $options_dept[NULL] = 'Please Select'; 
        foreach($result_dept as $row)
            $options_dept[$row->Dept_ID]=$row->Dept_Name;
                
        $result_location = $this->administrator_model->get_Specific_Info('tbl_location_info',array('Is_Exist' => 1),'Location_ID,Location_Name',2);
        $options_location[NULL] = 'All Locations';
        foreach($result_location as $row)
            $options_location[$row->Location_ID]=$row->Location_Name;
                
        $data['field']=array(
            'Company For' => array('dropdown' => array('company',$this->globals->getOptionsCompany(),$this->input->post('company') ? $this->input->post('company') : NULL,'id="company" style="width:12em"')),
            'Department For' => array('dropdown' => array('dept',$options_dept,$this->input->post('dept') ? $this->input->post('dept') : NULL,'id="dept" style="width:12em"')),
            'Designation For' => array('dropdown' => array('desig',$options_designation,$this->input->post('desig') ? $this->input->post('desig') : NULL,'id="desig" style="width:12em"')),
            'Education' => array('textarea' => array('name'=>'education','rows'=>'3','cols'=>'32','value'=>set_value('education'))),
            'Experiences (Years)' => array('input' => array('name'=>'experience','id' => 'experience','size'=>4,'pattern' => '[0-9]','value'=>set_value('experience'))),   
                
//            'Number of employee needed' => array('input' => array('name'=>'recruitment','id'=>'recruitment','size'=>4,'value'=>set_value('recruitment'))),
            'Number of existing employees' => array('input' => array('name'=>'exsisting','id'=>'exsisting','size'=>4,'value'=>set_value('exsisting'))),
                
            'Joining Date' => array('input' => array('name' => 'join_date','id' => 'join_date','size'=>12,'value'=>set_value('join_date',mdate('%d-%m-%Y',now())))),
            'Location' => array('dropdown' => array('location',$options_location,$this->input->post('location') ? $this->input->post('location') : NULL,'id="location"')),
            'fieldset' => 'Salary Range (Taka)',
            'From' => array('input' => array('name'=>'sal_from','id'=>'sal_from','size'=>14,'value'=>set_value('sal_from'))), 
            'To' => array('input' => array('name'=>'sal_to','id'=>'sal_to','size'=>14,'value'=>set_value('sal_to'))), 
            'fieldset_close' => '',
            'Reason' => array('textarea' => array('name'=>'reason','rows'=>'3','cols'=>'32','value'=>set_value('reason'))),
            'Remarks' => array('textarea' => array('name'=>'remarks','rows'=>'3','cols'=>'32','value'=>set_value('remarks'))),
            '' => array('submit' => array('name'=>'apply','value'=>'Apply'))
            );
                
        return $data; 
    }
        
    public function load_RecruitmentRequest_ValidationConfig(){
        $config = array(
            array(
                'field' => 'company', 
                'label' => 'Company Name', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'dept', 
                'label' => 'Department Name', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'location', 
                'label' => 'Location Name', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'remarks', 
                'label' => 'Remarks', 
                'rules' => 'trim|xss_clean'
            ),
            array(
                'field' => 'reason', 
                'label' => 'Reason', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'education', 
                'label' => 'Education', 
                'rules' => 'trim|required|xss_clean'
            ),
            array(
                'field' => 'sal_from', 
                'label' => 'Salary from', 
                'rules' => 'trim|is_natural_no_zero|xss_clean'//required|
            ),
            array(
                'field' => 'sal_to', 
                'label' => 'Salary to', 
                'rules' => 'trim|is_natural_no_zero|xss_clean'//required|
            ),
            array(
                'field' => 'experience', 
                'label' => 'Experience', 
                'rules' => 'trim|required|is_natural|xss_clean'
            ),
//            array(
//                'field' => 'recruitment', 
//                'label' => 'Number of Recruitment', 
//                'rules' => 'trim|required|is_natural_no_zero|xss_clean'
//            ),
            array(
                'field' => 'exsisting', 
                'label' => 'Number of Employees', 
                'rules' => 'trim|required|is_natural_no_zero|xss_clean'
            ),
            array(
                'field' => 'join_date', 
                'label' => 'Join Date', 
                'rules' => 'trim|required|xss_clean|callback_recruitmentJoinDateValidation[join_date]' //|is_natural_no_zero
            )
                
        );
        return $config;
    }
        
    public function send_RecruitmentRequest(){
        if($this->input->post('apply')){
            $user = $this->session->userdata('logged_in');
            $data = array(
                'F_User_ID' => $user['id'],
                'F_Company_ID' => $this->input->post('company'),
                'F_Dept_ID' => $this->input->post('dept'),
                'F_Location_ID' => $this->input->post('location'),
                'Designation' => $this->input->post('desig'),
                'Education' => $this->input->post('education'),
                'Experience' => $this->input->post('experience'),
                'Number' => '1',//$this->input->post('recruitment')
                'Existing_Number' => $this->input->post('exsisting'),
                'Join_Date' => $this->administrator_model->systemDateFormatConverter($this->input->post('join_date')),
                'Remarks' => $this->input->post('remarks'),
                'Reason' => $this->input->post('reason'),
                'Salary_From' => $this->input->post('sal_from'),
                'Salary_To' => $this->input->post('sal_to'),
                'Date' => $this->login_model->get_Local_Date()
            );
            $this->db->insert('tbl_recruitment_record',$data);
        }
    }
        
    public function load_PasswordUpdate_ValidationConfig(){
        $config = array(
            array(
                'field' => 'user_name', 
                'label' => 'User Name', 
                'rules' => 'trim|required|min_length[3]|max_length[50]|xss_clean',
            ),
            array(
                'field' => 'password', 
                'label' => 'Password', 
                'rules' => 'trim|required|min_length[3]|max_length[100]|xss_clean'
            ),
            array(
                'field' => 'confirm_password', 
                'label' => 'Confirm Password', 
                'rules' => 'trim|required|min_length[3]|max_length[100]|xss_clean|matches[password]'
            )
        );
        return $config;
    }
        
    public function load_PasswordUpdateFromInfo(){
        $encryption = new Encryption;
        $data['session'] = $user = $this->session->userdata('logged_in');
        $js = 'waitForMsg();waitForMsgRecruit();';
        if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
            $js .= 'waitForMsg1();waitForMsg2();'; 
        $this->javascript->ready($js);
        $this->javascript->compile();
        $row =$this->administrator_model->get_User_Inforamtion(array('User_ID' => $user['id']));
        if(isset($row))
           $password = $encryption->decrypt($row->Password);
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'),$this->config->item('datepicker_css'));
        $data['width'] = '440';
        $data['height'] = '300';
        $data['title'] = 'Update Password';
        $data['form']='home/update_Password'; 
        $data['hidden']= array('id' => $row->User_ID);
            
        $data['field']=array(
            'User Name' => array('input' => array('name' => 'user_name','maxlength' => '50','size' => '30','value' =>  set_value('user_name', $row->User_Name ),'id'=>'user_name','readonly' => true)),
            'Password' => array('password' => array('name' => 'password','maxlength' => '100','size' => '30','value' =>  set_value('password',isset($password) ? $password : NULL))),
            'Confirm Password' => array('password' => array('name' => 'confirm_password','maxlength' => '100','size' => '30','value' =>  set_value('confirm_password',isset($password) ? $password : NULL))),
            '' => array('submit' => array('name' => 'update','value' =>  'Update'))
            );
        return $data; 
    }
        
    public function update_Password_Info(){
        $encryption = new Encryption;
        $password = $encryption->encrypt($this->input->post('password'));
            
        $data_login = array(
            'Password' =>  $password,
            );
                
        if($this->input->post('update')){ 
            $this->db->where('F_User_ID',$this->input->post('id'));
            $this->db->update('tbl_login_info',$data_login);
        }
//        $this->email_User_Info();
    
    }
        
    public function get_Employee_Information($id){
        $this->db->distinct();
        $this->db->select('User_ID,Document,Office_Contact,Job_Desc,Photo,Grace_Time,Employee_ID,tbl_employee_profile.F_Company_ID,Company_Name,F_Location_ID,Location_Name,F_Dept_ID,Dept_Name,RS_ID,tbl_employee_profile.Full_Name,Nick_Name,Gender,DOB,Father,Mother,tbl_employee_profile.Email,tbl_employee_profile.Join_Date,Emergency_Contact,Blood_Group,Height,Weight,Marital_Status,
        Identification_Mark,Training,NID,Passport,Passport_Issue_Date,Passport_Expiry_Date,Permanent_Address,tbl_employee_profile.Address,Previous_Organization,tbl_employee_profile.`Leave`,tbl_employee_profile.Is_Exist,
        tbl_work_days.Sun,tbl_work_days.Mon,tbl_work_days.Tue,tbl_work_days.Wed,tbl_work_days.Thu,tbl_work_days.Fri,tbl_work_days.Sat');
        $this->db->join('tbl_work_days','tbl_employee_profile.Employee_ID = tbl_work_days.F_Employee_ID','left');
        $this->db->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left');
        $this->db->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left');
        $this->db->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left');
        $this->db->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
        $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner');
         if($id!=NULL)
            $this->db->where($id);
        $query=$this->db->get('tbl_employee_profile');
        return  $query->result();
    }
    
    public function load_IDCardInfoReportData($employeeID){
        $Full_Name = $Nick = $Company = $Location = $Dept = $Employee_ID =  $NID = $Blood_Group = $Photo = $Designation = $Email =  $Office_Contact = NULL;
        $data['session'] = $this->session->userdata('logged_in');
        $columnDef='{"width": \'15%\', "targets": [0,3] },{"width": \'10%\', "targets": [2] },{"width": \'8%\', "targets": [1,5] },{"width": \'12%\', "targets": [4] }';
            
        $this->datatable->load_Script('grid',NULL,$columnDef,'"order": [[ 0, "asc" ]]');
            
        $js = 'waitForMsg();waitForMsgRecruit();';
        $js .= "function change_dept() { 
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
            
        $js .= "$('#company').change(function() { 
                    $('#supervisor > option').remove(); 
                    $('#supervisor').append(\"<option value= ''>All Supervisor</option>\");
                    var company_id = $('#company').val();; 
                    $.ajax({
                        type: 'GET',
                        url: '".base_url('/home/get_SuperVisor_ByCompany')."/' + company_id, 
                            
                        success: function(data) 
                        {  
                            var obj = jQuery.parseJSON(data);
                            $.each(obj, function(i, obj)
                            {   
                                var opt = $('<option />'); 
                                opt.val(obj.Employee_ID);
                                opt.text(obj.Full_Name);
                                $('#supervisor').append(opt);
                            });
                        }
                            
                    });
                        
                });";
                    
        $this->javascript->ready($js);
        $this->javascript->compile(); 
            
        $data['menu'] = $this->administrator_model->load_Menu();
        $data['link'] = array($this->login_model->favicon,$this->config->item('site_css'),$this->config->item('menu_css'));
        $data['form'] = 'home/idCardInfo_report';
        $data['title'] = 'ID Card Information Report';
        $data['width'] = '1280';
        $data['height'] = '500';
            
                if($employeeID != NULL){
//                     
                    $employee_array = explode(',',$employeeID);
                    $this->db->distinct();
                    $this->db->select('Office_Contact,Email,Designation,Photo,Employee_ID,Company_Name,Location_Name,Dept_Name,RS_ID,tbl_employee_profile.Full_Name,Nick_Name,Emergency_Contact,Blood_Group,NID');
    //                $this->db->join('tbl_work_days','tbl_employee_profile.Employee_ID = tbl_work_days.F_Employee_ID','left');
                    $this->db->join('tbl_company_info','tbl_employee_profile.F_Company_ID = tbl_company_info.Company_ID','left');
                    $this->db->join('tbl_location_info','tbl_employee_profile.F_Location_ID = tbl_location_info.Location_ID','left');
                    $this->db->join('tbl_dept_info','tbl_employee_profile.F_Dept_ID = tbl_dept_info.Dept_ID','left');
    //                $this->db->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left');
                    $this->db->join('tbl_hierarchy_info','tbl_hierarchy_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner');
                    $this->db->where_in('RS_ID',$employee_array);
    //                $this->db->where($array_where);
                    $query=$this->db->get('tbl_employee_profile');
                    $result =  $query->result();
                
//        $result = $this->get_Employee_Information($array_where);
                foreach($result as $row){
                    $Photo[] = img(array('src' => './uploaded_images/'.$row->Photo,'width' => '300'));
                    $Full_Name[] = $row->Full_Name;
                    $Nick[] = $row->Nick_Name;
                    $Company[] = $row->Company_Name;
                    $Location[] = $row->Location_Name;
                    $Dept[] = $row->Dept_Name;
                    $Employee_ID[] = $row->RS_ID;
                    $NID[] = $row->NID;
                    $Blood_Group[] = $row->Blood_Group; 
                    $Designation[] = $row->Designation;
                    $Email[] = $row->Email;
                    $Office_Contact[] = $row->Office_Contact;
                }
        }
        $data['field'] = array('Photo' => $Photo, 'Name' => $Full_Name,'Nick Name' => $Nick,'Employee ID' => $Employee_ID,'National ID' => $NID,'Company' => $Company,'Department' => $Dept,'Location' => $Location,'Blood Group' => $Blood_Group,'Designation' => $Designation, 'Email' => $Email,  'Official Contact' => $Office_Contact);

        $data['other_fields'] = array('Employee IDs' => array('input' => array('name'=>'employeeids','id'=>'employeeids','size'=>80,'value'=>set_value('employeeids'))),'' => array('submit' => array('name'=>'search','value'=>'Search')));
     
        return $data;
    }
    
    public function test_half_time(){
        $user = $this->session->userdata('logged_in');
        $weekcount= mdate('%w',  strtotime(now()));
        $row_ot = $this->db->distinct()->select('In,Out')->get_where('tbl_office_time', array('F_Employee_ID' => $user['eid'], 'Weekday'=> $weekcount))->row();
        $dateDiff = intval((strtotime($row_ot->Out)-strtotime($row_ot->In))/60);
        $office_hours = intval($dateDiff/60);
        $half_office_hour = $office_hours/2;
        $half_office_min = $half_office_hour * 60;
        echo mdate('%H:%i:%s',strtotime("+ $half_office_min minutes",strtotime($row_ot->In)));
        
    }
        
}
?>