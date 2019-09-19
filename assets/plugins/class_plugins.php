<?php 
/* @author LdrMx - Pp Galvan */ 
 
class LdrSoft{    
	
	function UploadImage($file, $name) {
		global $wo, $sqlConnect;
		//user exist
		if (Wo_IsLogged() === false) { return false; }
		//value
		if (empty($file) || empty($name)) { return false; }
		//ext
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		//create directory 
		if (!file_exists('upload/ads')) { mkdir('upload/ads', 0777, true); }
		
		$allowed           = 'jpg,png,jpeg,gif';
		$new_string        = pathinfo($name, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
		$extension_allowed = explode(',', $allowed);
		$file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
		//extension allowed
		if (!in_array($file_extension, $extension_allowed)) { return false; }
		
		$dir = 'upload/ads';
		
		$filename   = $dir . '/ldrmx_' . date('d') . '_' . md5(time()) . '_ads_image.' . $ext;
		move_uploaded_file($file, $filename);     
	}


	
	// get plugins active/disable/all
	public static function get_plugins_list($limit = "", $last_id = false, $first_id = false, $sort_by = " p_order ASC ", $where = " p_active='1' "){
		global $sqlConnect;
		
		// BEGIN BUILDING AD QUERY 
		$sql = " SELECT * FROM plugins_ldrsoft ";
		
		// ADD WHERE CLAUSE, IF NECESSARY
		if( !empty($where) ){ $sql .= " WHERE {$where} "; }
		$sql .= ( $last_id ) ? " AND p_id < " . $last_id : '';
		$sql .= ( $first_id ) ? " AND p_id > " . $first_id : '';
		if( !empty($limit) ){ $sql .= " ORDER BY {$sort_by} ";  }
		if( !empty($limit) ){ $sql .= " LIMIT {$limit} "; }	
		
		// DETERMINE WHICH ADS SHOULD BE SHOWN
		$plugins_query = $sqlConnect->query($sql);
		$Count = $plugins_query->num_rows;
		$p_act = array();
		$p_array = array();
		if($Count>0){ 
			while( $row = $plugins_query->fetch_assoc() ){ 
				$p_act[] = $row['p_type'];
				$p_array[] = $row;			
			}
		}
		/*retur array*/
		return array('count' => $Count, 'plugins' => $p_array, 'active' => $p_act);
	} 

}
?>