<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Lead</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<?php

/**
*	This module allows user to update lead details and lead status. It also allows the user
*	to move on to the next lead after updating the status of the current lead. This module
*	allows the user to view the lead history of a specific lead to see if its details were
*	changed
*
*	include_once is a control structure that evaluates the specified file during the 
*	execution of the script
*
*	session_start() is a session function that creates a session or resumes the current 
*	one based on a session identifier passed via a GET or POST request, or passed via a 
*	cookie. 
*	date_default_timezone_set() is a function that sets the default timezone used by all 
*	date/time functions in a script 
*	extract() is a an array function that allows importing variables into the current 
*	symbol table from an array
*
*	$lead_history_query	checks if there have been changes done to a certain lead
*	$intent stores the value of whether the user chooses to change the details of a lead or 
*	update a lead's status
*	$status_query is the query that allows a status list to be used in the status dropdown
*
* 	@var resource
*/

  include_once("db_query.php");
  db_connection();
  // session_details();
  session_start();
  if (empty($_SESSION))
    {
        header("location:login.php");
    }

  $name=$_SESSION['name'];
  $user_id=$_SESSION['User_ID'];
  if($_GET!=NULL)
  {
    $selected_campaign=$_GET['selected_campaign'];
    $row_index=$_GET['row_index'];
    $filter_selection=$_GET['filter_selection'];
    $navigation=$_GET['navigation'];
    $total_rows=$_GET['total_rows'];

  }

  

  if(isset($_POST['intent']))
  {
    extract($_POST);
    if($intent=="edit_lead_details")
    {
          update_lead_details($first_name, $middle_name, $last_name, $company, $state,$email, $title,  $lead_id,$user_id,$selected_campaign, $phone_number, $city, $country );
    }
    else if($intent=="update_status")
    {
      if(empty($callback_date)&$selected_status=='12')
      {
              echo "<script type='text/javascript'>alert('You did not enter a date');  </script>";
      }
      else
      {
        if($filter_selection!=$selected_status)
        {
          $total_rows=$total_rows-1;
        }
         update_lead($filter_selection, $selected_status, $notes, $lead_id,$user_id,$selected_campaign,$callback_date, $state2, $country2, $city2, $county2);
        if($total_rows == 0)
        {
          echo "<script type='text/javascript'>alert('There are no more leads'); location.href='manage_leads_list.php?selected_campaign='+'$selected_campaign'</script>";
        }
      }
    }
  }

  if($navigation=="previous")
  {
    if($row_index==0)
    {
      echo "<script type='text/javascript'>alert('This is the first lead'); </script>";
    }
    else
    {
      $row_index=$row_index-1;
    }
   }
  else if($navigation=="next")
  {
    if($row_index+1==$total_rows)
    {
        echo "<script type='text/javascript'>alert('This is the last lead'); </script>";
    }
    else
    {
     $row_index=$row_index+1;
    }
  }

  if($filter_selection=="12")
  {
    $date=date("Y-m-d");                                                                                                                                                                                            
    $query=mysql_query("SELECT contact_first_name, contact_middle_name, contact_last_name, contact_title, company_name, primary_state, email, c.campaign_id, status_id, notes, c.lead_id, phone_number, primary_city, primary_country, primary_county, DATE_FORMAT(callback_date, '%Y-%m-%d' ) AS cdate, DATE_FORMAT(callback_date, '%H:%i:%s') AS time FROM campaign_lead c, lead l WHERE status_id='3'
     AND user_id='$user_id' AND DATE_FORMAT( callback_date, '%Y-%m-%d')='$date' AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign' ORDER BY callback_date ASC LIMIT $row_index,1");
    $row=mysql_fetch_row($query);
    $default_date=$row[15];
    $default_date.="T";
    $default_date.=$row[16];

  }
  else 
  {
    $query=mysql_query("SELECT contact_first_name, contact_middle_name, contact_last_name, contact_title, company_name, primary_state, email, c.campaign_id, status_id, notes, c.lead_id, phone_number, primary_city, primary_country, primary_county FROM campaign_lead c, lead l WHERE status_id='$filter_selection' 
      AND user_id='$user_id' AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign'ORDER BY company_name ASC LIMIT $row_index,1 ;");
    $row=mysql_fetch_row($query);

    
    if($row_index!=0)
    {

      $previous_row=$row_index-1;
    }
    else
    {
      $previous_row=0;
    }
    $previous_lead=mysql_query("SELECT l.lead_id FROM campaign_lead c, lead l WHERE status_id='$filter_selection' 
      AND user_id='$user_id' AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign'ORDER BY company_name ASC LIMIT $previous_row,1 ;");
    
    if(mysql_num_rows($previous_lead)==1)
    {
      $row2=mysql_fetch_row($previous_lead);
       $previous_id=$row2[0];
    }
    
    if($row_index==$total_rows-1)
    {
      $next_row=$row_index;
    }
    else
    {
       $next_row=$row_index+1;
    }
    $next_lead=mysql_query("SELECT l.lead_id FROM campaign_lead c, lead l WHERE status_id='$filter_selection' 
      AND user_id='$user_id' AND c.lead_id=l.lead_id AND c.campaign_id='$selected_campaign'ORDER BY company_name ASC LIMIT $next_row,1 ;");
    if(mysql_num_rows($next_lead)==1)
    {
      $row3=mysql_fetch_row($next_lead);
      $next_id=$row3[0];
    }
    

  }
  
$lead_id=$row[10];
$lead_history_query=mysql_query("SELECT DATE_FORMAT(call_date, '%Y-%m-%d ' '%H:%i ' ) as call_date, status_name FROM call_history c, status  s WHERE campaign_id='$selected_campaign' AND lead_id='$lead_id' AND c.status_id=s.status_id ORDER BY call_date DESC;");
    $lead_history="";
if(mysql_num_rows($lead_history_query)==0)
{
    $lead_history.="There has been no changes made for this lead";
}
  else
  {
   while($row2=mysql_fetch_array($lead_history_query))
    {
      $date=$row2['call_date'];
      $status_name=$row2['status_name'];
      $lead_history .=''.$date.'  '.$status_name.' \n';
    }
  }

//Query to make a status list used in the status dropdown
$status_query=mysql_query("SELECT status_name, s.status_id FROM campaign_status c, status s WHERE c.status_id=s.status_id AND c.campaign_id='$selected_campaign';");
  $status_list="";
  $selected="selected";
  while   ($array= mysql_fetch_array($status_query) )
  {
        $status_name=$array['status_name'];
        $status_id=$array['status_id'];
        
        if($status_id!=$row[8]){
        $status_list.="<option value=".$status_id.">".$status_name."</option>";}
        else
        {$status_list.="<option value=".$status_id." selected=".$selected.">".$status_name."</option>";}
}
?>

<body>
    <?php include "navbar2.php"; ?>
    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $row[4] ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Lead Details
                        </div>
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-lg-6">
                              <form role="form" action="lead.php" method="POST" onsubmit="return confirm('Are you sure you want to update the lead details?');">
                                <input type="hidden" name="intent" value="edit_lead_details">  
                                <input type="hidden" name="navigation" value="current">  
                                <input type="hidden" name="filter_selection" value="<?php echo $filter_selection;?>">  
                                <input type="hidden" name="row_index" value="<?php echo $row_index;?>">   
                                <input type="hidden" name="total_rows" value="<?php echo $total_rows;?>"> 
                                <input type="hidden" name="selected_campaign" value="<?php echo $selected_campaign;?>"> 
                                <input type="hidden" name="lead_id" value="<?php echo $row[10];?>">
                                <div class="form-group">         
                                    <label>First Name</label> 
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $row[0]; ?>">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo $row[1]; ?>">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $row[2]; ?>">
                                    <label>Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $row[3]; ?>">
                                    <label>Company</label>
                                    <input type="text" class="form-control" id="company" name="company" value="<?php echo $row[4]; ?>" required="required">
                                    <label>Country</label>
                                    <input type='text' class="form-control" id="country" name="country" value="<?php echo $row[13]; ?>" required="required">
                                    <label>State</label>
                                    <input type='text' class="form-control" id="state" name="state" value="<?php echo $row[5]; ?>" required="required">
                                    <label>City</label>
                                    <input type='text' class="form-control" id="city" name="city" value="<?php echo $row[12]; ?>" required="required">
                                    <label>Phone Number</label>
                                    <input type='text' pattern="[0-9 .\-]{0,20}"class="form-control" id="phone_number" name="phone_number" value="<?php echo $row[11]; ?>">
                                    <label>Email</label>
                                    <input type='input' class="form-control" id="email" name="email" value="<?php echo $row[6]; ?>">
                                    <br>
                                    <br>
                                    
                                    <button class="btn btn-primary btn-block">Change Lead Details</button> 
                                                                                             
                                </div>
                              </form>
                            </div>
                            <div class="col-lg-6">
                              <form role="form" action="lead.php" method="POST" onsubmit="return confirm('Are you sure you want to update the lead ?');">
                                <input type="hidden" name="intent" value="update_status">  
                               <input type="hidden" name="intent" value="update_status">  
                              <input type="hidden" name="navigation" value="current">  
                              <input type="hidden" name="filter_selection" value="<?php echo $filter_selection;?>">  
                              <input type="hidden" name="row_index" value="<?php echo $row_index;?>">   
                              <input type="hidden" name="total_rows" value="<?php echo $total_rows;?>"> 
                              <input type="hidden" name="selected_campaign" value="<?php echo $selected_campaign;?>"> 
                              <input type="hidden" name="lead_id" value="<?php echo $row[10];?>"> 
                              <input type="hidden" name="state2" value="<?php echo $row[5];?>">  
                              <input type="hidden" name="country2" value="<?php echo $row[13];?>">  
                              <input type="hidden" name="city2" value="<?php echo $row[12];?>">  
                              <input type="hidden" name="county2" value="<?php echo $row[14]?>">  
                                <div class="form-group">  
                                <label>Status</label>       
                                <select id="status" class="form-control" name="selected_status" value="">
                                  <?=$status_list?>                               
                                </select>
                                <div id="date" class="form-group" style="visibility:hidden">
                                  <label>Date Time</label>
                                  <input id='datepicker' type='datetime-local' class="form-control" id="callback_date" name="callback_date" value="<?php echo $default_date;?>">
                                </div>                                
                                <div class="form-group">
                                  <label>Notes</label>
                                  <textarea class="form-control" rows="18" name="notes"><?php echo $row[9]; ?></textarea>
                                </div>                                
                                <button class="col-lg-3 btn btn-block btn-success">Update Status</button>
                              </form>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>                
                <!-- /.col-lg-12-->                
                <div class="row">
                  <button id="previous" <?php if($row_index==0){ echo "disabled";}?> type="submit" onClick="getID(this)" class="btn col-lg-4">
                    Previous Lead
                  </button> 
                  <button id="lead_history" onClick="history(this)" class="btn btn-info col-lg-4" value="<?php echo $lead_history; ?>">
                    Lead History
                  </button>
                  <button id="next" <?php $end_row=$total_rows-1; if($row_index==$end_row){ echo "disabled";}?> type="submit" onClick="getID(this)" class="btn col-lg-4">
                    Next Lead
                  </button>
                </div>
            </div>
        </div>
        <!-- /#page-wrapper -->
  <!-- jQuery -->
  <script src="js/jquery.js"></script>

  <!-- Bootstrap Core JavaScript -->
  <script src="js/bootstrap.min.js"></script>

  <!-- Metis Menu Plugin JavaScript -->
  <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

  <!-- Custom Theme JavaScript -->
  <script src="js/sb-admin-2.js"></script>

  <!-- DataTables JavaScript -->
  <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
  <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>   

  <!-- Page-Level Demo Scripts - Tables - Use for reference -->
  <script>
    function getID(oObject)
    {
      var navigation=oObject.id;
      var selected_campaign="<?php echo $selected_campaign;?>";
      var filter_selection="<?php echo $filter_selection;?>";
      var row_index="<?php echo $row_index;?>";
      var total_rows="<?php echo $total_rows;?>";
      if( navigation=="previous")
      {
        var leads_id="<?php echo $previous_id;?>";
      }
      else if(navigation=="next")
      {
        var leads_id="<?php echo $next_id;?>";
      }

      document.location.href = 'lead.php?selected_campaign='+ selected_campaign+'&row_index='+row_index
      +'&filter_selection=' +filter_selection+'&navigation=' + navigation +'&total_rows='+total_rows + '&leads_id=' + leads_id;
    }  
    function history(oObject )
    {
      var lead_history="<?php echo $lead_history;?>"
      alert(lead_history);
    }
    $(document).ready(function(){ 
      $('#status').change(function(){ 
        var status= $('#status').val();
        if(status=="12")
        {
        
          document.getElementById('date').style.visibility='visible';
        }
        else
        {
          document.getElementById('date').style.visibility='hidden';
        }
      }) 
    })  
    $(document).ready(function(){ 
      var status= $('#status').val();
      if(status=="12")
      {
        document.getElementById('date').style.visibility='visible';
      }
      else
      {
        document.getElementById('date').style.visibility='hidden';
      }
    }) 
  </script>
</body>
</html>