<?php
/*
Plugin Name:  Better Captcha
Description:  Stop bad bots from attacking your forms using hCaptcha or simple maths questions
Version:      2.2
Author:       Better Security
Author URI:   https://bettersecurity.co
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.en.html
Text Domain:  better-capt-text
Domain Path:  /languages
*/

//prevent direct access
defined('ABSPATH') or die('Forbidden');

/*
----------------------------- Settings ------------------------------
*/

//add settings page
function better_capt_menus() {
	add_options_page(__('Better Captcha','better-capt-text'), __('Better Captcha','better-capt-text'), 'manage_options', 'better-captcha-settings', 'better_capt_show_settings');
}

//add the settings
function better_capt_settings() {
  register_setting('better-captcha','better-captcha-settings');

  add_settings_section('better-captcha-general', __('Settings', 'better-capt-text'), 'better_capt_general', 'better-captcha');
  add_settings_field('better-captcha-general-mode', __('Captcha Mode', 'better-capt-text'), 'better_capt_general_mode', 'better-captcha', 'better-captcha-general');

	add_settings_section('better-captcha-account', __('hCaptcha Account', 'better-capt-text'), 'better_capt_account', 'better-captcha');
	add_settings_field('better-captcha-site-key', __('hCaptcha Site Key', 'better-capt-text'), 'better_capt_site_key', 'better-captcha', 'better-captcha-account');
	add_settings_field('better-captcha-secret-key', __('hCaptcha Secret Key', 'better-capt-text'), 'better_capt_secret_key', 'better-captcha', 'better-captcha-account');
	add_settings_field('better-captcha-theme', __('hCaptcha Theme', 'better-capt-text'), 'better_capt_theme', 'better-captcha', 'better-captcha-account');
  add_settings_field('better-captcha-size', __('hCaptcha Size', 'better-capt-text'), 'better_capt_size', 'better-captcha', 'better-captcha-account');

  add_settings_section('better-captcha-maths', __('Simple Maths Questions', 'better-capt-text'), 'better_capt_maths', 'better-captcha');
  add_settings_field('better-captcha-maths-words', __('Numbers or Words', 'better-capt-text'), 'better_capt_maths_words', 'better-captcha', 'better-captcha-maths');
  add_settings_field('better-captcha-maths-complex', __('Complexity', 'better-capt-text'), 'better_capt_maths_complex', 'better-captcha', 'better-captcha-maths');
  add_settings_field('better-captcha-maths-timeout', __('Timeout (seconds)', 'better-capt-text'), 'better_capt_maths_timeout', 'better-captcha', 'better-captcha-maths');

  add_settings_section('better-captcha-places', __('Show Captcha', 'better-capt-text'), 'better_capt_places', 'better-captcha');
  add_settings_field('better-captcha-place-login', __('Login Form', 'better-capt-text'), 'better_capt_place_login', 'better-captcha', 'better-captcha-places');
  add_settings_field('better-captcha-place-lastp', __('Lost Password Form', 'better-capt-text'), 'better_capt_place_lostp', 'better-captcha', 'better-captcha-places');
  add_settings_field('better-captcha-place-regis', __('Register Form', 'better-capt-text'), 'better_capt_place_regis', 'better-captcha', 'better-captcha-places');
  add_settings_field('better-captcha-place-comme', __('Comment Form', 'better-capt-text'), 'better_capt_place_comme', 'better-captcha', 'better-captcha-places');
  // TODO: add more
}

//allow the settings to be stored
add_filter('whitelist_options', function($whitelist_options) {
  $whitelist_options['better-captcha'][] = 'better-captcha-general-mode';
  $whitelist_options['better-captcha'][] = 'better-captcha-site-key';
  $whitelist_options['better-captcha'][] = 'better-captcha-secret-key';
  $whitelist_options['better-captcha'][] = 'better-captcha-theme';
  $whitelist_options['better-captcha'][] = 'better-captcha-size';
  $whitelist_options['better-captcha'][] = 'better-captcha-maths-words';
  $whitelist_options['better-captcha'][] = 'better-captcha-maths-complex';
  $whitelist_options['better-captcha'][] = 'better-captcha-maths-timeout';
  $whitelist_options['better-captcha'][] = 'better-captcha-place-login';
  $whitelist_options['better-captcha'][] = 'better-captcha-place-lostp';
  $whitelist_options['better-captcha'][] = 'better-captcha-place-regis';
  $whitelist_options['better-captcha'][] = 'better-captcha-place-comme';
  // TODO: add more
  return $whitelist_options;
});

