<?php
if (!defined("FREEPBX_IS_AUTH")) {
    die("No direct script access allowed");
}

$module = "dundi";

require_once dirname(__FILE__) . "/functions.inc.php";

$tables = array(
	"dundi_config",
	"dundi_mappings",
	"dundi_peers",
	"dundi_contexts",
	"dundi_context_entries",
);
foreach ($tables as $t) {
	$result = $db->query("DROP TABLE $t");
	if (DB::IsError($result)) {
		out(sprintf(_("Cannot delete table %s: %s"), $t, $result->getMessage()));
	} else {
		out(sprintf(_("Deleted table %s."), $t));
	}
}

$config_file = $amp_conf["ASTETCDIR"] . "/" . Dundi::$config_file;
if (file_exists($config_file)) {
    if (rename($config_file, "$config_file.module")) {
		out(sprintf(_("Renaming %s as %s."), $config_file, "$config_file.module"));
        if (file_exists("$config_file.0")) {
            if (rename("$config_file.0", $config_file)) {
				out(sprintf(_("Restored previous configuration from %s."), "$config_file.0"));
            } else {
				out(sprintf(_("Unable to restore previous configuration from %s."), "$config_file.0"));
            }
        } else {
        	out(sprintf(_("Backup file %s does not exist, unable to restore previous configuration."), "$config_file.0"));
        }
    } else {
        out(sprintf(_("Unable to rename %s, it should be removed manually."), $config_file));
    }
}
?>