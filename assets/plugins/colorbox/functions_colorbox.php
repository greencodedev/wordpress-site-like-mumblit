<?php 
/* plugins-> functions wallcolor
* @autor Pp Galvan - LdrMx
*/
	function get_colorbox($c) {
	global $wo, $sqlConnect;
		if(!$c){
			return false;
		}
		
		$resource = $sqlConnect->query('SELECT * FROM colorbox WHERE id='.$c.' LIMIT 1');
		$file = $resource->fetch_assoc();

		if($resource->num_rows > 0){
			if($file['type'] == 1){
				$t = 'background-color : '.$file['c1'].';';
			} else if($file['type'] == 2){
				$t = 'background-image : linear-gradient(45deg, '.$file['c1'].' 0%, '.$file['c2'].' 100%);';
			} else if($file['type'] == 3){
				$t = 'background-image : url(&quot;'.$wo['config']['site_url'].'/upload/colorbox/'.$file['id'].'.'.$file['filetype'].'&quot;);';
			}
		}
		if(!empty($t)){ 
			$a = $t.'color:rgba(255,255,255,1.00);font-size:30px;font-weight:700;line-height:1.2000em;padding:50px 30px;text-align:center; background-repeat: no-repeat;
		background-size: 100% 100%; height: 245px;';
			return $a;
		}
	}
?>