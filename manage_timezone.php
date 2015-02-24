<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Manage Timezone</title>

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
    if(isset($_POST['intent']))
    {
            
        extract($_POST);
        if($intent == "add_country")
        {              
            add_country($state, $country, $selected_timezone, $radio);
        }
        else if($intent=="add_city")
        {
            add_city($selected_country, $state2, $county, $city, $selected_timezone2);
        }
    }
    $select_query="SELECT DISTINCT country from main_time_zone";
    $select_query_run=mysql_query($select_query);
    $countrylist="";
    while($row=mysql_fetch_array($select_query_run))
    {
            $country=$row['country'];
            $countrylist.="<option value=\"$country\">".$country."</option>";
    }

    $select_query="SELECT timezone from time_zone";
    $select_query_run=mysql_query($select_query);
    $timezonelist="";
    while($row=mysql_fetch_array($select_query_run))
    {
            $timezone=$row['timezone'];
            $timezonelist.="<option value=\"$timezone\">".$timezone."</option>";
    }

    
?>

<body>
    <?php include "navbar.php"; ?>
    <div id="wrapper">
        
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Manage Timezone</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Country
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" action="manage_timezone.php" method="POST" onsubmit="return confirm('Are you sure you want to add this a timezone for this country/state?');">
                                        <div class="form-group">         
                                            <label>Country</label> 
                                            <input class="form-control" placeholder="ex. United States" name="country">
                                            <label>State</label>
                                            <input class="form-control" placeholder="ex. CA for California | can be null" name="state">
                                            <label>Timezone</label>
                                            <select class="form-control" id="tz" name="selected_timezone">
                                                <option id="default_option1">Choose a Timezone</option>
                                                <?=$timezonelist?>
                                            </select>    
                                            <div class="radio">
                                            <label class="radio-inline">
                                            <input type="radio" name="radio" value="0"   class="radio" checked="checked"/> State in 1 Timezone</label>
                                            <label class="radio-inline">
                                            <input type="radio" name="radio" value="1"  class="radio"/> State in 2 Timezones</label>
                                            </div>  
                                            <input type="hidden" name="intent" value="add_country">
                                            <button class="col-lg-3 btn btn-primary" style="float:right">Add</button>                                                           
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            Add City/State
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" action="manage_timezone.php" method="POST" onsubmit="return confirm('Are you sure you want to add this a timezone for this county/city?');">
                                        <div class="form-group">  
                                            <label>Country</label>       
                                            <select class="form-control" id="country" name="selected_country">
                                                <option id="default_option2">Choose a Country</option>
                                                <?=$countrylist?>
                                            </select>
                                            <label>State</label>
                                            <input class="form-control" placeholder="ex. CA which is for California | CANNOT be null" name="state2">
                                            <label>County</label>
                                            <input class="form-control" placeholder="ex. Los Angeles County | CAN be null" name="county">
                                            <label>City</label>
                                            <input class="form-control" placeholder="ex. New York City | CAN be null" name="city">
                                            <label>Timezone</label>
                                            <select class="form-control" id="tz2" name="selected_timezone2">
                                                <option id="default_option3" value="">Choose a Timezone</option>
                                                <?=$timezonelist?>
                                            </select>
                                            </br>
                                            <input type="hidden" name="intent" value="add_city">
                                            <button class="col-lg-3 btn btn-primary" style="float:right">Add</button>                                                           
                                        </div>
                                    </form>
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
    $(document).ready(function() {
        $('#account-table').dataTable();

        $('#delete_user').on('show.bs.modal', function(e) {
            $(this).find('.danger').attr('href', $(e.relatedTarget).data('href'));
        });
    });

    $(document).ready(function(){ 
      $('#tz').change(function(){ 
        $('#default_option1').prop("disabled", true)     
        
      }) 
      $('#country').change(function(){ 
        $('#default_option2').prop("disabled", true)     
        
      }) 
      $('#tz2').change(function(){ 
        $('#default_option3').prop("disabled", true)     
        
      }) 
    }) 
    </script>
</body>

</html>