//define output for settings page
function better_capt_show_settings() {
  echo '<div class="wrap">';
  echo '  <div style="padding:12px;background-color:white;margin:24px 0;">';
  echo '    <a href="https://bettersecurity.co" target="_blank" style="display:inline-block;width:100%;">';
  echo '      <img src="' . plugins_url('header.png', __FILE__) . '" style="height:64px;">';
  echo '    </a>';
  echo '  </div>';
  echo '  <div style="margin:0 0 24px 0;">';
  echo '    <a href="https://www.php.net/supported-versions.php" target="_blank"><img src="' . better_capt_badge_php() . '"></a>';
  if(better_capt_dbtype()==='MYSQL') {
    echo ' &nbsp; <a href="https://www.fromdual.com/support-for-mysql-from-oracle" target="_blank"><img src="' . better_capt_badge_mysql() . '"></a>';
	}
	else {
		echo ' &nbsp; <a href="https://www.fromdual.com/support-for-mysql-from-oracle" target="_blank"><img src="' . better_capt_badge_maria() . '"></a>';
	}
  echo '  </div>';
  echo '  <h1>' . __('Better Captcha', 'better-capt-text') . '</h1>';
  echo '  <form action="options.php" method="post">';
	settings_fields('better-captcha');
  do_settings_sections('better-captcha');
	submit_button();
  echo '  </form>';
  echo '</div>';
}

function better_capt_badge_php() {
  $ver = better_capt_phpversion();
  $col = "critical";
  if(version_compare($ver,'7.3','>=')) {
    $col = "important";
  }
  if(version_compare($ver,'7.4','>=')) {
    $col = "success";
  }
  return 'https://img.shields.io/badge/PHP-' . $ver . '-' . $col . '.svg?logo=php&style=for-the-badge';
}

function better_capt_phpversion() {
	return explode('-',phpversion())[0]; //trim any extra information
}

function better_capt_dbtype() {
	global $wpdb;
	$vers = $wpdb->get_var("SELECT VERSION() as mysql_version");
	if(stripos($vers,'MARIA')!==false) {
		return 'MARIA';
	}
	return 'MYSQL';
}

function better_capt_dbversion() {
	global $wpdb;
	$vers = $wpdb->get_var("SELECT VERSION() as mysql_version");
  return explode('-',$vers)[0]; //trim any extra information
}

function better_capt_badge_mysql() {
  $ver = better_capt_dbversion();
  $col = "critical";
  if(version_compare($ver,'5.6','>=')) {
    $col = "important";
  }
  if(version_compare($ver,'5.7','>=')) {
    $col = "success";
  }
  return 'https://img.shields.io/badge/MySQL-' . $ver . '-' . $col . '.svg?logo=mysql&style=for-the-badge';
}

function better_capt_badge_maria() {
  $ver = better_capt_dbversion();
  $col = "critical";
  if(version_compare($ver,'10.0','>=')) {
    $col = "important";
  }
  if(version_compare($ver,'10.1','>=')) {
    $col = "success";
  }
  return 'https://img.shields.io/badge/MariaDB-' . $ver . '-' . $col . '.svg?logo=mariadb&style=for-the-badge';
}

//define output for settings section
function better_capt_general() {
  echo '<hr>';
  echo '<p>General plugin settings.</p>';
}

//defined output for settings
function better_capt_general_mode() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-general-mode'] ?: 'hcapt';
	echo '<select id="better-captcha-general-mode" name="better-captcha-settings[better-captcha-general-mode]">';
	better_capt_create_option($value, 'hcapt', __('hCaptcha (account required)', 'better-capt-text'), true);
	better_capt_create_option($value, 'maths', __('Simple Maths Questions', 'better-capt-text'), true);
	echo '</select>';
}

