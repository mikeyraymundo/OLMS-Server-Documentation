<?php

/**
 *  This function connects the program to the mysql database
 *
 *  $db is a resource and references PHP's special built-in mysql_connect function. The 
 *  mysql_connect function is used to connect the program to the database and localhost.
 *  
 *  @var resource
 *  
 *  
*/

function db_connection()
{
	
		$db = mysql_connect("localhost", "root", "root");
        //SELECT DB
        mysql_select_db("wts_olms", $db);	
}

/**
*   This function checks if the user filled up both the username and password fields. If
*   the user does not fill up both fields, the page displays a prompt that informs the
*   user that both fields need to be filled up.
*   It checks whether the username and password is assigned to an administrator or agent.
*   
*   $squery is a resource that references the mysql query to select all rows in the database
*   according to the conditions. In this case, it will search for the username and password
*   the user had input
*   // lagay after description --> @var resource

*   $result passes $squery through the special php function mysql_query which sends a unique
*   query to the currently active database
*   //@link http://php.net/manual/en/function.mysql-query.php

*   $row passes the result through the special php function mysql_fetch_array that returns
*   an array that corresponds to the fetched row
*   //@link http://php.net/manual/en/function.mysql-fetch-array.php

*   $_SESSION is an associative array containing session variables available to the current
*   script
*   @link http://php.net/manual/en/reserved.variables.session.php
*
*   @param string $username The username that the user will input
*   @param string $password The password that the user will input
*   
*/

function login_verification($username, $password)
{
	
    mysql_ping();
		$squery	 = "SELECT * From account WHERE  password  LIKE BINARY '$password' AND username  LIKE'$username' " ;
		$result= mysql_query($squery);
      
		if(empty($username) || empty($password) )
		{
					echo "<script type='text/javascript'>alert('You did not fill up all the fields'); location.href='login.php'</script>";
		}
		else
		{
			if (mysql_num_rows($result) != 0)
			{
				session_start();
				$row=mysql_fetch_array($result);
				extract($row);
				$_SESSION=array();
				$_SESSION['User_ID']="$user_id";
				$_SESSION['role']="$account_type";
				$_SESSION['name']="$name";
				
				if($_SESSION['role']=="Agent")
                {
                    header("location:manage_leads_agent_default.php");
                }
                else
                {
                    header("location:account_management.php");
                }
			}
			else
			{
				echo "<script type='text/javascript'>alert('You have entered a wrong username or password.')</script>";
				
			}
		}
	
}

/**
*   This function allows the program to initiate a session or resume the current one
*
*   session_start() creates a session or resumes the current one based on a session 
*   identifier passed via a GET or POST request, or passed via a cookie. When this function 
*   is called or when a session automatically starts, PHP will call the open and read 
*   session save handlers
*
*   $login_id passes the username of the user into the $_SESSION array
*   $name passes the user's registered full name into the $_SESSION array
*   
*   @var resource

*/

function session_details()
{
	session_start();
	 $login_id=$_SESSION['User_ID'];
    $name=$_SESSION['name'];
}

/**
*   This function allows an administrator account to add a new user of the system in its
*   list of registered accounts. It allows the user to input the pertinent information 
*   regarding the details of an account within the system. If the user does not fill up 
*   all the fields, the system will prompt the user to fill up all the fields. If the 
*   administrator inputs a username that is already in the database (username is already 
*   taken), the system will inform the administrator that the username has input already
*   belongs to an account and he must input a different one. The user is required to 
*   re-input the password that he/she is to set for the new account thus the passwords --
*   the one initially entered and the one asked for confirmation -- must match 
*
*   $repeatcheck is a resource that uses the special mysql function mysql_query to check
*   if the username the administrator inputs already exists in the database
*   $iquery allows the program to insert the query and information into the database
*   
*   my_real_escape_string is a function which returns the escaped string version of the
*   given string. It allows the program to escape special characters in a string for use 
*   in an SQL statement.
*   
*   @param string $username The username that the administrator will input
*   @param string $password1 The first password that the administrator will set for the 
*   new account
*   @param string $password2 The second password that the administrator will input in order
*   to confirm the password for the new account
*   @param string $fullname The first name, middle initial? and last name of the person
*   the administrator is making an account for
*   @param string $account_type The type of account the user selects for the new account
*   @var resource
*/
function add_account($username, $password1, $password2, $fullname, $account_type)
{
	
                $repeatcheck = mysql_query("SELECT * FROM account WHERE username = '$username';");
                
                if(mysql_num_rows($repeatcheck) == 0)
                {
                    if($password1 == $password2)
                    {
                        $username=mysql_real_escape_string($username);
                        $password1=mysql_real_escape_string($password1);
                        $fullname=mysql_real_escape_string($fullname);
                        $iquery = "INSERT INTO account (username, password,  name, account_type) VALUES ('$username', '$password1', '$fullname', '$account_type');";
                            
                            mysql_query($iquery);
                        echo "<script type='text/javascript'>alert('Added New User'); location.href='account_management.php'</script>";
                    }
                    else if($password1 != $password2)
                    {
                        echo "<script type='text/javascript'>alert('Passwords Do Mot Match'); </script>";
                    }
                }
                else
                {
                  echo "<script type='text/javascript'>alert('Username Exists');</script>";
                }          
}

