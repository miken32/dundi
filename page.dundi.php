<?php
$v["module"] = "dundi";

if (isset($_GET["action"])) {
    $v["action"] = strtolower($_GET["action"]);
} elseif (isset($_POST["action"])) {
    $v["action"] = strtolower($_POST["action"]);
} else {
    $v["action"] = "general";
}
$cwd = dirname(__FILE__);
$item = $v["item"] = isset($_GET["item"]) ? $_GET["item"] : "0";

if (!empty($_POST)) {
    $err = Dundi::save_post($action);
} else {
    $err = array();
}
//all this to avoid "undefined index" errors in the log...
$empty_errs = array(
    "organization", "department", "locality", "stateprov", "email", "phone", "bindaddr", "port", "entityid",
    "tos", "cachetime", "ttl", "autokill", "secretpath", "mapping_descr", "mapping_name", "mapping_context",
    "mapping_weight", "mapping_tech", "mapping_dest", "peer_descr", "peer_peerid", "peer_host", "peer_port",
    "peer_inkey", "peer_outkey", "peer_ord", "peer_qualify", "peer_include", "peer_noinclude", "peer_permit",
    "peer_deny", "peer_model", "peer_precache", "context_descr", "context_name"
);
$empty_errs = array_fill_keys($empty_errs, "");
$v["err"] = array_merge($empty_errs, $err);

$v["g_class"] = $v["m_class"] = $v["p_class"] = $v["c_class"] = "";


if ($action === "general") {

    $v["g_class"] = "class=\"ui-state-active current\"";
    $v = array_merge($v, Dundi::get_general_config());
    $v["storehistory_check"] = isset($v["storehistory"]) && $v["storehistory"] ? "checked=\"checked\"" : "";
    $v["nocontext_check"] = isset($v["nocontextcreate"]) && $v["nocontextcreate"] ? "checked=\"checked\"" : "";
    show_view("$cwd/views/rnav.php", $v);
    show_view("$cwd/views/general.php", $v);

} elseif ($action === "mappings") {

    $v["m_class"] = ($item === "0") ? "class=\"ui-state-active current\"" : "";
    $v = array_merge($v, Dundi::get_mappings($item));
    $v["dest_length"] = Dundi::dest_length();
    foreach(Dundi::$bitflags as $flag) {
        $v[$flag . "_check"] = $v[$flag] ? "checked=\"checked\"" : "";
    }
    show_view("$cwd/views/rnav.php", $v);
    show_view("$cwd/views/mapping.php", $v);

} elseif ($action === "peers") {

    $v["p_class"] = ($item === "0") ? "class=\"ui-state-active current\"" : "";
    $v["amp_conf"] = $amp_conf;
    $v = array_merge($v, Dundi::get_peers($item));
    show_view("$cwd/views/rnav.php", $v);
    show_view("$cwd/views/peer.php", $v);

} elseif ($action === "contexts") {

    $v["c_class"] = ($item === "0") ? "class=\"ui-state-active current\"" : "";
    $v = array_merge($v, Dundi::get_contexts($item));
    //add a blank for a potential new entry
    $v["entries"][] = "";

    $v["context_entry_extensions"] = $v["context_entry_dids"] = "<select name=\"context_entries[]\">";
    $exts = core_users_list(true);
    foreach ($exts as $ext) {
        $v["context_entry_extensions"] .= "<option value=\"ext-$ext[0]\">&lt;$ext[0]&gt; $ext[1]</option>";
    }
    $v["context_entry_extensions"] .= "</select>";

    $dids = core_did_list();
    foreach ($dids as $did) {
        $v["context_entry_dids"] .= "<option value=\"did-$did[extension]\">&lt;$did[extension]&gt; $did[description]</option>";
    }
    $v["context_entry_dids"] .= "</select>";

    $v["context_entry_custom"] = "<input name=\"context_entries[]\" value=\"\"/>";

    show_view("$cwd/views/rnav.php", $v);
    show_view("$cwd/views/context.php", $v);

} else {
    show_view("$cwd/views/rnav.php", $v);
}
