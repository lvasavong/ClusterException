<?php
//*******************************************************************************************************
//	m_cluster-exception-form.php  -- Processing for submission of Cluster Exception Forms.
//
//	Author: Linda Vasavong
//	Date Created: ????
//	Date Modified: ???
//*******************************************************************************************************

//===================================================================
// Initialization
//===================================================================
require_once('class_library/Common.php');
require_once('class_library/LoginForm.php');
require_once('class_library/database_drivers/MySQLDriver.php');

session_start();
session_name('cluster_exception');

$loginForm = new LoginForm("Cluster Exception Form");
$common = new Common();

$validTest = true;

$dump = "";
$userStatus = "";

$emails;

$formData = array();
$errors = array();
$realTimeNotices;
$error_messages = array();
$userData = $_SESSION['UserData'];
$studentNetID;
$studentInfo;
$clusterDeptCode;

//===================================================================
// Request Handling
//===================================================================

if($_SESSION['Processed'] == 'Yes')
{
	//Catch to eliminate duplicate submissions
	unset($_SESSION['Processed']);
	
	// Redirect to the login page
	if($_SERVER['SERVER_NAME'] == 'secure1.wdev.rochester.edu')
		header('Location: https://secure1.wdev.rochester.edu/ccas/cluster-exception-form.php');
	else
		header('Location: https://secure1.rochester.edu/ccas/cluster-exception-form.php');
}
else if(isset($_POST['Login']))
{
	$loginForm->Instantiate($_POST['username'], $_POST['password']);
	$loginForm->Validate();
	
	if($loginForm->IsValid())
	{
		$userData = $loginForm->GetInfo();
		$_SESSION['UserData'] = $userData;

		if(AuthStudentNetID())
		{
			$_SESSION['LoggedIn'] = 'Yes';
			$_SESSION['State'] = "Agree";
		}
		else
		{
			$userStatus = "Invalid User";
			unset($_SESSION['UserData']);
		}
	}
}
else if(isset($_POST['Continue']) && ($_SESSION['State'] == "Agree"))
{
	if(!isset($_POST['policyUnderstood']))
	{
		$validTest = false;
	}
	else
	{
		$_SESSION['State'] = "Form";
	}
}
else if(isset($_POST['Save']) && ($_SESSION['LoggedIn'] == 'Yes') && ($_SESSION['State'] == "Form"))
{
	$formData = $_POST;
	$errors = Validate($formData);
	
	if(empty($errors) && empty($error_messages))
	{
		if(Process($formData))
		{
			$status = "OK";
			SendEmail($formData,date('Y-m-d H:i:s', time()));
			unset($_SESSION['LoggedIn']);
			unset($_SESSION['UserData']);
			unset($_SESSION['State']);
			$_SESSION['Processed'] = 'Yes';
		}
		else
		{
			$status = "DB_ERR";
		}			
	}
	else
	{
		$validTest = false;
	}
}
else
{
	unset($_SESSION['LoggedIn']);
	unset($_SESSION['UserData']);
	unset($_SESSION['State']);
}

