<?php
if (!defined("FREEPBX_IS_AUTH")) {
    die("No direct script access allowed");
}

$module = "dundi";

/**
 * Provides support functions for the FreePBX DUNDi module
 * @see http://svnview.digium.com/svn/asterisk/branches/11/include/asterisk/dundi.h?view=markup dundi.h
 * @see http://svnview.digium.com/svn/asterisk/branches/11/pbx/pbx_dundi.c?view=markup dundi.c
 * @see http://svnview.digium.com/svn/asterisk/branches/11/configs/dundi.conf.sample?view=markup dundi.conf.sample
 */
class Dundi
{
    /**
     * The DUNDi configuration file
     * @var string $config_file
     */
    public static $config_file = "dundi.conf";

    /**
     * The flags for mappings, stored as a bitfield (SET) in the database
     * @var array $bitflags
     */
    public static $bitflags = array(
        1=>"nounsolicited",
        2=>"nocomunsolicit",
        4=>"residential",
        8=>"commercial",
        16=>"mobile",
        32=>"nopartial",
    );

    /**
     * Returns the allowed length of a mapping destination.
     *
     * Prior to Asterisk version 1.8.3, the maximum length of the destination field was 80
     * characters, which made the use of nested dialplan functions nearly impossible.
     * As of Asterisk 1.8.3, the maximum length is 512 characters.
     * @return int Length of the destination field
     */
    public static function dest_length()
    {
        global $amp_conf;

        return version_compare($amp_conf["ASTVERSION"], "1.8.3") >= 0 ? 512 : 80;
    }

