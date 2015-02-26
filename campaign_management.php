
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Campaign Management</title>

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
* This allows the user to select an agent or a status to be assigned into a campaign.
* This uses the db_query.php file for its database connection.
* $select_query contains the query to be used in the database.
* $select_query_run contains the special PHP function mysql_query that uses and runs $select_query.
* $agentlist would contain the agent name results
* $row contains the special PHP function mysql_fetch_array that places the results of $select_query_run in an array.
* $agent_name would contain the column of 'name' from the $row array
* $agent_id would contain the column of the 'user_id' column from the $row array
* $statuslist would contain the status results
* $status_name would contain the column of the 'status_name'from the $row array
* $status_id would contain the column of the 'status_id' from the $row array
* $campaighnlist would contain the campaign results
* $campaign_name would contain the column of the 'campaign_name' from the $row array
* $campaign_id would contain the column of the 'campaign_id' from the $row array
* $leadlist would contain the leads under the specific campaign results
*
* @var resource
*/
    include_once("db_query.php");
    db_connection();
    session_start();
if (empty($_SESSION))
{
    header("location:login.php");
}

if(isset($_POST['intent']))
      {
        extract($_POST);
        if($intent=="unassign_leads")
        {
           foreach($_POST['checkbox'] as $checkbox)
           {
            $campaign_id=$_POST['campaign_id'];
            unassign_leads($checkbox, $campaign_id);    
           }
           echo "<script type='text/javascript'>alert('Leads Removed'); location.href='campaign_management.php'</script>";
        }
        else if($intent=="unassign_agents")
        {
           foreach($_POST['checkbox'] as $checkbox)
           {
            $campaign_id=$_POST['campaign_id'];
            unassign_agents($checkbox, $campaign_id);
                
           }
           echo "<script type='text/javascript'>alert('Agents Unassigned'); location.href='campaign_management.php'</script>"; 
        }
         else if($intent=="assign_agents")
        {
           foreach($_POST['checkbox'] as $checkbox)
           {
            $campaign_id=$_POST['campaign_id'];
            assign_agents($checkbox, $campaign_id);
                
           }
           $get_name=mysql_query("SELECT campaign_name from campaign WHERE campaign_id='$campaign_id'");
          $row=mysql_fetch_row($get_name);
          extract($row);
          $name=mysql_escape_string($row[0]);
           echo "<script type='text/javascript'>alert('Agents assigned to $name'); location.href='campaign_management.php'</script>"; 
        }
         else if($intent=="assign_status")
        {
           foreach($_POST['checkbox'] as $checkbox)
           {
            $campaign_id=$_POST['campaign_id'];
            assign_statuses($checkbox, $campaign_id);
                
           }
           $get_name=mysql_query("SELECT campaign_name from campaign WHERE campaign_id='$campaign_id'");
          $row=mysql_fetch_row($get_name);
          extract($row);
          $name=mysql_escape_string($row[0]);
           echo "<script type='text/javascript'>alert('Statuses assigned to $row[0]'); location.href='campaign_management.php'</script>"; 
        }
        else if($intent=="add_campaign")
        {
             add_campaign($campaign, $company, $creation_date);
        }
        else if($intent=="add_status")
        {
             add_status($status, $description);
        }
      }
$select_query="SELECT user_id, name from account Where account_type='agent'";
$select_query_run =  mysql_query($select_query);
$agentlist="";
while   ($row=   mysql_fetch_array($select_query_run) )
{
        $agent_name=$row['name'];
        $agent_id=$row['user_id'];
        $agentlist.="<option value=\"$agent_id\">".$agent_name."</option>";
}
$select_query=mysql_query("SELECT status_id, status_name from status");
$statuslist="";
while   ($row=   mysql_fetch_array($select_query) )
{
        $status_name=$row['status_name'];
        $status_id=$row['status_id'];
        $statuslist.="<option value=\"$status_id\">".$status_name."</option>";
}

$select_query=          "SELECT campaign_id, campaign_name from campaign";
$select_query_run =  mysql_query($select_query);
$campaignlist="";
while   ($row=   mysql_fetch_array($select_query_run) )
{
        $campaign_name=$row['campaign_name'];
        $campaign_id=$row['campaign_id'];
        $campaignlist.="<option value=\"$campaign_id\">".$campaign_name."</option>";
}

$select_query= "SELECT campaign_name, campaign_id from campaign ";
$select_query_run =  mysql_query($select_query);

$campaign_list="";
$selected="selected";
while   ($row= mysql_fetch_array($select_query_run) )
{
        $campaign_name=$row['campaign_name'];
        $campaign_id=$row['campaign_id'];
        $campaign_list.="<option value=".$campaign_id.">".$campaign_name."</option>";
}


