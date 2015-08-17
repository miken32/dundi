<?php
if (!defined("FREEPBX_IS_AUTH")) {
    die("No direct script access allowed");
}

$module = "dundi";

require_once dirname(__FILE__) . "/functions.inc.php";

$result = $db->getOne("SELECT * FROM dundi_config");
$first_run = (DB::IsError($result));

out(_("Creating database tables..."));

$query = <<< SQL
CREATE TABLE IF NOT EXISTS dundi_config (
	setting VARCHAR(80) NOT NULL,
	value VARCHAR(80) NOT NULL,
	UNIQUE INDEX idx_setting (setting)
)
SQL;
$result = $db->query($query);
if (DB::IsError($result)) {
    die_freepbx(_("Can not create table `%s`: %s"), "dundi_config", $result->getMessage());
}

$query = <<< SQL
CREATE TABLE IF NOT EXISTS dundi_mappings (
	name VARCHAR(80) NOT NULL,
	context VARCHAR(80) NOT NULL,
	weight VARCHAR(512) NOT NULL,
	tech VARCHAR(8) NOT NULL,
	dest VARCHAR(512) NOT NULL,
	flags SET(
		"nounsolicited",
		"nocomunsolicit",
		"residential",
		"commercial",
		"mobile",
		"nopartial"
	) NOT NULL DEFAULT "",
	descr VARCHAR(80) NOT NULL
)
SQL;
$result = $db->query($query);
if (DB::IsError($result)) {
    die_freepbx(_("Can not create table `%s`: %s"), "dundi_mappings", $result->getMessage());
}

$query = <<< SQL
CREATE TABLE IF NOT EXISTS dundi_peers (
	peerid VARCHAR(17) NOT NULL PRIMARY KEY,
	inkey VARCHAR(80) NOT NULL,
	outkey VARCHAR(80) NOT NULL,
	host VARCHAR(80) NOT NULL,
	port INT NOT NULL DEFAULT 4520,
	qualify VARCHAR(8) NOT NULL,
	ord VARCHAR(80) NOT NULL,
	include VARCHAR(80) NOT NULL,
	noinclude VARCHAR(80) NOT NULL,
	permit VARCHAR(80) NOT NULL,
	deny VARCHAR(80) NOT NULL,
	model ENUM(
		"",
		"inbound",
		"outbound",
		"symmetric",
		"none"
	) NOT NULL,
	precache ENUM(
		"",
		"inbound",
		"outbound",
		"symmetric",
		"none"
	) NOT NULL,
	descr VARCHAR(80) NOT NULL
)
SQL;
$result = $db->query($query);
if (DB::IsError($result)) {
    die_freepbx(_("Can not create table `%s`: %s"), "dundi_peers", $result->getMessage());
}

$query = <<< SQL
CREATE TABLE IF NOT EXISTS dundi_contexts (
	name VARCHAR(80) PRIMARY KEY,
	descr VARCHAR(80) NOT NULL
)
SQL;
$result = $db->query($query);
if (DB::IsError($result)) {
    die_freepbx(_("Can not create table `%s`: %s"), "dundi_contexts", $result->getMessage());
}

$query = <<< SQL
CREATE TABLE IF NOT EXISTS dundi_context_entries (
	context VARCHAR(80) NOT NULL,
	extension VARCHAR(80) NOT NULL,
	UNIQUE INDEX idx_ext (context, extension)
)
SQL;
$result = $db->query($query);
if (DB::IsError($result)) {
    die_freepbx(_("Can not create table `%s`: %s"), "dundi_context_entries", $result->getMessage());
}

$config_file = $amp_conf["ASTETCDIR"] . "/" . Dundi::$config_file;
if (file_exists($config_file) && $first_run) {
    out(_("Importing existing DUNDi configuration&hellip;"));
    $config = Dundi::parse_conf_file($config_file);

    $fields = array();
    $query = "INSERT INTO dundi_config (setting, value) VALUES (?, ?)";
    $stmt = $db->prepare($query);
    foreach ($config["general"] as $k=>$v) {
        $fields[] = array($k, $v);
    }
    $result = $db->executeMultiple($stmt, $fields);
    if (DB::IsError($result)) {
        error_log("Database error: $query");
        error_log(var_dump($fields, true));
        die_freepbx(sprintf(_("Can not insert value from [%s] section: %s"), "general", $result->getMessage()));
    }
    unset($config["general"]);

    $fields = array();
    $query = "INSERT INTO dundi_mappings (name, context, weight, tech, dest, flags) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    foreach ($config["mappings"] as $k=>$v) {
        $v = explode(",", $v, 5);
        $v[0] = str_replace("> ", "", $v[0]);
        $fields[] = array($k, $v[0], $v[1], $v[2], $v[3], $v[4]);
    }
    $result = $db->executeMultiple($stmt, $fields);
    if (DB::IsError($result)) {
        error_log("Database error: $query");
        error_log(var_dump($fields, true));
        die_freepbx(sprintf(_("Can not insert value from [%s] section: %s"), "mappings", $result->getMessage()));
    }
    unset($config["mappings"]);

    $fields = array();
    $query = "INSERT INTO dundi_peers (peerid, inkey, outkey, host, port, qualify, ord, include, noinclude, permit, deny, model, precache) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db->prepare($query);
    foreach($config as $k=>$v) {
        //ensure no unset values
        $v = array_merge(Dundi::default_peer(), $v);
        $fields[] = array($k, $v["inkey"], $v["outkey"], $v["host"], $v["port"], $v["qualify"], $v["order"], $v["include"], $v["noinclude"], $v["permit"], $v["deny"], $v["model"], $v["precache"]);
    }
    $result = $db->executeMultiple($stmt, $fields);
    if (DB::IsError($result)) {
        error_log("Database error: $query");
        error_log(var_dump($fields, true));
        die_freepbx(sprintf(_("Can not insert value from [%s] section: %s"), "peers", $result->getMessage()));
    }

    if (!file_exists("$config_file.0")) {
        out(sprintf(_("Backing up previous configuration file as %s."), "$config_file.0"));
        rename($config_file, "$config_file.0");
    } else {
        out(sprintf(_("<strong>A file named %s already exists, %s must be manually removed and backed up!</strong>"), "$config_file.0", $config_file));
    }
}
touch($config_file);
?>
