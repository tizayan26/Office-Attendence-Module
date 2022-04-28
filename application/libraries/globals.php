<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of globals
 *
 * @author User
 */
class globals {
    private $obj,$rec_emp,$var_session;
    public function __construct(){
        
        $this->obj =& get_instance();
        $this->obj->load->library('session');
        $this->obj->load->model('administrator_model');
        $this->var_session  = $this->obj->session->userdata('logged_in');
        $this->rec_emp = $this->obj->administrator_model->get_Specific_Info('tbl_employee_profile',array('Is_Exist' => 1, 'Employee_ID' => $this->var_session['eid']),'F_Dept_ID,F_Company_ID',1);
   
    }
    
    public function getOptionsCompany(){
        $this->obj->db->order_by('Company_Name');
        $result_company = $this->obj->administrator_model->get_Specific_Info('tbl_company_info',($this->var_session['type'] == "Supervisor" || $this->var_session['type'] == "Co-Supervisor") ? array('Is_Exist'=>1,'Company_ID' => $this->rec_emp->F_Company_ID) : array('Is_Exist'=>1),'Company_ID,Company_Name',2);
        $options_company[NULL] = 'Please Select';
        foreach($result_company as $row)
            $options_company[$row->Company_ID] = $row->Company_Name;
        return $options_company;
    }
    
    public function getOptionsDept($companyID = NULL){
        $array_where_dept = ($this->var_session['type'] == "Supervisor" || $this->var_session['type'] == "Co-Supervisor") ? array('Is_Exist'=>1,'Dept_ID' => $this->rec_emp->F_Dept_ID) : array('Is_Exist'=>1);
        $this->obj->db->order_by('Dept_Name');
        if($this->var_session['type'] == "Supervisor" || $this->var_session['type'] == "Co-Supervisor"){
            $array_where = array('tbl_dept_info.Is_Exist' => 1,'tbl_employee_profile.Is_Exist' => 1);
            if($companyID != NULL)
                $array_where['tbl_dept_info.F_Company_ID'] = $companyID;
            $this->obj->db->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','inner')->where('Supervisor1_ID' ,$this->var_session['eid'])->or_where('Supervisor2_ID', $this->var_session['eid'])->or_where('Supervisor3_ID', $this->var_session['eid']);  
            $result_dept = $this->obj->administrator_model->get_Specific_Info('tbl_hierarchy_info',$array_where,'Dept_ID,Dept_Name',2);
        }else

            if($companyID != NULL)
                $array_where_dept['tbl_dept_info.F_Company_ID'] = $companyID;
            $result_dept = $this->obj->administrator_model->get_Specific_Info('tbl_dept_info',$array_where_dept,'Dept_ID,Dept_Name',2);
        $options_dept[NULL] = ($companyID == NULL) ? 'Please Select' : 'All Dept'; 
        foreach($result_dept as $row)
            $options_dept[$row->Dept_ID]=$row->Dept_Name;
        return $options_dept;
    }
      
    public function getOptionsUser($deptID = NULL){
        $this->obj->db->order_by('Full_Name');
        $array_where = array('tbl_user_info.Is_Exist'=>1,'F_User_Permission_ID <> '=>1);
        if($deptID != NULL)
            $array_where['F_Dept_ID'] = $deptID;
        $result_user = $this->obj->db->distinct()->select('User_ID,tbl_user_info.Full_Name,RS_ID')->join('tbl_employee_profile','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','left')->get_where('tbl_user_info',$array_where)->result();
        $options_user[NULL] = ($deptID == NULL) ? 'Please Select' : 'All User';
        if($this->var_session['type'] == "Administrator" || $this->var_session['type'] == "Super Administrator")
        foreach($result_user as $row)
            $options_user[$row->User_ID]=$row->Full_Name.'['.$row->RS_ID.']';
        return $options_user;
    }
    
