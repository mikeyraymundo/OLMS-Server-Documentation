
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Assignment</title>

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
* This function allows the administrator to assign leads to a campaign and also assign leads to an agent belonging to a specific industry.
* This function uses the db_connection() function from the db_query.php file.
* This function checks if the user input a specific number of leads to be assigned to an agent.
*
* $login_id contains the specific User_ID for that session.
* $name contains the specific name of the user for that session.
* $get_name contains the special MySQL function mysql_query that selects the campaign name
* $row puts the results of the $get_name query into an array.
* $name stores the selected campaign from the array
* $select_query stores the query to be used in the MySQL database.
* $select_query_run contains the special MySQL function mysql_query which uses $select_query to execute it on the database.
* $campaign_name stores the campaign name of the results of the query into a variable.
* $campaign_id stores the campaign ID of the results of the query into a variable.
* $leadlist is a variable that will generate the dropdown list of all the campaign names.
* $agent_name stores the agent name of the results of the query into a variable.
* $agent_id stores the agent ID of the results of the query into a variable.
* $agentlist is a variable that will generate the dropdown list of all the agents.
* $primary_industry stores the primary industries of the results of the query into a variable.
* $industrylist is a variable that will generate the dropdown list of all the primary industries. 
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

    $login_id=$_SESSION['User_ID'];
    $name=$_SESSION['name'];
    
    if(isset($_POST['intent']))
      {
        extract($_POST);
        if($intent=="all")
        {
          all_leads_assign_to_campaign($selected_campaign);
          $get_name=mysql_query("SELECT campaign_name from campaign WHERE campaign_id='$selected_campaign'");
          $row=mysql_fetch_row($get_name);
          extract($row);
          $name=mysql_escape_string($row[0]);
          echo "<script type='text/javascript'>alert('Successfully assigned all leads to $name')";
        }
        if($intent=="assign_leads_to_campaign")
        {
          if(isset($_POST['checkbox']))
          {
            foreach($_POST['checkbox'] as $checkbox)
            {
                $campaign_id=$_POST['campaign_id'];
                assign_leads_to_campaign($checkbox, $campaign_id);               
            }
            $get_name=mysql_query("SELECT campaign_name from campaign WHERE campaign_id='$campaign_id'");
            $row=mysql_fetch_row($get_name);
            extract($row);
            $name=mysql_escape_string($row[0]);
            echo "<script type='text/javascript'>alert('Succesfully assigned leads to $name'); location.href='assignment.php'</script>";
          }
          else
          {
              echo "<script type='text/javascript'>alert('You cannot assign anymore leads'); location.href='assignment.php'</script>";
          }
        }
        if($intent=="assign_leads_to_agents")
        {
          if(strlen($_POST['number_assign1'])=='0'&& strlen(isset($_POST['number_assign2']))=='0' && strlen(isset($_POST['number_assign3']))=='0')
          {
               echo "<script type='text/javascript'>alert('You did not enter any number'); </script>"; 
          }
          else
          {
            $prompt="Succesfully assigned leads from";
            if($industry1!="default" && (integer)$_POST['number_assign1'])
            {
                assign_leads_to_agents($selected_agent,$industry1, $number_assign1, $radio);
                $prompt.=" $industry1";
            }        
            if(isset($_POST['number_assign2']))
            {
                if($industry2!="default" && (integer)$_POST['number_assign2'])
                {
                assign_leads_to_agents($selected_agent,$industry2, $number_assign2, $radio);
                $prompt.=", $industry2";
                }
            } 
            if( isset($_POST['number_assign3']))
            {
                if($industry3!="default" &&(integer)$_POST['number_assign3']){
                assign_leads_to_agents($selected_agent,$industry3, $number_assign3, $radio);
              $prompt.=", $industry3";}
            } 
            echo "<script type='text/javascript'>alert('$prompt'); </script>"; 
          } 
        }
    }
$select_query= "SELECT campaign_name, campaign_id from campaign ";
$select_query_run =  mysql_query($select_query);

