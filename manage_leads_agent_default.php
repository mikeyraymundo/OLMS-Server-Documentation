<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Campaign List For Agent</title>

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
?>

<body>
    <?php include "navbar2.php"; ?>
    <div id="wrapper">        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Choose a Campaign</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php 
                                        $table="";
                                        $table .= <<<EOT
                                                    <table class="table table-striped table-bordered table-hover" id="campaign-table">
                                                    <thead>
                                                        <tr>                                
                                                            <th class="hidden-phone" style="width: 15%"> Campaign Name</th>   
                                                        </tr>
                                                    </thead>
                                                    <tbody>
EOT;
                                        $query=mysql_query("SELECT c.campaign_name, c.campaign_id FROM agent_list a, campaign c WHERE c.campaign_id=a.campaign_id AND a.user_id='$login_id';");
                                        while($row = mysql_fetch_row($query))
                                        {
                                            $table .= "<tr>";
                                            $campaign_name = $row[0];
                                            $campaign_id = $row[1];
                                            $table .= <<< EOT
                                                        <td id="$campaign_id" onclick="getID(this);">$campaign_name</td>
EOT;
                                        }
                                        $table .= <<< EOT
                                                    </tbody>
                                                </table>
EOT;
                                        echo $table;    
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- /.col-lg-12-->                
            </div>
        </div>
        <!-- /#page-wrapper -->

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
        function getID(oObject)
        {
            var selected_campaign=oObject.id;
            document.location.href = 'manage_leads_list.php?selected_campaign='+selected_campaign;
        }  
    </script>
</body>

</html>