<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Account Management</title>

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
*   This function uses the function db_connection from db_query.php
*   session_start() entails opening of a new page of account_management.php
*
*   $login_id is creating a new variable and assigning the specific input by the user to it.
*   $name is creating a new variable for name and assigning the specific input bu the user to it.
*   $select_query contains the query for MYySQL. In this case, it will select the user ID and name of an Agent account.
*   $select_query_run is a resource and contains the special MySQL function mysql_query that will run $select_query 
*   $row is a resource that contains the special MySQL query that stores the query results into an array.
*   $agent_name stores the column "name" from the array "row"
*   $agent_id stores the column "user_id" from the array "row"
*
*   $campaign_name stores the column "campaign_name" from the array "row"
*   $campaign_id stores the column "campaign_id" from the array "row"
*   If user selects "Delete", agent details in the database gets deleted.
*   If user selects "Edit", redirection to edit_user.php 
*   If user selects "Add User", user must input: username, password, re-enter password, fullname and account typ
*   If user selects "Add Campaign", user must input: campaign name, company and creation date.
*   If user selects " Assign Campaign", user assigns an agent into a campaign.
*/   
    include_once("db_query.php");
    db_connection();
   // session_details();
    session_start();
    if (empty($_SESSION))
    {
        header("location:login.php");
    }

     $login_id=$_SESSION['User_ID'];
    $name=$_SESSION['name'];

    
    if(isset($_POST['intent']))
        {
            
            extract($_POST);
            if($intent == "add_account")
            {              
                add_account($username, $password1, $password2, $fullname, $account_type);
                
            }
            else if($intent=="add_campaign")
            {
                add_campaign($campaign_name, $company, $creation_date);
            }
            else if ($intent=="assign_campaign") 
            {
                assign_campaign($agent_id, $campaign_id);
            }           
        }
    $select_query="SELECT user_id, name from account Where account_type='agent'";
    $select_query_run =  mysql_query($select_query);
    $agentlist="";
    while   ($row=mysql_fetch_array($select_query_run) )
    {
            $agent_name=$row['name'];
            $agent_id=$row['user_id'];
            $agentlist.="<option value=\"$agent_id\">".$agent_name."</option>";
    }

    $select_query="SELECT campaign_id, campaign_name from campaign";
    $select_query_run =  mysql_query($select_query);
    $campaignlist="";
    while   ($row=mysql_fetch_array($select_query_run) )
    {
            $campaign_name=$row['campaign_name'];
            $campaign_id=$row['campaign_id'];
            $campaignlist.="<option value=\"$campaign_id\">".$campaign_name."</option>";
    }
    
    if(isset($_GET['intent']))
    {
        extract($_GET);
        $user_id = $_GET['user_id'];
        if($intent == "delete")
        {   
            delete_agent($user_id);
        }
        else if($intent=="edit")
        {
            $_SESSION['update_id']=$user_id;
            header("location:edit_user.php"); 
        }
    }
?>

<body>
    <?php include "navbar.php"; ?>
    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-header">Account Management</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Accounts
                        </div>
                        <?php
                            account_table();
                        ?>  
                    </div>
                </div>
                <!-- /.col-lg-12 -->
                <button type="button" class="btn btn-default btn-lg" style="float:right" data-toggle="modal" data-target="#add_user">
                <span class="glyphicon glyphicon-plus"></span>
                Add user
            </button>
            </div>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    
    
    <div class="modal fade" id="add_user" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Add New User</h4>
          </div>
          <div class="modal-body">
                <form class="form-horizontal" action="account_management.php" method="POST" id="form1">
                    <input type="hidden" id="intent" name="intent" value="add_account">
                    <div class="form-group">
                <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Full Name</label>
                <div class="col-lg-8">
                <input type="text" class="form-control" pattern="[a-zA-Z .\-]{1,5}[a-zA-Z]{1,35}[a-zA-Z .\-]{0,35}" id="fullname" name="fullname" placeholder="Enter Full Name" value="<?php echo $fullname; ?>" required>
                 </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Type</label>
                    <div class="col-lg-8">
                        <select class="form-control" name="account_type">
                            <option value="Agent">Agent</option>
                            <option value="Administator">Administrator</option>
                                    
                        </select>
                    </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Username</label>
                <div class="col-lg-8">
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" value="<?php echo $username; ?>" required="required">
                 </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Password</label>
                <div class="col-lg-8">
                <input type="password" class="form-control" id="password1" name="password1" placeholder="Enter Password" value="<?php echo $password1; ?>" required="required">
                 </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Confirm</label>
                <div class="col-lg-8">
                <input type="password" class="form-control" id="password2" name="password2" placeholder="Confirm Password" value="<?php echo $password2; ?>"required="required">
                 </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Add User</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </form>

          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="delete_user" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Delete User</h4>
                </div>
                <div class="modal-body">
                    Are you sure you want to Delete this User?
                </div>
                <div class="modal-footer">
                    <a href="account_management.php" class="btn btn-danger danger">Delete</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>  
    

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
    $(document).ready(function() {
        $('#account-table').dataTable();
        $('#delete_user').on('show.bs.modal', function(e) {
            $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        });
    });
    
    </script>
</body>

</html>