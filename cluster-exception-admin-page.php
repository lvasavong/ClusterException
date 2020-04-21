<?php
//*******************************************************************************************************
//	cluster-exception-admin-page.php  -- view of forms
//
//	Author: Linda Vasavong
//	Date Created: ????
//	Date Modified: Linda Vasavong
//*******************************************************************************************************

require_once('form_processors/m_cluster-exception-admin-page.php');

$html .= "<div class='page row'>";  // starts pg row id='web_form'

if(!isset($_SESSION['LoggedIn']))
{
	$html .= $loginForm->GetRiverBankInputDisplay();
}
else if(!isset($_SESSION['LoggedIn']) && $status == "Invalid Admin")
{
    $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>Your NetID is not part of our Administrator Database. Contact the College Center for Advising Services (585) 275-2354 for further assistance.</div></div>";
    $html .= $loginForm->GetRiverBankInputDisplay();
}
else if(isset($_SESSION['LoggedIn']) && ($_SESSION['State'] == "View_Form"))
{
    ob_start();
    if(!$validTest && !empty($errors))
      $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>One or more required fields indicated below have been left blank!</div></div>"; 

    if(!$validTest && !empty($error_messages))
    {
      echo $common->GetErrorDisplay($error_messages); 
    }
    ?>
    <article class="columns small-12">
<br/>
<fieldset class="formField"> <!-- APPPpppppppppPPPPPPPpppppppppppPPPPPPPPROVAL FORM -->
  <form action="?" method="POST">
  <br>
  <div class="row--with-borders">
    <div class="columns small-12">
      <input class='small button secondary button-pop' name='back' type='submit' value='Go Back'/>
    </div>
  </div>
  <div class="row--with-borders">
      <div class="row--with-borders">
        <div class="columns small-12">
            <p>
            	  Please review the cluster exception form below. Using the buttons at the bottom of the 
                form you can indicate whether or not you approve the student's cluster exception form. Comments included 
                in the notes section will be sent to the student via email to explain the action.
          	</p>
         </div>
      </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
			<h2>Cluster Exception Approval Form</h2>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p><b>Student Instructions:</b></p>
        <ul>
          <li>Fill in the form fields below to propose an exception to a cluster.</li>
          <li>Once submitted your form/proposal will be sent to the appropriate department for approval.</li>
          <li>You will receive email correspondence regarding approval/rejection of the Cluster Exception proposal.</li>            
        </ul>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p align="center"><b>NOTE:</b> Fields marked with a <span class="required">*</span> are <b>required</b> fields</p><br>
    </div>
  </div>
  <br/>
  <br/>
  <div class="row--with-borders">
    <div class="columns small-12">
      <h3>Student Information</h3><br>
    </div>
  </div>
  <div class="row--with-borders">
      <div class="columns small-12 medium-4 large-4">
        <label for="studentFirstName"><span class="required">*</span>First Name</label>
        <input type="text" id="studentFirstName" name="studentFirstName" readonly value="<?php echo $studentRecordData['studentFirstName'];?>"/>
      </div>
      <div class="columns small-12 medium-4 large-4">
        <label for="studentLastName"><span class="required">*</span>Last Name</label>
        <input type="text" id="studentLastName" name="studentLastName" readonly value="<?php echo $studentRecordData['studentLastName'];?>"/>
      </div>
      <div class="columns small-12 medium-1 large-1">
        <label for="studentMiddleInitial">M.I.</label>
        <input type="text" id="studentMiddleInitial" maxlength='1' name="studentMiddleInitial" readonly value="<?php echo $studentRecordData['studentMiddleInitial'];?>"/>
      </div>
      <div class="columns small-12 medium-3 large-3">
        <label for="studentID"><span class="required">*</span>Student ID</label>
        <input type="text" id="studentID" name="studentID" maxlength='8' size='8' readonly value="<?php echo $studentRecordData['studentID'];?>"/>
      </div>
  </div>
  <input type="hidden" id="recordID" name="recordID" value="<?php echo $studentRecordData['recordID'];?>">
  <div class="row--with-borders">
    <div class="columns small-12 medium-4 large-3">
        <label for="classYear"><span class="required">*</span>Class Year</label>
        <input type="text" id="classYear" name="classYear" readonly value="<?php echo $studentRecordData['classYear'];?>"/>
    </div>
    <div class="columns small-12 medium-3 large-3">
        <label for="emailAddress"><span class="required">*</span>Email Address</label>
        <input type="text" id="emailAddress" name="emailAddress" readonly value="<?php echo $studentRecordData['emailAddress'];?>"/>
		</div>
    <div class="columns small-12 medium-4 large-3">
        <label for="phoneNumber"><span class="required">*</span>Local or Cell Phone</label>
        <input type="text" id="phoneNumber" size='15' maxlength='15' name="phoneNumber" readonly value="<?php echo $studentRecordData['phoneNumber'];?>"/>
    </div>
    <div class="columns small-12 medium-3 large-3">
        <label for="localAddress"><span class="required">*</span>Local Address or CMC BOX</label>
    	<input type="text" id="localAddress" size='50' maxlength='50' name="localAddress" readonly value="<?php echo $studentRecordData['localAddress'];?>" max="70">
    </div>
	</div>
	<br>
	<br>
	<div class="row--with-borders">
    <div class="columns small-12">
			<h3>Cluster Exception Information</h3><br>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
			<table>
		    <tbody>
			<tr>
            <input type="hidden" name="clusterType" id='clusterType' readonly value="<?php echo $studentRecordData['clusterType'];?>"/>
            <td><span class="required">*</span><b>In which division are you proposing this cluster exception?</b></td>
            <td><input type="text" name="clusterTypeReadable" id='clusterTypeReadable' readonly value="<?php if($studentRecordData['clusterType'] == 'naturalSciences'){ echo 'Natural Sciences'; } else if($studentRecordData['clusterType'] == 'socialSciences'){ echo 'Social Sciences';} else{ echo 'Humanities';} ?>"/></td>
			</tr>
      <tr>
			      <td><span class="required">*</span><b>Name of Authorized Cluster</b></td>
            <td><input type="text" name="clusterName" id='clusterName' readonly value="<?php echo $studentRecordData['clusterName']; ?>" /></td>
            <input type='hidden' id='clusterDept' name='clusterDept' readonly value='<?php echo $studentRecordData['clusterDept'];?>'>
		  </tr>
	    </tbody>
		</table>		
	</div>
  </div>
  <br/>
	<div class="row--with-borders">
    <div class="columns small-12">
			<p><span class="required">*</span>Please use the space below to enter the courses that will be used to complete the Cluster. (At least 12 credit hours are required.) Use the checkbox(es) that correspond to the course row(s) to show the course(s) not included in the authorized Cluster. For example, CSC 172 (Course Number), Data Structures and Algorithms (Course Title), Spring 2018 (Semester), Summer 2018 (Semester), or Fall 18 (Semester), and 4.0 (Credit Hrs).</p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <table align="center">
        <tbody><tr><th>Proposed <br>Course <br>Exception(s)</th><th>Course Number</th><th>Course Title</th><th>Semester</th><th>Credit Hrs</th>
          <tr><td></td><td>e.g., CSC 172</td><td>Data Structures and Algorithms</td><td>Fall 2018</td><td>4.0</td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked1" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked1'];?>' <?php if($studentRecordData['courseChecked1'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber1' name="courseNumber1" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber1'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle1" id='courseTitle1' readonly value="<?php echo $studentRecordData['courseTitle1'];?>"/></td><td><input type="text" name="courseSemester1" size="15" readonly value="<?php echo $studentRecordData['courseSemester1'];?>"/></td><td><input type='text' id="courseCredit1" name="courseCredit1" size='3' readonly value='<?php echo $studentRecordData['courseCredit1'];?>'/></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked2" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked2'];?>' <?php if($studentRecordData['courseChecked2'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber2' name="courseNumber2" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber2'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle2" id='courseTitle2' readonly value="<?php echo $studentRecordData['courseTitle2'];?>"/></td><td><input type="text" name="courseSemester2" size="15" readonly value="<?php echo $studentRecordData['courseSemester2'];?>"/></td><td><input type='text' id="courseCredit2" name="courseCredit2" size='3' readonly value='<?php echo $studentRecordData['courseCredit2'];?>'/></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked3" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked3'];?>' <?php if($studentRecordData['courseChecked3'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber3' name="courseNumber3" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber3'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle3" id='courseTitle3' readonly value="<?php echo $studentRecordData['courseTitle3'];?>"/></td><td><input type="text" name="courseSemester3" size="15" readonly value="<?php echo $studentRecordData['courseSemester3'];?>"/></td><td><input type='text' id="courseCredit3" name="courseCredit3" size='3' readonly value='<?php echo $studentRecordData['courseCredit3'];?>'/></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked4" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked4'];?>' <?php if($studentRecordData['courseChecked4'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber4' name="courseNumber4" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber4'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle4" id='courseTitle4' readonly value="<?php echo $studentRecordData['courseTitle4'];?>"/></td><td><input type="text" name="courseSemester4" size="15" readonly value="<?php echo $studentRecordData['courseSemester4'];?>"/></td><td><input type='text' id="courseCredit4" name="courseCredit4" size='3' readonly value='<?php echo $studentRecordData['courseCredit4'];?>'/></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked5" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked5'];?>' <?php if($studentRecordData['courseChecked5'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber5' name="courseNumber5" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber5'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle5" id='courseTitle5' readonly value="<?php echo $studentRecordData['courseTitle5'];?>"/></td><td><input type="text" name="courseSemester5" size="15" readonly value="<?php echo $studentRecordData['courseSemester5'];?>"/></td><td><input type='text' id="courseCredit5" name="courseCredit5" size='3' readonly value='<?php echo $studentRecordData['courseCredit5'];?>'/></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked6" onclick="return false;" size='3' value='<?php echo $studentRecordData['courseChecked6'];?>' <?php if($studentRecordData['courseChecked6'] == 'Yes') echo ' checked';?> readonly/></td><td><input type="text" id='courseNumber6' name="courseNumber6" size='12' maxlength='10' readonly value="<?php echo $studentRecordData['courseNumber6'];?>"/></td><td><input type="text" size='70' maxlength='70' name="courseTitle6" id='courseTitle6' readonly value="<?php echo $studentRecordData['courseTitle6'];?>"/></td><td><input type="text" name="courseSemester6" size="15" readonly value="<?php echo $studentRecordData['courseSemester6'];?>"/></td><td><input type='text' id="courseCredit6" name="courseCredit6" size='3' readonly value='<?php echo $studentRecordData['courseCredit6'];?>'/></td></tr>
		</tbody>
	</table>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p><span class="required">*</span>Use the text area below to indicate any reasons you wish to convey regarding your course choices.</p>
      <p align="center"><textarea name="clusterReason" id="clusterReason" rows="5" cols="70" maxlength='240' readonly value="<?php echo $studentRecordData['clusterReason'];?>"><?php echo $studentRecordData['clusterReason'];?></textarea></p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p>Additional information for reviewer. (Optional)</p>
      <p align="center"><textarea name="additionalInfo" id="additionalInfo" rows="5" cols="70" maxlength='400' readonly value="<?php echo $studentRecordData['additionalInfo'];?>"><?php echo $studentRecordData['additionalInfo'];?></textarea></p>
    </div>
  </div>
  <br><br><br><br>
  <div class="row--with-borders">
    <div class="columns small-12">
		  <h3>Faculty Member Information</h3><br>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p><b>Faculty Instructions:</b></p>
        <ul>
          <li>Use the buttons below to approve or deny this cluster exception or save your rationale below to go back to the administrator page.</li>
          <li>You will be cc'd in an email and <b>your rationale will be sent to this student</b> regarding approval or denial of the Cluster Exception.</li>            
        </ul>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p align="center"><b>NOTE:</b> If you are indicating your department rationale for approving/rejecting this form you must have less than 240 characters in the notes area.</p><br>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p>Use the notes area below to indicate your department rationale (optional). <b>Your rationale will be sent to this student.</b></p>
      <p align="center"><textarea name="facultyNotes" rows="5" cols="70" onkeydown="textCounter(this.form.facultyNotes,this.form.facultyNotesCount,240);" onkeyup="textCounter(this.form.facultyNotes,this.form.facultyNotesCount,240);"><?php echo $studentRecordData['facultyNotes'];?></textarea></p>
    </div>
  </div>
    <br>
    <br>
<input type="hidden" id="facultyLastName" name="facultyLastName" size='45' readonly value="<?php echo $userData['lastName'];?>"/>
<input type="hidden" id="facultyFirstName" name="facultyFirstName" size='45' readonly value="<?php echo $userData['firstName'];?>"/>
<input type="hidden" id="facultyEmailAddress" name="facultyEmailAddress" value="<?php echo $userData['emailAddress'];?>">
  <div class="row--with-borders">
    <div class="text-center columns small-12">
      <input class="small button secondary button-pop" name="Approve" type="submit" value="Approve"/>
      <input class="small button secondary button-pop" name="Deny" type="submit" value="Deny"/>
      <input type="submit" class="small button secondary button-pop" name="SaveNotSubmit" value="Save Rationale and Take No Action"/>
    </div>
  </div>
</form>
</fieldset>
<br/>
</article>

<script type='text/javascript'>

function textCounter(field, countfield, maxlimit)
{
  if(field.value.length > maxlimit)
  {
    field.value = field.value.substring(0, maxlimit);
  }
  else
  {
    countfield.value = maxlimit - field.value.length;
  }
}

</script>
<?php
// end inner if statement
$html .= ob_get_contents();
ob_end_clean();
}
else if(isset($_SESSION['LoggedIn']) && ($_SESSION['State'] == "Dashboard"))
{
    ob_start();
    if($status == "DB_ERR")
    {
		  $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>There was a problem submitting this form to the Database, the database could currently be offline. Contact the College Center for Advising Services (585) 275-2354 for further assistance.</div></div>";
      $status = "";
    }
    if($status == "APPROVE OK")
    {
		  $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>The form was successfully APPROVED and the student notified!</div></div>";
      $status = "";
    }
    if($status == "DENY OK")
    {
		  $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>The form was successfully REJECTED and the student notified!</div></div>";
      $status = "";
    }
    if($facultyNotesStatus == "OK")
    {
		  $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>Advisor notes input into the Cluster Exception Approval Form were saved.</div></div>";
      $status = "";
    }
    if($deleteStatus == "OK")
    {
      $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>The form was successfully removed from the system.</div></div>";
      $deleteStatus = "";
    }
    if($ok == "OK")
    {
      $ok = "";
    }
    else if($ok == "TYPE")
	  {
		  $html .= "<article class='columns small-12'><br/><div class='row--with-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>You must select both a 'Form Status' to get form data.</div></div></article>";
      $ok = "";
    }
	  else if($ok == "DATE")
	  {
		  $html .= "<article class='columns small-12'><br/><div class='row--with-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>Dates for data requests must have Month, Day, and Year if selected.</div></div></article>";
      $ok = "";
    }
    ?>
    <article class='columns medium-12'> <!-- DAaaaaaasssssSHBOARD -->
    <br/>
        <fieldset class="formField">
            <form action="?" method="POST">
            </br></br></br>
            <div class="row--with-borders">
              <div class="columns small-12">
                <p> Welcome to the Cluster Exception Administrator Page. Here you will see all Cluster Exception forms for your department 
                    that are waiting to be reviewed. Click on the 'Review' button next to the form you wish to review 
                    and you'll be taken to the approval/rejection page for that form. </p>
              </div>
            </div>
            <div class="row--with-borders">
              <div class="columns small-12">
                <p><b>Review</b> - Click the review button below next to a form to view and take action on a proposed cluster exception.</p>
                <p><b>Delete</b> - This button will remove the proposal from the system, no notice will be sent to the student. Use this button for cleaning up unnecessary forms (such as duplicate submissions by students).</p>
                <p><input class='small button secondary button-pop' name='Logout' type='submit' value='Logout'/></p>
              </div>
            </div>
            </form>

            <?php
            $cnt = 0;   // counts number of student records
            foreach($adminDepts as $adminDept)
            { ?>
                <div class="row--with-borders">
                    <div class="columns small-12">
                        <h4><b>DEPARTMENT: <?php echo $adminDept; ?> </b></h4>
                    </div>
                </div>
                <div class='row--with-borders'>
                    <div class='columns small-12'>
                        <table class='unstriped'>
                            <thead>
                                <tr><th width="80">First Name</th><th width="80">Last Name</th><th width="80">Net ID</th><th width="80">Cluster</th><th width="80">Date Submitted</th><th width="10"></th><th width="10"></th></tr>
                            </thead>
                            <tbody>
                <?php 
                if(empty($studentRecords[$adminDept]))
                { ?>
                    <tr><td>No Record</td></tr>
                <?php 
                }
                else
                {   ?>
                    <!--Something new-->
                    <?php 
                    foreach($studentRecords[$adminDept] as $studentRecordDept)
                    {
                        $cnt++;
                        $formData[$adminDept][$cnt] = $studentRecordDept;
                        ?>
                        <form action="?" method="POST">
                        <input type="hidden" name="recordIDForViewForm" value="<?php echo $studentRecordDept['recordID']; ?>">
                        <tr><td><?php echo $studentRecordDept['studentFirstName'];?></td><td><?php echo $studentRecordDept['studentLastName'];?></td><td><?php echo $studentRecordDept['studentID']; ?> </td><td><?php echo $studentRecordDept['clusterName']; ?> </td><td> <?php echo $studentRecordDept['dateSubmitted']; ?> </td>
                            <td>
                                <input class='small button button-pop' name='View_Form' type='submit' value='Review'/>
                            </td>
                            <td>
                        </form>
                                  <form action='?' method='POST' onsubmit="return confirm('Are you sure you want to delete this form?');">
							 	                    <input type='hidden' name='recordIDForDeleteForm' value='<?php echo $studentRecordDept['recordID']; ?>'/>
								                    <input type='submit' class='small button button-pop' name='Delete_Form' value='Delete'/>
						                      </form>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php 
                } ?>
                </tbody>
                </table>
                </div>
                </div>
            <?php
            } 
            ?>
            
            <br/>
            <br/>
            <div class="row--with-borders">
      	      <div class="columns small-12">
        	      <h3>Data Access</h3>
      	      </div>
   	        </div>
            <br/>
            <div class="row--with-borders">
      	      <div class="columns small-12">
        	      <p>Use the following interface to download data regarding approved/rejected forms.</p>
      	      </div>
            </div>
            <div class="row--with-borders">
      	      <div class="columns small-12 text-center">
        	      <p><b>NOTE:</b> Fields marked with a <span class="required">*</span> are <b>required</b> fields</p>
      	      </div>
            </div>
            <form action="?" method="POST">
              <div class="row--with-borders">
                <div class="columns small-12 text-center">
                  <div class="selectLabel">
                    <span class="required">*</span><b>Form Status:</b>
                  </div>
                  <select name='download_status'>
                    <option value='approve'>Approved</option>
                    <option value='deny'>NOT Approved</option>
                  </select>
                <div>
              </div>      
              <div class="row--with-borders">
                <div class="columns small-6 text-center">
                  <div class="selectLabel">
                      <b>Start Date:</b>
                    </div>
                </div>
                <div class="columns small-6 text-center">
                  <div class="selectLabel">
                      <b>End Date:</b>
                    </div>
                </div>
              </div>
              <hr class="KEEP"/>
              <div class="row--with-borders">
                <div class="columns small-3 medium-3 large-3">
                    <select name="start_month">
                      <?php echo $common->GetMonthOptions(); ?>
                    </select>
                </div>
                <div class="columns small-1 medium-1 large-1">
                    <select name="start_day">
                      <?php echo $common->GetDayOptions(); ?>
                    </select>
                </div>
                <div class="columns small-2 medium 2 large-2">
                    <select name="start_year">
                      <?php echo $common->GetYearOptions(5,0); ?>
                    </select>
                </div>
                <div class="columns small-3 medium-3 large-3">
                    <select name="end_month">
                      <?php echo $common->GetMonthOptions(); ?>
                    </select>
                </div>
                <div class="columns small-1 medium-1 large-1">
                    <select name="end_day">
                      <?php echo $common->GetDayOptions(); ?>
                    </select>
                </div>
                <div class="columns small-2 medium-2 large-2">
                    <select name="end_year">
                      <?php echo $common->GetYearOptions(5,0); ?>
                    </select>
                </div>
              </div>
              <div class="row--with-borders">
                <div class="columns small-12 text-center">
                    <input class="small button secondary button-pop" type="submit" name="DOWNLOAD" value="Download Data"/>
                </div>
              </div>
            </form>
          
        </fieldset>
      <br/>
    </article>

<?php		
	$html .= ob_get_contents();
	ob_end_clean();
}

$html .= "</div>";	//Make sure we close the page container.

$style = "style_riverbank.css";
$pageTitle = "Cluster Exception Administrator Page";
$pageHeader = "Cluster Exception Administrator Page";
$pageContent = $html;

include_once('templates/responsive_riverbank.php');
?>