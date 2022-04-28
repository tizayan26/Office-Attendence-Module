<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menu
 *
 * @author User
 */
class menu {
    final function load_Menu($session,$row,$out_time=NULL,$profile_pic=NULL){
        $obj =& get_instance();
        $obj->load->helper('html');
        $menu  = "<ul id='nav'>";
            $menu .= "<li>";
            $menu .= anchor(site_url(array('home')),img(array('src'=>'assets/images/home.png','height'=>16,'width'=>16)).nbs().'Home');
            $menu .= "</li>";
            $menu .= "<li>";
            if($session['type']=="Super Administrator" || $session['type']=="Administrator"){
            $menu .= "<a href='#'>";
            $menu .= img(array('src'=>'assets/images/profile.png','height'=>16,'width'=>16)).nbs()."Employee Manager</a>";
            }else
            $menu .= anchor(site_url(array('home','my_Profile')),img(array('src'=>'assets/images/profile.png','height'=>16,'width'=>16)).nbs().'Profile');
            if($session['type']=="Super Administrator" || $session['type']=="Administrator"){
               $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/update_employee_db','Update Employee DB');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/employee_List','Employee List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/employee_Entry','Employee Entry');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/leave_Calculator/1','Leave Count');
                $menu .= "</li>";
            $menu .= "</ul>";
            }
            $menu .= "</li>";
           
