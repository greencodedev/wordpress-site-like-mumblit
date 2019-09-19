<?php
include('PHPBackgroundProcesser.php');

$proc=new BackgroundProcess();
$arrayProcess=$proc->showAllPocess();
echo '<pre>';
echo $arrayProcess[0].'<br />';
for($j=1;$j<400;$j++)
	{
		if (strpos($arrayProcess[$j], 'mumblit') !== false) {
		 echo $arrayProcess[$j].'<br />';
		}
	}
echo '</pre>';
?>