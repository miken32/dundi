<?php
$html = heading(_("DUNDi Contexts"), 2);
if (isset($message)) {
    $html .= "<p class='dundi_infobox'>$message</p>";
}
if (isset($errormessage)) {
    $html .= "<p class='dundi_infobox northern911_error'>$errormessage</p>";
}

$html .= "<p>";
$html .= _("Here you can create a list of DIDs or extensions that you will refer to in a DUNDi context.");
$html .= "</p>";

$html .= "<p>";
$html .= "<a href=\"config.php?display=dundi&amp;action=delcontext&amp;item=$item\">";
$html .= sprintf(_("Delete context %s"), $item);
$html .= "</a>";
$html .= "</p>";

$html .= form_open("config.php?display=dundi", "", array("item"=>$item));

$table = new CI_Table;

$label = fpbx_label(_("Description"), _("A description for the context (not parsed)"));
$atts = array("name"=>"context_descr", "value"=>$descr, "maxlength"=>80, "size"=>40);
$input = form_input($atts);
$table->add_row($label, array("data"=>"$input <span class=\"error\">$err[context_descr]</span>", "colspan"=>"3"));

$label = fpbx_label(_("Context name"), _("The name of the Asterisk context; after creation, this can be included in your DUNDi mapping definition."));
$atts = array("name"=>"context_name", "id"=>"dundi_ctxname", "value"=>$name, "pattern"=>"^[a-z0-9A-Z_\-]+$", "maxlength"=>"80", "size"=>"40");
$input = form_input($atts);
$table->add_row($label, array("data"=>"$input <span class=\"error\">$err[context_name]</span>", "colspan"=>"3"));

$label = fpbx_label(_("Context entries"), _("Select an extension or DID that will be included in the Asterisk context"));
for ($i = 0; $i < count($entries); $i++) {
    $e = $entries[$i];
    $extsel = substr($e, 0, 3) === "ext" ? "selected=\"selected\"" : "";
    $didsel = substr($e, 0, 3) === "did" ? "selected=\"selected\"" : "";
    $cussel = substr($e, 0, 3) === "cus" ? "selected=\"selected\"" : "";
    $td1 = <<< HTML
		<select class="dundi_ctxentry_type">
			<option value="">== choose one ==</option>
			<option $extsel value="ext">Local Extension</option>
			<option $didsel value="did">DID</option>
			<option $cussel value="cus">Custom</option>
		</select>

HTML;

    switch (substr($e, 0, 3)) {
        case "ext":
            $td2 = str_replace("value=\"$e\"", "value=\"$e\" selected=\"selected\"", $context_entry_extensions);
            break;
        case "did":
            $td2 = str_replace("value=\"$e\"", "value=\"$e\" selected=\"selected\"", $context_entry_dids);
            break;
        case "cus":
            $td2 = str_replace("value=\"\"", "value=\"$e\"", $context_entry_custom);
            break;
        default:
            $td2 = "<span></span>";
            break;
    }

    if($i === count($entries) - 1) { //last entry
        $td3 = "<img src=\"images/core_add.png\" id=\"dundi_ctxentry_more\" alt=\"" . _("Add") . "\"/>";
    } else {
        $td3 = "<img src=\"images/core_delete.png\" class=\"dundi_ctxentry_del\" alt=\"" . _("Delete") . "\"/>";
    }

    if ($i === 0) {
        $table->add_row(array("data"=>$label, "rowspan"=>count($entries)), array("data"=>$td1, "class"=>"dundi_ctx_entry"), $td2, $td3);
    } else {
        $table->add_row(array("data"=>$td1, "class"=>"dundi_ctx_entry"), $td2, $td3);
    }
}

$html .= $table->generate();
$html .= "<p>";
$html .= form_submit("action", _('Save'));
$html .= "</p>";
$html .= form_close();

$html .= str_replace("<select", "<select id=\"dundi_ctx_ext\" style=\"display:none\"", $context_entry_extensions);
$html .= str_replace("<select", "<select id=\"dundi_ctx_did\" style=\"display:none\"", $context_entry_dids);
$html .= "<input id=\"dundi_ctx_cus\" style=\"display: none\"/>";

echo $html;
?>