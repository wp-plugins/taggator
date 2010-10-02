<?php
/**
 * @package TagGator
 * @author PsMan
 * @version 0.4
 */
/*
Plugin Name: Taggegator
Plugin URI: http://angrybyte.com/wordpress-plugins/taggator/
Description: TagGator is an auto tagging plugin, provide the plugin with some keywords, it will convert these keywords to tags and automatically adds this tag to all posts containing these keywords. v 0.3 Fixed some bugs, & made compatible with wordpress 3 <br /> v 0.4 Based on the recommendations of "Robert" on angrybyte.com Now we are able to save tags you used once for using over on over, also Casesensetivity is now optional.
Author: PsMan
Version: 0.4
Author URI: http://angrybyte.com
*/

add_action('admin_menu', 'taggatormenu');
add_option("taggatorcs", '1', 'Case sensitivity for taggator?', 'yes'); 
//add_option("taggatortags", '1', 'Taggator tags', 'yes'); 

function taggatormenu() {

  add_options_page('TagGator Auto-Tagger', 'TagGator', 8, __FILE__, 'taggator_plugin_options');
}

function taggator_plugin_options() {
  echo '<div class="wrap">';

echo  $_POST["ftag"]. $_POST["sss"] ;
 $serv= "/wp-admin/options-general.php?page=taggator.php";
  
Echo $_POST["xx"];
if($_POST["xx"]){
	$cst=$_POST["cst"];
	update_option( 'taggatorcs', $cst );
	
	$tagz=$_POST["xx"];
	update_option( 'taggatortags', $tagz );
	$tagz=explode(',',$tagz);
	 global $post;
	  global $wpdb;
	  $table_name = $wpdb->prefix . "posts" ;
	  $pfx=$wpdb->prefix;
 $myposts = get_posts('numberposts=-1');
	foreach($tagz as $atag){
		//echo "tag";
		 foreach($myposts as $post){
		 //	echo "1";
		 	
		 	$ido=$post->ID;
		 	$zepost=get_post(the_ID());
		 	$aa= $zepost->post_content; 
		 	
	
			if((!$cst  &&(strpos(strtolower($aa),strtolower($atag))))||($cst  &&(strpos(($aa),($atag)))) ){
			//	echo "<b> gotone </b>";
			//	$news=str_replace($atag,"<b>" . $atag . "</b>",$aa);
				
				$pst["name"]=$atag;
				$slugg=strtolower(str_replace(" ","-",$atag));
				$pst["slug"]=$slugg;
				wp_insert_term($atag, 'post_tag', $pst);
				

 $qry= "
select 
`term_id` from ". $pfx . "terms where name = '$atag' limit 1;
";
$tagid= $wpdb->get_var($qry);


$qry= "SELECT term_taxonomy_id from ". $pfx . "term_taxonomy where term_id= $tagid limit 1;";


$taxid= $wpdb->get_var($qry);


$qry= "
INSERT INTO `" . $pfx . "term_relationships` (
`object_id` ,
`term_taxonomy_id` ,
`term_order` 
)
VALUES (
'$ido', '$taxid', '0'
);
";


 $wpdb->query($qry);
			}
		}
	}
} 
$serv=str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
echo <<<EOST
<h1>TagGator</h1>
<h2>TagGator will search for the keywords you write below through your posts and add these keywords as tags to the returned posts. For more info <a href="http://angrybyte.com/wordpress-plugins/taggator">visit TagGator's homepage</a><a href="http://angrybyte.com/wordpress-plugins/taggator"> http://angrybyte.com</a></h2>
<br> You can write multiple tags just separate them by commas ',' the search is case insensitive. Please do not use special characters, just letters and numbers.

If you don't like the result, Manually remove thwe added tags from the tags page under "Posts"<br>
Tags:
EOST;
if (get_option('taggatorcs')){
	$chkd="checked='checked'";
}else{
	$chkd='';
}
$oldtags=get_option('taggatortags');
echo "<Form method ='post' action='$serv'><input type='text' name='xx' value='$oldtags'><input type='submit' value='submit'> <br /> <input type='checkbox' name='cst' $chkd />Case Sensetive? </form>";
  
	
	


}

?>
