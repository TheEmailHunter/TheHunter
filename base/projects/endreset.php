<?php

/**
 * end and reset file for projects on 2rip
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Scripts
 * @package   TinyMVC
 * @author    NocRoom https://github.com/NocRoom/TheHunter
 * @copyright 2016 NocRoom.com
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 */

defined('V5_SITE') or die ('Hack attemt');

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
}

$dbCls->query("SELECT `project`.*
               FROM `project`");
$projects = $dbCls->fetch();

foreach ($projects AS $null => $project) {
    $dbCls->query("UPDATE `project` 
                   SET status         = 0,
                       crawled_urls   = 0,
                       crawled_emails = 0,
                       crawled_failed = 0
                   WHERE `id` = ?
                   LIMIT 1", 
                  array($project['id']));
    $dbCls->query("DELETE FROM `email` 
                   WHERE `project_id` = ?", 
                  array($project['id']));
    $dbCls->query("DELETE FROM `spider` 
                   WHERE `project_id` = ?
                   AND   `ref_id` != 0", 
                  array($project['id']));
    $dbCls->query("UPDATE `spider`
                   SET `processed`  = 0,
                       `failed`     = 0,
                       `failed_msg` = ''
                   WHERE `project_id` = ?
                   LIMIT 1",
                  array($project['id']));
    $dbCls->query("DELETE FROM `project_stats` 
                   WHERE `project_id` = ?
                   AND   `type` = 'p'", 
                  array($project['id']));
}

$_SESSION['notice'] = 'All projects have been resetted!';
            
header("Location: " . $configCls->get("application/site_url") . "projects");
exit;