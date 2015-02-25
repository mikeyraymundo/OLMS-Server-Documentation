<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Agent Lead List</title>

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
*	This module allows the system to manage the lit of leads according to status id
*
*	$select_query is the query that allows selection of status id from statuses in a 
*	specific campaign 
*
*	@var resource
*/

    include_once("db_query.php");
    db_connection();
    // session_details();
    session_start();
    if (empty($_SESSION))
    {
        header("location:login.php");
    }


    $db = mysql_connect("localhost", "root", "");
    mysql_select_db("wts_olms", $db);

    $login_id=$_SESSION['User_ID'];
    $name=$_SESSION['name'];
    $selected_campaign=$_GET['selected_campaign'];


    $select_query="SELECT status_name, c.status_id from campaign_status c, status s Where c.campaign_id='$selected_campaign' and s.status_id=c.status_id";
    $select_query_run =  mysql_query($select_query);
    $statuslist="";
    while   ($row=   mysql_fetch_array($select_query_run) )
    {
        $status_name=$row['status_name'];
        $status_id=$row['status_id'];
        $statuslist.="<option value=\"$status_id\">".$status_name."</option>";
    }
?>

<body>
    <?php include "navbar2.php"; ?>
    <div id="wrapper">        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Choose a Lead</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form>
                                        <label>Filter</label>
                                        <select id="filter" class="form-control" name="filter_option" value="">
                                            <option id="default_option" value="">Choose a Filter</option> 
                                            <?=$statuslist?>                            
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <!-- /.col-lg-12-->                
            </div>
        <!-- /#page-wrapper -->
        </div>
    </div>
    <!-- /#wrapper --> 



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
        $('#filter').change(function(){  
            $('#default_option').prop("disabled", true);   
            var filter_selection = $('#filter').val();
            var user_id="<?php echo $login_id; ?>";
            var selected_campaign="<?php echo $selected_campaign; ?>";
            $.post("filter_lead_table.php",
               {
                 filter_selection:filter_selection,
                 user_id:user_id,
                 selected_campaign:selected_campaign,
               },
               function(data) {
                 $('#content').html(data);
               }
            );
        }); 
    </script>
</body>

</html>