<?php
/**
 * @package TagGator
 * @author PsMan
 * @version 0.6
 */
/*
Plugin Name: TagGator
Plugin URI: http://angrybyte.com/wordpress-plugins/taggator/
Description: TagGator is an auto tagging plugin, provide the plugin with some keywords, it will convert these keywords to tags and automatically adds this tag to all posts containing these keywords. v 0.3 Fixed some bugs, & made compatible with wordpress 3 <br /> v 0.4 Based on the recommendations of "Robert" on angrybyte.com Now we are able to save tags you used once for using over on over, also Casesensetivity is now optional. <br />
V 0.5 Auto tagging for new posts, small modifications here and there..<br />V 0.6 Now you are able to match whole words only!! , Fixed a bug where the first word in a post is not detected.
Author: PsMan
Version: 0.6
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
        $aa = "  " . $zepost->post_content;

if ($mhw && ((!$cst && (preg_match("/\b$atag\b/i", strtolower($aa)))) || ($cst && (preg_match("/\b$atag\b/", ($aa)))))|| !$mhw && (!$cst && (strpos(strtolower($aa), strtolower($atag)))) || ($cst && (strpos
                    (($aa), ($atag))))) {
       // if ((!$cst && (strpos(strtolower($aa), strtolower($atag)))) || ($cst && (strpos
        //    (($aa), ($atag))))) {
            //	echo "<b> gotone </b>";
            //	$news=str_replace($atag,"<b>" . $atag . "</b>",$aa);

            $pst["name"] = $atag;
            $slugg = strtolower(str_replace(" ", "-", $atag));
            $pst["slug"] = $slugg;
            wp_insert_term($atag, 'post_tag', $pst);


            $qry = "
select 
`term_id` from " . $pfx . "terms where name = '$atag' limit 1;
";
            $tagid = $wpdb->get_var($qry);


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
function taggatormenu()
{

    add_options_page('TagGator Auto-Tagger', 'TagGator', 8, __file__,
        'taggator_plugin_options');
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
        foreach ($tagz as $atag) {
            //echo "tag";
            foreach ($myposts as $post) {
                //	echo "1";

                $ido = $post->ID;
                $zepost = get_post(the_ID());
                $aa = "  " . $zepost->post_content;


                if ($mhw && ((!$cst && (preg_match("/\b$atag\b/i", strtolower($aa)))) || ($cst && (preg_match("/\b$atag\b/", ($aa)))))|| !$mhw && (!$cst && (strpos(strtolower($aa), strtolower($atag)))) || ($cst && (strpos
                    (($aa), ($atag))))) {
                    //	echo "<b> gotone </b>";
                    //	$news=str_replace($atag,"<b>" . $atag . "</b>",$aa);

                    $pst["name"] = $atag;
                    $slugg = strtolower(str_replace(" ", "-", $atag));
                    $pst["slug"] = $slugg;
                    wp_insert_term($atag, 'post_tag', $pst);


                    $qry = "
select 
`term_id` from " . $pfx . "terms where name = '$atag' limit 1;
";
                    $tagid = $wpdb->get_var($qry);


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
    }
    $serv = str_replace('%7E', '~', $_SERVER['REQUEST_URI']);
    echo <<< EOST
<h1>TagGator</h1>
<h2>TagGator will search for the keywords you write below through your posts and add these keywords as tags to the returned posts. For more info <a href="http://angrybyte.com/wordpress-plugins/taggator">visit TagGator's homepage</a><a href="http://angrybyte.com/wordpress-plugins/taggator"> http://angrybyte.com</a> <br /> If you find my work useful, <a href="http://angrybyte.com/donate" style="color:red;">please consider making a donation</a>, any amount is welcome and highly appreciated.<br /></h2>
<br> You can write multiple tags just separate them by commas ',' the search is case insensitive. Please do not use special characters, just letters and numbers.<br />


If you don't like the result, Manually remove the added tags from the tags page under "Posts"<br>



Tags:
EOST;
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
    $oldtags = get_option('taggatortags');
    echo "<Form method ='post' action='$serv'><textarea name='xx' cols='50' rows='10'>$oldtags</textarea><input type='submit' value='submit'> <br /> <input type='checkbox' name='cst' $chkd />Case Sensetive? <br /> <input type='checkbox' name='mhw' $chkd2 />Match whole words only? </form>";


}

?>