<?php

//General settings
$rnav_list = "<li><a $g_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=general\">General settings</a></li>\n";

//Mappings
$rnav_list .= "\t\t<li class=\"ui-menu-divider ui-menu-item\">Mappings</li>\n";
$rnav_list .= "\t\t<li><a $m_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=mappings&amp;item=0\">New mapping</a></li>\n";
foreach(Dundi::get_mappings() as $v) {
    $m_class = ($item === $v["name"]) ? "class=\"ui-state-active current\"" : "";
    $rnav_list .= "\t\t<li><a $m_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=mappings&amp;item=$v[name]\">$v[descr]</a></li>\n";
}

//Peers
$rnav_list .= "\t\t<li class=\"ui-menu-divider ui-menu-item\">Peers</li>\n";
$rnav_list .= "\t\t<li><a $p_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=peers&amp;item=0\">New peer</a></li>\n";
foreach(Dundi::get_peers() as $v) {
    $p_class = ($item === $v["peerid"]) ? "class=\"ui-state-active current\"" : "";
    $rnav_list .= "\t\t<li><a $p_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=peers&amp;item=$v[peerid]\">$v[descr]</a></li>\n";
}

//Contexts
$rnav_list .= "\t\t<li class=\"ui-menu-divider ui-menu-item\">Contexts</li>\n";
$rnav_list .= "\t\t<li><a $c_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=contexts&amp;item=0\">New context</a></li>\n";
foreach(Dundi::get_contexts() as $v) {
    $c_class = ($item === $v["name"]) ? "class=\"ui-state-active current\"" : "";
    $rnav_list .= "\t\t<li><a $c_class href=\"$_SERVER[PHP_SELF]?display=dundi&amp;action=contexts&amp;item=$v[name]\">$v[descr]</a></li>\n";
}

//Trunks
$rnav_list .= "\t\t<li class=\"ui-menu-divider ui-menu-item\">Trunks</li>\n";
foreach(core_trunks_listbyid() as $v) {
    if ($v["tech"] === "dundi") {
        array_map('htmlspecialchars', $v);
        $rnav_list .= "\t\t<li><a href=\"$_SERVER[PHP_SELF]?display=trunks&amp;extdisplay=OUT_$v[trunkid]\">$v[name]</a></li>\n";
    }
}

$html = "<div class=\"rnav\">";
$html .= "<ul>$rnav_list</ul>";
$html .= "</div>";
echo $html;
?>