<?php
//*******************************************************************************************************
//	m_cluster-exception-admin-page.php  -- Processing for submission of Cluster Exception Forms.
//
//	Author: Linda Vasavong
//	Date Created: ????
//	Date Modified: Linda Vasavong
//*******************************************************************************************************

//===================================================================
// Initialization
//===================================================================
require_once('class_library/Common.php');
require_once('class_library/LoginForm.php');
require_once('class_library/database_drivers/OnBaseDriver.php');
require_once('class_library/database_drivers/MySQLDriver.php');

session_start();
session_name('cluster_exception_admin_page');

$loginForm = new LoginForm("Cluster Exception Admin Page");
$common = new Common();

$ok = "";

$validTest = true;
$dump = "";
$status = "";
$deleteStatus = "";

$checkForRecordIDViewForm = "";

$checking;
$hello;
$adminInfo;
$adminNetID;
global $adminDepts;

$adminFullDepts;
$studentRecords;	// for clusterExceptionAdminDashboard

$viewForm;
global $studentViewForm;
$studentRecordID;
$studentRecordData;	// for clusterExceptionAdminViewForm
$typeSubmit = "";

$formData = array();
$errors = array();
$error_messages = array();
$userData = $_SESSION['UserData'];

//===================================================================
// Request Handling
//===================================================================