//define output for settings section
function better_capt_account() {
  echo '<hr>';
  echo '<p>Please sign up for a free <a href="https://bettersecurity.co/hcaptcha/" target="_blank">hCaptcha</a> account to get your site key and secret key (not required for simple maths questions mode).</p>';
}

//defined output for settings
function better_capt_site_key() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-site-key'] ?: '';
  echo '<input id="better-captcha-site-key" name="better-captcha-settings[better-captcha-site-key]" type="text" value="' . $value . '" size="50">';
}

//defined output for settings
function better_capt_secret_key() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-secret-key'] ?: '';
  echo '<input id="better-captcha-secret-key" name="better-captcha-settings[better-captcha-secret-key]" type="password" value="' . $value . '" size="50">';
}

//defined output for settings
function better_capt_theme() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-theme'] ?: 'light';
  echo '<select id="better-captcha-theme" name="better-captcha-settings[better-captcha-theme]">';
  better_capt_create_option($value,"light",__("Light theme", 'better-capt-text'),true);
  better_capt_create_option($value,"dark",__("Dark theme", 'better-capt-text'),true);
  echo '</select>';
}

//defined output for settings
function better_capt_size() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-size'] ?: 'normal';
  echo '<select id="better-captcha-size" name="better-captcha-settings[better-captcha-size]">';
  better_capt_create_option($value,"normal",__("Normal size", 'better-capt-text'),true);
  better_capt_create_option($value,"compact",__("Compact size", 'better-capt-text'),true);
  echo '</select>';
}

//define output for settings section
function better_capt_maths() {
  echo '<hr>';
}

//defined output for settings
function better_capt_maths_words() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-maths-words'] ?: 'numb';
  echo '<select id="better-captcha-maths-words" name="better-captcha-settings[better-captcha-maths-words]">';
  better_capt_create_option($value,"numb",__("Numbers only", 'better-capt-text'),true);
  better_capt_create_option($value,"word",__("Words only", 'better-capt-text'),true);
  better_capt_create_option($value,"both",__("Numbers and words", 'better-capt-text'),true);
  echo '</select>';
}

//defined output for settings
function better_capt_maths_complex() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-maths-complex'] ?: '100';
  echo '<input id="better-captcha-maths-complex" name="better-captcha-settings[better-captcha-maths-complex]" type="number" value="' . $value . '" min="10" min="200">';
}

//defined output for settings
function better_capt_maths_timeout() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-maths-timeout'] ?: '120';
  echo '<input id="better-captcha-maths-timeout" name="better-captcha-settings[better-captcha-maths-timeout]" type="number" value="' . $value . '" min="30" min="600">';
}

//define output for settings section
function better_capt_places() {
  echo '<hr>';
  echo '<p>Each form can be individually toggled to give you maximum control.</p>';
}

//defined output for settings
function better_capt_place_login() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-place-login'] ?: 'YES';
  echo '<input name="better-captcha-settings[better-captcha-place-login]" type="hidden" value="NO">';
  echo '<input id="better-captcha-place-login" name="better-captcha-settings[better-captcha-place-login]" type="checkbox" value="YES"' . ($value==='YES' ? ' checked' : '') . '>';
}

//defined output for settings
function better_capt_place_lostp() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-place-lostp'] ?: 'YES';
  echo '<input name="better-captcha-settings[better-captcha-place-lostp]" type="hidden" value="NO">';
  echo '<input id="better-captcha-place-lostp" name="better-captcha-settings[better-captcha-place-lostp]" type="checkbox" value="YES"' . ($value==='YES' ? ' checked' : '') . '>';
}

//defined output for settings
function better_capt_place_regis() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-place-regis'] ?: 'YES';
  echo '<input name="better-captcha-settings[better-captcha-place-regis]" type="hidden" value="NO">';
  echo '<input id="better-captcha-place-regis" name="better-captcha-settings[better-captcha-place-regis]" type="checkbox" value="YES"' . ($value==='YES' ? ' checked' : '') . '>';
}

//defined output for settings
function better_capt_place_comme() {
	$settings = get_option('better-captcha-settings');
	$value = $settings['better-captcha-place-comme'] ?: 'YES';
  echo '<input name="better-captcha-settings[better-captcha-place-comme]" type="hidden" value="NO">';
  echo '<input id="better-captcha-place-comme" name="better-captcha-settings[better-captcha-place-comme]" type="checkbox" value="YES"' . ($value==='YES' ? ' checked' : '') . '>';
}

