<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* Return User Response */
function get_User_Json1($companyID=NULL,$deptID=NULL,$locationID=NULL,$supervisorID=NULL){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('tbl_user_info.Full_Name');
    $array_where=array( 'tbl_user_info.Is_Exist' => 1,'F_User_Permission_ID <> '=>1);
    if($deptID!=NULL)
        $array_where['F_Dept_ID'] = $deptID;
    if($companyID!=NULL)
        $array_where['F_Company_ID'] = $companyID;
    if($locationID!=NULL)
        $array_where['F_Location_ID'] = $locationID;
    if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
        $result = $obj->db->distinct()->select("User_ID,CONCAT(tbl_user_info.Full_Name,'[',tbl_employee_profile.RS_ID,']') AS Full_Name",FALSE)->from('tbl_employee_profile')->join('tbl_hierarchy_info','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')->where($array_where)->where("(Supervisor1_ID = '".$user['eid']."' OR Supervisor2_ID = '".$user['eid']."' OR Supervisor3_ID = '".$user['eid']."')")->get()->result();//->or_where('Supervisor2_ID', $user['eid'])->or_where('Supervisor3_ID', $user['eid'])
    else{ 
        if($supervisorID!=NULL)
             $array_where['Supervisor1_ID'] = $supervisorID;
        $result = $obj->db->select("User_ID,CONCAT(tbl_user_info.Full_Name,'[',tbl_employee_profile.RS_ID,']') AS Full_Name",FALSE)->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID')->join('tbl_hierarchy_info','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->get_where('tbl_user_info',$array_where)->result();
    }
    $obj->output->set_output(json_encode($result));
}

function get_User_Json($deptID){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('tbl_user_info.Full_Name');
    if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
        $result = $obj->db->distinct()->select('User_ID,tbl_user_info.Full_Name')->from('tbl_hierarchy_info')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')->where(array('F_Dept_ID' => $deptID, 'tbl_user_info.Is_Exist' => 1, 'F_User_Permission_ID <> '=>1, 'Supervisor1_ID' => $user['eid']))->or_where('Supervisor2_ID', $user['eid'])->or_where('Supervisor3_ID', $user['eid'])->get()->result();
    else    
        $result = $obj->db->select('User_ID,tbl_user_info.Full_Name')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID')->get_where('tbl_user_info',array('F_Dept_ID' => $deptID, 'tbl_user_info.Is_Exist' => 1,'F_User_Permission_ID <> '=>1))->result();
    $obj->output->set_output(json_encode($result));
}

function get_Backup_Json($from_date){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->_protect_identifiers=false;
    $obj->db->order_by('tbl_user_info.Full_Name'); 
//    $obj->db->where("tbl_user_info.User_ID NOT IN ('SELECT GROUP_CONCAT(CONVERT(F_User_ID, CHAR(8))) AS User_ID FROM tbl_leave_record WHERE From_Date = '".mdate('%Y-%m-%d',strtotime($from_date))."')");
    $obj->db->where("tbl_user_info.User_ID IS NOT NULL AND NOT FIND_IN_SET(tbl_user_info.User_ID,IFNULL((SELECT DISTINCT GROUP_CONCAT( CONVERT( F_User_ID, CHAR( 8 ) ) ) AS User_ID
FROM tbl_leave_record
WHERE From_Date = '".mdate('%Y-%m-%d',strtotime($from_date))."'),0))");
    $result = $obj->db->distinct()->select("Employee_ID,CONCAT(tbl_employee_profile.Full_Name,'[',RS_ID,']') AS Full_Name")->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID','inner')->get_where('tbl_user_info',array('Employee_ID <>' => $user['eid'], 'tbl_user_info.Is_Exist' => 1,'F_User_Permission_ID <> ' => 1))->result();
    $obj->output->set_output(json_encode($result));
}


function get_UserByCompany_Json($companyID){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('tbl_user_info.Full_Name');
    if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
        $result = $obj->db->distinct()->select('User_ID,tbl_user_info.Full_Name')->from('tbl_hierarchy_info')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')->where(array('F_Company_ID' => $companyID, 'tbl_user_info.Is_Exist' => 1, 'F_User_Permission_ID <> '=>1, 'Supervisor1_ID' => $user['eid']))->or_where('Supervisor2_ID', $user['eid'])->or_where('Supervisor3_ID', $user['eid'])->get()->result();
    else    
        $result = $obj->db->select('User_ID,tbl_user_info.Full_Name')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID')->get_where('tbl_user_info',array('F_Company_ID' => $companyID, 'tbl_user_info.Is_Exist' => 1,'F_User_Permission_ID <> '=>1))->result();
    $obj->output->set_output(json_encode($result));
}

