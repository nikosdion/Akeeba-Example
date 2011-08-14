<?php
defined('_JEXEC') or die();

?>
Example Akeeba CLI Application
Copyright ©2011 Nicholas K. Dionysopoulos - All rights reserved
Licensed under the GNU General Public License, version 3 or later

Usage: php index.php --host="hostname" --secret="secretkey"

Where:
    hostname    The hostname to your site, without the http:// or https://
                protocol identifier, e.g. www.example.com
    secretkey   Your Akeeba Backup secret key

IMPORTANT: Remember to set the "Enable front-end and remote backups" option to
    Yes in Akeeba Backup's Component Parameters page in order for this appli-
    cation to work with your site.

