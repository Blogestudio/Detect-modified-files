Detect-modified-files
=====================

Detect changes in files from a directory in a specified time period and report via e-mail.

This script is intended to run as an automated task in crontab, and provides a quick way of alerting
changes in files due to hacking attempts or other uncontrolled issues.

Usage:

/usr/bin/php [path]detect-modified-files.php [--config=filename] 2>/dev/null

The other script we provide is `detect-modified-files-config-sample.php` that can be used as a 
default config file if renamed to `detect-modified-files-config.php` and placed in the same
directory of the main script.

The `--config` parameter refers to a config file located in the directory or subdirectories
from main script location. This parameter is optional, if omitted the script will attempt
to load the default config file.

Available configuration options in the config file:

dir
Required
The directory where performs the scan.

hours
Required
Files changed in the recent specified hours.

minutes
Required
Files changed in the last minutes.

niceness
Optional
Script process priority from -20 to highest priority to 19, or false to disable it.

extensions
Optional
Limit the file search to the extensions of this array.

excludes
Optional
Exclude results matched by any string of this array.

email_if_empty
Optional
When false, it does not send the e-mail if no results.

email_to
Required
The recipient's e-mail.

email_subjectOptional
Partial subject of the e-mail to identify server.

The hours and minutes parameters should be consistent with the period over the crontab and the time wasted to
perform the whole search. For example, for a hourly cron task, the hours should be 1 hour, and set 5 minutes
to support the approximate script execution time.

Licensed under the GPL version 3 or later:
http://www.gnu.org/licenses/gpl.txt

Blogestudio
http://blogestudio.com/