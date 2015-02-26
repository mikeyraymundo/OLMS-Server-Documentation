<?php

/**
*   This function displays the result of the specific query/selection made by the user in a table form.
* 
*  This function uses the db_connection() function from the db_query.php file.
*   $selected_agent stores the entry of the user for "selected agent"
*   $radio_selection stores the selection of the user from the radioboxes.
*   $table shows the results and puts them in a table format
*   $query contains the query to be used in MySQL
*   $result contains the special PHP function mysql_query which runs $query.
*   $row contains the special MySQL function mysql_fetch_row that stores $result into an array.
*   $company_name assigns the first column of the array $row as such
*   $primary_industry assigns the next column of the array $row as such
*   $campaign_name assigns the next column of the array $row as such 
*   $lead_id assigns the next column of the array $row as such
*   $agent_name assigns the next column $row as such
*
*   @var resource
*/
include_once("db_query.php");
db_connection();

$selected_agent=$_POST['selected_agent'];
$radio_selection=$_POST['radio_selection'];
$table="";
if($radio_selection=="New")
{
$table .= <<<EOT
 
                    
<div class="panel-body">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="assignment-table">
            <input type="hidden" id="intent" name="intent" value="assign_leads">
            <input type="hidden" id="agent_id" name="agent_id" value="$selected_agent">
            <thead>
                <tr>
                    <th class="hidden-phone" style="width: 15%">Company Name</th>
                    <th class="hidden-phone" style="width: 15%"> Industry</th>   
                    <th class="hidden-phone" style="width: 15%"> Campaign</th>                                 
                </tr>
            </thead>
            <tbody>

EOT;
$query = "SELECT company_name, primary_industry, campaign_name, l.lead_id  FROM  campaign, lead l, agent_list a LEFT JOIN campaign_lead c  ON (c.campaign_id=a.campaign_id AND a.user_id='$selected_agent')  WHERE l.lead_id=c.lead_id AND campaign.campaign_id=c.campaign_id  AND c.user_id IS NULL ;";
$result = mysql_query($query);


    while($row = mysql_fetch_row($result))
    {
      
        $table .= "<tr>";
        $company_name = $row[0];
        $primary_industry = $row[1];
        $campaign_name=$row[2];
        $lead_id=$row[3];
        
        $table .= <<< EOT
        
        <td>$company_name </td>
        <td>$primary_industry </td>
        <td>$campaign_name </td>
EOT;

       
    }
}
else if($radio_selection=="Reassign")
{
    $table .= <<<EOT
<div class="panel-body">
    <div class="table-responsive">
    <table class="table table-striped table-advance table-hover" id="assignment-table">
        <input type="hidden" id="intent" name="intent" value="assign_leads">
        <input type="hidden" id="agent_id" name="agent_id" value="$selected_agent">
            <thead>
                <tr>
                    <th class="hidden-phone" style="width: 15%">Company Name</th>
                    <th class="hidden-phone" style="width: 15%"> Industry</th>   
                    <th class="hidden-phone" style="width: 15%"> Campaign</th>                                 
                    <th class="hidden-phone" style="width: 15%"> Agent Assigned</th>  
                </tr>
            </thead>
            <tbody>

EOT;
$query = "SELECT company_name, primary_industry, campaign_name, l.lead_id, account.name  FROM account, campaign, lead l, agent_list a LEFT JOIN campaign_lead c  ON (c.campaign_id=a.campaign_id AND a.user_id='$selected_agent')  WHERE l.lead_id=c.lead_id AND campaign.campaign_id=c.campaign_id AND account.user_id=c.user_id AND c.user_id !='$selected_agent';";
$result = mysql_query($query);


    while($row = mysql_fetch_row($result))
    {
      
        $table .= "<tr>";
        $company_name = $row[0];
        $primary_industry = $row[1];
        $campaign_name=$row[2];
        $lead_id=$row[3];
        $agent_name=$row[4];
        
        $table .= <<< EOT
        
                <td>$company_name </td>
                <td>$primary_industry </td>
                <td>$campaign_name </td>
                <td>$agent_name </td>

EOT;
    }
}

$table .= <<< EOT

            </tbody>
        </table>
     </div>
</div>
<script src="js/plugins/dataTables/jquery.dataTables.js"></script>
<script src="js/plugins/dataTables/dataTables.bootstrap.js"></script> 
<script>
    $(document).ready(function() {
        $('#assignment-table').dataTable();
        $('#allcb').change(function () {
            $('tbody tr td input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        });
    });
</script>
</html> 
              
EOT;

echo $table;

?>