<?php
/**
 * @package TagGator
 * @author PsMan
 * @version 1.21
 */
/*
Plugin Name: TagGator
Plugin URI: http://angrybyte.com/wordpress-plugins/taggator/
Description: TagGator is an auto tagging plugin, provide the plugin with some keywords, it will convert these keywords to tags and automatically adds this tag to all posts containing these keywords. 
Author: PsMan
Version: 1.21
Author URI: http://angrybyte.com
*/

add_action('admin_menu', 'taggatormenu');
//add_action('admin_menu', 'taggatormenu');
add_action('publish_post', 'autotag');
add_option("taggatorcs", '1', 'Case sensitivity for taggator?', 'yes');
add_option("taggatormhw", '1', 'Match whole words?', 'yes');
//add_option("taggatortags", '1', 'Taggator tags', 'yes');


function autotag($post_ID)
{

    //this is to autotag new created pages
    $serv = "/wp-admin/options-general.php?page=taggator.php";
    $tagz = get_option('taggatortags');
    $cst = get_option('taggatorcs');
    $mhw = get_option('taggatormhw');
    $tagz = explode(',', $tagz);
    global $post;
    global $wpdb;
    $table_name = $wpdb->prefix . "posts";
    $pfx = $wpdb->prefix;
    foreach ($tagz as $atag) {
        $ido = $post_ID;
        $zepost = get_post($post_ID);
        $aa = "  " . $zepost->post_title . " " . $zepost->post_content;

        if ($mhw && ((!$cst && (preg_match("/\b$atag\b/iu", strtolower($aa)))) || ($cst &&
            (preg_match("/\b$atag\b/u", ($aa))))) || !$mhw && (!$cst && (strpos(strtolower($aa),
            strtolower($atag))) || ($cst && (strpos(($aa), ($atag)))))) {
            // if ((!$cst && (strpos(strtolower($aa), strtolower($atag)))) || ($cst && (strpos
            //    (($aa), ($atag))))) {
            //	echo "<b> gotone </b>";
            //	$news=str_replace($atag,"<b>" . $atag . "</b>",$aa);
// added to fix the repeating tag bugs
$nma=$atag;
$already=$wpdb->get_var("select term_id from {$pfx}terms where lcase(name) = lcase('$nma') limit 1");
if(!$already){
            $pst["name"] = $atag;
            $slugg = strtolower(str_replace(" ", "-", $atag));
            $pst["slug"] = $slugg;
            wp_insert_term($atag, 'post_tag', $pst);


            $qry = "
select 
`term_id` from " . $pfx . "terms where name = '$atag' limit 1;
";
 $tagid = $wpdb->get_var($qry);
}else{
$tagid	=$already;
}

           


            $qry = "SELECT term_taxonomy_id from " . $pfx . "term_taxonomy where term_id= $tagid limit 1;";


            $taxid = $wpdb->get_var($qry);


            $qry = "
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
    counttags();
}
function taggatormenu()
{

    add_options_page('TagGator Auto-Tagger', 'TagGator', 8, __file__,
        'taggator_plugin_options');
}
function counttags()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "posts";
    $pfx = $wpdb->prefix;
    $wpdb->query("update {$pfx}term_taxonomy set {$pfx}term_taxonomy.count= (select count(object_id) from {$pfx}term_relationships where {$pfx}term_relationships.term_taxonomy_id = {$pfx}term_taxonomy.term_taxonomy_id)");

}

function taggator_plugin_options()
{
    $mhw = get_option('taggatormhw');
    echo '<div class="wrap">';

    echo $_POST["ftag"] . $_POST["sss"];
    $serv = "/wp-admin/options-general.php?page=taggator.php";

    //echo $_POST["xx"];
    if ($_POST["xx"]) {
        $cst = $_POST["cst"];
        $mhw = $_POST["mhw"];
        update_option('taggatorcs', $cst);
        update_option('taggatormhw', $mhw);
        $tagz = $_POST["xx"];
        update_option('taggatortags', $tagz);
        $tagz = explode(',', $tagz);
        global $post;
        global $wpdb;
        $table_name = $wpdb->prefix . "posts";
        $pfx = $wpdb->prefix;
        $myposts = get_posts('numberposts=-1');
        $nofmatchs = 0;
        foreach ($tagz as $atag) {
            //echo "tag";
            foreach ($myposts as $post) {
                //	echo "1";

                $ido = $post->ID;
                $zepost = get_post(the_ID());
                $aa = "  " . $zepost->post_title . " " . $zepost->post_content;


                if ($mhw && ((!$cst && (preg_match("/\b$atag\b/iu", strtolower($aa)))) || ($cst &&
                    (preg_match("/\b$atag\b/u", ($aa))))) || !$mhw && (!$cst && (strpos(strtolower($aa),
                    strtolower($atag))) || ($cst && (strpos(($aa), ($atag)))))) {
                    //	echo "<b> gotone </b>";
                    //	$news=str_replace($atag,"<b>" . $atag . "</b>",$aa);
                    $nofmatchs = $nofmatchs + 1;
                    $pst["name"] = $atag;
                    $slugg = strtolower(str_replace(" ", "-", $atag));
                    $pst["slug"] = $slugg;
                    $nma=$atag;
$already=$wpdb->get_var("select term_id from {$pfx}terms where lcase(name) = lcase('$nma') limit 1");
if(!$already){
            $pst["name"] = $atag;
            $slugg = strtolower(str_replace(" ", "-", $atag));
            $pst["slug"] = $slugg;
            wp_insert_term($atag, 'post_tag', $pst);


            $qry = "
select 
`term_id` from " . $pfx . "terms where name = '$atag' limit 1;
";
 $tagid = $wpdb->get_var($qry);
}else{
$tagid	=$already;

}

                    $qry = "SELECT term_taxonomy_id from " . $pfx . "term_taxonomy where term_id= $tagid limit 1;";


                    $taxid = $wpdb->get_var($qry);


                    $qry = "
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
        $txtmat = "$nofmatchs tags were applied!";
        counttags();
    }
    $serv = str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
    $plugurl = plugin_dir_url(__file__);
    if (get_option('taggatorcs')) {
        $chkd = "checked='checked'";
    } else {
        $chkd = '';
    }
    if (get_option('taggatormhw')) {
        $chkd2 = "checked='checked'";
    } else {
        $chkd2 = '';
    }
    $oldtags =stripslashes(get_option('taggatortags')) ;
    echo <<< EOST
    <script type="text/javascript" src="{$plugurl}js/jquery.js"></script>
<script type="text/javascript" src="{$plugurl}js/thickbox.js"></script>
  <link rel="stylesheet" type="text/css" media="screen" href="{$plugurl}css/default.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="{$plugurl}css/thickbox.css" />
    
			
				<div id="contentz">
			<div class="three_columns">
				<h2>Rate Me!</h2>
				<img src="{$plugurl}images/document_icon.png" alt="Heart" />
				<p>Can you please take a second to rate this plugin at <a href="http://wordpress.org/extend/plugins/taggator/" target="_blank" onMouseOver="window.status='Rate me please!!'; return true" onMouseOut="window.status=' '">wordpress.org</a>.</p>
			</div>
			<div class="three_columns">
				<h2>Stay Updated</h2>
				<img src="{$plugurl}images/rss_icon.png" alt="Heart" />
				<p>Subscribe to the Angrybyte.com Feeds for up to date info and computers, games, and wordpress plugins.<a href="http://feeds2.feedburner.com/angrybyte/CyPb" target="_blank">Feeds</a></p>
			</div>
			<div class="three_columns">
				<h2>If you love this</h2>
				<img src="{$plugurl}images/heart_icon.png" alt="Heart" />
				<p> let's keep the internet a free place to take and to give. Please consider making a small donation. <a href="http://angrybyte.com/donate" target="_blank">Donate.</a> </p>
			</div><br />	</div><div style='clear:both;float:left'>
			<div id="icon-edit" class="icon32"></div><h2>TagGator</h2>
		<div class='right'>	<Form method ='post' action='$serv' ><textarea name='xx' cols='50' rows='10'>$oldtags</textarea><br /><input type='submit' value='submit' style='width:250px'> <br /> <input type='checkbox' title = 'Case Sensetive?' name='cst' $chkd />Case Sensetive? <br /> <input type='checkbox' name='mhw' $chkd2 />Match whole words only? </form><br /><br />	</div>		
			<p style='font-size: 100%; width:860px'>TagGator will search for the keywords you write below through your posts and add these keywords as tags to the returned posts. For more info <a href="http://angrybyte.com/wordpress-plugins/taggator">Click Here.</a>
<br /><br /> You can write multiple tags just separate them by commas ','. Please do not use special characters, just letters and numbers.If you don't like the result, Manually remove the added tags from the tags page under 'Posts'<br /><br /></p>
		



<p style="color:red;">$txtmat</p> 

EOST;
    
    echo "  ";


}
//function plugin_dir_path( $file ) {
//	    return trailingslashit( dirname( $file ) );
//		}


?>