$campaign_list="";
$selected="selected";
while   ($row= mysql_fetch_array($select_query_run) )
{
        $campaign_name=$row['campaign_name'];
        $campaign_id=$row['campaign_id'];
        if(isset($_POST['selected_campaign']))
        {
            extract($_POST);
            if($campaign_id!=$selected_campaign){
            $leadlist.="<option value=".$campaign_id.">".$campaign_name."</option>";}
            else
                {$campaign_list.="<option value=".$campaign_id." selected=".$selected.">".$campaign_name."</option>";}
        }
        else
        {
            $campaign_list.="<option value=".$campaign_id.">".$campaign_name."</option>";
        }
}

$select_query="SELECT user_id, name from account Where account_type='Agent'";
$select_query_run =  mysql_query($select_query);
$agentlist="";
while   ($row=   mysql_fetch_array($select_query_run) )
{
        $agent_name=$row['name'];
        $agent_id=$row['user_id'];
        $agentlist.="<option value=".$agent_id.">".$agent_name."</option>";
}

$select_query="SELECT DISTINCT primary_industry from lead ORDER BY primary_industry";
$select_query_run =  mysql_query($select_query);
$industrylist="";
while   ($row=   mysql_fetch_array($select_query_run) )
{
        $primary_industry=$row['primary_industry'];
        $industrylist.="<option value=\"$primary_industry\">".$primary_industry."</option>";
}
?>

<body class="cl-default fixed" id="login">
  <?php include "navbar.php"; ?>
  <div id="wrapper">
    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Assignment</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">
              Choose Campaign
            </div>
            <div class="panel-body">
              <div class row="row">
                <div class="col-lg-12">
                  <form role="form" id="myform" method="POST" action="assignment.php">
                    <div class="form-group">
                      <label>Campaign</label>
                      <select id="campaign" class="form-control" name="selected_campaign" >
                        <option id="default_option" value="default">Choose a Campaign</option> 
                        <?=$campaign_list?>                         
                      </select>
                    </div>    
                    <div class="form-group">
                      <label>Industry</label>
                      <select id="industry_assign_campaign" class="form-control" disabled name="industry" >
                        <option id="industry_selection_default" value="default">Select Lead Industry</option> 
                        <?=$industrylist?>                         
                      </select>
                    </div>
                    <div>
                      <input type="hidden" name="intent" value="all">
                      <button type="submit" disabled id="assign_all" class="btn btn-primary btn-lg">
                        Assign All to Campaign
                      </button>
                    </div>                         
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end of first panel -->
        <div class="col-lg-6">
          <div class="panel panel-green">
            <div class="panel-heading">
              Assign Leads to Agent
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group" style="margin: 0 auto"> 
                    <form id="myform2" method="POST" action="assignment.php" onsubmit="return confirm('Are you sure you want to assign the agent to these leads?');">
                      <input type="hidden" name="intent" value="assign_leads_to_agents">    
                      <select id="agent" class="form-control" name="selected_agent" value="">
                        <option id="default_agent" value="">Select Agent</option> 
                        <?=$agentlist?>                               
                      </select>
                      <div class="form group" id="agent_leads">
                        <label class="radio-inline"><input type="radio" name="radio" value="New" id="New"  class="radio" checked="checked"/>New</label>
                        <label class="radio-inline"><input type="radio" name="radio" value="Reassign" id="Reassign" class="radio"/>Reassign</label>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <select id="industry1" disabled class="form-control" name="industry1" value="">
                            <option id="default_option1" value="default">Select Industry</option> 
                            <?=$industrylist?>  
                          </select>
                        </div>
                        <div class="col-lg-6">
                          <input type="number" class="form-control" disabled id="number_assign1" name="number_assign1" placeholder= "Enter number" min="1">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <select id="industry2" disabled class="form-control" name="industry2" value="">
                            <option id="default_option2" value="default">Select Industry</option> 
                            <?=$industrylist?>  
                          </select>
                        </div>
                        <div class="col-lg-6">
                          <input type="number" class="form-control" disabled id="number_assign2" name="number_assign2" placeholder= "Enter number" min="1">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <select id="industry3" disabled class="form-control" name="industry3" value="">
                            <option id="default_option3" value="default">Select Industry</option> 
                            <?=$industrylist?>  
                          </select>
                        </div>
                        <div class="col-lg-6">
                          <input type="number" class="form-control" disabled id="number_assign3" name="number_assign3" placeholder= "Enter number" min="1">
                        </div>
                       <button type="submit" disabled id="assign_leads" class="btn btn-success btn-block btn-lg">
                          Assign Leads
                      </button>
                      </div>
                    </form>
                  </div>         
                </div>
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
    </div>
    <!-- end:first row panels -->
  </div>
  <!-- end:id wrapper -->
