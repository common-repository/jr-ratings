<?php
/*
Plugin Name: JR Ratings
Plugin URI: http://www.jakeruston.co.uk/2009/12/wordpress-plugin-jr-ratings/
Description: This plugin allows you to enable a rating form where users can rate a particular post!
Version: 1.3.7
Author: Jake Ruston
Author URI: http://www.jakeruston.co.uk
*/

/*  Copyright 2010 Jake Ruston - the.escapist22@gmail.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$pluginname="ratings";

// Hook for adding admin menus
add_action('admin_menu', 'jr_ratings_add_pages');

if (get_option("mt_ratings_plugin_support")=="Yes" || get_option("mt_ratings_plugin_support")=="") {
add_action('wp_footer', 'ratings_plugin_support');
}

add_filter('the_content', 'show_ratings');

// action function for above hook
function jr_ratings_add_pages() {
    add_options_page('JR Ratings', 'JR Ratings', 'administrator', 'jr_ratings', 'jr_ratings_options_page');
}

if (!function_exists("_iscurlinstalled")) {
function _iscurlinstalled() {
if (in_array ('curl', get_loaded_extensions())) {
return true;
} else {
return false;
}
}
}

if (!function_exists("jr_show_notices")) {
function jr_show_notices() {
echo "<div id='warning' class='updated fade'><b>Ouch! You currently do not have cURL enabled on your server. This will affect the operations of your plugins.</b></div>";
}
}

if (!_iscurlinstalled()) {
add_action("admin_notices", "jr_show_notices");

} else {
if (!defined("ch"))
{
function setupch()
{
$ch = curl_init();
$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
return($ch);
}
define("ch", setupch());
}

if (!function_exists("curl_get_contents")) {
function curl_get_contents($url)
{
$c = curl_setopt(ch, CURLOPT_URL, $url);
return(curl_exec(ch));
}
}
}

if (!function_exists("jr_ratings_refresh")) {
function jr_ratings_refresh() {
update_option("jr_submitted_ratings", "0");
}
}

register_activation_hook(__FILE__,'ratings_choice');

function ratings_choice () {
if (get_option("jr_ratings_links_choice")=="") {

if (_iscurlinstalled()) {
$pname="jr_ratings";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_ratings", "1");
wp_schedule_single_event(time()+172800, 'jr_ratings_refresh'); 
} else {
$content = "Powered by <a href='http://arcade.xeromi.com'>Free Online Games</a> and <a href='http://directory.xeromi.com'>General Web Directory</a>.";
}

if ($content!="") {
$content=utf8_encode($content);
update_option("jr_ratings_links_choice", $content);
}
}

if (get_option("jr_ratings_link_personal")=="") {
$content = curl_get_contents("http://www.jakeruston.co.uk/p_pluginslink4.php");

update_option("jr_ratings_link_personal", $content);
}
}

// jr_ratings_options_page() displays the page content for the Test Options submenu
function jr_ratings_options_page() {

    // variables for the field and option names 
    $opt_name_6 = 'mt_ratings_plugin_support';
    $hidden_field_name = 'mt_nofollow_submit_hidden';
    $data_field_name_6 = 'mt_ratings_plugin_support';

    // Read in existing option value from database
    $opt_val_6 = get_option( $opt_name_6 );
	
if (!$_POST['feedback']=='') {
$my_email1="the.escapist22@gmail.com";
$plugin_name="JR Ratings";
$blog_url_feedback=get_bloginfo('url');
$user_email=$_POST['email'];
$user_email=stripslashes($user_email);
$subject=$_POST['subject'];
$subject=stripslashes($subject);
$name=$_POST['name'];
$name=stripslashes($name);
$response=$_POST['response'];
$response=stripslashes($response);
$category=$_POST['category'];
$category=stripslashes($category);
if ($response=="Yes") {
$response="REQUIRED: ";
}
$feedback_feedback=$_POST['feedback'];
$feedback_feedback=stripslashes($feedback_feedback);
if ($user_email=="") {
$headers1 = "From: feedback@jakeruston.co.uk";
} else {
$headers1 = "From: $user_email";
}
$emailsubject1=$response.$plugin_name." - ".$category." - ".$subject;
$emailmessage1="Blog: $blog_url_feedback\n\nUser Name: $name\n\nUser E-Mail: $user_email\n\nMessage: $feedback_feedback";
mail($my_email1,$emailsubject1,$emailmessage1,$headers1);
?>

<div class="updated"><p><strong><?php _e('Feedback Sent!', 'mt_trans_domain' ); ?></strong></p></div>

<?php
}

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val_6 = $_POST[$data_field_name_6];

        // Save the posted value in the database
        update_option( $opt_name_6, $opt_val_6 );  

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Settings saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'JR Ratings Plugin Options', 'mt_trans_domain' ) . "</h2>";
$blog_url_feedback=get_bloginfo('url');
	$donated=curl_get_contents("http://www.jakeruston.co.uk/p-donation/index.php?url=".$blog_url_feedback);
	if ($donated=="1") {
	?>
		<div class="updated"><p><strong><?php _e('Thank you for donating!', 'mt_trans_domain' ); ?></strong></p></div>
	<?php
	} else {
	if ($_POST['mtdonationjr']!="") {
	update_option("mtdonationjr", "444");
	}
	
	if (get_option("mtdonationjr")=="") {
	?>
	<div class="updated"><p><strong><?php _e('Please consider donating to help support the development of my plugins!', 'mt_trans_domain' ); ?></strong><br /><br /><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ULRRFEPGZ6PSJ">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form></p><br /><form action="" method="post"><input type="hidden" name="mtdonationjr" value="444" /><input type="submit" value="Don't Show This Again" /></form></div>
<?php
}
}

    // options form
    
    $change5 = get_option("mt_nofollow_plugin_support");

if ($change5=="Yes" || $change5=="") {
$change5="checked";
$change51="";
} else {
$change5="";
$change51="checked";
}

    ?>
	<iframe src="http://www.jakeruston.co.uk/plugins/index.php" width="100%" height="20%">iframe support is required to see this.</iframe>
	<h3>Post Ratings</h3>
<?php	
 $lastposts = get_posts('numberposts=-1');
 foreach($lastposts as $post) :
    $id=$post->ID;
	$rating=ratings_action($id);
	
	if ($rating==0) {
	$rating="Not Yet Rated";
	} else {
	$rating=$rating."/5";
	}
	echo $post->post_title . " - " . $rating . "<br />";
	endforeach;
 ?>
<hr />
<h3>Settings</h3>
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Show Plugin Support?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="Yes" <?php echo $change5; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_6; ?>" value="No" <?php echo $change51; ?> id="Please do not disable plugin support - This is the only thing I get from creating this free plugin!" onClick="alert(id)">No
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>

<script type="text/javascript">
function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="")
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validateEmail(ctrl){

var strMail = ctrl.value
        var regMail =  /^\w+([-.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (regMail.test(strMail))
        {
            return true;
        }
        else
        {

            return false;

        }
					
	}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(subject,"Subject must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(email,"E-Mail must be filled out!")==false)
  {email.focus();return false;}
  if (validate_required(feedback,"Feedback must be filled out!")==false)
  {email.focus();return false;}
  if (validateEmail(email)==false)
  {
  alert("E-Mail Address not valid!");
  email.focus();
  return false;
  }
 }
}
</script>
<h3>Submit Feedback about my Plugin!</h3>
<p><b>Note: Only send feedback in english, I cannot understand other languages!</b><br /><b>Please do not send spam messages!</b></p>
<form name="form2" method="post" action="" onsubmit="return validate_form(this)">
<p><?php _e("Your Name:", 'mt_trans_domain' ); ?> 
<input type="text" name="name" /></p>
<p><?php _e("E-Mail Address (Required):", 'mt_trans_domain' ); ?> 
<input type="text" name="email" /></p>
<p><?php _e("Message Category:", 'mt_trans_domain'); ?>
<select name="category">
<option value="General">General</option>
<option value="Feedback">Feedback</option>
<option value="Bug Report">Bug Report</option>
<option value="Feature Request">Feature Request</option>
<option value="Other">Other</option>
</select>
<p><?php _e("Message Subject (Required):", 'mt_trans_domain' ); ?>
<input type="text" name="subject" /></p>
<input type="checkbox" name="response" value="Yes" /> I want e-mailing back about this feedback</p>
<p><?php _e("Message Comment (Required):", 'mt_trans_domain' ); ?> 
<textarea name="feedback"></textarea>
</p>
<p class="submit">
<input type="submit" name="Send" value="<?php _e('Send', 'mt_trans_domain' ); ?>" />
</p><hr /></form>
</div>
<?php
}

if (get_option("jr_ratings_links_choice")=="") {
ratings_choice();
}

function ratings_action( $post_id, $action = 'get', $rating = '' ) {
  
  //Let's make a switch to handle the three cases of 'Action'
  switch ($action) {
    case 'update' :
      
      if( $rating != '' ) {
	  $value1=get_post_meta($post_id, 'jrratings-no', false);
	  $value2=get_post_meta($post_id, 'jrratings-total', false);
	  $value1=$value1[0];
	  $value2=$value2[0];
	  $value3=$value2+$rating;
	  $value4=$value1+1;
	  
	  if ($value1!="" && $value2!="") {
        update_post_meta( $post_id, 'jrratings-total', $value3 );
		update_post_meta( $post_id, 'jrratings-no', $value4 );
        return true;
        } else {
		add_post_meta($post_id, 'jrratings-no', '1', true);
		add_post_meta($post_id, 'jrratings-total', $rating, true);
		return true;
		}
		} else { return false; }
      
    case 'get' :

      $total = get_post_meta( $post_id, 'jrratings-total', false );
	  $total2 = get_post_meta( $post_id, 'jrratings-no', false );
	  $total = $total[0];
	  $total2 = $total2[0];
	  
	  $return = "1: $total 2: $total2";

if ($total!="" && $total2!="") {      
$return=round($total/$total2);
} else {
$return=0;
}
      
      return $return;
	  break;
    default :
      return false;
    break;
  } 
}

function show_ratings($comment2) {
global $single, $feed, $post;

if ($single) {
if ($_POST['rating'] != "") {
$value=$_POST['rating'];
global $wp_query;
$id = $wp_query->post->ID;

$var=ratings_action($id, 'update', $value);

$val=ratings_action($id, 'get', '');

if ($val==1) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/1star.png' alt='1 star' title='1 star' />";
} else if ($val==2) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/2stars.png' alt='2 stars' title='2 stars' />";
} else if ($val==3) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/3stars.png' alt='3 stars' title='3 stars' />";
} else if ($val==4) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/4stars.png' alt='4 stars' title='4 stars' />";
} else if ($val==5) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/5stars.png' alt='5 stars' title='5 stars' />";
} else if ($val==0) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/5stars.png' alt='5 stars' title='5 stars' />";
}

$comment2 .= "<p><center>Rating: ".$val."</center></p>";

} else {

global $wp_query;
$id = $wp_query->post->ID;

$val=ratings_action($id, 'get', '');

if ($val==1) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/1star.png' alt='1 star' title='1 star' />";
} else if ($val==2) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/2stars.png' alt='2 stars' title='2 stars' />";
} else if ($val==3) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/3stars.png' alt='3 stars' title='3 stars' />";
} else if ($val==4) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/4stars.png' alt='4 stars' title='4 stars' />";
} else if ($val==5) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/5stars.png' alt='5 stars' title='5 stars' />";
} else if ($val==0) {
$val="<img src='".get_bloginfo('siteurl')."/wp-content/plugins/jr-ratings/images/5stars.png' alt='5 stars' title='5 stars' />";
}

$comment2 .= "<p><center>Rating: ".$val."</center></p>";

$comment2 .= "<p><center><form action='' method='post'><select name='rating'><option value='5'>*****</option><option value='4'>****</option><option value='3'>***</option><option value='2'>**</option><option value='1'>*</option></select><input type='submit' value='Vote!' /></form></center></p>";
}
}
return $comment2;
}


function ratings_plugin_support() {
$linkper=utf8_decode(get_option('jr_ratings_link_personal'));

if (get_option("jr_ratings_link_newcheck")=="") {
$pieces=explode("</a>", get_option('jr_ratings_links_choice'));
$pieces[0]=str_replace(" ", "%20", $pieces[0]);
$pieces[0]=curl_get_contents("http://www.jakeruston.co.uk/newcheck.php?q=".$pieces[0]."");
$new=implode("</a>", $pieces);
update_option("jr_ratings_links_choice", $new);
update_option("jr_ratings_link_newcheck", "444");
}

if (get_option("jr_submitted_ratings")=="0") {
$pname="jr_ratings";
$url=get_bloginfo('url');
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname);
update_option("jr_submitted_ratings", "1");
update_option("jr_ratings_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_ratings_refresh'); 
} else if (get_option("jr_submitted_ratings")=="") {
$pname="jr_ratings";
$url=get_bloginfo('url');
$current=get_option("jr_ratings_links_choice");
$content = curl_get_contents("http://www.jakeruston.co.uk/plugins/links.php?url=".$url."&pname=".$pname."&override=".$current);
update_option("jr_submitted_ratings", "1");
update_option("jr_ratings_links_choice", $content);

wp_schedule_single_event(time()+172800, 'jr_ratings_refresh'); 
}

echo "<p style='font-size:x-small'>Ratings Plugin created by ".$linkper." - ".stripslashes(get_option('jr_ratings_links_choice'))."</p>";
}

?>