    public function getOptionsSV(){
        $this->obj->db->order_by('Full_Name');
        if($this->var_session['type'] == "Administrator" || $this->var_session['type'] == "Super Administrator"){
            $result = $this->obj->db->distinct()->select('Full_Name,Employee_ID')
                ->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_hierarchy_info.Supervisor1_ID','inner')
                ->get_where('tbl_hierarchy_info',array('tbl_hierarchy_info.Is_Exist'=>1,'tbl_employee_profile.Is_Exist'=>1))->result();
            $options_supervisor[NULL] = 'All Supervisor';
            foreach($result as $row)
                $options_supervisor[$row->Employee_ID]=$row->Full_Name;
        }else
            $options_supervisor = NULL;
        return $options_supervisor;
    }
    
    public function getOptionsLocation(){
        $this->obj->db->order_by('Location_Name');
        if($this->var_session['type'] == "Administrator" || $this->var_session['type'] == "Super Administrator"){
        $result_location = $this->obj->administrator_model->get_Specific_Info('tbl_location_info',array('Is_Exist' => 1),'Location_ID,Location_Name',2);
        }else{
            $result_location = $this->obj->db->distinct()->select('Location_Name,Location_ID')
                    ->join('tbl_employee_profile', 'tbl_location_info.Location_ID = tbl_employee_profile.F_Location_ID','inner')
                    ->join('tbl_hierarchy_info','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')
                    ->get_where('tbl_location_info',"(`Supervisor1_ID` = ".$this->var_session['eid']." OR `Supervisor2_ID` = ".$this->var_session['eid']." OR `Supervisor3_ID` = ".$this->var_session['eid'].")")->result();
        }
        $options_location[NULL] = 'All Locations';
        foreach($result_location as $row)
            $options_location[$row->Location_ID]=$row->Location_Name;
        return $options_location;
    }
    
    public function getOptionsPermission(){
         $user = $this->obj->session->userdata('logged_in');
        if($user['type']=="Super Administrator")
            $query=$this->obj->db->get_where('tbl_user_type',array('Is_Exist' => 1));
        elseif($user['type']=="Administrator")
            $query=$this->obj->db->get_where('tbl_user_type',array('Is_Exist' => 1,'tbl_user_type.User_Permission_ID <>' => 1));
        else
            $query=$this->obj->db->get_where('tbl_user_type','Is_Exist = 1 AND User_Permission_ID NOT IN (1, 6)');
        $options_permission[NULL] = 'Please Select';
        foreach($query->result() as $rs)
            $options_permission[$rs->User_Permission_ID] = $rs->User_Permission_Type;
        return $options_permission;
    }
    
    public function getOptionsDesignation(){
        $this->obj->db->order_by('Designation_Name');
        $result_designation = $this->obj->administrator_model->get_Specific_Info('tbl_designation_info','Designation_Name IS NOT NULL AND Is_Exist = 1','Designation_Name,Designation_ID',2);
        $options_designation[NULL] = 'Please Select';
        foreach($result_designation as $row)
            $options_designation[$row->Designation_ID]=$row->Designation_Name;
        return $options_designation;
    }
    
    public function getOptionsEmployee(){
        $result = $this->obj->administrator_model->get_Specific_Info('tbl_employee_profile', array('Is_Exist' => 1),'Employee_ID,Full_Name,RS_ID',2);
        $options[NULL] = 'Please Select';
        foreach($result as $row)
            $options[$row->Employee_ID] = $row->Full_Name.'['.$row->RS_ID.']';
        return $options;
    }
    
    function getOffday($date,$user_id,$i){
    $offdays_row = $this->obj->administrator_model->get_Specific_Info('tbl_login_record',array('F_User_ID' => $user_id, 'tbl_login_record.Date' => mdate('%Y-%m-%d',strtotime('-'.$i.' day', strtotime($date)))),'F_User_ID,Offday',1);
     print_r($offdays_row);
     
     if($offdays_row){
                             $offdays = explode(',',$offdays_row->Offday); 
                              return $offdays; 
                              exit();
                         }else{
                             $this->getOffday($date,$user_id,$i++);
                         }
                                    
    }
}
