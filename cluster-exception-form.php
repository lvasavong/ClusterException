<?php
//*******************************************************************************************************
//	cluster-exception-form.php  -- Submission of Cluster Exception Forms.
//
//	Author: Linda Vasavong
//	Date Created: ????
//	Date Modified: Linda Vasavong
//*******************************************************************************************************

require_once('form_processors/m_cluster-exception-form.php');

// Setup the stock Responsive header and the page container
$html .= "<div class='page row'>";

if(!isset($_SESSION['LoggedIn']) && ($userStatus != "Invalid User"))
{
  if($status == "OK")
  {
		$html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>You have successfully submitted your Cluster Exception Form! You will receive email confirmation of the submitted form.</div></div>";
  }
  $html .= $loginForm->GetRiverBankInputDisplay();
}
else if(!isset($_SESSION['LoggedIn']) && $userStatus == "Invalid User")
{
    $html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>You need to declare your major and get it approved to access the cluster exception form. Contact the College Center for Advising Services (585) 275-2354 for further assistance.</div></div>";
    $html .= $loginForm->GetRiverBankInputDisplay();
}
else if(isset($_SESSION['LoggedIn']) && ($_SESSION['State'] == "Agree"))
{
	if(!$validTest) 
		$html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>Please indicate you have read the policies and qualify for declaring a Cluster Exception.</div></div>"; 
		
	ob_start();
	?>

<article class="columns small-12">
<br/>
<fieldset class="formField">
  <div class="row--with-borders">

    <div class="columns small-12">
      <p>Matriculated undergraduates may propose a Cluster Exception.</p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p>The following policies will apply:</p>
      <ul>
        <li>Students must choose among listed existing clusters.</li>
        <li>At least 12 credit hours are required. The courses must be graded, and the grades must average a "C" (2.0) or better.</li>
        <li>Be sure to check the courses that interest you in the Cluster Search Engine. You may find what you're looking for, or may find something that is close. If it's the latter, talk to the undergraduate advisor in the department that established or administers the Cluster to review your options. After talking it over with them. you may find that an existing Cluster is closer to your interests than you thought.</li>
      </ul>
    </div>
  </div>
  <form action="?" method="POST">
    <div class="row--with-borders">
      <div class="columns small-1 text-right">
        <input type='radio' name='policyUnderstood' value='Yes'/>
      </div>
      <div class="columns small-10 end"> <span class="required">*</span>&nbsp;I have read the above policies, and I qualify for declaring a Cluster Exception at this time. </div>
    </div>
    <br/>
    <div class="row--with-borders">
      <div class="text-center columns small-12">
        <input class="small button secondary button-pop" name="Continue" type="submit" value="Continue"/>
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
else
{
	ob_start();
	
	if($status == "DB_ERR")
		$html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_succ medium-centered section--thick'>There was a problem submitting this form to the Database, the database could currently be offline. Contact the College Center for Advising Services (585) 275-2354 for further assistance.</div></div>";
	
	if(!$validTest && !empty($errors))
		$html .= "<br/><div class='row--with-column-borders'><div class='columns small-12 medium-10 large-10 text-center alert_panel_fail medium-centered section--thick'>One or more required fields indicated below have been left blank!</div></div>"; 
	
  if(!$validTest && !empty($error_messages))
  {
    echo $common->GetErrorDisplay($error_messages); 
  }
  ?>
<article class="columns small-12">
<br/>
<fieldset class="formField">
	<div class="row--with-borders">
    <div class="columns small-12">
      <h2>Cluster Exception Form</h2>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p><b>Instructions:</b></p>
        <ul>
          <li>Fill in the form fields below to propose an exception to a cluster.</li>
          <li>Once submitted your form/proposal will be sent to the appropriate department for approval.</li>
          <li>You will receive email correspondence regarding approval/rejection of the Cluster Exception proposal.</li>            
        </ul>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p align="center"><b>NOTE:</b> Fields marked with <span class="required">*</span> are <b>required</b> fields</p><br>
    </div>
  </div>
  <form action="?" method="POST">
  <br/>
  <br/>
  <div class="row--with-borders">
    <div class="columns small-12">
      <h3>Student Information</h3><br>
    </div>
  </div>
  <div class="row--with-borders">
      <div class="columns small-12 medium-4 large-4">
        <label for="studentFirstName" <?php if(in_array('studentFirstName',$errors)) echo "class='error'";?>><span class="required">*</span>First Name</label>
        <input type="text" id="studentFirstName" name="studentFirstName" readonly value="<?php echo $userData['firstName'];?>"/>
      </div>
      <div class="columns small-12 medium-4 large-4">
        <label for="studentLastName" <?php if(in_array('studentLastName',$errors)) echo "class='error'";?>><span class="required">*</span>Last Name</label>
        <input type="text" id="studentLastName" name="studentLastName" readonly value="<?php echo $userData['lastName'];?>"/>
      </div>
      <div class="columns small-12 medium-1 large-1">
        <label for="studentMiddleInitial">M.I.</label>
        <input type="text" id="studentMiddleInitial" maxlength='1' name="studentMiddleInitial" value="<?php echo $formData['studentMiddleInitial'];?>"/>
      </div>
      <div class="columns small-12 medium-3 large-3">
        <label for="studentID" <?php if(in_array('studentID',$errors)) echo "class='error'";?>><span class="required">*</span>Student ID</label>
        <input type="text" id="studentID" name="studentID" maxlength='8' size='8' readonly value="<?php echo $userData['studentID'];?>"/>
      </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12 medium-4 large-3">
        <label for="classYear" <?php if(in_array('classYear',$errors)) echo "class='error'";?>><span class="required">*</span>Class Year</label>
        <input type="text" id="classYear" name="classYear" value="<?php echo $userData['classYear'];?>"/>
    </div>
    <div class="columns small-12 medium-3 large-3">
        <label for="emailAddress" <?php if(in_array('emailAddress',$errors)) echo "class='error'";?>><span class="required">*</span>Email Address</label>
        <input type="text" id="emailAddress" name="emailAddress" value="<?php echo $userData['emailAddress'];?>"/>
		</div>
    <div class="columns small-12 medium-4 large-3">
        <label for="phoneNumber" <?php if(in_array('phoneNumber',$errors)) echo "class='error'";?>><span class="required">*</span>Local or Cell Phone</label>
        <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $formData['phoneNumber'];?>"/>
    </div>
    <div class="columns small-12 medium-3 large-3">
        <label for="localAddress" <?php if(in_array('localAddress',$errors)) echo "class='error'";?>><span class="required">*</span>Local Address or CMC BOX</label>
    	  <input type="text" id="localAddress" name="localAddress" value="<?php echo $formData['localAddress'];?>" max="70">
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
						<td <?php if(in_array('clusterType',$errors)) echo "class='error'";?>><span class="required">*</span><b>In which division are you proposing this cluster exception?</b></td>
            <td><div class="entrySelectWide">
              <select id="clusterType" name="clusterType">
                <option value="" <?php if($formData['clusterType'] == '') echo ' selected';?>>>--Select One--<</option>
                <option value="humanities" <?php if($formData['clusterType'] == 'humanities') echo ' selected';?>>Humanities</option>
                <option value="naturalSciences" <?php if($formData['clusterType'] == 'naturalSciences') echo ' selected';?>>Natural Sciences</option>
                <option value="socialSciences" <?php if($formData['clusterType'] == 'socialSciences') echo ' selected';?>>Social Sciences</option>
              </select>
            </td>
            </div>
					</tr>
          <tr>
						<td <?php if(in_array('clusterName',$errors)) echo "class='error'";?>><span class="required">*</span><b>Name of Authorized Cluster</b></td>
            <td><div class="entrySelectWide">
              <select id="clusterName" name="clusterName">
              </select>
            </td>
            </div>
            <input type='hidden' class='nameCode' id='clusterNameCode' name='clusterNameCode' value='<?php echo $formData['clusterNameCode'];?>'>
					</tr>
				</tbody>
			</table>		
		</div>
  </div>
  <br/>
	<div class="row--with-borders">
    <div class="columns small-12">
			<p <?php if(in_array('clusterTotalCredits',$errors) || in_array('clusterTotalChecks',$errors)) echo "class='error'";?> <?php if(in_array('clusterCourse',$errors)) echo "class='error'";?>><span class="required">*</span>Please use the space below to enter the courses that will be used to complete the Cluster. (At least 12 credit hours are required.) Use the checkbox(es) that correspond to the course row(s) to show the course(s) not included in the authorized Cluster. For example, CSC 172 (Course Number), Data Structures and Algorithms (Course Title), Spring 2018 (Semester), Summer 2018 (Semester), or Fall 18 (Semester), and 4.0 (Credit Hrs). </p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <table align="center">
        <tbody><tr><th>Proposed Course Exception(s)</th><th>Course Number</th><th>Course Title</th><th>Semester</th><th>Credit Hrs</th></tr>
          <tr><td></td><td>e.g., CSC 172</td><td>Data Structures and Algorithms</td><td>Fall 2018</td><td>4.0</td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked1" size='3' value='Yes' <?php if($formData['courseChecked1'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber1' name="courseNumber1" size='10' maxlength='10' value="<?php echo $formData['courseNumber1'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle1" id='courseTitle1' class='title1' value="<?php echo $formData['courseTitle1'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester1" value="<?php echo $formData['courseSemester1'];?>"/></td><td><select id="courseCredit1" name="courseCredit1"><?php echo $common->GetCourseCreditOptions($formData['courseCredit1']); ?></select></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked2" size='3' value='Yes' <?php if($formData['courseChecked2'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber2' name="courseNumber2" size='10' maxlength='10' value="<?php echo $formData['courseNumber2'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle2" id='courseTitle2' class='title2' value="<?php echo $formData['courseTitle2'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester2" value="<?php echo $formData['courseSemester2'];?>"/></td><td><select id="courseCredit2" name="courseCredit2"><?php echo $common->GetCourseCreditOptions($formData['courseCredit2']); ?></select></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked3" size='3' value='Yes' <?php if($formData['courseChecked3'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber3' name="courseNumber3" size='10' maxlength='10' value="<?php echo $formData['courseNumber3'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle3" id='courseTitle3' class='title3' value="<?php echo $formData['courseTitle3'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester3" value="<?php echo $formData['courseSemester3'];?>"/></td><td><select id="courseCredit3" name="courseCredit3"><?php echo $common->GetCourseCreditOptions($formData['courseCredit3']); ?></select></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked4" size='3' value='Yes' <?php if($formData['courseChecked4'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber4' name="courseNumber4" size='10' maxlength='10' value="<?php echo $formData['courseNumber4'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle4" id='courseTitle4' class='title4' value="<?php echo $formData['courseTitle4'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester4" value="<?php echo $formData['courseSemester4'];?>"/></td><td><select id="courseCredit4" name="courseCredit4"><?php echo $common->GetCourseCreditOptions($formData['courseCredit4']); ?></select></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked5" size='3' value='Yes' <?php if($formData['courseChecked5'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber5' name="courseNumber5" size='10' maxlength='10' value="<?php echo $formData['courseNumber5'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle5" id='courseTitle5' class='title5' value="<?php echo $formData['courseTitle5'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester5" value="<?php echo $formData['courseSemester5'];?>"/></td><td><select id="courseCredit5" name="courseCredit5"><?php echo $common->GetCourseCreditOptions($formData['courseCredit5']); ?></select></td></tr>
          <tr><td align='center'><input type="checkbox" name="courseChecked6" size='3' value='Yes' <?php if($formData['courseChecked6'] == 'Yes') echo ' checked';?>/></td><td><input type="text" id='courseNumber6' name="courseNumber6" size='10' maxlength='10' value="<?php echo $formData['courseNumber6'];?>"/></td><td><input type="text" maxlength='70' name="courseTitle6" id='courseTitle6' class='title6' value="<?php echo $formData['courseTitle6'];?>"/></td><td><input type="text" size='3' maxlength='11' name="courseSemester6" value="<?php echo $formData['courseSemester6'];?>"/></td><td><select id="courseCredit6" name="courseCredit6"><?php echo $common->GetCourseCreditOptions($formData['courseCredit6']); ?></select></td></tr>
				</tbody>
			</table>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p <?php if(in_array('clusterReason',$errors)) echo "class='error'";?>><span class="required">*</span>Use the text area below to indicate any reasons you wish to convey regarding your course choices.</p>
      <p align="center"><textarea name="clusterReason" rows="5" cols="70" id="clusterReason" onkeydown="textCounter(this.form.clusterReason,this.form.clusterReasonCount,240);" onkeyup="textCounter(this.form.clusterReason,this.form.clusterReasonCount,240);"><?php echo $formData['clusterReason'];?></textarea></p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-3 medium-1 large-1">
      <input class="text-center" readonly type="text" id="clusterReasonCount" name="clusterReasonCount" value="240">
    </div>
    <br><br> 
    <div class="columns small-8 medium-3 end">
        <label for="charactersRemaining" class="postfix radius">Characters Remaining</label>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-12">
      <p>Additional information for reviewer. (Optional)</p>
      <p align="center"><textarea name="additionalInfo" rows="5" cols="70" id="additionalInfo" onkeydown="textCounter(this.form.additionalInfo,this.form.additionalInfoCount,400);" onkeyup="textCounter(this.form.additionalInfo,this.form.additionalInfoCount,400);"><?php echo $formData['additionalInfo'];?></textarea></p>
    </div>
  </div>
  <div class="row--with-borders">
    <div class="columns small-3 medium-1 large-1">
      <input class="text-center" readonly type="text" id="additionalInfoCount" name="additionalInfoCount" value="400">
    </div>
    <br><br> 
    <div class="columns small-8 medium-3 end">
        <label for="charactersRemaining" class="postfix radius">Characters Remaining</label>
    </div>
  </div>
  <br><br>
  <div class="row--with-borders">
    <div class="text-center columns small-12">
        <input class="small button secondary button-pop" name="Save" type="submit" value="Submit"/>
    </div>
  </div>
</form>
</fieldset>
<br/>
</article>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>

var s1= document.getElementById("clusterType");
var s2 = document.getElementById("clusterName");
onchange(); //Change options after page load
s1.onchange = onchange; // change options when s1 is changed

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

function onchange() {
  if (s1.value == "humanities") 
  {
    option_html = "<?php echo $common->GetPrimaryClusterOptions('HUM',$formData['clusterName']); ?>";
    s2.innerHTML = option_html;
  }
  else if(s1.value == "naturalSciences")
  {
    option_html = "<?php echo $common->GetPrimaryClusterOptions('NSE',$formData['clusterName']); ?>";
    s2.innerHTML = option_html;
  }
  else if(s1.value == "socialSciences")
  {
    option_html = "<?php echo $common->GetPrimaryClusterOptions('SSC',$formData['clusterName']); ?>";
    s2.innerHTML = option_html;
  }
}

$(document).ready(function() {
  $('#clusterName').on('input', function() {
    var name = $(this).val();
    console.log("CLUSTERNAME CHANGED");
    console.log(name);
  });
});

$(document).ready(function() {
  $('#clusterName').on('input', function() {
    var name = $(this).val();
    $(".nameCode").val(calcClusterNameCode(name));
    document.getElementById("clusterNameCode").value = calcClusterNameCode(name);
  });
});
function calcClusterNameCode(name)
{
  var cnt = 0;
  var clusterNameCode;
  var str = "";

    for(var i=0; i<name.length; i++)
    {
      var ch = name.charAt(i);
      if(ch == '(')
      {
        for(var j=i+1; j<name.length; j++)
        {
          var ch2 = name.charAt(j);
          if(ch2 == ')')
          {
            break;
          }
          else 
          {
            str += ch2;
          }
        }
      }
    }
    clusterNameCode = str;
    console.log("CLUSTER NAME CODE");
    console.log(clusterNameCode);
    return clusterNameCode;
}

</script>
<?php		
	$html .= ob_get_contents();
	ob_end_clean();
}

$html .= "</div>";	//Make sure we close the page container.

$style = "style_riverbank.css";
$pageTitle = "Cluster Exception Form";
$pageHeader = "Cluster Exception Form";
$pageContent = $html;


include_once('templates/responsive_riverbank.php');
?>