/**
*   This function allows the user to add a new country to the timezone functionality of the system.
*   This function checks if the user field up all the required fields to be filled.
*   This function also checks if the data to be added already exists in the database.
*   
*   @param string $state is a state for the country. Applicable to USA only.
*   @param string $country is a country for the timezone
*   @param string $selected_timezone is the timezone with respect to the country
*   @param  $radio determines if the state belongs to only one timezone or is divided into two timezones. Applicable to US states only.
*
*/
function add_country($state, $country, $selected_timezone, $radio)
{       
    if( empty($country) || $selected_timezone=='Choose a Timezone')
    {
        echo "<script type='text/javascript'>alert('You did not fill up country or timezone'); location.href='manage_timezone.php'</script>";
    }
    else
    {        
        $repeatcheck=mysql_query("SELECT * FROM main_time_zone WHERE country='$country' AND state='$state'");
        $repeatchecker=mysql_num_rows($repeatcheck);
        if($repeatchecker!=0)
        {
            echo "<script type='text/javascript'>alert('This entry already exists in the database!'); location.href='manage_timezone.php'</script>";
        }
        else
        {
            $country=mysql_real_escape_string($country);
            $state=mysql_real_escape_string($state);
            $selected_timezone=mysql_real_escape_string($selected_timezone);
            $tquery = "INSERT INTO main_time_zone (state, country, timezone, extra_field) VALUES ('$state', '$country', '$selected_timezone', '$radio');";
            
            mysql_query($tquery);
            echo "<script type='text/javascript'>alert('Added New Timezone'); location.href='manage_timezone.php'</script>";       
        }
    }     

}

/**
*   This function allows the user to add a new city to the timezone functionality of the system.
*   This function checks if the user field up all the required fields to be filled.
*   This function also checks if the data to be added already exists in the database.
*   
*   @param string $county is a county found in the USA.
*   @param string $state is a state for the country. Applicable to USA only.
*   @param string $country is a country for the timezone
*   @param string $selected_timezone is the timezone with respect to the country
*   @param string $city is a city found in the country.
*   @param string $selected_timezone is the timezone selected from the dropdown list. 
*
*/
function add_city($country, $state, $county, $city, $selected_timezone)
{       
    if($country=='Choose a Country' ||empty($state)||$selected_timezone=='Choose a Timezone')
    {
        echo "<script type='text/javascript'>alert('You did not fill up all the required fields'); location.href='manage_timezone.php'</script>";
    }
    else
    {        
        if(empty($county)&&empty($city))
        {
            echo "<script type='text/javascript'>alert('You cannot have both county and city as blank'); location.href='manage_timezone.php'</script>"; 
        }
        else
        {   
            $repeatcheck=mysql_query("SELECT * FROM city_time_zone WHERE county='$county' AND state='$state' AND city='$city' ");
            $repeatchecker=mysql_num_rows($repeatcheck);
            if($repeatchecker!=0)
            {
                echo "<script type='text/javascript'>alert('This entry already exists in the database!'); location.href='manage_timezone.php'</script>";
            }
            else
            {
                $country=mysql_real_escape_string($country);
                $state=mysql_real_escape_string($state);
                $county=mysql_real_escape_string($county);
                $city=mysql_real_escape_string($city);
                $selected_timezone=mysql_real_escape_string($selected_timezone);
                $tquery = "INSERT INTO city_time_zone (country, state, county, city, timezone) VALUES ('$country', '$state', '$county', '$city', '$selected_timezone');";
                
                mysql_query($tquery);
                echo "<script type='text/javascript'>alert('Added New Timezone'); location.href='manage_timezone.php'</script>";    
            }  
        } 
    }     
}

/**
*   This function allows the user to add a new campaign in the database. It allows the 
*   system to check whether a campaign already exists in the database. If a campaign 
*   already exists, the system will not add the campaign the user had input. When adding a
*   new campaign, the system requires all fields to be filled up. If the user does not fill 
*   up all the fields, the system will not add the campaign and will subsequently prompt 
*   the user to fill up all of the forms. 
*
*   @param string $campaign_name The name of the new campaign the user will input
*   @param string $company The name of the company associated with the campaign
*   @param string $creation_date The date when the campaign was initiated
*   @var resource
*/