?>
<body class="cl-default fixed" id="login">
  <?php include "navbar.php"; ?>
  <div id="wrapper">
    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Campaign Management</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="panel panel-green">
            <div class="panel-heading">
              Choose an Action
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group" style="margin: 0 auto"> 
                    <button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#add_campaign">
                        Add Campaign
                    </button>
                    <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="modal" data-target="#add_status">
                        Add Status
                    </button>   
                    <label> Choose a campaign first: </label>
                    <form id="myform2" method="POST" action="update_campaign.php">
                      <input type="hidden" id="update_campaign_id"  name="update_campaign_id" value="">    
                        <button type="submit" class="btn btn-warning btn-lg btn-block" id="edit_campaign" <?php if($_POST!=NULL){}else{echo "disabled";} ?>>
                          Edit Campaign
                        </button>
                    </form>
                  </div>         
                </div>
              </div>  
            </div>
          </div>
        </div>
        <!-- end of first panel -->
        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">
              Choose Campaign
            </div>
            <div class="panel-body">
              <div class row="row">
                <div class="col-lg-12">
                  <form role="form" id="myform" method="POST">
                    <div class="form-group">
                      <label>Campaign</label>
                      <select id="campaign" class="form-control" name="selected_campaign" >
                        <option id="default_option" value="">Choose a Campaign</option> 
                        <?=$campaign_list?>                         
                      </select>
                    </div>    
                    <div class="form group" id="agent_leads">
                      <div class="radio">
                        <label class="radio-inline"><input type="radio" name="radio" value="Leads" id="Assign_Agents" disabled class="radio"/>Assign Agents</label>
                        <label class="radio-inline"><input type="radio" name="radio" value="Agents" id="Status" disabled class="radio"/>Assign Status</label>
                        <br>
                        <label class="radio-inline"><input type="radio" name="radio" value="Agents" id="Agents" disabled class="radio"/>Unassign Agents</label>
                        <label class="radio-inline"><input type="radio" name="radio" value="Leads" id="Leads" disabled class="radio"/>Unassign Leads</label> 
                      </div>
                    </div>              
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>  

      <?php

      /**
      * $content would contain the results
      * $intent is the task that the user intends to be done
      * $checkbox will store all those items selected 
      * $campaign_id would contain the 'campaign_id' from the database
      * unassign_leads is used here for those $campaign_id with those selected corresponding $checkbox to be unassigned leads
      * unassign_agents is used here for those $campaign_id with those selected corresponding $checkbox to be unassigned to selected agents
      * assign_agents is used here for those $campaign_id with those selected corresponding $checkbox to be assigned to selected agents
      * $get_name contains a special PHP function mysql_query. It contains the query to be used in the database.
      * $row contains the special PHP function mysql_fetch_row. This function retrieves the results of the query and puts them in an array.
      * extract is a special PHP function. It retrieves the elements of $row
      * $name contains a special PHP function mysql_escape_string. It stores the first column of the array $row
      *
      * @var resource
      *
      */
    $content =<<<EOT
    <div class="col-lg-12">
      <div class="panel panel-yellow">
        <div class="panel-heading">
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-lg-12" id="content">
          </div>
        </div>
      </div>
    </div>
EOT;
    echo $content;
      
?> 
    </div>
    <!-- end:first row panels -->
  </div>
  <!-- end:id wrapper -->
</body>
<!-- end:body -->
     


<div class="modal fade" id="add_campaign" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Add New Campaign</h4>
      </div>
        <form class="form-horizontal" action="campaign_management.php" method="POST">
        <input type="hidden" id="intent" name="intent" value="add_campaign">
        <div class="modal-body">        
          <div class="form-group">
            <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Campaign Name</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="campaign" name="campaign" placeholder="Enter Campaign Name" value="<?php echo $campaign; ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Company</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="company" name="company" placeholder="Enter Company Name" value="<?php echo $company; ?>"required>
            </div>
          </div>
                
        </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Add Campaign</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="add_status" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Add New Status</h4>
      </div>
      <form class="form-horizontal" action="campaign_management.php" method="POST">
          <input type="hidden" id="intent" name="intent" value="add_status">
      <div class="modal-body">
          <div class="form-group">
            <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Status Name</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="status" name="status" placeholder="Enter Status Name" value="<?php echo $status; ?>"required>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Description</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" value="<?php echo $description; ?>"required>
            </div>
          </div>   
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Status</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

     <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script>
      $(document).ready(function(){ 
        $('#campaign').change(function(){ 
          var selected_campaign = $('#campaign').val();
          var radio_selection=$('input[name=radio]:checked').attr('id');
          $.post("campaign_management_table.php",
             {
               selected_campaign: selected_campaign,
               radio_selection: radio_selection,
             },
             function(data) {
               $('#content').html(data);
             }
          );
          $('#default_option').prop("disabled", true)   
          $('#update_campaign_id').val($('#campaign').val()) 
          $('#edit_campaign').prop("disabled", false)
          $('#show_table').prop("disabled", false)
          $('#Leads').prop("disabled", false)
          $('#Agents').prop("disabled", false)
          $('#Assign_Agents').prop("disabled", false)
          $('#Status').prop("disabled", false)
        }) 
        $('input[name=radio]:radio').click(function(){
          var selected_campaign = $('#campaign').val()
          var radio_selection=$(this).attr("id")
          $.post("campaign_management_table.php",
            {
              selected_campaign: selected_campaign,
              radio_selection: radio_selection,
            },
            function(data) {
              $('#content').html(data);
            }
          );
        })
      })
    </script> 
</html>