if(isset($_POST['Login']))
{
	$loginForm->Instantiate($_POST['username'], $_POST['password']);
    $loginForm->Validate();
	
	if($loginForm->IsValid())
	{
		$userData = $loginForm->GetInfo();
		$checking = $userData;
        $_SESSION['UserData'] = $userData;

        if(AuthAdminNetID())
        {
            $_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			
            $_SESSION['adminDept'] = GetAdminDept();
			GetStudentRecordsBasedOnDept();
        }
        else
        {
            $status = "Invalid Admin";
            unset($_SESSION['UserData']);
        }
	}
}
else if(isset($_POST['View_Form']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
{
	$_SESSION['LoggedIn'] == 'Yes';
	$_SESSION['State'] = "View_Form";
	//$studentViewForm = $_POST['View_Form'];
	$studentViewForm = $_POST['recordIDForViewForm'];
	$checkForRecordIDViewForm = $_POST['recordIDForViewForm'];
	viewForm($studentViewForm);
	$viewForm = "OK";
}
else if(isset($_POST['Delete_Form']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
{
	$_SESSION['LoggedIn'] == 'Yes';
	$_SESSION['State'] = "Dashboard";
	//$studentViewForm = $_POST['View_Form'];
	$studentViewForm = $_POST['recordIDForDeleteForm'];
	deleteForm($studentViewForm);
	$deleteForm = "OK";
	AuthAdminNetID();
    GetAdminDept();
	GetStudentRecordsBasedOnDept();
}
else if(isset($_POST['DOWNLOAD']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
{
	// Handle sending data download request.
	$_SESSION['LoggedIn'] = 'Yes';
	$_SESSION['State'] = "Dashboard";
	AuthAdminNetID();
	GetAdminDept();
	DownloadData($_POST);
	AuthAdminNetID();
    GetAdminDept();
	GetStudentRecordsBasedOnDept();
}
else if(($_SESSION['LoggedIn']) == 'Yes' && ($_SESSION['State'] == "Dashboard"))
{
	$_SESSION['LoggedIn'] = 'Yes';
	$_SESSION['State'] = "Dashboard";
	AuthAdminNetID();
    GetAdminDept();
	GetStudentRecordsBasedOnDept();
}
else if(isset($_POST['back']) && ($_SESSION['State'] == "View_Form") && ($_SESSION['LoggedIn'] == 'Yes'))
{
    $_SESSION['LoggedIn'] = 'Yes';
	$_SESSION['State'] = "Dashboard";
	AuthAdminNetID();
    GetAdminDept();
	GetStudentRecordsBasedOnDept();
}
else if(isset($_POST['SaveNotSubmit']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "View_Form"))
{
	$formData = $_POST;
	$studentRecordData = $_POST;
	if(UpdateFacultyNotes($formData))
	{
			$facultyNotesStatus = "OK";
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			if(($facultyNotesStatus == "OK") && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
			{
				$_SESSION['LoggedIn'] = 'Yes';
				$_SESSION['State'] = "Dashboard";
				AuthAdminNetID();
    			GetAdminDept();
				GetStudentRecordsBasedOnDept();
			}
	}
	else
	{
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			$status = "DB_ERR";
			AuthAdminNetID();
    		GetAdminDept();
			GetStudentRecordsBasedOnDept();
	}			
}
else if(isset($_POST['Approve']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "View_Form"))
{
	$typeSubmit = "Approve";
	$formData = $_POST;
	$studentRecordData = $_POST;
	$errors = Validate($formData);
	
	if(empty($errors) && empty($error_messages))
	{
		if(ProcessApprove($formData))
		{
			$status = "APPROVE OK";
			SendEmail($formData,date('Y-m-d H:i:s', time()));
			$_SESSION['Processed'] = 'Yes';
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			if(($_SESSION['Processed'] == 'Yes') && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
			{
				unset($_SESSION['Processed']);
				$_SESSION['LoggedIn'] = 'Yes';
				$hello = "Unprocessed";
				$_SESSION['State'] = "Dashboard";
				AuthAdminNetID();
    			GetAdminDept();
				GetStudentRecordsBasedOnDept();
				$typeSubmit = "";
			}
		}
		else
		{
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			$status = "DB_ERR";
			AuthAdminNetID();
    		GetAdminDept();
			GetStudentRecordsBasedOnDept();
			$typeSubmit = "";
		}			
	}
	else
	{
		$validTest = false;
		$typeSubmit = "";
	}
}
else if(isset($_POST['Deny']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "View_Form"))
{
	$typeSubmit = "Deny";
	$formData = $_POST;
	$studentRecordData = $_POST;
	$errors = Validate($formData);
	
	if(empty($errors) && empty($error_messages))
	{
		if(ProcessDeny($formData))
		{
			$status = "DENY OK";
			SendEmail($formData,date('Y-m-d H:i:s', time()));
			$_SESSION['Processed'] = 'Yes';
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			if(($_SESSION['Processed'] == 'Yes') && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
			{
				unset($_SESSION['Processed']);
				$_SESSION['LoggedIn'] = 'Yes';
				$hello = "Unprocessed";
				$_SESSION['State'] = "Dashboard";
				AuthAdminNetID();
    			GetAdminDept();
				GetStudentRecordsBasedOnDept();
				$typeSubmit = "";
			}
		}
		else
		{
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Dashboard";
			$status = "DB_ERR";
			AuthAdminNetID();
    		GetAdminDept();
			GetStudentRecordsBasedOnDept();
			$typeSubmit = "";
		}			
	}
	else
	{
		$validTest = false;
		$typeSubmit = "";
	}
}
if(isset($_POST['Logout']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Dashboard"))
{
	unset($_SESSION['LoggedIn']);
	unset($_SESSION['UserData']);
	unset($_SESSION['State']);

	// Redirect to the login page
	if($_SERVER['SERVER_NAME'] == 'secure1.wdev.rochester.edu')
		header('Location: https://secure1.wdev.rochester.edu/ccas/cluster-exception-admin-page.php');
	else
		header('Location: https://secure1.rochester.edu/ccas/cluster-exception-admin-page.php');
}

//===================================================================
// Functions
//===================================================================
//-------------------------------------------------------------------
function Validate($data)
{
	global $error_messages;
	$errors = array();

	return $errors;
}
//-------------------------------------------------------------------
function ValidateSearch($criteria)
{
	if(empty($criteria['download_status']))
		return "TYPE";
	if((!empty($criteria['start_month']) || !empty($criteria['start_day']) || !empty($criteria['start_year'])) && 
		(empty($criteria['start_month']) || empty($criteria['start_day']) || empty($criteria['start_year'])))	
			return "DATE";
	if((!empty($criteria['end_month']) || !empty($criteria['end_day']) || !empty($criteria['end_year'])) && 
		(empty($criteria['end_month']) || empty($criteria['end_day']) || empty($criteria['end_year'])))	
			return "DATE";
			
	return "OK";
}
//------------------------------------------------------------------------------------------------------
// Function to autheticate Admin NetID Cluster Exception Admin Page
//------------------------------------------------------------------------------------------------------
function GetAdmin($adminNetID)
{
	$db_drvr = new MySQLDriver();
	$results = $db_drvr->ReadTable('ClusterExceptionAdmins',array('netID' => $adminNetID));
	return $results;
}
//-------------------------------------------------------------------
function AuthAdminNetID()
{
    global $userData;
    global $adminNetID;
    global $adminInfo;
    $adminNetID = $userData['user'];
    $results = GetAdmin($adminNetID);
    
    $adminInfo = $results[0];
    if(strcasecmp($adminInfo['netID'], $adminNetID) == 0)
    {
       return true;
    }
    else
    {
        return false;
    }
}
//-------------------------------------------------------------------
function GetAdminDept()
{
    global $adminInfo;
	global $adminDepts;
	global $adminFullDepts;
    $adminDepts = $adminInfo['clusterDepartment'];
	$adminDepts = explode(",", $adminDepts);
	$adminFullDepts = $adminDepts;
	//echo var_dump($adminFullDepts);
}
//-------------------------------------------------------------------
function GetFullDeclarations($criteria)
{
	$db_drvr = new MySQLDriver();
	global $adminInfo;
	global $adminDepts;
	
	global $adminFullDepts;
	$records = array();
			
	$startFlag = (!empty($criteria['start_month']) ? true : false);
	$endFlag = (!empty($criteria['end_month']) ? true : false);
		
	$startDate = $criteria['start_year'] . "-" . $criteria['start_month'] . "-" . $criteria['start_day'] . " 00:00:00";
	$endDate = $criteria['end_year'] . "-" . $criteria['end_month'] . "-" . $criteria['end_day'] . " 23:59:59";
	
	$formType = $criteria['download_status'];
	// Get all declarations that match the given criteria
		if($formType == "approve")
		{
			$query = "SELECT * FROM ClusterExceptionForms WHERE approve = 'Yes'";
		}
		else
		{
			$query = "SELECT * FROM ClusterExceptionForms WHERE deny = 'Yes'";
		}

		$query .= " AND (";

		$cnt = 0;
		foreach($adminFullDepts as $department)
		{
			$cnt++;
			$query .= "(clusterDept = '" . $department . "')";
			if($cnt == count($adminFullDepts))
			{

			} else {
				$query .= " OR ";
			}
		}

		$query .= ")";

		if(!$startFlag && !$endFlag)
			$query .= " AND DATE(facultyDateSubmitted) != '0000-00-00 00:00:00'";
		else if(!$endFlag)
			$query .= " AND DATE(facultyDateSubmitted) >= '" . $startDate . "'";
		else if(!$startFlag)
			$query .= " AND DATE(facultyDateSubmitted) <= '" . $endDate . "'" . " AND DATE(facultyDateSubmitted) != '0000-00-00 00:00:00'";
		else
			$query .= " AND DATE(facultyDateSubmitted) >= '" . $startDate . "'" . " AND DATE(facultyDateSubmitted) <= '" . $endDate . "'";
		$records = $db_drvr->ReadTableQuery($query);

	
	return $records;
}
//-------------------------------------------------------------------
function DownloadData($criteria)
{
	global $ok;
	$flag = ValidateSearch($criteria);
	$ok = $flag;
		
	if($flag == "OK")
	{
		$data = GetFullDeclarations($criteria);

		$filename = "ClusterExceptionForms_" . date('Ymd') . ".csv";	

		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv");
		
		$out = fopen("php://output",'w');
				
		$flag = false;
		foreach($data as $row)
		{
			if(!$flag)
			{
				fputcsv($out, array_keys($row), ',', '"');
				$flag = true;	
			}
				
			array_walk($row, 'cleanData');
			fputcsv($out, array_values($row), ',', '"');
		}
			
		fclose($out);
		exit();
		$ok = "";
	}
}
//------------------------------------------------------------------------------------------------------
// Function to get student record based on netID for cluster exception form 
//------------------------------------------------------------------------------------------------------
function GetStudentRecordBasedOnRecordId($studentRecordID)
{
	$db_drvr = new MySQLDriver();
	$studentRecordData = $db_drvr->ReadTable('ClusterExceptionForms',array('recordID' => $studentRecordID));
	return $studentRecordData[0];
}
//-------------------------------------------------------------------
function viewForm($studentViewForm)
{
	global $studentRecordData;
	global $studentRecordID;
    $studentRecordID = $studentViewForm;
	$studentQuery = GetStudentRecordBasedOnRecordId($studentRecordID);
	$studentRecordData = $studentQuery; 
}
//-------------------------------------------------------------------
function deleteForm($studentViewForm)
{
	global $deleteStatus;
	global $studentRecordID;
    $studentRecordID = $studentViewForm;
	$db_drvr = new MySQLDriver();

    // Delete this record in MySQL
	$id = $db_drvr->Delete('ClusterExceptionForms',$studentRecordID);
	
	if($id == false)
	{
		$status = "DB_ERR";
		return false;
	}
	else
	{
		$deleteStatus = "OK";
		return true;
	}
}
//------------------------------------------------------------------------------------------------------
// Function to get a student's records based on the admin's department(s) for cluster exception admin dashboard
//------------------------------------------------------------------------------------------------------
function GetStudentRecordsBasedOnDepartment($clusterDept)
{
	$db_drvr = new MySQLDriver();
	$results = $db_drvr->ReadTableQuery("SELECT * FROM ClusterExceptionForms WHERE clusterDept = '$clusterDept' AND (approve = '' AND deny = '') ORDER BY dateSubmitted ASC");
	//return $results;

	global $adminInfo;
	$returnedData = array();

	// Handling for Alphabetic load balancing 
	if($adminInfo['alphaLoadBalance'] == 'OFF')
	{
		return $results;
	}
	else
	{
		$alpha = explode(",",$adminInfo['alphaLoadBalance']);
		
		// Moving data from $results to adviser's assigned students according to first letter of last name
		foreach($results as $declaration)
		{
			$string = $declaration['studentLastName'];
			
			if(in_array(strtoupper($string[0]),$alpha))
			{
				$returnedData[] = $declaration;	
			}
		}
		return $returnedData;
	}
}
//-------------------------------------------------------------------
function GetStudentRecordsBasedOnDept()	// also ones not checked yet
{
    global $adminDepts;
    global $studentRecords;

    foreach($adminDepts as $dept)
    {
        $results = GetStudentRecordsBasedOnDepartment($dept);
        if($results)
        {
			$studentRecords[$dept] = $results;
        }
    }
}
//-------------------------------------------------------------------
function UpdateFacultyNotes($data)
{
	$db_drvr = new MySQLDriver();
    // Submit this record to MySQL
	$record = array();
	
	foreach($data as $key => $value)
	{
		/* STRIP OUT ANY KEYS YOU'RE NOT SENDING TO THE MYSQL TABLE */
		if($key != 'Save_From_Admin' && $key != 'SaveNotSubmit' && $key != 'clusterReasonCount' && $key != 'Approve'  && $key != 'Deny'  && $key != 'Save Comments and Take No Action' && $key != 'facultyNotesCount' && $key != 'clusterTypeReadable')
		{
			$record[$key] = $value;	
		}
	}
	
	$id = $db_drvr->UpdateRecord('ClusterExceptionForms',$record['recordID'],$record);
	
	if($id == 0)
		return false;
	else
		return true;
}
//-------------------------------------------------------------------
function ProcessApprove($data)
{
	$ob_drvr = new OnBaseDriver();	// OnBase driver
    $db_drvr = new MySQLDriver();	// SQL driver
    
	$record = array();
	$ob_record = array();

	// Send this record to MySQL
	
	foreach($data as $key => $value)
	{
		/* STRIP OUT ANY KEYS YOU'RE NOT SENDING TO THE MYSQL TABLE */
		if($key != 'Save_From_Admin' && $key != 'SaveNotSubmit' && $key != 'Approve'  && $key != 'Deny'  && $key != 'Save Comments and Take No Action' && $key != 'clusterReasonCount' && $key != 'facultyNotesCount' && $key != 'clusterTypeReadable')
		{
			$record[$key] = $value;	
		}
	}

	$record['approve'] = "Yes";
	$record['facultyIPAddress'] = $_SERVER['REMOTE_ADDR'];
	$record['facultyDateSubmitted'] = date('Y-m-d H:i:s', time());
	
	$id = $db_drvr->UpdateRecord('ClusterExceptionForms',$record['recordID'],$record);
	
	if($id == 0)
		return false;

	// Send this record to OnBase	
	$ob_record['OBBtn_Save'] = 'Submit';
	$ob_record['OBDocumentType'] = '982'; // Cluster Exception E-Form
	$ob_record['LanguageParam'] = 'en-us';
	$ob_record['OBKey__105_1'] = $data['studentFirstName']; // First Name			
	$ob_record['OBKey__103_1'] = $data['studentLastName']; // Last Name
	$ob_record['OBKey__107_1'] = $data['studentMiddleInitial']; // Middle Name
	$ob_record['OBKey__102_1'] = $data['studentID']; // Student ID
	$ob_record['OBKey__143_1'] = $data['classYear']; // Current Class Year
	$ob_record['OBKey__111_1'] = $data['emailAddress']; // WF-StudentEmail
	$ob_record['OBKey__112_1'] = $data['phoneNumber']; // WF-StudentPhoneIn
	$ob_record['OBKey__123_1'] = $data['localAddress']; // WF-StudentAddressLN1 also there is WF-StudentAddressLN2
	$ob_record['OBKey__1096_1'] = $data['clusterName']; // CE-ClusterName
	$ob_record['OBKey__1097_1'] = htmlspecialchars($data['clusterTypeReadable']); // CE-ClusterType

	if($data['courseChecked1'] == "Yes")
	{
		$data['courseChecked1'] = "X";
	}
	$ob_record['OBKey__1098_1'] = $data['courseChecked1']; // CE-CourseChecked1
	$ob_record['OBKey__1099_1'] = $data['courseNumber1']; // CE-CourseNumber1
	$ob_record['OBKey__1100_1'] = htmlspecialchars($data['courseTitle1']); // CE-CourseTitle1
	$ob_record['OBKey__1101_1'] = $data['courseSemester1']; // CE-CourseSemester1
	$ob_record['OBKey__1102_1'] = $data['courseCredit1']; // CE-CourseCredit1

	if($data['courseChecked2'] == "Yes")
	{
		$data['courseChecked2'] = "X";
	}
	$ob_record['OBKey__1103_1'] = $data['courseChecked2']; // CE-CourseChecked2
	$ob_record['OBKey__1104_1'] = $data['courseNumber2']; // CE-CourseNumber2
	$ob_record['OBKey__1105_1'] = htmlspecialchars($data['courseTitle2']); // CE-CourseTitle2
	$ob_record['OBKey__1106_1'] = $data['courseSemester2']; // CE-CourseSemester2
	$ob_record['OBKey__1107_1'] = $data['courseCredit2']; // CE-CourseCredit2

	if($data['courseChecked3'] == "Yes")
	{
		$data['courseChecked3'] = "X";
	}
	$ob_record['OBKey__1108_1'] = $data['courseChecked3']; // CE-CourseChecked3
	$ob_record['OBKey__1109_1'] = $data['courseNumber3']; // CE-CourseNumber3
	$ob_record['OBKey__1110_1'] = htmlspecialchars($data['courseTitle3']); // CE-CourseTitle3
	$ob_record['OBKey__1111_1'] = $data['courseSemester3']; // CE-CourseSemester3
	$ob_record['OBKey__1112_1'] = $data['courseCredit3']; // CE-CourseCredit3

	if($data['courseChecked4'] == "Yes")
	{
		$data['courseChecked4'] = "X";
	}
	$ob_record['OBKey__1113_1'] = $data['courseChecked4']; // CE-CourseChecked4
	$ob_record['OBKey__1114_1'] = $data['courseNumber4']; // CE-CourseNumber4
	$ob_record['OBKey__1115_1'] = htmlspecialchars($data['courseTitle4']); // CE-CourseTitle4
	$ob_record['OBKey__1116_1'] = $data['courseSemester4']; // CE-CourseSemester4
	$ob_record['OBKey__1117_1'] = $data['courseCredit4']; // CE-CourseCredit4

	if($data['courseChecked5'] == "Yes")
	{
		$data['courseChecked5'] = "X";
	}
	$ob_record['OBKey__1118_1'] = $data['courseChecked5']; // CE-CourseChecked5
	$ob_record['OBKey__1119_1'] = $data['courseNumber5']; // CE-CourseNumber5
	$ob_record['OBKey__1120_1'] = htmlspecialchars($data['courseTitle5']); // CE-CourseTitle5
	$ob_record['OBKey__1121_1'] = $data['courseSemester5']; // CE-CourseSemester5
	$ob_record['OBKey__1122_1'] = $data['courseCredit5']; // CE-CourseCredit5

	if($data['courseChecked6'] == "Yes")
	{
		$data['courseChecked6'] = "X";
	}
	$ob_record['OBKey__1123_1'] = $data['courseChecked6']; // CE-CourseChecked6
	$ob_record['OBKey__1124_1'] = $data['courseNumber6']; // CE-CourseNumber6
	$ob_record['OBKey__1125_1'] = htmlspecialchars($data['courseTitle6']); // CE-CourseTitle6
	$ob_record['OBKey__1126_1'] = $data['courseSemester6']; // CE-CourseSemester6
	$ob_record['OBKey__1127_1'] = $data['courseCredit6']; // CE-CourseCredit6

	$ob_record['OBKey__1128_1'] = htmlspecialchars($data['clusterReason']); // CE-ClusterReason
	$ob_record['OBKey__1129_1'] = $data['clusterDept']; // CE-ClusterDept
	$ob_record['OBKey__1130_1'] = htmlspecialchars($data['facultyNotes']); // CE-FacultyNotes

	$facultyFullName = $data['facultyFirstName'] . ' ' . $data['facultyLastName']; // MDF-Division
	$ob_record['OBKey__1137_1'] = $facultyFullName; // WF-Approved/Denied

	$ob_submit = $ob_drvr->OnBaseSubmit($ob_record);

	if(!$ob_submit)
	{
		$db_drvr->UpdateRecord('ClusterExceptionForms',$data['recordID'],array('onBaseSubmitted' => 0));
	}
	else
	{
		$db_drvr->UpdateRecord('ClusterExceptionForms',$data['recordID'],array('onBaseSubmitted' => 1));
	}

	return true;
}
//-------------------------------------------------------------------
function ProcessDeny($data)
{
    $db_drvr = new MySQLDriver();
    // Submit this record to MySQL
	$record = array();
	
	foreach($data as $key => $value)
	{
		/* STRIP OUT ANY KEYS YOU'RE NOT SENDING TO THE MYSQL TABLE */
		if($key != 'Save_From_Admin' && $key != 'SaveNotSubmit' && $key != 'Approve'  && $key != 'Deny'  && $key != 'Save Comments and Take No Action' && $key != 'clusterReasonCount' && $key != 'facultyNotesCount' && $key != 'clusterTypeReadable')
		{
			$record[$key] = $value;	
		}
	}

	$record['deny'] = "Yes";
	$record['facultyIpAddress'] = $_SERVER['REMOTE_ADDR'];
	$record['facultyDateSubmitted'] = date('Y-m-d H:i:s', time());
	
	$id = $db_drvr->UpdateRecord('ClusterExceptionForms',$record['recordID'],$record);
	
	if($id == 0)
		return false;
	else
		return true;
}
//-------------------------------------------------------------------
function SendEmail($data, $date)
{
	$to = $data['emailAddress'];
	global $typeSubmit;
	
	if($typeSubmit == "Approve")
	{
		$subject = 'Cluster Exception Form Approved By Deparment';
	}
	else
	{
		$subject = 'Cluster Exception Form Denied By Department';
	}
	
	//$headers = "From: " . $data['facultyEmailAddress'] . "\r\n";
	//$headers .= "CC: " . "cascas@ur.rochester.edu" . "\r\n";
	$headers = "From: " . "cascas@ur.rochester.edu" . "\r\n";
	$headers .= "CC: " . $data['facultyEmailAddress'] . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$message = "<html><head><style>th, td{padding: 2px; text-align: left;} @media only screen and (max-width:480px){table{width:100% !important; max-width:480px !important;}</style></head><body>";
	$message .= "<table style='width:100%;'>";
	$message .= "<tr><td colspan='2' style='width: 100%; text-align: center; background-color: #021e47; color: #FFC125; border-bottom: 3px solid #FFC125; border-radius: 10px;'>Arts, Sciences and Engineering<hr/><div style='color:white; font-size: 175%; padding: 0px 0px 15px 0px;'><span style='color:#FFC125;'>U</span><span style='font-variant:small-caps;'>niversity</span> <i>of</i> <span style='color:#FFC125;'>R</span><span style='font-variant:small-caps;'>ochester</span></div></td></tr>";
	if($typeSubmit == "Approve")
	{
		$message .= "<tr><td colspan='2'>Submission of this document means your cluster exception form has been APPROVED by the department and has been sent to the CURRICULUM COMMITTEE for a FINAL REVIEW. You will be notified if your cluster exception form is approved or denied by the CURRICULUM COMMITTEE.</td></tr>";
	}
	else
	{
		$message .= "<tr><td colspan='2'>Submission of this document means your cluster exception form has been DENIED by the department. </td></tr>";
		$message .= "<tr><td colspan='2'>If you have any questions about this decision, please contact an authorized faculty responsible for making cluster exception decisions.";
		$message .= "<tr><td colspan='2'>Here is a link to the Authorized Approval List: <a href='https://www.rochester.edu/college/ccas/undergraduate/curriculum/authorized-approval-list.html' target='_new'>https://www.rochester.edu/college/ccas/undergraduate/curriculum/authorized-approval-list.html</a></td></tr>";
	
	}

	$message .= "<tr><td colspan='2'>You have submitted a Cluster Exception Form:<div align='center'><b>Student Information</b></div><hr/></td></tr>";
	$message .= "<tr><td>Student Name:</td><td>" . $data['studentFirstName'] . " " . $data['studentMiddleInitial'] . " " . $data['studentLastName'] . "</td></tr>";
	$message .= "<tr><td>Student UID:</td><td>" . $data['studentID'] . "</td></tr>";
	$message .= "<tr><td>Class Year:</td><td>" . $data['classYear'] . "</td></tr>";
	$message .= "<tr><td>Email Address:</td><td>" . $data['emailAddress'] . "</td></tr>";
	$message .= "<tr><td>Local/Cell Phone Number:</td><td>" . $data['phoneNumber'] . "</td></tr>";
	$message .= "<tr><td>Local Address or CMC BOX:</td><td>" . $data['localAddress'] . "</td></tr>";
	$message .= "<tr><td colspan='2'><div align='center'><b>Cluster Exception Information</b></div><hr/></td></tr>";
	
	if(!empty($data['clusterType']))
	{
		$dept = "";
		if($data['clusterType'] == "humanities")
		{
			$dept = "Humanities";
		} 
		else if($data['clusterType'] == "naturalSciences")
		{
			$dept = "Natural Sciences";
		}
		else if($data['clusterType'] == "socialSciences")
		{
			$dept = "Social Sciences";
		}
		$message .= "<tr><td colspan='2'><div align='center'><b>Cluster Type: " . $dept . "</b></div></td></tr>";
		$message .= "<tr><td colspan='2'><div align='center'><b>Cluster Department: " . $data['clusterDept'] . "</b></div></td></tr>";
		$message .= "<tr><td colspan='2'><div align='center'><b>Cluster Name: " . $data['clusterName'] . "</b></div></td></tr></table><br/><br/>";
		$message .= "<table style='width:100%;'><tr>";
		$message .= "<td><b>Proposed Course Exception(s)</b></td>";
		$message .= "<td><b>Course Number</b></td>";
		$message .= "<td><b>Course Title</b></td>";
		$message .= "<td><b>Semester</b></td>";
		$message .= "<td><b>Credit Hrs</b></td></tr>";

		if(!empty($data['courseChecked1']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked1'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber1']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber1'];
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle1']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle1'];
			$message .= "</td>";
		}

		if(!empty($data['courseSemester1']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester1'];
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit1']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit1'];
			$message .= "</td></tr>";
		}

		if(!empty($data['courseChecked2']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked2'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber2']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber2'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle2']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle2'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseSemester2']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester2'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit2']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit2'];
			$message .= "</td></tr>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td></tr>";
		}

		if(!empty($data['courseChecked3']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked3'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber3']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber3'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle3']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle3'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseSemester3']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester3'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit3']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit3'];
			$message .= "</td></tr>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td></tr>";
		}

		if(!empty($data['courseChecked4']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked4'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber4']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber4'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle4']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle4'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseSemester4']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester4'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit4']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit4'];
			$message .= "</td></tr>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td></tr>";
		}

		if(!empty($data['courseChecked5']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked5'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber5']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber5'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle5']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle5'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseSemester5']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester5'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit5']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit5'];
			$message .= "</td></tr>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td></tr>";
		}

		if(!empty($data['courseChecked6']))
		{
			$message .= "<tr><td>";
			$message .= $data['courseChecked6'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<tr><td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}

		if(!empty($data['courseNumber6']))
		{
			$message .= "<td>";
			$message .= $data['courseNumber6'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseTitle6']))
		{
			$message .= "<td>";
			$message .= $data['courseTitle6'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseSemester6']))
		{
			$message .= "<td>";
			$message .= $data['courseSemester6'];
			$message .= "</td>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td>";
		}
			
		if(!empty($data['courseCredit6']))
		{
			$message .= "<td>";
			$message .= $data['courseCredit6'];
			$message .= "</td></tr>";
		}
		else
		{
			$message .= "<td>";
			$message .= "&nbsp;";
			$message .= "</td></tr>";
		}
	}
	$message .= "</table><br/>";

	if(!empty($data['clusterReason']))
	{
		$message .= "<p>Cluster Exception Reason: " . $data['clusterReason'] . "</p>";
			
	}

	if(!empty($data['facultyNotes']))
	{
		$message .= "<p>Department Rationale: " . $data['facultyNotes'] . "</p>";
			
	}

	if(!empty($data['additionalInfo']))
	{
		$message .= "<p>Additional information for reviewer. (Optional): " . $data['additionalInfo'] . "</p>";
			
	}

	$message .= "<table style='width:100%;'>";
	$message .= "<tr><td colspan='2'><hr/></td></tr>";

	$message .= "<tr><td>Date/Time Submitted: " . $date . "</td></tr>";

	$message .= "<tr><td colspan='2' style='width: 100%; text-align: center; background-color: #021e47; color: white; border-top: 3px solid #FFC125; border-radius: 10px;'><p>Copyright &#169; 2013&#150;2015. All rights reserved.<br /><a style='color:white;' href='http://www.rochester.edu/'>University of Rochester</a> | <a style='color:white;' href='http://www.rochester.edu/college/'>AS&#38;E</a> | <a style='color:white;' href='index.html'>Registrar</a><br/><a style='color:white;' href='http://www.rochester.edu/accessibility.html'>Accessibility</a> | <a style='color:white;' href='http://text.rochester.edu/tt/referrer' title='Access a text-only version of this page.'>Text</a> | <a style='color:white;' href='http://www.rochester.edu/college/webcomm/' title='Get help with your AS&amp;E website.'>Web Communications</a></p></td></tr>";
	$message .="</table>";	
	$message .= "</body></html>";
	
	mail($to, $subject, $message, $headers);
}