function add_campaign($campaign, $company)
{
    //query to check if the campaign name already exists 
    $company=mysql_real_escape_string($company);
    $campaign=mysql_real_escape_string($campaign);
    $creation_date=date("Y-m-d H-i-s");
    $repeatcheck=mysql_query("SELECT*FROM campaign where campaign_name='$campaign'");

    //Prompt if there are any fields not filled up
	if(empty($campaign) || empty($company) )
    {
       	echo "<script type='text/javascript'>alert('You did not fill up all the fields');</script>";
    }
    else if(mysql_num_rows($repeatcheck) != 0)
    {
        echo "<script type='text/javascript'>alert('This campaign already exists!');</script>";
    }
    else
    {
        
	    $query = "INSERT INTO campaign (campaign_name, company, date_created) VALUES ('$campaign', '$company', '$creation_date');";
	    mysql_query($query);
        $query=mysql_query("SELECT campaign_id FROM campaign WHERE campaign_name='$campaign' AND company='$company';");
        $row=mysql_fetch_row($query);
        $campaign_id=$row[0];
        $query=mysql_query("INSERT INTO campaign_status (campaign_id, status_id) VALUES ('$campaign_id','1'),('$campaign_id','3'),('$campaign_id','2'),('$campaign_id','3'),('$campaign_id','4')
            ,('$campaign_id','5'),('$campaign_id','6'),('$campaign_id','7'),('$campaign_id','8'),('$campaign_id','9'),('$campaign_id','10')
            ,('$campaign_id','11'),('$campaign_id','12'),('$campaign_id','13'),('$campaign_id','14'),('$campaign_id','15')
            ,('$campaign_id','16'),('$campaign_id','17'),('$campaign_id','18'),('$campaign_id','19'),('$campaign_id','20')
            ,('$campaign_id','21'),('$campaign_id','22'),('$campaign_id','23'),('$campaign_id','24');");
	    echo "<script type='text/javascript'>alert('Added New Campaign'); location.href='campaign_management.php'</script>";
    }
}

/**
*   This function allows the user to add a new status in the database. It allows the system to check if
*   all fields were filled up by the user. Furthermore, it also checks if the status to be added already
*   exists in the database. 
*
*   @param string $status_name The name of the new status the user will input
*   @param string $description The description of the new status
*   @var resource
*/
function add_status($status, $description)
{
    $status=mysql_real_escape_string($status);
    $description=mysql_real_escape_string($description);
    $repeatcheck=mysql_query("SELECT*FROM status where status_name='$status'");
    if(empty($status) || empty($description) )
    {
        echo "<script type='text/javascript'>alert('You did not fill up all the fields'); location.href='campaign_management.php'</script>";
    }
    else if(mysql_num_rows($repeatcheck) != 0)
    {
        echo "<script type='text/javascript'>alert('This status already exists!');</script>";
    }
    else
    {
        $status=mysql_real_escape_string($status);
        $description=mysql_real_escape_string($description);
        $query = mysql_query("INSERT INTO status (status_name, description) VALUES ('$status', '$description');");

        echo "<script type='text/javascript'>alert('Added New Status'); location.href='campaign_management.php'</script>";
    }
}

/**
*   This function allows the administrator to assign selected agents into a campaign.
*   $insert_query passes a unique query to the currently active database on the server.
*   It inserts the user_id and campaign_id into the agent_list
*
*   @param int $user_id The ID of the agent that exists in the database
*   @param int $campaign_id The ID of the campaign that exists in the database where the selected agents will be assigned to
*/
function assign_agents($user_id, $campaign_id)
{
    
        $insert_query=mysql_query("INSERT INTO agent_list (user_id, campaign_id) VALUES ('$user_id', '$campaign_id');");
    
}

/**
*   This function allows the administrator to assign specific statuses to specific campaigns.
*
*   @param int $status_id The ID of the status to be added in the database.
*   @param int $campaign_id The ID of the selected campaign to where the status will be added.
*/
function assign_statuses($status_id, $campaign_id)
{
    
        $insert_query=mysql_query("INSERT INTO campaign_status (status_id, campaign_id) VALUES ('$status_id', '$campaign_id');");
    
}

/**
*   This function allows the administrator to delete an account from the database.
*   
*   @param int $user_id The ID of the account to be deleted
*/

function delete_agent($user_id)
{
	$delete = "DELETE FROM account WHERE user_id = $user_id";
    mysql_query($delete);      
    $delete_from_list = "DELETE FROM agent_list WHERE user_id = $user_id";
    mysql_query($delete_from_list); 
    $unassign_leads = "UPDATE campaign_lead set user_id = NULL WHERE user_id=$user_id'";
    mysql_query($unassign_leads); 
    echo "<script type='text/javascript'>alert('User Deleted'); location.href='account_management.php'</script>";
}

/**
*   This function allows the administrator to edit the account details of the accounts currently existing in the database.
*   This function also checks if the first name, middle name, last name, username, password, and re-enter password fields have been filled up.
*   This function also checks if the password field and re-enter password field inputs are the same.
*
*   $updatequery passes a unique query to the currently active database on the server.
*   It inserts the new first name, middle name, last name, username and password into the account
*
*   @param int $update_id The account ID that the details will be updated that exists in the database
*   @param string $fullname The first name, middle name, and last name of the account to be updated in the database
*   @param string $username The username of the account to be updated in the database
*   @param string $password The password of the account to be updated in the database
*   @param string $password2 The re-enter password to be compared if similar to $password
*   @var resource
*/
function edit_account($update_id, $fullname, $username, $password,$password2, $account_type, $original_name)
{
	if(empty($username) || empty($password) ||   empty($fullname) || empty($password2))
	{
		echo "<script type='text/javascript'>alert('You did not fill up all the fields'); location.href='edit_user.php'</script>";
	}
	else
	{
        if($password!=$password2)
        {
             echo "<script type='text/javascript'>alert('Passwords Do Mot Match'); </script>";
        }
        else
        {
            $fullname=mysql_real_escape_string($fullname);
            $username=mysql_real_escape_string($username);
            $password=mysql_real_escape_string($password);
            $password2=mysql_real_escape_string($password2);
            $original_name=mysql_real_escape_string($original_name);
            $repeatcheck = mysql_query("SELECT * FROM account WHERE username = '$username' AND username!='$original_name';");
            if(mysql_num_rows($repeatcheck)==1)
            {
                echo "<script type='text/javascript'>alert('Username Exists'); location.href='account_management.php'</script>";
            }
            else
            {
    		    $updatequery = mysql_query("UPDATE account set name='$fullname', username='$username', password='$password', account_type='$account_type'
    		    WHERE user_id='$update_id'");
                echo "<script type='text/javascript'>alert('Updated User'); location.href='account_management.php'</script>";		
            }			
    		
        }					
	}					
}

/**
*   This function allows the system to be able to display all the accounts in the database in a tabular form.
*
*/
function account_table()
{
$table="";
$table .= <<<EOT
<div class="panel-body">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="account-table">
            <thead>
                <tr>
                    <th class="hidden-phone" style="width: 15%">Username</th>
                    <th class="hidden-phone" style="width: 15%"> Password</th>   
                    <th class="hidden-phone" style="width: 15%"> Name</th>  
                    <th class="hidden-phone" style="width: 15%"> Account Type</th>                                      
                    <th class="hidden-phone" style="width: 10%"> Delete</th> 
                    <th class="hidden-phone" style="width: 10%"> edit</th> 
                </tr>
            </thead>
            <tbody>
EOT;
$query = "SELECT username, password, name, account_type, user_id  FROM account";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {      
        $table .= "<tr>";
        $username = $row[0];
        $password = $row[1];
        $fullname=$row[2];
        $account_type=$row[3];
        $user_id=$row[4];        
        $table .= <<< EOT
                <td>$username </td>
                <td>$password </td>
                <td>$fullname </td>
                <td>$account_type </td>
                <td><a data-href="account_management.php?user_id=$user_id&intent=delete" data-toggle="modal" data-target="#delete_user" href="#">
                    <button type="button" class="btn btn-danger btn-xs"> 
                        Delete
                    </button>
                </a></td>
                 <td><a href="account_management.php?user_id=$user_id&intent=edit"  >
                    <button type="button" class="btn btn-danger btn-xs"> 
                        Edit
                    </button>
                </a></td>
EOT;
     
    }

$table .= <<< EOT

            </tbody>
        </table>
     </div>
</div>
EOT;
echo $table;
}

/**
*   This function allows the administrator to assign leads to the campaign.
*
*   @param $checkbox indicates the selected leads to be assigned to the campaign.
*   @param int $campaign_id The ID of the selected campaign where the leads will be assigned to.
*/
function assign_leads_to_campaign($checkbox, $campaign_id)
{
    $date=date("Y-m-d H-i-s");
    $insert=mysql_query("INSERT INTO campaign_lead (campaign_id, lead_id, status_id, date_created) VALUES ('$campaign_id', '$checkbox', '1', '$date');");
}

/**
*   This function allows the administrator to unassign agents from the campaign.
*
*   @param $checkbox indicates the selected agents to be unassigned from the specific campaign.
*   @param int $campaign_id The ID of the campaign where the agents will be unassigned from.
*/

function unassign_agents($checkbox, $campaign_id)
{
    $insert=mysql_query("UPDATE campaign_lead SET user_id = NULL WHERE user_id='$checkbox' AND campaign_id='$campaign_id';"); 
    $insert=mysql_query("DELETE FROM agent_list WHERE user_id='$checkbox' AND campaign_id='$campaign_id';"); 
}

/**
*   This function allows the administrator to unassign leads from the campaign.
*
*   @param $checkbox indicates the selected leads to be unassigned from the specific campaign.
*   @param string $campaign_id The ID of the campaign where the leads will be unassigned from.
*/
function unassign_leads($checkbox, $campaign_id)
{
    $insert=mysql_query("DELETE FROM campaign_lead  WHERE lead_id='$checkbox' AND campaign_id='$campaign_id';");
}

/**
*   This function allows the administrator to update the details of a campaign.
*   This function checks if all the fields needed in this page was filled up properly.
*
*   @param string $campaign_name The name of the campaign to be updated
*   @param int $update_campaign_id The ID of the selected campaign to be updated
*   @param string $company The company of the specific campaign
*   @param $creation_date The date the campaign was created
*   @param $date_ended The date the campaign ended
*/
function update_campaign($update_campaign_id, $campaign_name, $company, $creation_date, $date_ended)
{
    if(empty($campaign_name) || empty($company) ||   empty($creation_date) )
    {
        echo "<script type='text/javascript'>alert('You did not fill up all the fields'); location.href='update_campaign.php?update_campaign_id=' +'$update_campaign_id'</script>";
    }
    else
    {
        $campaign_name=mysql_real_escape_string($campaign_name);
        $company=mysql_real_escape_string($company);
        $creation_date=mysql_real_escape_string($creation_date);
        $date_ended=mysql_real_escape_string($date_ended);
        $updatequery = "UPDATE campaign set campaign_name='$campaign_name', company='$company', date_created='$creation_date', date_ended='$date_ended'
                     WHERE campaign_id='$update_campaign_id'";                   
        mysql_query($updatequery);
        
        echo "<script type='text/javascript'>alert('Updated Campaign'); location.href='campaign_management.php'</script>";                   
    }   
}
/**
*   This function allows the administrator to assign leads to agents.
*
*   @param string $selected_agent Indicates the selected agent where the leads will be assigned to.
*   @param string $industry The industry where the leads to be assigned to the agent belongs to.
*   @param $number_assign The amount of leads from each industry that the user want to assign to the agent.
*   @param $radio determines if the leads are newly assigned or they will be re-assigned to the specific agent.
*/
function assign_leads_to_agents($selected_agent,$industry, $number_assign, $radio)
{
    if($radio=="New"){
    $insert=mysql_query("UPDATE campaign_lead c RIGHT JOIN (SELECT company_name, primary_industry, campaign_name, l.lead_id,c.campaign_id 
        FROM campaign, lead l, agent_list a LEFT JOIN campaign_lead c  ON c.campaign_id=a.campaign_id AND a.user_id='$selected_agent'  
        WHERE l.lead_id=c.lead_id AND l.primary_industry='$industry' AND campaign.campaign_id=c.campaign_id AND c.user_id IS NULL LIMIT $number_assign) 
        as output ON c.lead_id=output.lead_id and c.campaign_id=output.campaign_id SET c.user_id='$selected_agent';");
     
    }
    else if($radio=="Reassign")
    {
        $insert=mysql_query("UPDATE campaign_lead c RIGHT JOIN (SELECT company_name, primary_industry, campaign_name, l.lead_id, c.campaign_id, account.name 
         FROM account, campaign, lead l, agent_list a LEFT JOIN campaign_lead c  ON (c.campaign_id=a.campaign_id AND a.user_id='$selected_agent')  
         WHERE l.lead_id=c.lead_id AND campaign.campaign_id=c.campaign_id AND account.user_id=c.user_id AND c.user_id !='$selected_agent' AND c.user_id IS NOT NULL LIMIT $number_assign) 
        as output ON c.lead_id=output.lead_id and c.campaign_id=output.campaign_id SET c.user_id='$selected_agent';");
    }
    
}
/**
*   This function allows the user to see the assigned campaigns to him/her.
*
*   @param int $login_id is the ID of the account currently logged-in.
*/

function display_assigned_campaigns($login_id)
{
    $query=mysql_query("SELECT campaign_name FROM agent_list a, campaign c WHERE c.campaign_id=a.campaign_id AND user_id='$login_id';");
}

/**
*   This function allows the user to update the lead details.
*   It also checks if there was really a change made with the lead details.
*
*   @param string $first_name The first name of the contact of the lead
*   @param string $middle_name The middle name of the contact of the lead
*   @param string $last_name The last name of the contact of the lead
*   @param string $company The company of the lead
*   @param string $state The state where the lead is located
*   @param string $email The email of the lead
*   @param string $title The title/salutation of the lead
*   @param int $lead_id The ID of the lead
*   @param int $user_id The ID of the logged-in account
*   @param string $selected_campaign The specific campaign where the lead is assigned to
*   @param varchar $phone_number The phone number of the lead
*   @param string $city The city where the lead is located
*   @param string $country The country where the lead is located
*/
function update_lead_details($first_name, $middle_name, $last_name, $company, $state,$email, $title, $lead_id,$user_id,$selected_campaign, $phone_number, $city, $country )
{
    $lead_change_checker=mysql_query("SELECT contact_first_name, contact_middle_name, contact_last_name, contact_title, company_name, primary_state, email, phone_number, primary_city, primary_country
        FROM lead WHERE lead_id='$lead_id';");
    $row=mysql_fetch_row($lead_change_checker);
    if(empty($country)||empty($state)||empty($city)||empty($phone_number))
    {
        echo "<script type='text/javascript'>alert('The Country, State, City and Phone Number cannot be blank!'); </script>";
    }
    else 
    {
        if($row[0]!=$first_name || $row[1]!=$middle_name || $row[2]!=$last_name || $row[3]!=$title || $row[4]!=$company || $row[5]!=$state || $row[6]!=$email || $row[7]!=$phone_number || $row[8]!=$city|| $row[9]!=$country)
        {
        $first_name=mysql_real_escape_string($first_name);
        $middle_name=mysql_real_escape_string($middle_name);
        $last_name=mysql_real_escape_string($last_name);
        $title=mysql_real_escape_string($title);
        $company=mysql_real_escape_string($company);
        $country=mysql_real_escape_string($country);
        $primary_state=mysql_real_escape_string($state);
        $email=mysql_real_escape_string($email);
        $phone_number=mysql_real_escape_string($phone_number);
        $city=mysql_real_escape_string($city);
        $update_lead_info=mysql_query("UPDATE lead SET contact_first_name='$first_name', contact_middle_name='$middle_name', contact_last_name='$last_name',
            contact_title='$title', company_name='$company', primary_state='$state', email='$email', phone_number='$phone_number', primary_city='$city', primary_country='$country' WHERE lead_id='$lead_id'");
        $date=date("Y-m-d");
        $flag_lead=mysql_query("INSERT INTO flagged_leads (user_id,lead_id,date_modified) VALUES ('$user_id','$lead_id','$date');");
        echo "<script type='text/javascript'>alert('Successfully Updated Lead Details!'); </script>";
        }
        else
        {
            echo "<script type='text/javascript'>alert('You did not make any changes!'); </script>";
        }
    } 
}

/**
*   This function allows the user to update the lead status
*   It also checks if a timezone is assignedto the specific lead if callback status is set.
*
*   @param string $filter_selection The industry of the lead to be updated
*   @param string $selected_status The new status of the lead
*   @param string $notes The notes that the user input
*   @param int $lead_id The ID of the lead
*   @param int $user_id The ID of the logged-in account
*   @param string $selected_campaign The specific campaign where the lead is assigned to
*   @param datetime $callback_date The callback date assigned by the agent
*   @param string $state2 The timezone of the state where the lead belongs to
*   @param string $country2 The timezone of the country where the lead belongs to
*   @param string $city2 The timezone of the city where the lead belongs to
*   @param string $county2 The timezone of the city where the lead belongs to
*/
function update_lead($filter_selection, $selected_status, $notes, $lead_id,$user_id,$selected_campaign,$callback_date, $state2, $country2, $city2, $county2)
{
    if($selected_status=="12")
    {
        $date=date("Y-m-d H-i-s");
        //get details of the lead
        $get_details=mysql_query("SELECT primary_state, primary_city, primary_county FROM lead WHERE lead_id='$lead_id'");
        $row=mysql_fetch_array($get_details);
        $primary_state=$row[0];
        $primary_city=$row[1];
        $primary_county=$row[2];

        $date=date("Y-m-d H-i-s");

        $country_exist_query=mysql_query("SELECT state FROM main_time_zone WHERE country='$country2'");
        $country_exist=mysql_num_rows($country_exist_query);

        $country=mysql_query("SELECT DISTINCT country FROM main_time_zone WHERE country='$country2' AND state IS NULL ");
        $no_state_checker=mysql_num_rows($country);
        if($country_exist==0)
        {
            echo "<script type='text/javascript'>alert('There is no timezone assigned to this country yet. You cannot enter a callback date until a Timezone for that country has been added');</script>";
        }
        else
        {
            if($no_state_checker=="1")
            {
                $get_timezone=mysql_query("SELECT timezone FROM main_time_zone WHERE country='$country2'");
            }
            else
            {
                $state_checker_query=mysql_query("SELECT timezone, extra_field FROM main_time_zone WHERE state='$primary_state' AND country='$country2'");
                $row=mysql_fetch_row($state_checker_query);
                if($row[1]=="1")
                {
                    $find_county=mysql_query("SELECT*FROM city_time_zone WHERE county='$primary_county' AND country='$country2' AND state='$primary_state'");
                    $matching_county=mysql_num_rows($find_county);

                    $find_city=mysql_query("SELECT*FROM city_time_zone WHERE city='$primary_city' AND country='$country2' AND state='$primary_state'");
                    $matching_city=mysql_num_rows($find_city);

                    if($primary_county== NULL || $matching_county==0)
                    {
                        if($primary_city==NULL||$matching_city==0)
                        {
                             $get_timezone=mysql_query("SELECT timezone FROM main_time_zone WHERE state='$primary_state' AND country='$country2' ");
                        }
                        else
                        {                        
                            $get_timezone=mysql_query("SELECT timezone FROM city_time_zone WHERE city='$primary_city' AND state='$state2' AND country='$country2' ");
                        }
                    }
                    else
                    {
                        $get_timezone=mysql_query("SELECT timezone FROM city_time_zone WHERE county='$primary_county' AND state='$state2' AND country='$country2' ");
                    }
                }
                else
                {
                    $get_timezone=mysql_query("SELECT timezone FROM main_time_zone WHERE state='$primary_state' AND country='$country2' ");
                }
            }
            $output=mysql_fetch_row($get_timezone);
            $timezone=$output[0];
            $converstion_date=mysql_query("SELECT convert_tz('$callback_date','$timezone','Asia/Manila')");
            $blah = "SELECT convert_tz('$callback_date',  '$timezone','Asia/Manila')";
            $row=mysql_fetch_row($converstion_date);
            $converted_date=$row[0];
            if($date>$converted_date)
            {
                echo "<script type='text/javascript'>alert('Cannot set a callback for $converted_date'); </script>";
            }
            else
            {
                $update_lead=mysql_query("UPDATE campaign_lead SET notes='$notes', status_id='$selected_status', callback_date='$converted_date', date_last_updated='$date' WHERE campaign_id='$selected_campaign' AND lead_id='$lead_id';");
                $archive=mysql_query("INSERT INTO call_history (user_id, campaign_id, lead_id, call_date, status_id) VALUES ('$user_id','$selected_campaign', '$lead_id', '$date', '$selected_status');");
                echo "<script type='text/javascript'>alert('Successfully updated Lead. Callback should be made on $converted_date'); </script>";
            }
        }     
    }
    else
    {
        $date=date("Y-m-d H-i-s");
        $update_lead=mysql_query("UPDATE campaign_lead SET notes='$notes', status_id='$selected_status', callback_date=NULL, date_last_updated='$date' WHERE campaign_id='$selected_campaign' AND lead_id='$lead_id';");
        $archive=mysql_query("INSERT INTO call_history (user_id, campaign_id, lead_id, call_date, status_id) VALUES ('$user_id','$selected_campaign', '$lead_id', '$date', '$selected_status');");
        echo "<script type='text/javascript'>alert('Successfully updated Lead.'); </script>";
    }
}

/**
* This function allows the user to delete a lead and all its records from the database.
*
* @param int $lead_id The ID of the lead to be deleted
*/

function delete_all_records_of_lead($lead_id)
{
    $delete = mysql_query("DELETE FROM lead WHERE lead_id = $lead_id");
    $delete = mysql_query("DELETE FROM campaign_lead WHERE lead_id = $lead_id");
    $delete = mysql_query("DELETE FROM call_history WHERE lead_id = $lead_id");
    $delete = mysql_query("DELETE FROM flagged_leads WHERE lead_id = $lead_id");
}

/**
* This function allows the user to assign all leads into a campaign
*
* @param int $campaign_id The ID of the campaign where all of the leads will be assigned to.
*/
function all_leads_assign_to_campaign($campaign_id)
{
    $date=date("Y-m-d H-i-s");
    $findnonrepeat=mysql_query("SELECT  l.lead_id  FROM lead l LEFT JOIN campaign_lead c ON l.lead_id=c.lead_id AND c.campaign_id='$campaign_id' WHERE c.lead_id IS NULL ;");
    $lead_array=array();
    while($row=mysql_fetch_array($findnonrepeat))
    {
        $lead_array[]=$row[0];
    }

    foreach($lead_array as $lead)
    {
        $insert=mysql_query("INSERT INTO campaign_lead (campaign_id, lead_id, status_id, date_created) VALUES ('$campaign_id', '$lead', '1', '$date');");
    }

}

/**
* This function allows the user to generate reports depending on the date he/she selected.
*
* @param date $report_date The date of the report that is desired to be generated.
*/
function generate_report($report_date)
{
    error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


// Set document properties
$status_list=mysql_query("SELECT status_name, status_id FROM status");
$status_ids=array();
while($statuses=mysql_fetch_array($status_list))
{
    $status_ids[]=$statuses[1];
}

$status_list2=mysql_query("SELECT status_name FROM status WHERE status_id !='1'");
$status_count=mysql_num_rows($status_list);
$report_date_min=$report_date;
$report_date_min.=' 9:00:00';
$report_date_max=date( 'Y-m-d',strtotime($report_date.'+1 day'));
$report_date_max.=' 6:00:00';

$letter_count_column_title="B";


$styleArray = array(
    'font' => array(
        'bold' => true
    )
);

while($row=mysql_fetch_array($status_list))
{
    
    $objPHPExcel->setActiveSheetIndex(0) 
              ->setCellValue($letter_count_column_title .'1', $row['status_name'])
              ->getStyle($letter_count_column_title.'1')->getFont()->setBold(true)->setUnderline(true);

    $letter_count_column_title++;
}
$letter_count_column_title="B";


$campaign=mysql_query("SELECT campaign_id FROM campaign");
$campaign_count=mysql_num_rows($campaign);
$campaign_ids=array();
while($row=mysql_fetch_array($campaign))
{
    $campaign_ids[]=$row[0];
}


$row_number=2;

$total_new_leads=0;
foreach($campaign_ids as $c_id)
{
    
    $campaign_name=mysql_query("SELECT campaign_name FROM campaign WHERE campaign_id='$c_id' ");
    $row=mysql_fetch_array($campaign_name);
    
    $objPHPExcel->setActiveSheetIndex(0) 
            ->setCellValue('A' .$row_number, $row[0])
            ->getStyle('A'.$row_number)->getFont()->setBold(true)->setUnderline(true);

   $date= date('Y-m-d');
    $letter_counter="B";

    foreach($status_ids as $s_id)
    {
        $status_total=0;
        $tally=mysql_query("SELECT SUM(IF(status_id=$s_id,1,0)) from call_history c 
        RIGHT JOIN (SELECT lead_id, MAX(history_id) as history_id FROM `call_history` 
        WHERE campaign_id='$c_id' AND call_date<='$report_date_max' GROUP BY lead_id ORDER BY call_date DESC) as history 
        ON c.history_id=history.history_id");
        $row=mysql_fetch_array($tally);
        extract($row);
        $status_total=$status_total+$row[0];
        if($s_id==1)
        {
            $new_leads=mysql_query("SELECT COUNT(DISTINCT lead_id) from campaign_lead WHERE date_created<='$report_date_max' AND campaign_id='$c_id'");
            $lead_count=mysql_fetch_array($new_leads);
            $called_lead=mysql_query("SELECT COUNT(DISTINCT lead_id) from call_history WHERE call_date<='$report_date_max' AND campaign_id='$c_id'");
            $called_leads=mysql_fetch_array($called_lead);
            $status_total=$status_total+$lead_count[0]-$called_leads[0];
        }
        $objPHPExcel->setActiveSheetIndex(0) 
              ->setCellValue($letter_counter .$row_number, $status_total);

       $letter_counter++; 
       
    }
    $row_number++;
}


$objPHPExcel->setActiveSheetIndex(0) 
              ->setCellValue('A' .$row_number, 'Total')
              ->getStyle('A'.$row_number)->getFont()->setBold(true)->setUnderline(true);

$status_counter=1;
$letter_counter='B';              
foreach($status_ids as $s_id)
{
    $total_tally_status=0;
    foreach($campaign_ids as $c_id)
    {
        $tally=mysql_query("SELECT SUM(IF(status_id=$s_id,1,0)) from call_history c 
        RIGHT JOIN (SELECT lead_id, MAX(history_id) as history_id FROM `call_history` 
        WHERE campaign_id='$c_id' AND call_date<='$report_date_max' GROUP BY lead_id ORDER BY call_date DESC) as history 
        ON c.history_id=history.history_id");
        $row=mysql_fetch_array($tally);
        extract($row);
        $tally_count=$row[0];
        $total_tally_status=$total_tally_status+$tally_count;
        if($status_counter==1)
        {
            $new_leads=mysql_query("SELECT COUNT(DISTINCT lead_id) from campaign_lead WHERE date_created<='$report_date_max' AND campaign_id='$c_id'");
            $lead_count=mysql_fetch_array($new_leads);
            $called_lead=mysql_query("SELECT COUNT(DISTINCT lead_id) from call_history WHERE call_date<='$report_date_max' AND campaign_id='$c_id'");
            $called_leads=mysql_fetch_array($called_lead);
            $total_tally_status=$total_tally_status+$lead_count[0]-$called_leads[0];
        }
    }
    $objPHPExcel->setActiveSheetIndex() 
              ->setCellValue($letter_counter .$row_number, $total_tally_status);
    $letter_counter++;
    $status_counter++;
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Campaign Summary');

$objPHPExcel->createSheet();


while($row=mysql_fetch_array($status_list2))
{
    
    $objPHPExcel->setActiveSheetIndex(1) 
              ->setCellValue($letter_count_column_title .'1', $row['status_name'])
              ->getStyle($letter_count_column_title.'1')->getFont()->setBold(true)->setUnderline(true);

    $letter_count_column_title++;
} 
    $objPHPExcel->setActiveSheetIndex(1) 
              ->setCellValue($letter_count_column_title .'1', 'Total Calls Made')
              ->getStyle($letter_count_column_title.'1')->getFont()->setBold(true)->setUnderline(true);

$agents=mysql_query("SELECT * FROM account WHERE account_type='Agent'");
$agent_count=mysql_num_rows($agents);


$row_number=2;
$user_number=0;
for($i=0;$i<$agent_count;$i++)
{
    $user_id=mysql_query("SELECT user_id, name FROM account WHERE account_type='Agent' LIMIT $user_number, 1");
    $row=mysql_fetch_array($user_id);
    $get_user_id=$row[0];
    $get_user_name=$row[1];
    
    
    $objPHPExcel->setActiveSheetIndex(1) 
            ->setCellValue('A' .$row_number, $get_user_name)
            ->getStyle('A'.$row_number)->getFont()->setBold(true)->setUnderline(true);

    $letter_counter="B";
    $status_counter=2;
    foreach($status_ids as $s_id)
    {
        $status_tally=0;
        foreach($campaign_ids as $c_id)
        {
            $tally=mysql_query("SELECT SUM(IF(status_id=$s_id,1,0)) from call_history c 
        RIGHT JOIN (SELECT lead_id, MAX(history_id) as history_id FROM `call_history` 
        WHERE campaign_id='$c_id' AND user_id='$get_user_id'AND call_date<='$report_date_max' AND call_date>='$report_date_min' GROUP BY lead_id ORDER BY call_date DESC) as history 
        ON c.history_id=history.history_id");
        $row=mysql_fetch_array($tally);
        extract($row);
        $status_tally=$status_tally+$row[0];
        }
        $objPHPExcel->setActiveSheetIndex(1) 
              ->setCellValue($letter_counter .$row_number, $status_tally);
        $letter_counter++;
        
    }
    $call_count_query=mysql_query("SELECT COUNT(lead_id) FROM call_history c WHERE user_id='$get_user_id' and call_date<='$report_date_max' AND call_date>='$report_date_min'");
    $total_calls=mysql_fetch_array($call_count_query);
    $objPHPExcel->setActiveSheetIndex(1) 
              ->setCellValue($letter_counter .$row_number, $total_calls[0]);
    $row_number++;
    $user_number++;


}



// Miscellaneous glyphs, UTF-8


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Agent Productivity');



// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Excelsia Daily Report:'.$report_date.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
ob_end_clean();
$objWriter->save('php://output');
exit;
}
?>