// TODO: add more

//add actions
add_action('admin_menu','better_capt_menus');
add_action('admin_init','better_capt_settings');

//helper function for dropdowns
function better_capt_create_option($def,$val,$rep,$boo) {
  echo '  <option value="' . $val . '"' . ($def===$val ? ' selected' : '') . ($boo ? '' : ' disabled') . '>' . $rep . '</option>';
}

/*
----------------- Initalisation and Utilities -----------------
*/

//initialisation
function better_capt_init() {
  $settings = get_option('better-captcha-settings');
  $mode = $settings['better-captcha-general-mode'] ?: 'hcapt';
  $skey = $settings['better-captcha-site-key'] ?: '';
  $sssh = $settings['better-captcha-secret-key'] ?: '';
  $enqu = false;
  if($mode==='maths' || ($mode==='hcapt' && $skey!=='' && $sssh!=='')) {
    //login form
    if(($settings['better-captcha-place-login'] ?: 'YES')==='YES') {
      add_action('login_enqueue_scripts', 'better_capt_scripts');
      add_filter('login_form', 'better_capt_display_captcha');
      add_filter('wp_authenticate_user', 'better_capt_verify_captcha');
    }

    //lost password form
    if(($settings['better-captcha-place-lostp'] ?: 'YES')==='YES') {
      $enqu = true;
      add_filter('lostpassword_form', 'better_capt_display_captcha');
    }

    //register form
    if(($settings['better-captcha-place-regis'] ?: 'YES')==='YES') {
      $enqu = true;
      add_filter('register_form', 'better_capt_display_captcha');
      add_filter('registration_errors', 'better_capt_verify_captcha');
    }

    //comment form
    if(($settings['better-captcha-place-comme'] ?: 'YES')==='YES') {
      $enqu = true;
      add_filter('comment_form_after_fields', 'better_capt_display_captcha');
      add_filter('pre_comment_approved', 'better_capt_verify_captcha');
    }

    // TODO: add more

    //include external script
    if($enqu && $mode==='hcapt') {
      add_action('wp_enqueue_scripts', 'better_capt_scripts');
    }
  }
}
add_action('init', 'better_capt_init', 0);

//include external script
function better_capt_scripts() {
  wp_enqueue_script('hcaptcha-script', 'https://hcaptcha.com/1/api.js', array(), false, true);
}

//generate nonce name
function better_capt_nonce_name() {
  $name = 'nonce';
  switch(current_filter()) {
    case 'login_form': case 'wp_authenticate_user':
      $name = 'login';
      break;
    case 'lostpassword_form':
      $name = 'lostp';
      break;
    case 'register_form': case 'registration_errors':
      $name = 'regis';
      break;
    case 'comment_form_after_fields': case 'pre_comment_approved':
      $name = 'comme';
      break;
    // TODO: add more
  }
  return 'better_captcha_' . $name;
}

