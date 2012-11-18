<?php

/**
 * Rename this file to `detect-modified-files-config.php` to use as the default config file
 * and locate it in the same directory of detect-modified-files.php script.
 */
 
$dmf_config = array(
	'dir' => 				'/path/to/web/sites',
	'hours' => 				1,
	'minutes' => 			10,
	'niceness' => 			false, // range from -20 (high priority) to 19 (least favorable), or false to disable
	'extensions' => 		array('php', 'js', 'html', 'css', 'htaccess', 'conf', 'svn', 'sh', 'pl', 'py', 'exe'),
	'excludes' => 			array(), // Exclude results matched by any string of this array
	'email_if_empty' => 	false,
	'email_to' => 			'my-email@domain.com',
	'email_subject' => 		'Modified files for server X',
);

?>