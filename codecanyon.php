<?php

add_action('wp_dashboard_setup', 'codecanyon_add_dashboard_widgets');

add_action('admin_head', 'tickboxfun');

add_option("ABCCtop", '', 'yes');
add_option("ABCClatest", '', 'yes');
add_option("ABCCangry", '', 'yes');
add_option("ABCCdate", '', 'yes');

//class angrybytecodecanyon(){
function codecanyon_add_dashboard_widgets()
{
    wp_add_dashboard_widget('angrybytecodecanyon', 'Premium WordPress Plugins',
        'CodeCanyon');
}

// Hook into the 'wp_dashboard_setup' action to register our other functions


function tickboxfun()
{

    $mypath = plugin_dir_url(__file__);
    //  $mypath = get_option('siteurl') . '/wp-content/plugins' ;
    echo "<style type='text/css'>
.thumbnail{
border: 1px solid white;
margin: 5px 5px 5px 5px;
 
}
.gallery {
	list-style: none;
	margin: 0;
	padding: 0;
}

.gallery li {
	margin: 10px;
	padding: 0;
	float: left;
	position: relative;
	width: 80px;
	height: 80px;
}
.gallery img {
	background: #fff;
	border: solid 1px #ccc;
	padding: 4px;
}
.gallery span {
	width: 77px;
	height: 27px;
	display: block;
	position: absolute;
	top: -12px;
	left: 5px;
	background: url({$mypath}images/tape.png) no-repeat;
}
.gallery a {
	text-decoration: none;
}
.preview {
	list-style: none;
	margin: 0;
	padding: 0;
    width:100%;
    
}
.preview li {
	margin: 0px;
	padding: 0;
	
	position: relative;
	width: 100%;
	
}
.preview li {
	margin: 0px;
	padding: 0;
	
	position: relative;
	width: 97%;
	
}

.preview a {
	text-decoration: none;
	color: #666;
}
.preview a:hover {
	color: #000;
	text-decoration: underline;
}
.preview img {
	padding: 20px 0 0 21px;
}
.preview em {
	width: 97%;
	height: 50px;
	background: url({$mypath}images/picture-frame.png) no-repeat;
    background-size:100%;
	display: block;
	position: absolute;
	text-align: center;
	font: 100%/100% Georgia, 'Times New Roman', Times, serif;
	padding-top: 50%;
    align:center;
}
</style>";
    // }
}


