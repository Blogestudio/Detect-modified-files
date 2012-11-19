<?php

/**
 * Detect modified files PHP script, version 1.0
 * By Pau Iglesias, pauiglesias@blogestudio.com
 * License: GPL version 3 or later - http://www.gnu.org/licenses/gpl.txt
 * 
 * Usage: /usr/bin/php [path]detect-modified-files.php [--config=filename] 2>/dev/null
 * 
 * The `config` parameter refers to the config file located in the directory or subdirectories
 * from this script location. This parameter is optional, if omitted the script will attempt
 * to load by default the file `detect-modified-files-config.php` in the same script directory.
 */

// End if is running under web server
if (isset($_SERVER['HTTP_HOST']) || isset($_SERVER['HTTP_USER_AGENT']) || isset($_SERVER['REQUEST_URI']))
	die;

// Initialize
$processed = date('Y-m-d H:i');

// Disable timeout
set_time_limit(0);

// Check command line config file argument
$filename = false;
if (isset($argv) && !empty($argv[1])) {
	if (strpos($argv[1], '--config=') === 0) {
		$param = explode('=', $argv[1]);
		if (count($param) == 2 && !empty($param[1]))
			$filename = $param[1];
	} elseif ($argv[1] == '-c' && !empty($argv[2])) {
		$filename = $argv[2];
	}
}

// Check default config file or use another file
$filename = realpath(dirname(__FILE__).'/'.($filename? str_replace('../', '', ltrim($filename, '/')) : 'detect-modified-files-config.php'));
if (!file_exists($filename))
	die;
include($filename);

// Check minimum configuration
if (empty($dmf_config['dir']) || (empty($dmf_config['hours']) && empty($dmf_config['minutes'])) || empty($dmf_config['email_to']))
	die;

// Array for time description
$time_desc = array();
if (!empty($dmf_config['hours']))
	$time_desc[] = $dmf_config['hours'].' h';
if (!empty($dmf_config['minutes']))
	$time_desc[] = $dmf_config['minutes'].' m';

// Prepare extensions to find
$items = array();
foreach ($dmf_config['extensions'] as $extension)
	$items[] = '-name "*.'.$extension.'"';

// Detect modified files
$result = shell_exec(((isset($dmf_config['niceness']) && $dmf_config['niceness'] !== false)? 'nice -n '.$dmf_config['niceness'].' ' : '').'find '.rtrim($dmf_config['dir'], '/').'/'.' -mmin -'.((empty($dmf_config['hours'])? 0 : ($dmf_config['hours'] * 60)) + (empty($dmf_config['minutes'])? 0 : $dmf_config['minutes'])).(empty($items)? '' : ' \! -type d \( '.implode(' -o ', $items).' \)').' -exec ls -aAl {} \;');

// Prepare result
$count = 0;
$modified = array();
$result = explode("\n", $result);
foreach ($result as $line) {
	
	// Check line result
	if (!empty($line)) {
		
		// Check excludes
		$is_excluded = false;
		if (!empty($dmf_config['excludes']) && is_array($dmf_config['excludes'])) {
			foreach ($dmf_config['excludes'] as $exclude) {
				if (strpos($line, $exclude) !== false) {
					$is_excluded = true;
					break;
				}
			}
		}
		
		// Add line and prepare format
		if (!$is_excluded) {
			$count++;
			if (false !== ($pos = strpos($line, $dmf_config['dir'])))
				$line = substr($line, 0, $pos)."\n".substr($line, $pos)."\n";
			$modified[] = $line;
		}
	}
}

// Check if send e-mail when no files changes detected
if ($count == 0 && isset($dmf_config['email_if_empty']) && !$dmf_config['email_if_empty'])
	die;

// Compose output
$message = '
- Report processed: '.$processed.'
- New or modified files last '.implode(' and ', $time_desc).' in '.$dmf_config['dir'].' ('.implode(', ', $dmf_config['extensions']).')
- Excluded strings: '.((!empty($dmf_config['excludes']) && is_array($dmf_config['excludes']))? '"'.implode('", "', $dmf_config['excludes']).'"' : 'none').'
====================================================================================================

'.implode("\n", $modified).'

'.$count.' files

---End---
';

// And send e-mail
mail($dmf_config['email_to'], $dmf_config['email_subject'].' - '.$processed.' - last '.implode(' ', $time_desc).' - '.$count.' files found', $message);

?>