<?php
 define("SETTINGS", "./resources/settings.ini");
 $settings = parse_ini_file(SETTINGS, true);
 define("CLASSPATH", $settings['global']['classpath']);
 define("PAGES_CLASSPATH", $settings['global']['pages_classpath']);
 define("AJAX_CLASSPATH", $settings['global']['ajax_classpath']);
 define("CONTENT_PATH", $settings['global']['content_path']);
 define("CONTENT_IMAGE_PATH", $settings['global']['content_image_path']);
 define("UPLOAD_PATH", $settings['global']['upload_path']);
 define("SPONSORS_IMAGE_PATH", $settings['global']['sponsors_image_path']);
 define("LIFECYCLE_PATH", $settings['global']['lifecycle_path']);
 define("LIFECYCLE", $settings['lifecycle']['lifecycle']);

 $scripts['debate'] = "Debate";
 $scripts['rollcall'] = "Rollcall";
 $scripts['vote'] = "Vote";
 $scripts['setup'] = "Setup";
?>
