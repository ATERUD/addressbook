<!DOCTYPE html>
<meta charset="utf-8">

<?php
require_once(__DIR__.'/ater-phplibs/ater_ldap.php');

$ldap = new ATERUD\LDAP();
if ($ldap) {
	$fields = array("cn", "givenName", "sn", "department", "manager", "title");
	// (userAccountControl:1.2.840.113556.1.4.803:=2 sono gli utenti disabilitati
	$disabledUsersFilter = "(&(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))"; 
	$info = $ldap->GetUsers($disabledUsersFilter, $fields, 'manager') ;
		
	for ($i = 0; $i < $info["count"]; $i++) {
		$manager = $info[$i]["manager"][0]; 
		$commaPos = strpos($manager, ',');
		// Manager è nel formato LDAP (CN=nome, OU=ou, etc). Prendiamo solo Cognome e Nome.
		if ($commaPos != false) {
			$info[$i]['manager'][0] = substr($manager, 3, $commaPos - 3);
		}
	}

	$ldap = null;
}

?>

<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
		data.addColumn('string', 'Name');
 		data.addColumn('string', 'Manager');
		data.addColumn('string', 'ToolTip');

		var ADEntries = <?php echo json_encode($info); ?>;
		var entryCount = "<?php echo $info["count"]; ?>";
		for (i = 0; i < entryCount; i++) {
			try {
				var cn = ADEntries[i]['cn'][0];
				var givenName = ADEntries[i]['givenname'][0];
				var sn = ADEntries[i]['sn'][0];
				var department = ADEntries[i]['department'][0];
				var manager = '';
				try {
					manager = ADEntries[i]['manager'][0];			
				} catch (error) {
				}
				var jobTitle = '';
				try {
					jobTitle = ADEntries[i]['title'][0];
				} catch (error) {
				}
				data.addRow([{v:cn,f:cn+'<div style="color:red; font-style:italic">'+department+'</div>'}, manager, jobTitle]);
			} catch (error) {
			}

		}

		var chartOptions = {
        	title: "Organigramma ATER di Udine",
        	allowHtml: true,
			allowCollapse: true,
      	};

        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
		// Wait for the chart to finish drawing before calling the getImageURI() method.
		//google.visualization.events.addListener(chart, 'ready', function () {
		//	chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
		//	document.getElementById('png').outerHTML = '<a href="' + chart.getImageURI() + '">Printable version</a>';
		//	console.log(chart_div.innerHTML);
		//});

        chart.draw(data, chartOptions);
      }
   </script>
    </head>
  <body>
    <div id="chart_div" style="width: 600px; height: 400px;"></div>
	<div id='png'></div>
  </body>
</html>

