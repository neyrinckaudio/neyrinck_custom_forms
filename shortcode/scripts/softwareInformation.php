<?php
class NeyrinckSoftware 
{
	function __construct() {
		$connection = mysqli_connect($GLOBALS['ncf_server'], $GLOBALS['ncf_user'], $GLOBALS['ncf_password'], $GLOBALS['ncf_database'], 3306);


		if ($connection->connect_error) {
		    die('Connect Error (' . $connection->connect_errno . ') '
		            . $connection->connect_error);
		}

		$query = "SELECT * FROM main.software_downloads";
		$result = mysqli_query($connection, $query);
		while ($row = mysqli_fetch_assoc($result) ) {
			$this->records[] = $row;
		}



		// group tag=>name relationship
		// this controls which products show up in the drop down on which pages

		$this->products['V-Control Pro'][] = 'V-Control Pro';
		$this->products['V-Control Pro'][] = 'V-Control Pro - Logic Pro X 10.5 and earlier';

		$this->products['V-Control'][] = 'Ney-Fi';

		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Bundle - PT 10.3.6 and earlier';
		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Encoder - PT 10.3.6 and earlier';
		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Decoder - PT 10.3.6 and earlier';
		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Bundle - PT 10.3.7 and later';
		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Encoder - PT 10.3.7 and later';
		$this->products['SC-Dolbly-E'][] = 'SoundCode for Dolby E Decoder - PT 10.3.7 and later';


		$this->products['LtRt'][] = 'SoundCode LtRt Tools 2.1 - PT 10.3.5 or later';
		$this->products['LtRt'][] = 'SoundCode LtRt Tools 2.0 - PT 10.3.4 or earlier';
		$this->products['LtRt'][] = 'SoundCode Stereo LtRt - Legacy TDM';

		$this->products['SC-Exchange'][] = 'SoundCode Exchange MXF-PT 10.3.4 or earlier';
		$this->products['SC-Exchange'][] = 'SoundCode Exchange MXF Import-PT 10.3.4 or earlier';
		$this->products['SC-Exchange'][] = 'SoundCode Exchange MXF-PT 10.3.5 or later';
		$this->products['SC-Exchange'][] = 'SoundCode Exchange MXF Import-PT 10.3.5 or later';

		$this->products['V-Mon'][] = 'V-Mon';

		$this->products['Dolbly-Digital'][] = 'SoundCode For Dolby Digital 2';

		$this->products['DTS'][] = 'SoundCode For DTS';

		$this->products['Spill'][] = 'Spill';

		$this->products['S3 TEST'][] = 'S3 TEST';

		$this->addInfo();



	}


	function addinfo() {


		foreach ($this->records as $row) {

			// Manual Download associations
			$downloads[$row['software']] = $row['file'];

			// Send iLok emails for these products
			if ($row['email'] == 1) {
				$this->iLok[] = $row['software'];
			}
		}

		// Dynamically add Mac/Win specifications
		foreach($this->products as $product=>$software) {
			foreach ($software as $index=>$name) {
				$mac = "$name - MAC OS X";
				$win = "$name - Windows";
				
				// Package list
				$packages[$product][]= $mac;
				if ($name != 'V-Control Pro - Logic Pro X 10.5 and earlier') $packages[$product][]= $win;
				
			}
		}


		$this->packages = $packages;
		$this->downloads = $downloads;
	}


}
$NeyrinckSoftware = new NeyrinckSoftware;

if(isset($_GET['show']))
{
if ($_GET['show'] == 'info') {
	echo "<pre>";
	echo "<h1>Product Associations</h1>";
	print_r($NeyrinckSoftware->packages);

	echo "<h1>Download Associations</h1>";
	print_r($NeyrinckSoftware->downloads);

	echo "<h1>iLok emails</h1>";
	print_r($NeyrinckSoftware->iLok);
}
}
?>
