<?php
//Step1
 $db = mysqli_connect('localhost','root','','greenpark')
 or die('Error connecting to MySQL server.');

 if(isset($_POST['task_rec1'])=="GO")
{ 
$hotel_id = $_POST['hotel_id'];
$from_date = date('Y-m-d 00:00:01',strtotime($_POST['from_date']));
$to_date  = date("Y-m-d 23:59:59",strtotime($_POST['to_date']));
}else
{  
$from_date = date('Y-m-d 00:00:01');
$to_date  = date('Y-m-d 23:59:59');
}    
  

$query = "SELECT * 
FROM tbl_gp_tickets
INNER JOIN tbl_gp_ticket_process_critical ON tbl_gp_ticket_process_critical.ticket_id = tbl_gp_tickets.ticket_id INNER JOIN tbl_gp_hotels ON tbl_gp_tickets.hotel_id = tbl_gp_hotels.hotel_id INNER JOIN tbl_gp_sections ON tbl_gp_sections.section_id = tbl_gp_tickets.section_id INNER JOIN tbl_gp_departments ON tbl_gp_tickets.department_id=tbl_gp_departments.department_id INNER JOIN tbl_gp_user ON tbl_gp_tickets.assigned_user_id=tbl_gp_user.user_id  AND tbl_gp_tickets.hotel_id='$hotel_id' AND tbl_gp_tickets.created_at >= '$from_date' AND tbl_gp_tickets.created_at <= '$to_date' ";



mysqli_query($db, $query) or die('Error querying database.');

//Step3
$result = mysqli_query($db, $query);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
	<script type="text/javascript">
var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
  return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }
})()
</script>
<!------ Include the above in your HEAD tag ---------->
<style>
table#example {
    margin-top: 50px;
}
table {
	margin-top: 50px;
}
</style>
<body>
<div class="container">
  <form method="post" name="taskFilter" class="form-horizontal">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
    		 <td width="30%" valign="top">
												<div class="control-group" style="width:90%;">											
													<label class="control-label" for="prp_id">Hotel name</label>
													<div class="controls">
													<select  class="form-control" name="hotel_id">
													<option value=""> --- Select Hotel Name -- </option>
													<option value="81"> GreenPark Hotel- GPH</option>
													<option value="85"> Marigold Hotel </option>
													<option value="86">GreenPark Hotel- GPC</option>
													<option value="87"> GreenPark Hotel- GPV </option>
													<option value="88"> AVASA Hotel </option>
													<option value="89"> ISB </option>
													<option value="90"> GPHSAH11 </option>
													<option value="92"> GPHS-Amazon HYD-16 </option>
													<option value="93"> GPHS-Amazon HYD-11 </option>
													
													</select>													
													</div>
												</div>
											 </td>
                <td  valign="top">
                  <div class="control-group" style="width:90%;">                      
                    <label class="control-label" for="equipment_name">From Date</label>
                    <div class="controls">
                      <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo date('Y-m-d',strtotime($from_date));?>" />                        
                    </div> <!-- /controls -->       
                  </div>
                </td>

                <td valign="top">
                  <div class="control-group" style="width:80%;">                      
                    <label class="control-label" for="equipment_name">To Date</label>
                    <div class="controls">
                      <input type="date"class="form-control" id="to_date" name="to_date" value="<?php echo date('Y-m-d',strtotime($to_date));?>"  />                        
                    </div> <!-- /controls -->       
                  </div>
                </td>

                <td width="30%" valign="top">
                  <div class="form-actions" style="float:left"><br />
                    <input type="submit" name="task_rec1" id="task_rec1" class="btn btn-primary" value="GO">
                  </div>
                </td>
           </tr>
            </table>   
          </form>
		  </br>
		  <input type="button" class="btn btn-success" onclick="tableToExcel('testTable', 'W3C Example Table')" value="Export to Excel">
</div>

<div class="container-fluid" id="testTable">           
  <table class="table table-striped table-border">
    <tr style="text-aline:center;" class="success">
        <th>Date</th>
        <th> Department </th>
        <th>Section</th>
        <th> Problem</th>
		<th> Problem Classification </th>
		<th> Root Cause</th>
        <th> Improvement Plan for Zero Devation </th>
	 </tr>
	 </thead>
   <tbody>
<?php
		 
		while ($row = mysqli_fetch_array($result)) { ?>
<tr style="text-aline:center;">
<td><?php echo $row['created_at']; ?></td>
<td><?php echo $row['department_name']; ?></td>
<td><?php echo $row['s_section_name']; ?></td>
<td><?php echo $row['subject']; ?></td>
<td><?php
    $row1 = array(23 => "Delay", 24 => "Service Standards", 25 => "Quality", 28 => "Process Lapse", 29 => "Incident", 30 => "Safety");
		
		echo $row1[$row['prob_module_id']];
	 ?></td>
<td><?php echo $row['root_cause']; ?></td>
<td><?php echo $row['improvement_plan']; ?></td>
</tbody>
    <?php } ?>
   
 </tbody>
    </tbody>
  </table>
</div>

</body>
</html>