function get_UserByLocation_Json($locationID){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('tbl_user_info.Full_Name');
    if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
        $result = $obj->db->distinct()->select('User_ID,tbl_user_info.Full_Name')->from('tbl_hierarchy_info')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_user_info','tbl_user_info.F_Employee_ID = tbl_employee_profile.Employee_ID','inner')->where(array('F_Location_ID' => $locationID, 'tbl_user_info.Is_Exist' => 1, 'F_User_Permission_ID <> '=>1, 'Supervisor1_ID' => $user['eid']))->or_where('Supervisor2_ID', $user['eid'])->or_where('Supervisor3_ID', $user['eid'])->get()->result();
    else    
        $result = $obj->db->select('User_ID,tbl_user_info.Full_Name')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_user_info.F_Employee_ID')->get_where('tbl_user_info',array('F_Location_ID' => $locationID, 'tbl_user_info.Is_Exist' => 1,'F_User_Permission_ID <> '=>1))->result();
    $obj->output->set_output(json_encode($result));
}

function get_Dept_Json($companyID){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('Dept_Name');
    if($user['type'] == "Supervisor" || $user['type'] == "Co-Supervisor")
        $result = $obj->db->distinct()->select('tbl_dept_info.Dept_Name,tbl_dept_info.Dept_ID')->from('tbl_hierarchy_info')->join('tbl_employee_profile','tbl_employee_profile.Employee_ID = tbl_hierarchy_info.F_Employee_ID','inner')->join('tbl_dept_info','tbl_dept_info.Dept_ID = tbl_employee_profile.F_Dept_ID','inner')->where(array('tbl_dept_info.Is_Exist' => 1,'tbl_dept_info.F_Company_ID' => $companyID, 'Supervisor1_ID' => $user['eid']))->or_where('Supervisor2_ID', $user['eid'])->or_where('Supervisor3_ID', $user['eid'])->get()->result();
    else
        $result = $obj->db->select('Dept_ID,Dept_Name')->get_where('tbl_dept_info',array('F_Company_ID' => $companyID, 'Is_Exist' => 1))->result();
    $obj->output->set_output(json_encode($result));
}

function get_All_Dept_Json($companyID){
    $obj =& get_instance();
    $user = $obj->session->userdata('logged_in');
    $obj->db->order_by('Dept_Name');
    $result = $obj->db->select('Dept_ID,Dept_Name')->get_where('tbl_dept_info',array('F_Company_ID' => $companyID, 'Is_Exist' => 1))->result();
    $obj->output->set_output(json_encode($result));
}

function get_Employee_Json($employeeID){
    $obj =& get_instance();
    $row = $obj->administrator_model->get_Employee_Information(array('Employee_ID' => $employeeID));
    $obj->output->set_output(json_encode($row));
}

function employee_Search($limit,$find){
    $obj =& get_instance();
    $find = rawurldecode($find);
    $len = strlen($find);
    if($len > 1){   
        $result = $obj->administrator_model->get_Specific_Info('tbl_employee_profile',"(Full_Name LIKE '".$find."%' OR RS_ID LIKE '".$find."%') AND Is_Exist=1",'Employee_ID,Full_Name',$limit);
        $data['results'] = $obj->autocomplete->make_Result($result, 'Employee_ID', 'Full_Name');
    }
    $obj->output->set_output(json_encode($data));
}

function get_Holiday_Employee_Json($holidayID){
    $obj =& get_instance();
    $row = $obj->administrator_model->get_Specific_Info('tbl_holiday_info',array('Holiday_ID' => $holidayID),'Employee',1);
    $obj->output->set_output(json_encode(explode(',', $row->Employee)));
}

function get_Tour_Employee_Json($tourID){
    $obj =& get_instance();
    $row = $obj->administrator_model->get_Specific_Info('tbl_tour_info',array('Tour_ID' => $tourID),'Employee',1);
    $obj->output->set_output(json_encode(explode(',', $row->Employee)));
}

function get_SuperVisorByCompany_Json($companyID){
    $obj =& get_instance();
    $obj->db->order_by('Full_Name');
    $result = $obj->db->distinct()->select('Full_Name,Employee_ID')
                ->join('tbl_employee_profile', 'tbl_employee_profile.Employee_ID = tbl_hierarchy_info.Supervisor1_ID','inner')
                ->get_where('tbl_hierarchy_info',array('tbl_hierarchy_info.Is_Exist'=>1,'tbl_employee_profile.Is_Exist'=>1,'F_Company_ID' => $companyID))->result();
    $obj->output->set_output(json_encode($result));
}

function get_DesignationByDept_Json($deptID){
    $obj =& get_instance();
    $obj->db->order_by('Designation_Name');
    $result = $obj->db->distinct()->select('Designation_Name,Designation_ID')
                ->get_where('tbl_designation_info',"tbl_designation_info.Is_Exist = '1' AND FIND_IN_SET('".$deptID."',tbl_designation_info.Dept_IDs)")->result();
    $obj->output->set_output(json_encode($result));
}