            if($row->Report==1){
            $menu .= "<li>";
            $menu .= anchor(site_url(array('home','report')),img(array('src'=>'assets/images/report.png','height'=>16,'width'=>16)).nbs().'Report');
            if($row->All_Report == 1){
                $menu .= "<ul>"; 
                $menu .= "<li>";
                $menu .= anchor('home/late_report','Late Report');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('home/absent_report','Absent Report');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('home/leave_report','Leave Report');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('home/holiday_report','Holiday Report');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('home/staffUnderSV_report','Staffs Under Supervision');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('home/basicInfo_report','Basic Info Report');
                $menu .= "<li>";
                $menu .= anchor('home/inactiveUser_report','Inactive User Report');
                $menu .= "</li>";
                $menu .= "</li>";
                if($session['type']=="Super Administrator" || $session['type']=="Administrator" || $row->Absent_List_IT == 1 || $session['eid'] == '236'){
                    $menu .= "<li>";
                    $menu .= anchor('administrator/absentLeave_List/1','Absent List');
                    $menu .= "</li>";
                   
                }
                if($session['type']=="Super Administrator" || $session['type']=="Administrator"){
                    $menu .= "<li>";
                    $menu .= anchor('administrator/absentLeave_List/3','Absent List Services');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/sv_List/1','Supervisor List');
                    $menu .= "</li>";
                   
                }
                $menu .= "</ul>";
            }    
            $menu .= "</li>";
            }
            if($row->Holiday==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/holiday.png','height'=>16,'width'=>16)).nbs()."Holiday Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/holiday_List','Holiday List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/holiday_Entry','Holiday Entry');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/assign_Holiday','Holiday Assign');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($row->Holiday==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/tour.png','height'=>16,'width'=>16)).nbs()."Tour Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/tour_List','Tour List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/tour_Entry','Tour Entry');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/assign_Tour','Tour Assign');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($row->Company==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/company.png','height'=>16,'width'=>16)).nbs()."Company Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/company_List','Company List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/company_Entry','Company Entry');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
             if($row->Location==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/location.png','height'=>16,'width'=>16)).nbs()."Location Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/location_List','Location List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/location_Entry','Location Entry');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($row->Department==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/group_edit.png','height'=>16,'width'=>16)).nbs()."Department Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/dept_List','Department List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/dept_Entry','Department Entry');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($row->Department==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/group_edit.png','height'=>16,'width'=>16)).nbs()."Designation Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/designation_List','Designation List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/designation_Entry','Designation Entry');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($session['type']=="Super Administrator" || $session['type']=="Administrator" || $row->User_Manager==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/user_edit.png','height'=>16,'width'=>16)).nbs()."User Manager</a>";
            $menu .= "<ul>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/user_List','User List');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/user_Entry','Add New User');
                    $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($session['type']!="Administrator"){
                $menu .= "<li>";
                $menu .= anchor('home/update_Password',img(array('src'=>'assets/images/user_edit.png','height'=>16,'width'=>16)).nbs()."Change Password");
                $menu .= "</li>";
            }
            if($row->Hierarchy==1){
            $menu .= "<li>";
            $menu .= "<a href='#'>".img(array('src'=>'assets/images/hierarchy.png','height'=>16,'width'=>16)).nbs()."Hierarchy Manager</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/employeeHierarchy_List','Hierarchy List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/employeeHierarchy_Entry','Hierarchy Entry');
                $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($session['type']=="Super Administrator" || $row->Recruitment_Manager){
            $menu .= "<li>";
            $menu .= "<span id=\"notification_count_recruit\"></span><a href='#'>".img(array('src'=>'assets/images/recruitment.png','height'=>16,'width'=>16)).nbs()."Recruitment Mngr</a>";
            $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('home/recruitment_request','Recruitment Request');
                $menu .= "</li>";
                if($session['type']=="Supervisor"){
                $menu .= "<li>";
                $menu .= anchor('home/recruitment_status','Recruitment Status');
                $menu .= "</li>";
                }
                if($session['type']=="Super Administrator" || $row->Recruitment_Preapproval){
                $menu .= "<li>";
                $menu .= anchor('administrator/recruitmentRequest_List','Request List');
                $menu .= "</li>"; 
                $menu .= "<li>";
                $menu .= anchor('administrator/recruitmentPreApproval_List','Request Status');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/allRecruitment_List','Accomplished List');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/recruitmentCancelRequest_List','Cancel Request List');
                $menu .= "</li>";
                }              
//                if($session['type']=="Super Administrator" || $session['type']=="Administrator"){
//                $menu .= "<li>";
//                $menu .= anchor('administrator/candidate_Entry','Candidate Entry');
//                $menu .= "</li>"; 
//                }
//                $menu .= "<li>";
//                $menu .= anchor('administrator/candidate_List','Pre-selection List');
//                $menu .= "</li>";
//                $menu .= "<li>";
//                $menu .= anchor('administrator/candidate_Selected_List','Selected List');
//                $menu .= "</li>";
                
                
            $menu .= "</ul>";
            $menu .= "</li>";
            }
            if($session['type']=="Super Administrator"){
            $menu .= "<li>";
            $menu .= '<a href="#">'.img(array('src'=>'assets/images/user_permission.png','height'=>16,'width'=>16)).nbs().'Role Manager</a>';
                    $menu .= "<ul>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/userRole_List','Role List');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/userRole_Entry','Role Entry');
                    $menu .= "</li>";
            $menu .= "</ul>";
            $menu .= "</li>";    
            }

            $menu .= "<li>";
            if($session['type']=="Super Administrator" || $session['type']=="Administrator" || $row->Leave_Manager || $row->Leave_Preapproval)//|| $session['type']=="Supervisor" || $session['type']=="Co-Supervisor"
            $menu .= "<span id=\"notification_count\"></span><a href='#'>";
            $menu .= img(array('src'=>'assets/images/luggage.png','height'=>16,'width'=>16)).nbs()."Leave</a>";
            $menu .= "<ul>";
            if($session['type']=="Super Administrator" || $session['type']=="Administrator" || $row->Leave_Manager){
                    $menu .= "<li>";
                    $menu .= anchor('administrator/leaveType_List','Leave Type List');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/leaveType_Entry','Leave Type Entry');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/leaveRequest_List','Leave Request List');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('administrator/allLeave_List','All Leave List');
                    $menu .= "</li>";
            }
            if($row->Leave_Preapproval== 1){
                    $menu .= "<li>";
                    $menu .= anchor('administrator/leavePreApproval_List','Leave Pre-approval List');
                    $menu .= "</li>";
                    
                    $menu .= "<li>";
                    $menu .= anchor('administrator/supAllLeave_List','All Leave List');
                    $menu .= "</li>";
            }
            if($session['type']!="Administrator"){
                    $menu .= "<li>";
                    $menu .= anchor('home/leave_status','Leave Status');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('home/leave_application','Leave Apply');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('home/leaveBackup_List','Backup Duty List');
                    $menu .= "</li>";
                    $menu .= "<li>";
                    $menu .= anchor('home/leave_List','All Staff List');
                    $menu .= "</li>";
                    
            }
            $menu .= "</ul>";
            $menu .= "</li>";

            if($session['type'] == "Supervisor" || $session['type'] == "Co-Supervisor" || $row->Leave_Preapproval== 1){
                $menu .= "<li>";
                $menu .= anchor('administrator/approvedLeave_List',img(array('src'=>'assets/images/luggage.png','height'=>16,'width'=>16)).nbs().'Approved Leave'.nbs().'<span id="notification_count1"></span>');
                $menu .= "</li>";
                $menu .= "<li>";
                $menu .= anchor('administrator/deniedLeave_List',img(array('src'=>'assets/images/luggage.png','height'=>16,'width'=>16)).nbs().'Denied Leave'.nbs().'<span id="notification_count2"></span>');
                $menu .= "</li>";
            }
            
