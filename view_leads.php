<?php 

include_once("db_query.php");
    db_connection();
    session_start();
    date_default_timezone_set('Asia/Manila');
    if (empty($_SESSION))
    {
        header("location:login.php");
    }


    if(isset($_POST['intent']))
    {
            extract($_POST);
            if($intent == "generate_report")
            {
                $date=date("Y-m-d");
                if(empty($report_date))
                {
                    echo "<script type='text/javascript'>alert('You did not enter a date');  </script>";
                }
                else if($report_date>$date)
                {
                    echo "<script type='text/javascript'>alert('Cannot create a report for that date!');  </script>";
                }
                else
                {
                    generate_report($report_date);
                }
            }
            
    }
    if(isset($_GET['intent']))
    {
        extract($_GET);
        $lead_id = $_GET['lead_id'];
        if($intent == "delete")
        {   
            delete_all_records_of_lead($lead_id);
        }
    }

    $select_query=mysql_query("SELECT status_id, status_name from status");
    $statuslist="";
    while   ($row=   mysql_fetch_array($select_query) )
    {
        $status_name=$row['status_name'];
        $status_id=$row['status_id'];
        $statuslist.="<option value=\"$status_id\">".$status_name."</option>";
    }

    
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>View Lead Summary & Report Generation</title>

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


<body>
    <?php include "navbar.php"; ?>
    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Leads
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" action="manage_timezone.php" method="POST">
                                        <div class="form-group">         
                                            <select id="filter" class="form-control" name="filter_option" value="">
                                            <option id="default_option" selected="selected" value="All">All</option>  
                                            <?=$statuslist?> 
                                            <option  value="flagged_leads">Flagged Leads</option>                       
                                            </select>                                                          
                                        </div>
                                        
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-12-->                
            </div>
            <?php
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
        <div class="col-lg-6">
            <div class="panel panel-green">
                <form role="form" action="view_leads.php" method="POST">
                    <input type="hidden" name="intent" value="generate_report">     
                    <div class="row" style="margin: 0 auto">
                        <label class="col-sm-2 control-label col-lg-4" for="inputSuccess">Select Report Date</label>
                        <div class="col-lg-4">
                            <input type='date' class="form-control" id="report_date" name="report_date" value="">       
                         </div>
                    </div>
             
                    <button type="submit" class="btn btn-default btn-block btn-lg btn-perspective">
                        Generate Report
                    </button>
                </form> 
            </div>
         </div>   
        </div>
        <!-- /#page-wrapper -->
    </div>
   

    <div class="modal fade" id="delete_lead" role="dialog" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Delete User</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to do this? This will completely delete all data of the lead.
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
<script>
    
       
$(document).ready(function(){ 
   var filter_selection = $('#filter').val();
    $.post("administrator_view_leads_table.php",
       {
         filter_selection: filter_selection,
       },
       function(data) {
         $('#content').html(data);
       }
     );
}) 
$(document).ready(function(){ 
    $('#filter').change(function(){  
        var filter_selection = $('#filter').val();
        $.post("administrator_view_leads_table.php",
           {
             filter_selection: filter_selection,
           },
           function(data) {
             $('#content').html(data);
           }

        );
     }) 
})
</script>
</body>

</html>