//===================================================================
// Functions
//===================================================================
//-------------------------------------------------------------------
function AuthStudentNetID()
{
	global $userData;
	global $studentNetID;
	global $studentInfo;
    
    $studentNetID = $userData['studentID'];
	$db_drvr = new MySQLDriver();

	$results = $db_drvr->ReadTableQuery("SELECT * FROM OfficiallyDeclaredStudents WHERE URID = '$studentNetID' ORDER BY URID ASC");
	$studentInfo = $results[0];
    if(strcasecmp($studentInfo['URID'], $studentNetID) == 0)
    {
       	return true;
    }
    else
    {
        return false;
    }
}
//-------------------------------------------------------------------
function Validate($data)
{
	global $error_messages;
	$errors = array();

	if(empty($data['studentFirstName']))
		$errors[] = "studentFirstName";
	if(empty($data['studentLastName']))
		$errors[] = "studentLastName";
	if(empty($data['studentID']))
		$errors[] = "studentID";
	if(empty($data['classYear']))
		$errors[] = "classYear";
	if(empty($data['emailAddress']))
		$errors[] = "emailAddress";
	if(empty($data['localAddress']))
		$errors[] = "localAddress";
	if(empty($data['phoneNumber']))
		$errors[] = "phoneNumber";

	if(empty($data['clusterType']))
	{
	   $error_messages[] = "You must select a division for the cluster to access the name of authorized clusters.";
	   $errors[] = "clusterType";
	}
	if(empty($data['clusterName']))
	{
	   $error_messages[] = "You must select the cluster name.";
	   $errors[] = "clusterName";
	}

	$cnt = 0;
	   
	if(!empty($data['courseNumber1']))
	{
		if(empty($data['courseTitle1']) ||
		   empty($data['courseSemester1']) ||
		   empty($data['courseCredit1']))
		   {
			   	$cnt++;
		   }
	}
	if(!empty($data['courseTitle1']))
	{
		if(empty($data['courseNumber1']) ||
		   empty($data['courseSemester1']) ||
		   empty($data['courseCredit1']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseSemester1']))
	{
		if(empty($data['courseNumber1']) ||
		   empty($data['courseTitle1']) ||
		   empty($data['courseCredit1']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit1']))
	{
		if(empty($data['courseNumber1']) ||
		   empty($data['courseTitle1']) ||
		   empty($data['courseSemester1']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseNumber2']))
	{
		if(empty($data['courseTitle2']) ||
		   empty($data['courseSemester2']) ||
		   empty($data['courseCredit2']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseTitle2']))
	{
		if(empty($data['courseNumber2']) ||
		   empty($data['courseSemester2']) ||
		   empty($data['courseCredit2']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseSemester2']))
	{
		if(empty($data['courseNumber2']) ||
		   empty($data['courseTitle2']) ||
		   empty($data['courseCredit2']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit2']))
	{
		if(empty($data['courseNumber2']) ||
		   empty($data['courseTitle2']) ||
		   empty($data['courseSemester2']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseNumber3']))
	{
		if(empty($data['courseTitle3']) ||
		   empty($data['courseSemester3']) ||
		   empty($data['courseCredit3']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseTitle3']))
	{
		if(empty($data['courseNumber3']) ||
		   empty($data['courseSemester3']) ||
		   empty($data['courseCredit3']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseSemester3']))
	{
		if(empty($data['courseNumber3']) ||
		   empty($data['courseTitle3']) ||
		   empty($data['courseCredit3']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit3']))
	{
		if(empty($data['courseNumber3']) ||
		   empty($data['courseTitle3']) ||
		   empty($data['courseSemester3']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseNumber4']))
	{
		if(empty($data['courseTitle4']) ||
		   empty($data['courseSemester4']) ||
		   empty($data['courseCredit4']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseTitle4']))
	{
		if(empty($data['courseNumber4']) ||
		   empty($data['courseSemester4']) ||
		   empty($data['courseCredit4']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseSemester4']))
	{
		if(empty($data['courseNumber4']) ||
		   empty($data['courseTitle4']) ||
		   empty($data['courseCredit4']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit4']))
	{
		if(empty($data['courseNumber4']) ||
		   empty($data['courseTitle4']) ||
		   empty($data['courseSemester4']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseNumber5']))
	{
		if(empty($data['courseTitle5']) ||
		   empty($data['courseSemester5']) ||
		   empty($data['courseCredit5']))
		   { 
				$cnt++;
		   }
	}
	if(!empty($data['courseTitle5']))
	{
		if(empty($data['courseNumber5']) ||
		   empty($data['courseSemester5']) ||
		   empty($data['courseCredit5']))
		   { 
				$cnt++;
		   }
	}
	if(!empty($data['courseSemester5']))
	{
		if(empty($data['courseNumber5']) ||
		   empty($data['courseTitle5']) ||
		   empty($data['courseCredit5']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit5']))
	{
		if(empty($data['courseNumber5']) ||
		   empty($data['courseTitle5']) ||
		   empty($data['courseSemester5']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseNumber6']))
	{
		if(empty($data['courseTitle6']) ||
		   empty($data['courseSemester6']) ||
		   empty($data['courseCredit6']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseTitle6']))
	{
		if(empty($data['courseNumber6']) ||
		   empty($data['courseSemester6']) ||
		   empty($data['courseCredit6']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseSemester6']))
	{
		if(empty($data['courseNumber6']) ||
		   empty($data['courseTitle6']) ||
		   empty($data['courseCredit6']))
		   { 
		   		$cnt++;
		   }
	}
	if(!empty($data['courseCredit6']))
	{
		if(empty($data['courseNumber6']) ||
		   empty($data['courseTitle6']) ||
		   empty($data['courseSemester6']))
		   { 
		   		$cnt++;
		   }
	}

	if($cnt > 0)
	{
		$error_messages[] = "You must complete entire course row for the course(s) you have entered.";
		$errors[]='clusterCourse';
	}

	$creditTotal = 0;

	if(is_numeric($data['courseCredit1']) && !empty($data['courseCredit1']))
	{
			$creditTotal += $data['courseCredit1'];
	}
	if(is_numeric($data['courseCredit2']) && !empty($data['courseCredit2']))
	{
			$creditTotal += $data['courseCredit2'];
	}
	if(is_numeric($data['courseCredit3']) && !empty($data['courseCredit3']))
	{
			$creditTotal += $data['courseCredit3'];
	}
	if(is_numeric($data['courseCredit4']) && !empty($data['courseCredit4']))
	{
			$creditTotal += $data['courseCredit4'];
	}
	if(is_numeric($data['courseCredit5']) && !empty($data['courseCredit5']))
	{
			$creditTotal += $data['courseCredit5'];
	}
	if(is_numeric($data['courseCredit6']) && !empty($data['courseCredit6']))
	{
			$creditTotal += $data['courseCredit6'];
	}

	$minTotalCredits = 12;

	$courseCheckedCount = 0;

	if(!empty($data['courseChecked1']))
	{
		$courseCheckedCount++;
	}
	if(!empty($data['courseChecked2']))
	{
		$courseCheckedCount++;
	}
	if(!empty($data['courseChecked3']))
	{
		$courseCheckedCount++;
	}
	if(!empty($data['courseChecked4']))
	{
		$courseCheckedCount++;
	}
	if(!empty($data['courseChecked5']))
	{
		$courseCheckedCount++;
	}
	if(!empty($data['courseChecked6']))
	{
		$courseCheckedCount++;
	}

	if($courseCheckedCount == 0)
	{
		$error_messages[] = "You must check one of the following checkboxes for your proposed courses";
		$error[] = "clusterTotalChecks";
	}

	if($creditTotal < $minTotalCredits)
	{
		$error_messages[] = "You must have a minimum of 12 credits for your course choices for a cluster.";
		$errors[]='clusterTotalCredits';
	}

	if(empty($data['clusterReason']))
	{
	   $error_messages[] = "You must indicate any reasons for your course choices.";
	   $errors[]='clusterReason';
	}
	return $errors;
}
//-------------------------------------------------------------------
function GetClusterDept($clusterNumber)
{
	$db_drvr = new MySQLDriver();
	$results = $db_drvr->ReadTableQuery("SELECT academicDepartment FROM CSEPublishClusters WHERE clusterNumber = '$clusterNumber'");
	$result = $results[0];
	return $result['academicDepartment'];
}
//-------------------------------------------------------------------
function Process($data)
{	
	//$ob_drvr = new OnBaseDriver();
	$db_drvr = new MySQLDriver();
	
	//strips all parenthesis and dashes from phone number to ensure submission
	$phone = preg_replace("/[^0-9]/", "", $data['phoneNumber']);
		
	/* Submit this record to MySQL */
	$record = array();
	$clusterNumber = $data['clusterNameCode'];
	$record['clusterDept'] = GetClusterDept($clusterNumber);

	//if($record['clusterDept'] == "MLC" || $record['clusterDept'] == "AAH"){
	if($record['clusterDept'] == "MLC")
	{
		$pattern1 = '/^[A-Z]{1}\d{1}[A-Z]{2}\d{3}$/m';
		$pattern2 ='/^[A-Z]{1}\d{1}[A-Z]{3}\d{3}$/m';

		if(preg_match($pattern1, $clusterNumber)){
			$matched = substr($clusterNumber, 2, 2);
			$record['clusterDept'] = $matched;
		}

		if(preg_match($pattern2, $clusterNumber)){
			$matched = substr($clusterNumber, 2, 3);
			$record['clusterDept'] = $matched;
		}	
	}
	global $clusterDeptCode;
	if(($data['clusterNameCode'] == "N1INT017") || ($data['clusterNameCode'] == "N4INT016") || ($data['clusterNameCode'] == "N1INT018") || ($data['clusterNameCode'] == "N1INT019") || ($data['clusterNameCode'] == "N4INT008")) 
	{
		$record['clusterDept'] = "INT:lnrw";

	} 
	else if(($data['clusterNameCode'] == "N1INT010") || ($data['clusterNameCode'] == "N1INT003") || ($data['clusterNameCode'] == "N1INT015") || ($data['clusterNameCode'] == "N1INT005")) 
	{
		$record['clusterDept'] = "INT:krogalsk";
	} 
	else if($data['clusterNameCode'] == "S1INT002")
	{
		$record['clusterDept'] = "INT:rstone";
	}
	else if(($data['clusterNameCode'] == "H1INT005") || ($data['clusterNameCode'] == "S1INT007"))
	{
		$record['clusterDept'] = "PHL";
	}
	else if(($data['clusterNameCode'] == "N1INT013") || ($data['clusterNameCode'] == "N1INT004"))
	{
		$record['clusterDept'] = "PAS";
	}
	else if($data['clusterNameCode'] == "N1INT008")
	{
		$record['clusterDept'] = "STT";
	}
	else if($data['clusterNameCode'] == "H1INT003")
	{
		$record['clusterDept'] = "IT";
	}
	else if($data['clusterNameCode'] == "H1INT004")
	{
		$record['clusterDept'] = "JPN";
	}
	else if($data['clusterNameCode'] == "S1INT009")
	{
		$record['clusterDept'] = "HIS";
	}
	else if($data['clusterNameCode'] == "H1INT001")
	{
		$record['clusterDept'] = "RCL";
	}
	else if($data['clusterNameCode'] == "N1INT014")
	{
		$record['clusterDept'] = "MTH";
	}
	else if($data['clusterNameCode'] == "S1INT006")
	{
		$record['clusterDept'] = "PSC";
	}

	$clusterDeptCode = $record['clusterDept'];

	foreach($data as $key => $value)
	{
		/* STRIP OUT ANY KEYS YOU'RE NOT SENDING TO THE MYSQL TABLE */
		if($key != 'Save' && $key != 'clusterReasonCount' && $key != 'clusterNameCode' && $key != 'additionalInfoCount')
		{
			$record[$key] = $value;	
		}
	}
	$record['phoneNumber'] = $phone;
	$record['ipAddress'] = $_SERVER['REMOTE_ADDR'];
	$record['dateSubmitted'] = date('Y-m-d H:i:s', time());
	
	$id = $db_drvr->Insert('ClusterExceptionForms',$record);
	
	if($id == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
//------------------------------------------------------------------------------------------------------
function HasRealTimeNotice($department)
{
	global $realTimeNotices;
	if(in_array($department, array_keys($realTimeNotices)))
		return true;
	else
		return false;	
}
//------------------------------------------------------------------------------------------------------
function SetFacultyEmails()
{
	// Populate 'Real Time' notice recievers list
	global $realTimeNotices;
	$realTimeNotices = array();
			
	$db_drvr = new MySQLDriver();		
	$records = $db_drvr->ReadTable('ClusterExceptionAdmins', array('realTimeNotice' => 1));

	foreach($records as $record)
	{
		$majors = explode(',',$record['clusterDepartment']);
		$email = $record['emailAddress'];
		
		if(!empty($email))
		{
			foreach($majors as $major)
			{
				if(!in_array($major, array_keys($realTimeNotices)))
				{
					$realTimeNotices[$major] = array();							
				}
				
				$realTimeNotices[$major][] = $email;
			}
		}
	}
}
//------------------------------------------------------------------------------------------------------
function GetRealTimeNoticeAddresses($department, $lastName)
{
	global $realTimeNotices;
	$emailAddresses = array();
		
	$db_drvr = new MySQLDriver();
	foreach($realTimeNotices[$department] as $address)
	{
		$records = $db_drvr->ReadTable('ClusterExceptionAdmins', array('emailAddress' => $address));
		$record = $records[0];
			
		if($record['alphaLoadBalance'] == 'OFF')
		{
			$emailAddress[] = $address;
		}
		else
		{
			$alpha = explode(',', $record['alphaLoadBalance']);
				
			if(in_array(strtoupper($lastName[0]), $alpha))
			{
				$emailAddresses[] = $address;	
			}
		}
	}
	return $emailAddresses;
}
//-------------------------------------------------------------------
function SendEmail($data, $date)
{
	global $clusterDeptCode; global $emails;

	$to = $data['emailAddress'];
	
	$subject = 'Cluster Exception Form Submission';
	
	//$headers = "From: cascas@ur.rochester.edu\r\n";
	$headers = "From: cascas@ur.rochester.edu\r\n";
	//$headers .= "CC: " . $data['facultyEmailAddress'] . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	$message = "<html><head><style>th, td{padding: 2px; text-align: left;} @media only screen and (max-width:480px){table{width:100% !important; max-width:480px !important;}</style></head><body>";
	$message .= "<table style='width:100%;'>";
	$message .= "<tr><td colspan='2' style='width: 100%; text-align: center; background-color: #021e47; color: #FFC125; border-bottom: 3px solid #FFC125; border-radius: 10px;'>Arts, Sciences and Engineering<hr/><div style='color:white; font-size: 175%; padding: 0px 0px 15px 0px;'><span style='color:#FFC125;'>U</span><span style='font-variant:small-caps;'>niversity</span> <i>of</i> <span style='color:#FFC125;'>R</span><span style='font-variant:small-caps;'>ochester</span></div></td></tr>";
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

	if(!empty($data['additionalInfo']))
	{
		$message .= "<p>Additional information for reviewer: " . $data['additionalInfo'] . "</p>";
			
	}

	$message .= "<table style='width:100%;'>";
	$message .= "<tr><td colspan='2'><hr/></td></tr>";
	$message .= "<tr><td>Date/Time Submitted: " . $date . "</td></tr>";
	$message .= "<tr><td colspan='2'>Submission of this document <b>DOES NOT</b> guarantee your form has been accepted.</td></tr>";
	$message .= "<tr><td colspan='2' style='width: 100%; text-align: center; background-color: #021e47; color: white; border-top: 3px solid #FFC125; border-radius: 10px;'><p>Copyright &#169; 2013&#150;2015. All rights reserved.<br /><a style='color:white;' href='http://www.rochester.edu/'>University of Rochester</a> | <a style='color:white;' href='http://www.rochester.edu/college/'>AS&#38;E</a> | <a style='color:white;' href='index.html'>Registrar</a><br/><a style='color:white;' href='http://www.rochester.edu/accessibility.html'>Accessibility</a> | <a style='color:white;' href='http://text.rochester.edu/tt/referrer' title='Access a text-only version of this page.'>Text</a> | <a style='color:white;' href='http://www.rochester.edu/college/webcomm/' title='Get help with your AS&amp;E website.'>Web Communications</a></p></td></tr>";
	$message .="</table>";	
	$message .= "</body></html>";
	
	mail($to, $subject, $message, $headers);

	/*SetFacultyEmails();
	if(HasRealTimeNotice($clusterDeptCode))
	{
		$addresses = GetRealTimeNoticeAddresses($clusterDeptCode, $data['studentLastName']);
		$emails = $addresses;

		foreach($addresses as $address)
		{
			$message_data = array();
			$message_data['studentFirstName'] = $data['studentFirstName'];
			$message_data['studentLastName'] = $data['studentLastName'];
			$message_data['studentID'] = $data['studentID'];
			$message_data['field'] = $clusterDeptCode;
			$message_data['dateSubmitted'] = date('Y-m-d H:i:s', time());

			$message_faculty = "";
			if($_SERVER['SERVER_NAME'] == 'secure1.wdev.rochester.edu')
				$message_faculty .= "The following Cluster Exception Form is awaiting review in the Online Cluster Exception Form Application (https://secure1.wdev.rochester.edu/ccas/cluster-exception-admin-dashboard.php)\n\n";
			else
				$message_faculty .= "The following Cluster Exception Form is awaiting review in the Online Cluster Exception Form Application (https://secure1.rochester.edu/ccas/cluster-exception-admin-dashboard.php)\n\n";
			$message_faculty .= $message_data['studentFirstName'] . " " . $message_data['studentLastName'] . " (" . $message_data['studentID'] . ")" . " " . $message_data['field'] . " Submitted: " . $message_data['dateSubmitted'] . "\n";

			$message_faculty .= "\r\n\r\n";
			$message_faculty .= "Please login to the Cluster Exception Form Application at your earliest convenience to take appropriate action on these forms.\n";
			$message_faculty .= "If you are receiving this message in error, or are no longer approving cluster exception forms for your department please contact the Registrar's Office at (585) 275-8131 so we can update our records.\n";
				
			mail($address,'Cluster Exception Forms Awaiting Action',$message_faculty,"From: cascas@ur.rochester.edu\r\n");
		}
	}*/
}