//            $menu .= "<li>";
//                $menu .= anchor('home/leaveBackup_List',img(array('src'=>'assets/images/luggage.png','height'=>16,'width'=>16)).nbs().'Backup Duty List'.nbs().'<span id="notification_count1"></span>');
//            $menu .= "</li>";
            if($session['type']=="Administrator"){
            $menu .= "<li>";
            $menu .= anchor('administrator/user_Inactive_Form',img(array('src'=>'assets/images/user_edit.png','height'=>16,'width'=>16)).nbs().'Inactive Form');
            $menu .= "</li>";

            $menu .= "<li>";
            $menu .= anchor('administrator/force_Present',img(array('src'=>'assets/images/user_edit.png','height'=>16,'width'=>16)).nbs().'Force Present');
            $menu .= "</li>";
            }
            
            if($session['type']=="Graphics Designer" || $session['eid']== '508'){
            $menu .= "<li>";
            $menu .= anchor('home/idCardInfo_report',img(array('src'=>'assets/images/id_card.png','height'=>16,'width'=>16)).nbs().'ID Card Info');
            $menu .= "</li>";
            }
            
            if($session['type']=="Photographer"){
                $menu .= "<li>";
                $menu .= "<a href='#'>".img(array('src'=>'assets/images/camera.png','height'=>16,'width'=>16)).nbs()."Photo Manager</a>";
                $menu .= "<ul>";
                $menu .= "<li>";
                $menu .= anchor('administrator/photo_List','Photo Entry');
                $menu .= "</li>";
//                $menu .= "<li>";
//                $menu .= anchor('administrator/photo_Entry','Employee Entry');
//                $menu .= "</li>";
          
            $menu .= "</ul>";
            
            $menu .= "</li>";
            }
            
            $menu .= "<li>";
            $menu .= anchor('home/logout',img(array('src'=>'assets/images/lock_go.png','height'=>16,'width'=>16)).nbs().'Logout');
            $menu .= "</li>";
//            if($out_time=='00:00:00'){
//            $menu .= "<li>";
//            $menu .= anchor('home/signout',img(array('src'=>'assets/images/sign_out.png','height'=>16,'width'=>16)).nbs().'Office out','title="Signout" onClick="javascript:return confirm(\'Do you want to Signout?\')"');
//            $menu .= "</li>";
//            }
//            $menu .= "<li>";
//            $menu .= $profile_pic;
//            $menu .= "</li>";
        $menu .= "</ul>";
        
        return $menu;
    }
}

?>