function CodeCanyon()
{
    $updateme = date('d');
    $saved = get_option("ABCCdate");
    if ($updateme !== $saved)
    {
        update_option('ABCCdate', $updateme);
        $updateme = 1;

    } else
    {
        $updateme = 0;

    }


    if ($updateme == 1)
    {
        $read = getjsonfile1("http://marketplace.envato.com/api/edge/popular:codecanyon.json");
        update_option('ABCCtop', $read);

    } else
    {
        $read = get_option("ABCCtop");
    }


    $fields = json_decode($read);
    $fields = $fields->popular;
    $fields = $fields->items_last_week;

    $counter = 0;


    $mypath = plugin_dir_url(__file__);
    $plugs .= "<table align='center' valign='center'><tr  align='center' valign='center'><td COLSPAN=3 width='100%'><div style='width:100%'><ul class='preview'>
	<li><a href='#'><em id='titl'></em>
<img src='{$mypath}images/premiumplugings.png' id='preview' style='margin-top: 3%; margin-right: 7%; margin-bottom: 50px; margin-left: 0px; width: 77%;height:50%' /></a></li></ul><br /><script type='text/javascript'>
     function spanit(url,tit){
        document.getElementById('preview').src=url;
        document.getElementById('titl').innerHTML=tit;
        }
     </script><br /></ul><ul id='topul' class=\"gallery\" style='top:50px;position:relative'>";
    if (count($fields) == 0)
    {
        echo "Oops! we are unable to connect you to the lastest and coolest source of premium WordPress plugins: CodeCanyon. It should be back online soon, meanwhile, <a href='http://www.codecanyon.net?ref=AngryByte'>click here to go there</a>";
        return 0;
    }
    foreach ($fields as $field)
    {

        if (strpos($field->category, "wordpress") === 0)
        {
            $counter += 1;
            if ($counter >= 21)
                break;
            $plugs .= "
     
<li>
<a href=\"{$field->url}?ref=AngryByte\" title=\"{$field->item}\">
<span></span>
<img src='' class='galthumb' rel=\"{$field->thumbnail}\" alt=\"{$field->item}\" onmousemove=\"spanit('{$field->live_preview_url}','{$field->item}')\"/>
</a>
</li>";
        }

    }
    echo $plugs . "</ul>";
    $plugs = '';
    if ($updateme == 1)
    {
        $read = getjsonfile1("http://marketplace.envato.com/api/edge/new-files:codecanyon,wordpress.json");
        update_option('ABCClatest', $read);

    } else
    {
        $read = get_option("ABCClatest");
    }

    $fields = json_decode($read);

    $fields = $fields->{'new-files'};

    $counter = 0;
    echo "<ul id='latestulul' class=\"gallery\" style='top:0px;position:relative;display:none'>";
    foreach ($fields as $field)
    {
        $counter += 1;
        if ($counter >= 21)
            break;
        $plugs .= "
     
<li>
<a href=\"{$field->url}?ref=AngryByte\" title=\"{$field->item}\">
<span></span>
<img src='' class='galthumb' rel=\"{$field->thumbnail}\" alt=\"{$field->item}\" onmousemove=\"spanit('{$field->live_preview_url}','{$field->item}')\"/>
</a>
</li>";
        //   }

    }
    echo $plugs . "</ul>";


    $plugs = '';
    if ($updateme == 1)
    {
        $read = getjsonfile1("http://marketplace.envato.com/api/edge/new-files-from-user:angrybyte,codecanyon.json");
        update_option('ABCCangry', $read);

    } else
    {
        $read = get_option("ABCCangry");
    }

    $fields = json_decode($read);
    $fields = $fields->{'new-files-from-user'};

    $counter = 0;
    echo "<ul id='latestangrybyteul' class=\"gallery\" style='top:50px;position:relative;display:none'>";
    foreach ($fields as $field)
    {


        $counter += 1;
        if ($counter >= 21)
            break;
        $plugs .= "
     
<li>
<a href=\"{$field->url}?ref=AngryByte\" title=\"{$field->item}\">
<span></span>
<img src='' class='galthumb' rel=\"{$field->thumbnail}\" alt=\"{$field->item}\" onmousemove=\"spanit('{$field->live_preview_url}','{$field->item}')\"/>
</a>
</li>";
        //   }

    }
    echo $plugs . "</ul></td></tr><tr style='text-align:center'><td style='width:33%;height:50px'>";
    echo "</td><td style='width:33%'></td><td style='width:33%'></td></tr><tr style='text-align:center'><td style='width:33%'>";
    echo "<a id='latest' href='#' style='color:green'>Latest</a></td><td style='width:33%'><a id='top' style='color:red' href='#'>Top</a></td><td style='width:33%'><a id='angrybyte' href='#' style='color:green' >AngryByte</a></td></tr></table>
     <script>
jQuery(document).ready(function(){
    
    jQuery('.galthumb').each(function() {
        jQuery(this).attr('src', jQuery(this).attr('rel'));
        jQuery(this).attr('rel', '');
        }
        );
        
jQuery(\"#latest\").click(function(){
    jQuery('#latestulul').slideDown();
    jQuery('#latestangrybyteul').slideUp();
    jQuery('#preview, #titl').slideUp();
    jQuery('#topul').slideUp();
    event.preventDefault();
    jQuery(\"#latest\").css('color', 'red');
    jQuery(\"#top\").css('color', 'green');
    jQuery(\"#angrybyte\").css('color', 'green');
    });
    jQuery(\"#top\").click(function(){
    jQuery('#latestulul').slideUp();
    jQuery('#preview, #titl').slideDown();
    jQuery('#preview').attr('src', '{$mypath}images/premiumplugings.png');
    jQuery('#topul').slideDown();
    jQuery('#latestangrybyteul').slideUp();
    event.preventDefault();
    jQuery(\"#latest\").css('color', 'green');
    jQuery(\"#top\").css('color', 'red');
    jQuery(\"#angrybyte\").css('color', 'green');
    });
  jQuery(\"#angrybyte\").click(function(){
    jQuery('#latestangrybyteul').slideDown();
    jQuery('#preview, #titl').slideDown();
    jQuery('#preview').attr('src', '{$mypath}images/premiumplugings.png');
    //jQuery('#preview, #titl').slideUp();
    jQuery('#latestulul').slideUp();
    jQuery('#topul').slideUp();
      jQuery(\"#latest\").css('color', 'green');
    jQuery(\"#top\").css('color', 'green');
    jQuery(\"#angrybyte\").css('color', 'red');
    event.preventDefault();
    });
});
</script>
     ";

}


function getjsonfile1($url)
{
    $handle = @fopen("$url", "r");

    $buffer = '';
    if ($handle)
    {
        while (!feof($handle))
        {
            $buffer .= fgetss($handle, 5000);
        }

        fclose($handle);
        return $buffer;
    }
}
//}
?>