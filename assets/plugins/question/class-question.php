<?php 
/* Class Question
 * Plugins for Wowonder
 * @author LdrMx - Pp Galvan
 */ 
 
class Question{
     /*@question list return array
     *$limit    // array list 
	 *$last_id  // for show news
	 *$first_id // for show olds
	 *$sort_by  // Orden by
	 *$where    // add more variables
     */
	public static function get_question_lists($limit = "", $last_id = false, $first_id = false, $sort_by = "", $where = "", $depth = ""){
 global $sqlConnect;
      $question_sql = "";
	  
	  // BEGIN BUILDING AD QUERY 
	  $question_sql .= " SELECT * FROM posts_questions ";
	  
	  // ADD WHERE CLAUSE, IF NECESSARY
	  if( !empty($where) ){ $question_sql .= " WHERE {$where} "; }
	  $question_sql .= ( $last_id ) ? " AND question_id < " . $last_id : '';
      $question_sql .= ( $first_id ) ? " AND question_id > " . $first_id : '';
	  if( !empty($limit) ){ $question_sql .= " ORDER BY {$sort_by} ";  }
	  if( !empty($limit) ){ $question_sql .= " LIMIT {$limit} "; }	
	  
	  // DETERMINE WHICH ADS SHOULD BE SHOWN
	  $question_query = $sqlConnect->query($question_sql);
	  $Count = $question_query->num_rows;
      $question_array = array();
      if($Count>0){ 
	    while( $question_info = $question_query->fetch_array(MYSQLI_ASSOC) ){ 
         $question_array[] = $question_info;			
	    }
	  }
    
	/*retur array*/
	return array('count' => $Count, 'question' => $question_array);
 } 
 /*end list*/
 
 /*delete all question
  *return true 
  */ 
 public static function admin_question_delete($question_id){
    global $sqlConnect;
	if(!$question_id){ return false; }
	  $get_post = $sqlConnect->query("SELECT post_id FROM posts_questions WHERE question_id='{$question_id}' LIMIT 1");

	  if($get_post->num_rows > 0){
	      $post = $get_post->fetch_assoc();
			$sqlConnect->query("DELETE FROM posts_questions WHERE question_id='{$question_id}' ");
			$sqlConnect->query("DELETE FROM posts_questions_options WHERE question_id='{$question_id}' ");
	        $sqlConnect->query("DELETE FROM questions_options_votes WHERE question_id='{$question_id}' ");
			Delete_Post($post['post_id']);
	  }
	return true;
    }
}
?>