//output the captcha field
function better_capt_display_captcha() {
  $settings = get_option('better-captcha-settings');
  $mode = $settings['better-captcha-general-mode'] ?: 'hcapt';
  if($mode==='hcapt') {
    $skey = $settings['better-captcha-site-key'] ?: '';
    $sssh = $settings['better-captcha-secret-key'] ?: '';
    if($skey!=='' && $sssh!=='') {
      $them = $settings['better-captcha-theme'] ?: 'light';
      $size = $settings['better-captcha-size'] ?: 'normal';
      echo '<div class="better-captcha h-captcha h-captcha-' . $them . ' h-captcha-' . $size . '" data-sitekey="' . $skey . '" data-theme="' . $them . '" data-size="' . $size . '"></div>';
      echo wp_nonce_field(better_capt_nonce_name(), 'better_captcha_nonce', true, false);
    }
  }
  if($mode==='maths') {
    $max = intval($settings['better-captcha-maths-complex'] ?: 100);
    $mod = $settings['better-captcha-maths-words'] ?: 'numb';
    if($mod!=='numb') {
      $nfs = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    }
    $ope = rand(1,2);
    switch($ope) {
      case 1: //add (+)
        $max = intval($max/2);
        $a = rand(1,$max);
        $b = rand(1,$max);
        $val = $a+$b;
        $lab = ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? $nfs->format($a) : $a) . ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? ' plus ' : ' + ') . ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? $nfs->format($b) : $b);
        break;
      case 2: //subtract (-)
        $a = rand(1,$max);
        $b = rand(1,$max);
        $val = max($a,$b)-min($a,$b);
        $lab = ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? $nfs->format(max($a,$b)) : max($a,$b)) . ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? ' minus ' : ' - ') . ($mod==='word' || ($mod==='both' && rand(1,2)===1) ? $nfs->format(min($a,$b)) : min($a,$b));
        break;
    }
    $guid = better_capt_guid();
    $secs = intval($settings['better-captcha-maths-timeout'] ?: 120);
    set_transient("better_capt_" . $guid, $val, $secs);
    echo '<p class="better-capt-wrap">';
    echo '  <label for="better-capt">What is ' . $lab  . '?</label>';
    echo '  <input type="text" name="better_captcha_value" id="better-capt" aria-describedby="login_error" class="input" value="" size="20">';
    echo '  <input type="hidden" name="better_captcha_guid" value="' . $guid . '">';
    echo '</p>';
    echo wp_nonce_field(better_capt_nonce_name(), 'better_captcha_nonce', true, false);
  }
}

//create GUID-ish
function better_capt_guid() {
	return rtrim(strtr(base64_encode(rand() . uniqid('',true)), '+/', '-_'), '=');
}

//verify the captcha value
function better_capt_verify_captcha($par1) {
  $settings = get_option('better-captcha-settings');
  $mode = $settings['better-captcha-general-mode'] ?: 'hcapt';
  $titl = 'invalid';
  if($mode==='hcapt') {
    if(isset($_POST['h-captcha-response']) && isset($_POST['better_captcha_nonce']) && wp_verify_nonce(sanitize_text_field($_POST['better_captcha_nonce']), better_capt_nonce_name())) {
      $resp = htmlspecialchars(sanitize_text_field($_POST['h-captcha-response']));
      if($resp!=='') {
        $sssh = $settings['better-captcha-secret-key'] ?: '';
        if($sssh!=='') {
          $body = wp_remote_get('https://hcaptcha.com/siteverify?secret=' . $sssh . '&response=' . $resp);
          $data = json_decode($body["body"], true);
          if($data["success"]==true) {
            return $par1; //captcha verified successfully
          }
        }
      }
    }
  }
  if($mode==='maths') {
    if(isset($_POST['better_captcha_value']) && isset($_POST['better_captcha_guid']) && isset($_POST['better_captcha_nonce']) && wp_verify_nonce(sanitize_text_field($_POST['better_captcha_nonce']), better_capt_nonce_name())) {
      $guid = htmlspecialchars(sanitize_text_field($_POST['better_captcha_guid']));
      if($guid!=='') {
        $val = htmlspecialchars(sanitize_text_field($_POST['better_captcha_value']));
        if($val!=='') {
          $was = get_transient("better_capt_" . $guid);
          if($was!==false && $val==$was) { //don't check type
            delete_transient("better_capt_" . $guid);
            return $par1; //captcha verified successfully
          }
          if($was===false) {
            $titl = 'timeout';
          }
        }
      }
    }
  }

  //return error (depending on filter)
  $code = 'captcha_invalid';
  $mess = __("<strong>Error</strong>: Captcha " . $titl . " - please try again", 'better-capt-text');
  switch(better_capt_nonce_name()) {
    case 'regis': //registration form
      $par1->add($code, $mess);
      return $par1;
  }
  return new WP_Error($code, $mess, 403); //other forms (eg. login)
}

/*
--------------------- Add links to plugins page ---------------------
*/

//show settings link
function better_capt_links($links) {
	$links[] = sprintf('<a href="%s">%s</a>',admin_url('options-general.php?page=better-captcha-settings'),__('Settings', 'better-capt-text'));
	return $links;
}

//add actions
if(is_admin()) {
  add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'better_capt_links');
}

/*
----------------------------- The End ------------------------------
*/