</body>
<!-- end:body -->
   

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

<script  src="js/dataTables.fnGetHiddenNodes.js"></script>
<script  src="js/dataTables.fnGetFilteredNodes.js"></script>
<script>
 $(document).ready(function() {
        $('table').dataTable( {
            paginate: true,
            scrolly: 300,
            iDisplayStart: 0,
            iDisplayLength: 100,
        });
        
    });
$(document).ready(function() {
   oTable = $('table').dataTable();
    
   $('form').submit(function(){

      $(oTable.fnGetHiddenNodes()).find('input:checked').appendTo('#hiddencontainer');
   });
 
});
$('#allcb').click( function() { //this is the function that will mark all your checkboxes when the input with the .checkall class is clicked
   oTable = $('table').dataTable();
   
    $('input', oTable.fnGetFilteredNodes()).prop('checked',this.checked); //note it's calling fnGetFilteredNodes() - this is so it will mark all nodes whether they are filtered or not
} );
      $(document).ready(function(){ 
        $('#campaign').change(function(){ 
          $('#default_option').prop("disabled", true) 
          $('#industry_assign_campaign').prop("disabled", false) 
          $('#assign_all').prop("disabled", false)
          var selected_campaign = $('#campaign').val(); 
          var selected_industry=$('#industry_assign_campaign').val();
          if(selected_industry!="default"){
          $.post("assignment_table.php",
             {
               selected_campaign: selected_campaign,
               selected_industry: selected_industry,
             },
             function(data) {
               $('#content').html(data);
             }
          );}
        }) 
      })     
      
      $(document).ready(function(){ 
        $('#industry_assign_campaign').change(function(){ 
          $('#default_option').prop("disabled", true) 
          $('#industry_selection_default').prop("disabled", true)  
          var selected_campaign = $('#campaign').val(); 
          var selected_industry=$('#industry_assign_campaign').val();
          if(selected_campaign!="default"){
          $.post("assignment_table.php",
             {
               selected_campaign: selected_campaign,
               selected_industry: selected_industry,
             },
             function(data) {
               $('#content').html(data);
             }
          );}
        }) 
      })   
    $(document).ready(function(){ 
    $('#agent').change(function(){ 
        $('#default_agent').prop("disabled", true)      
        var selected_agent = $('#agent').val();
        var radio_selection=$('input[name=radio]:checked').attr('id');
        $('#industry1').prop("disabled", false)

        $.post("assignment_table2.php",
           {
             selected_agent: selected_agent,
             radio_selection: radio_selection,
           },
           function(data) {
             $('#content').html(data);
           }

        );
     }) 
    })  
      
      $(document).ready(function(){ 
    $('input[name=radio]:radio').click(function(){
        var selected_agent = $('#agent').val()
        var radio_selection=$(this).attr("id")
        $.post("assignment_table2.php",
           {
             selected_agent: selected_agent,
             radio_selection: radio_selection,
           },
           function(data) {
             $('#content').html(data);
           }
        );
    }) 
    })

    $(document).ready(function(){ 
      $('#industry1').change(function(){ 
        $('#default_option1').prop("disabled", true)     
        $('#industry2').prop("disabled", false)
        $('#number_assign1').prop("disabled", false)
        $('#assign_leads').prop("disabled", false)
     }) 
    })  

    $(document).ready(function(){ 
      $('#industry2').change(function(){ 
        $('#default_option2').prop("disabled", true)     
        $('#industry3').prop("disabled", false)
        $('#number_assign2').prop("disabled", false)
      }) 
    }) 

    $(document).ready(function(){ 
      $('#industry3').change(function(){ 
        $('#default_option3').prop("disabled", true)     
        $('#number_assign3').prop("disabled", false)
      }) 
    })  
    </script> 
</html>