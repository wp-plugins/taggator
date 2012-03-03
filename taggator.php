<?php
/**
 * @package TagGator
 * @author PsMan
 * @version 1.3
 */
/*
Plugin Name: TagGator
Plugin URI: http://angrybyte.com/wordpress-plugins/taggator/
Description: TagGator is an auto tagging plugin, provide the plugin with some keywords, it will convert these keywords to tags and automatically adds this tag to all posts containing these keywords. 
Author: PsMan
Version: 1.3
Author URI: http://angrybyte.com
*/

add_action('admin_menu', 'taggatorfreemenu');
//add_action('admin_menu', 'taggatorfreemenu');
add_option("taggatorcs", '1', 'Case sensitivity for taggator?', 'yes');
add_option("taggatormhw", '1', 'Match whole words?', 'yes');
add_option("taggatortags", '', 'Taggator tags', 'yes');



function taggatorfreemenu()
{

    add_options_page('TagGator Auto-Tagger', 'TagGator', 8, __file__,'taggatorfree_plugin_options');
}
function counttagsfree()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "posts";
    $pfx = $wpdb->prefix;
    $wpdb->query("update {$pfx}term_taxonomy set {$pfx}term_taxonomy.count= (select count(object_id) from {$pfx}term_relationships where {$pfx}term_relationships.term_taxonomy_id = {$pfx}term_taxonomy.term_taxonomy_id)");

}

function taggatorfree_plugin_options()
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
        $tagz=str_ireplace("\n",",",$tagz);
        $tagz = explode(',', $tagz);
        global $post;
        global $wpdb;
        $table_name = $wpdb->prefix . "posts";
        $pfx = $wpdb->prefix;
        //$myposts = get_posts('numberposts=-1'); Solve memory issues
         $myposts = $wpdb->get_col("SELECT id FROM `{$pfx}posts` WHERE `post_status` = 'publish'");
        $nofmatchs = 0;
        foreach ($tagz as $atag) {
            //echo "tag";
            foreach ($myposts as $post) {
                //	echo "1";

                $ido = $post;
                $zepost = get_post($post);
                $aa =  " " . $zepost->post_content;


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


            $qry = "select `term_id` from " . $pfx . "terms where name = '$atag' limit 1;";
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
        counttagsfree();
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
			<div id="icon-edit" class="icon32"></div><h2>TagGator</h2>
	
<p style="color:red;">$txtmat</p> 

EOST;
    
    echo "<table><tr><td width='80%'>";
    echo freetaggatorboxer("Tag list","<Form method ='post' action='$serv' ><textarea  name='xx'  wrap='soft' cols='80' rows='10'>$oldtags</textarea><br />Separate tags with commas, TagGator will scan for these tags in your post contents then, create and apply the tag if it's found. <br /> <input type='checkbox' title = 'Case Sensetive?' name='cst' $chkd />Case Sensetive? <br /> <input type='checkbox' name='mhw' $chkd2 />Match whole words only? <br /><input type='checkbox' disabled='disabled' title = 'Scan titles?' name='taggatortagtitle' $chkdtit />Scan post titles for keywords? (Pro Feature) <br /><input disabled='disabled' type='checkbox' title = 'Auto Tag ?' name='autotagg' disabled='disabled'  />Auto tag new posts? (Pro Feature)<br /><input type='submit' class='button-primary widget-control-save' value='Save & apply tags now' style='width:250px'> </form>");

echo freetaggatorboxer("TagGator Pro","<a href = 'http://codecanyon.net/item/taggator-pro-wordpress-auto-tagging-plugin/1725033?ref=AngryByte' ><img width='100%' src='http://dl.dropbox.com/u/7048593/taggatorinline.png' /></a><br /> The Pro version of TagGator is now available. Get it now to get the following features: <br /><ul>
<li><b>Auto Tagging:</b> Your posts are tagged automatically when you create posts.</li>
<li><b>Autoblog friendly:</b> Taggator works with most autoblogging solutions, tags are created automatically when the posts are generated.</li>
<li><b>Multiple keywords for one Tag:</b> Pro version allows you to set multiple keywords for a single Tag!.</li>
<li><b>Set & Forget:</b> You only need to configure TagGator once. once done, you won�t have to worry about tags anymore</li>

</ul><br /><a href = 'http://codecanyon.net/item/taggator-pro-wordpress-auto-tagging-plugin/1725033?ref=AngryByte' >Buy TagGator Pro Now, and never worry about tagging a post ever again</a>");
echo "</td><td valign='top'>";
echo freetaggatorboxer('Help',"Enter your tags separated by commas.<br /><br /> Your tags should be relevant to the area of interest of your website. <br /><br /> For example if your website is about technology, you may use tags like:<br /><br /> <b>windows,linux,android,iphone</b> <br /></br>TagGator will then search your posts for these keywords and tag the posts contains them.<br/><br />TagGator Pro, allows you to scan for more than one keyword per tag, and also it can automatically tag posts when you create them.");
echo "</td></tr></table>";


}
function freetaggatorboxer($tit, $cont)
{
    $out = <<< EOBOX
<div class="metabox-holder" />
<div  class="postbox gdrgrid frontleft" style='width:100%'>
<h3 class="hndle">
<span>$tit</span>
</h3>
<div class="inside">
<div class="table">
<table>
<tbody>
<tr class="first">
<td class="first b">$cont </td></tr></tbody></table></div></div></div>
EOBOX;
    return $out;
}

?>