    /**
     * Tells us whether peers can have a port specified.
     *
     * Starting with Asterisk 1.8 (backported), DUNDi peers could be specified with a port number
     * @return boolean Whether or not a peer definition is allowed to contain a port number
     */
    public static function peer_ports()
    {
        global $amp_conf;

        $ver = $amp_conf["ASTVERSION"];
        if (version_compare($ver, "1.8") >= 0) {
            return true;
        } elseif (version_compare($ver, "1.6.2.10") >= 0) {
            return true;
        } elseif (version_compare($ver, "1.4.34") >= 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Parses an Asterisk config file into an array.
     *
     * @see http://mike.bitrevision.com/blog/2012-01-06_parsing_asterisk_configuration_files
     * @param string $path The configuration file location
     * @param string $dir The include directory, if different from the current directory
     * @return array Associative array representing the configuration file
     */
    public static function parse_conf_file($path, $dir = null)
    {
        if (file_exists($path) && is_readable($path)) {
            $string = file_get_contents($path);
        } else {
            return false;
        }
        $lines   = explode("\n", $string);
        $section = "0";
        $result  = array();
        $dir     = is_null($dir) ? rtrim(dirname($path), "/") : rtrim($dir, "/");
        foreach($lines as $line) {
            $line = trim($line);
            if($line === "" || substr($line, 0, 1) === ";") { /* ;Comment */
                continue;
            } elseif(substr($line, 0, 1) === "[") { /* [section] */
                $section = str_replace(array("[", "]"), "", $line);
                if(!isset($result[$section])) {
                    $result[$section] = array();
                }
            } elseif (substr($line, 0, 8) === "#include") { /* #include file */
                $file = trim(substr($line, 8));
                $included = self::parse_conf_file("$dir/$file");
                if (is_array($included)) {
                    if (isset($included[0])) {
                        $result[$section] = array_merge($result[$section], $included[0]);
                        unset($included[0]);
                    }
                    $result = array_merge($result, $included);
                }
            } else { /* key = val */
                $line = explode(";", $line);
                list($key, $val) = explode("=", $line[0]);
                $key = trim($key);
                $result[$section][$key] = trim($val);
            }
        }
        return $result;
    }

    /**
     * Ensure values in the POST fields are valid.
     *
     * @param array $errors An array to populate with any validation errors found
     * @return boolean TRUE on success or FALSE on error
     */
    private static function validate_post(&$errors) {
        $errors = array();
        foreach ($_POST as $k=>$v) {
            switch ($k) {
            case "organization":
            case "department":
            case "locality":
            case "stateprov":
            case "email":
            case "phone":
            case "secretpath":
            case "mapping_descr":
            case "mapping_context":
            case "peer_descr":
            case "peer_inkey":
            case "peer_outkey":
            case "peer_host":
            case "peer_include":
            case "peer_noinclude":
            case "peer_permit":
            case "peer_deny":
            case "context_descr":
                if (strlen($v) > 80) {
                    $errors[$k] = _("Value too long, must be no more than 80 characters.");
                }
                break;
            case "port":
            case "peer_port":
                if (!is_numeric($v) || $v < 1 || $v > 65535) {
                    $errors[$k] = _("Must be a valid UDP port number.");
                }
                break;
            case "bindaddr":
                $v = ip2long($v);
                if ($v === false) {
                    $errors[$k] = _("Must be a valid IP address.");
                }
                break;
            case "tos":
                if (!preg_match("/^(ef|CS[0-7]|AF[1-4][1-3])$/i", $v)) {
                    $errors[$k] = _("Must be a valid ToS value.");
                }
                break;
            case "entityid":
                if (!preg_match("/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i", $v)) {
                    $errors[$k] = _("Value must be a valid EID.");
                }
                break;
            case "cachetime":
                if (!is_numeric($v) || $v < 0 || $v > 999999) {
                    $errors[$k] = _("Value must be between 0 and 999999.");
                }
                break;
            case "ttl":
                if (!is_numeric($v) || $v < 1 || $v > 120) {
                    $errors[$k] = _("Value must be between 1 and 120.");
                }
                break;
            case "autokill":
            case "peer_qualify":
                if (!preg_match("/^(yes|no|\d{1,6})?$/i", $v)) {
                    $errors[$k] = _("Value must be between 0 and 999999, yes, or no.");
                }
                break;
            case "mapping_name":
            case "context_name":
                if (strlen($v) > 80 || preg_match("/[^a-z0-9_-]/i", $v)) {
                    $errors[$k] = _("Value must be no more than 80 characters and be a valid Asterisk extension name.");
                }
                break;
            case "mapping_weight":
                if (is_numeric($v)) {
                    if ($v < 0 || $v > 59999) {
                        $errors[$k] = _("Value must be a number between 0 and 59999 or a dialplan variable or function.");
                    }
                } else {
                    if (strlen($v) > 512 || !preg_match("/^\\\$\\\{.*\\}$/", $v))
                        $errors[$k] = _("Value must be a number between 0 and 59999 or a dialplan variable or function.");
                }
                break;
            case "mapping_tech":
                if (strlen($v) > 8) {
                    $errors[$k] = _("Value too long, must be no more than 8 characters.");
                }
                break;
            case "mapping_dest":
                $dest_length = self::dest_length();
                if (strlen($v) > $dest_length) {
                    $errors[$k] = _("Value too long, must be no more than $dest_length characters.");
                }
                break;
            case "peer_peerid":
                if (!preg_match("/^(([0-9a-f]{2}:){5}[0-9a-f]{2})|(\*)$/i", $v)) {
                    $errors[$k] = _("Value must be a valid EID or wildcard character.");
                }
                break;
            case "peer_ord":
                if ($v !== "primary" && $v !== "secondary" && $v !== "tertiary" && $v !== "quartiary" && $v !== "") {
                    $errors[$k] = _("Value must be a valid order statement.");
                }
                break;
            case "peer_model":
            case "peer_precache":
                if ($v !== "inbound" && $v !== "outbound" && $v !== "symmetric" && $v !== "none" && $v !== "") {
                    $errors[$k] = _("Value must be a valid model statement.");
                }
                break;
            case "context_entries":
                if (!is_array($v)) {
                    $errors[$k] = _("Invalid data posted.");
                }
                break;
            case "storehistory":
            case "nocontextcreate":
            case "mapping_nounsolicited":
            case "mapping_nocomunsolicit":
            case "mapping_commercial":
            case "mapping_residential":
            case "mapping_mobile":
            case "mapping_nopartial":
                if ($v !== "on") {
                    $errors[$k] = _("Invalid checkbox value.");
                }
                break;
            case "item":
                break;
            default:
                die_freepbx(sprintf(_("Invalid POST variable %s with value %s sent in request!"), $k, $v));
                return false;
            }
        }
        return (count($errors) === 0);
    }

    /**
     * Save values in the POST fields to the database.
     *
     * @param string $action The type of configuration being saved
     * @return array An array of errors, or an empty array on success
     */
    public static function save_post($action)
    {
        global $db;
        global $item;

        if (!self::validate_post($errors)) {
            return $errors;
        }
        $fields = array();
        if ($action === "general") {
            $query = "DELETE FROM dundi_config";
            $result = $db->query($query);
            $query = "INSERT INTO dundi_config (setting, value) VALUES (?, ?)";
            $stmt = $db->prepare($query);
            foreach($_POST as $k=>$v) {
                $result = $db->execute($stmt, array($k, $v));
                if(DB::IsError($result)) {
                    die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
                }
            }
        } elseif ($action === "mappings") {
            if ($_POST["item"] === "0") {
                $query = "INSERT INTO dundi_mappings SET ";
            } else {
                $query = "UPDATE dundi_mappings SET ";
                $mappingid = $db->escapeSimple($_POST["mapping_name"]);
            }
            unset($_POST["item"]);
            $flags = array();
            foreach($_POST as $k=>$v) {
                $k = str_replace("mapping_", "", $k);
                if (array_search($k, self::$bitflags) !== false) {
                    $flags[] = $k;
                    continue;
                }
                $k = $db->escapeSimple($k);
                $query .= "$k = ?,";
                $fields[] = $v;
            }
            if (count($flags)) {
                $flags = implode(",", $flags);
                $query .= "flags = ?";
                $fields[] = $flags;
            }
            $query = trim($query, ",");
            if (isset($mappingid)) {
                $query .= " WHERE name = '$mappingid'";
            }
            $result = $db->query($query, $fields);
            if(DB::IsError($result)) {
                die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
            }
            $item = $_POST["mapping_name"];
        } elseif ($action === "peers") {
            if ($_POST["item"] === "0") {
                $query = "INSERT INTO dundi_peers SET ";
            } else {
                $query = "UPDATE dundi_peers SET ";
                $peerid = $db->escapeSimple($_POST["peer_peerid"]);
            }
            unset($_POST["item"]);
            foreach($_POST as $k=>$v) {
                $k = str_replace("peer_", "", $k);
                $k = $db->escapeSimple($k);
                $query .= "$k = ?,";
                $fields[] = $v;
            }
            $query = trim($query, ",");
            if (isset($peerid)) {
                $query .= " WHERE peerid='$peerid'";
            }
            $result = $db->query($query, $fields);
            if(DB::IsError($result)) {
                die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
            }
            $item = $_POST["peer_peerid"];
        } elseif ($action === "contexts") {
            if ($_POST["item"] === "0") {
                $query = "INSERT INTO dundi_contexts SET name = ?, descr = ?";
                $fields = array($_POST["context_name"], $_POST["context_descr"]);
            } else {
                $query = "UPDATE dundi_contexts SET name = ?, descr = ? WHERE name = ?";
                $fields = array($_POST["context_name"], $_POST["context_descr"], $_POST["context_name"]);
            }
            $result = $db->query($query, $fields);
            if(DB::IsError($result)) {
                die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
            }
            $item = $_POST["context_name"];
            $query = "DELETE FROM dundi_context_entries WHERE context = ?";
            $result = $db->query($query, array($item));
            if(DB::IsError($result)) {
                die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
            }
            $query = "INSERT INTO dundi_context_entries (context, extension) VALUES (?,?)";
            $stmt = $db->prepare($query);
            foreach($_POST["context_entries"] as $v) {
                $result = $db->execute($stmt, array($item, $v));
                if(DB::IsError($result)) {
                    die_freepbx($result->getMessage() . ": " . htmlspecialchars($query));
                }
            }
        }
        needreload();
        return array();
    }

    /**
     * Returns an array containing some default general settings.
     *
     * @return array The settings
     */
    private static function default_config()
    {
        $mac = `ifconfig | grep -Poim 1 "([0-9a-f]{2}:){5}[0-9a-f]{2}"`;
        if (!preg_match("/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i", $mac)) {
            $mac = "";
        }
        $config = array(
            "organization"=>"",
            "department"=>"",
            "locality"=>"",
            "stateprov"=>"",
            "email"=>"",
            "phone"=>"",
            "bindaddr"=>"0.0.0.0",
            "port"=>"4520",
            "tos"=>"ef",
            "entityid"=>$mac,
            "cachetime"=>3600,
            "ttl"=>120,
            "autokill"=>"no",
            "secretpath"=>"dundi",
            "storehistory"=>"yes",
            "nocontextcreate"=>0,
        );
        return $config;
    }

    /**
     * Get the DUNDi general settings from the database.
     *
     * @param boolean $html Set to TRUE to escape for HTML output
     * @return array The settings, or an empty array on error
     */
    public static function get_general_config($html = true)
    {
        global $db;
        $result = $db->getAll("SELECT * FROM dundi_config");
        if (DB::IsError($result)) {
            $config = array();
        } elseif (count($result) === 0) {
            $config = self::default_config();
        } else {
            $config = array();
            foreach($result as $v) {
                if ($html) {
                    $v = array_map('htmlspecialchars', $v);
                }
                $config[$v[0]] = $v[1];
            }
        }
        return $config;
    }

    /**
     * Get the names and descriptions of mappings from the database.
     *
     * @param string name The name of a mapping; if provided, full details for this mapping will be returned
     * @param boolean $html Set to TRUE to escape for HTML output
     * @return array An array containing a summary of all mappings, or an associative array with details of a mapping
     */
    public static function get_mappings($name = "", $html = true)
    {
        global $db;
        if ($name === "0") {
            $mappings = array("name"=>"", "context"=>"", "weight"=>"", "tech"=>"", "dest"=>"", "descr"=>"");
            foreach (self::$bitflags as $v) {
                $mappings[$v] = false;
            }
        } elseif ($name !== "") {
            $name = $db->escapeSimple($name);
            $mappings = $db->getRow("SELECT *, flags+0 AS bitflags FROM dundi_mappings WHERE name = '$name' LIMIT 1", DB_FETCHMODE_ASSOC);
            foreach(self::$bitflags as $k=>$v) {
                $mappings[$v] = ($mappings["bitflags"] & $k) === $k ? true : false;
            }
            if ($mappings["descr"] === "") {
                $mappings["descr"] = $mappings["name"];
            }
            unset($mappings["flags"]);
            unset($mappings["bitflags"]);
            if ($html) {
                $mappings = array_map('htmlspecialchars', $mappings);
            }
        } else {
            $mappings = $db->getAll("SELECT *, flags+0 AS bitflags FROM dundi_mappings", DB_FETCHMODE_ASSOC);
            foreach ($mappings as $k=>$v) {
                if ($v["descr"] === "") {
                    $v["descr"] = $v["name"];
                }
                if ($html) {
                    $v = array_map('htmlspecialchars', $v);
                }
                $mappings[$k] = $v;
            }
        }
        if (DB::IsError($mappings)) {
            $mappings = array();
        }
        return $mappings;
    }

    /**
     * Returns an array containing empty peer settings.
     *
     * @return array The settings
     */
    public static function default_peer()
    {
        $peer = array(
            "peerid"=>"",
            "inkey"=>"",
            "outkey"=>"",
            "host"=>"",
            "port"=>"4520",
            "qualify"=>"",
            "ord"=>"",
            "include"=>"",
            "noinclude"=>"",
            "permit"=>"",
            "deny"=>"",
            "model"=>"",
            "precache"=>"",
            "descr"=>"",
        );
        return $peer;
    }

    /**
     * Get the details of peers from the database.
     *
     * @param string $id The EID of a peer; if empty, details for all peers are provided
     * @param boolean $html Set to TRUE to escape for HTML output
     * @return array An associative array containing peer details (or an array of multiple peers)
     */
    public static function get_peers($id = "", $html = true)
    {
        global $db;
        if ($id === "0") {
            $peers = self::default_peer();
        } elseif ($id !== "") {
            $id = $db->escapeSimple($id);
            $peers = $db->getRow("SELECT * FROM dundi_peers WHERE peerid = '$id' LIMIT 1", DB_FETCHMODE_ASSOC);
            if ($peers["descr"] === "") {
                $peers["descr"] = $peers["peerid"];
            }
            if ($html) {
                $peers = array_map('htmlspecialchars', $peers);
            }
        } else {
            $peers = $db->getAll("SELECT * FROM dundi_peers", DB_FETCHMODE_ASSOC);
            foreach($peers as $k=>$v) {
                if ($v["descr"] === "") {
                    $v["descr"] = $v["peerid"];
                }
                if ($html) {
                    $v = array_map('htmlspecialchars', $v);
                }
                $peers[$k] = $v;
            }
        }
        if (DB::IsError($peers)) {
            $peers = array();
        }
        return $peers;
    }

    /**
     * Get the details of contexts from the database.
     *
     * @param string $name The name of a context; if empty, a summary of all contexts are provided
     * @param boolean $html Set to TRUE to escape for HTML output
     * @return array An associative array containing context details (or an array of multiple contexts)
     */
    public static function get_contexts($name = "", $html = true)
    {
        global $db;
        if ($name === "0") {
            $contexts = array("name"=>"", "descr"=>"", "entries"=>array());
        } elseif ($name !== "") {
            $name = $db->escapeSimple($name);
            $contexts = $db->getRow("SELECT * FROM dundi_contexts WHERE name = '$name' LIMIT 1", DB_FETCHMODE_ASSOC);
            if ($contexts["descr"] === "") {
                $contexts["descr"] = $contexts["name"];
            }
            if ($html) {
                $contexts = array_map('htmlspecialchars', $contexts);
            }
            $entries = $db->getCol("SELECT extension FROM dundi_context_entries WHERE context = '$name'");
            if (is_array($entries) && count($entries) > 0) {
                if ($html) {
                    $entries = array_map('htmlspecialchars', $entries);
                }
                $contexts["entries"] = $entries;
            } else {
                $contexts["entries"] = array();
            }
        } else {
            $contexts = $db->getAll("SELECT * FROM dundi_contexts", DB_FETCHMODE_ASSOC);
            foreach($contexts as $k=>$v) {
                if ($v["descr"] === "") {
                    $v["descr"] = $v["name"];
                }
                if ($html) {
                    $v = array_map('htmlspecialchars', $v);
                }
                $name = $db->escapeSimple($v["name"]);
                $entries = $db->getCol("SELECT extension FROM dundi_context_entries WHERE context = '$name'");
                if (is_array($entries) && count($entries) > 0) {
                    if ($html) {
                        $entries = array_map('htmlspecialchars', $entries);
                    }
                    $v["entries"] = $entries;
                } else {
                    $v["entries"] = array();
                }
                $contexts[$k] = $v;
            }
        }
        if (DB::IsError($contexts)) {
            $contexts = array();
        }
        return $contexts;
    }
}

/**
 * Run when FreePBX is reloaded.
 *
 * Builds the configuration file and inserts custom contexts into the dialplan
 * @param string $engine The VoIP engine, currently only "asterisk" is supported
 * @return boolean TRUE on success, FALSE on error
 */
function dundi_get_config($engine)
{
    global $amp_conf;
    global $ext;
    global $db;

    if ($engine !== "asterisk") {
        return false;
    }

    $config = "";
    $result = Dundi::get_general_config(false);
    foreach($result as $k=>$v) {
        $config .= "$k = $v\n";
    }

    $mappings = "";
    $result = Dundi::get_mappings("", false);
    foreach($result as $v) {
        $mappings .= "; $v[descr]\n";
        $mappings .= "$v[name] => $v[context],$v[weight],$v[tech],$v[dest],$v[flags]\n";
    }

    $peers = "";
    $result = Dundi::get_peers("", false);
    foreach($result as $v) {
        $peers .= "; $v[descr]\n";
        $peers .= "[$v[peerid]]\n";
        $peers .= "inkey = $v[inkey]\n";
        $peers .= "outkey = $v[outkey]\n";
        $peers .= "host = $v[host]\n";
        if (Dundi::peer_ports()) {
            $peers .= "port = $v[port]\n";
        }
        $peers .= "qualify = $v[qualify]\n";
        $peers .= "order = $v[ord]\n";
        $peers .= "include = $v[include]\n";
        $peers .= "noinclude = $v[noinclude]\n";
        $peers .= "permit = $v[permit]\n";
        $peers .= "deny = $v[deny]\n";
        $peers .= "model = $v[model]\n";
        $peers .= "precache = $v[precache]\n";
        $peers .= "\n";
    }
    $config_file_contents = <<< INI
;--------------------------------------------------------------------------------;
; Do NOT edit this file as it is auto-generated by FreePBX. All modifications to ;
; this file must be done via the web gui. There are alternative files to make    ;
; custom modifications, details at: http://freepbx.org/configuration_files       ;
;--------------------------------------------------------------------------------;
[general]
$config
#include dundi_general_custom.conf

[mappings]
$mappings

$peers

INI;
    $config_file = $amp_conf["ASTETCDIR"] . "/" . Dundi::$config_file;
    file_put_contents($config_file, $config_file_contents);

    if ($db->getOne("SELECT value FROM dundi_config WHERE setting = 'nocontextcreate'") !== "on") {
        /* create a context we can use to look for DIDs */
        $context = "dundi-full-did-list";
        $dids = core_did_list();
        foreach ($dids as $did) {
            $ext->add($context, $did["extension"], "", new ext_noop($did["description"]));
        }
        $dids = core_directdid_list();
        foreach ($dids as $did) {
            $ext->add($context, $did["extension"], "", new ext_noop($did["description"]));
        }

        /* create a context we can use to look for local extensions */
        $context = "dundi-full-ext-list";
        /* don't use core_users_list because it filters against permissions */
        $users = $db->getAll("SELECT extension, name FROM users ORDER BY extension");
        foreach ($users as $v) {
            $ext->add($context, $v[0], "", new ext_noop($v[1]));
        }
    }
    /* create the user-defined contexts */
    $contexts = Dundi::get_contexts("", false);
    foreach($contexts as $v) {
        $context = $v["name"];
        foreach($v["entries"] as $e) {
            $e = preg_replace("/^(did|ext)-/", "", $e);
            $ext->add($context, $e, "", new ext_noop());
        }
    }
    return true;
}

?>