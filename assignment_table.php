<?php
include_once("db_query.php");
db_connection();

$selected_campaign=$_POST['selected_campaign'];
$selected_industry=$_POST['selected_industry'];
$table="";
$table .= <<<EOT
 
                    
<div class="panel-body">
    <div class="table-responsive">
        <form role="form" action="assignment.php" method="POST" onsubmit="return confirm('Are you sure you want to assign these leads?');">
        <div style="display:none" id="hiddencontainer"></div>
        <table class="table table-striped table-bordered table-hover" >
            <input type="hidden" id="intent" name="intent" value="assign_leads_to_campaign">
            <input type="hidden" id="campaign_id" name="campaign_id" value="$selected_campaign">
            <thead>
                <tr>
                    <th class="hidden-phone" style="width: 15%"><input type="checkbox" id="allcb" />ALL</th>
                    <th class="hidden-phone" style="width: 15%">Company Name</th>
                    <th class="hidden-phone" style="width: 15%"> Industry</th>   
                </tr>
            </thead>
            <tbody>

EOT;
$query = "SELECT company_name, primary_industry, l.lead_id  FROM lead l LEFT JOIN campaign_lead c ON l.lead_id=c.lead_id AND c.campaign_id='$selected_campaign' WHERE c.lead_id IS NULL AND primary_industry='$selected_industry'; ";
$result = mysql_query($query);
    while($row = mysql_fetch_row($result))
    {
      
        $table .= "<tr>";
        $company_name = $row[0];
        $primary_industry = $row[1];
        $lead_id=$row[2];
        
        
        $table .= <<< EOT
                <td><input type='checkbox' name='checkbox[]' value="$lead_id"></td>
                <td>$company_name </td>
                <td>$primary_industry </td>
EOT;
       
    }

$table .= <<< EOT

            </tbody>
        </table>
        <button type="submit" class="btn btn-primary btn-block" >
           Add Leads
        </button>
        </form>
    </div>
</div>

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
</script>
</html> 
              
EOT;

echo $table;

?>