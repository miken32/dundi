<form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>?display=dundi&amp;action=general">
	<table summary="Ugly-ass layout table">
		<tr>
			<td colspan="2">
				<h3>General Settings</h3>
				<p>These settings control the behaviour of DUNDi.</p>
				<hr/>
			</td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_organization">Organization <span>Used when answering queries from remote systems.</span></label></td>
			<td><input name="organization" id="dundi_organization" value="<?php echo $organization?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[organization]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_department">Department <span>Used when answering queries from remote systems.</span></label></td>
			<td><input name="department" id="dundi_department" value="<?php echo $department?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[department]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_locality">Locality <span>Used when answering queries from remote systems. Typically, a city.</span></label></td>
			<td><input name="locality" id="dundi_locality" value="<?php echo $locality?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[locality]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_stateprov">Province/State <span>Used when answering queries from remote systems.</span></label></td>
			<td><input name="stateprov" id="dundi_stateprov" value="<?php echo $stateprov?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[stateprov]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_email">Email <span>Used when answering queries from remote systems.</span></label></td>
			<td><input name="email" id="dundi_email" value="<?php echo $email?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[email]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_phone">Phone <span>Used when answering queries from remote systems. Enter in E.164 format (eg <var>+12501234567</var>.)</span></label></td>
			<td><input name="phone" id="dundi_phone" value="<?php echo $phone?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[phone]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_bindaddr">Bind address <span>IP address to bind to.<br/>Defaults to <var>0.0.0.0</var> (bind to all interfaces.)</span></label></td>
			<td><input name="bindaddr" id="dundi_bindaddr" value="<?php echo $bindaddr?>" pattern="^((1?\d?\d|2([0-4]\d|5[0-5]))\.){3}(1?\d?\d|2([0-4]\d|5[0-5]))?$" maxlength="17" size="40"/> <?php echo "<span class=\"error\">$err[bindaddr]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_port">Bind port <span>UDP port number to bind to.<br/>Defaults to <var>4520</var>.</span></label></td>
			<td><input type="number" name="port" id="dundi_port" value="<?php echo $port?>" min="1" max="65535" step="1" maxlength="5" size="40"/> <?php echo "<span class=\"error\">$err[port]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_tos">ToS <span>Terms of service for DUNDi signalling. See <a href="https://wiki.asterisk.org/wiki/display/AST/IP+Quality+of+Service">the documentation</a> for details.<br/>Defaults to <var>ef</var> (expidited forwarding.)</span></label></td>
			<td><select name="tos" id="dundi_tos">
				<option <?php echo ($tos === "ef" ? "selected='selected'" : "")?>>ef</option>
				<option <?php echo ($tos === "CS0" ? "selected='selected'" : "")?>>CS0</option>
				<option <?php echo ($tos === "CS1" ? "selected='selected'" : "")?>>CS1</option>
				<option <?php echo ($tos === "CS2" ? "selected='selected'" : "")?>>CS2</option>
				<option <?php echo ($tos === "CS3" ? "selected='selected'" : "")?>>CS3</option>
				<option <?php echo ($tos === "CS4" ? "selected='selected'" : "")?>>CS4</option>
				<option <?php echo ($tos === "CS5" ? "selected='selected'" : "")?>>CS5</option>
				<option <?php echo ($tos === "CS6" ? "selected='selected'" : "")?>>CS6</option>
				<option <?php echo ($tos === "CS7" ? "selected='selected'" : "")?>>CS7</option>
				<option <?php echo ($tos === "AF11" ? "selected='selected'" : "")?>>AF11</option>
				<option <?php echo ($tos === "AF12" ? "selected='selected'" : "")?>>AF12</option>
				<option <?php echo ($tos === "AF13" ? "selected='selected'" : "")?>>AF13</option>
				<option <?php echo ($tos === "AF21" ? "selected='selected'" : "")?>>AF21</option>
				<option <?php echo ($tos === "AF22" ? "selected='selected'" : "")?>>AF22</option>
				<option <?php echo ($tos === "AF23" ? "selected='selected'" : "")?>>AF23</option>
				<option <?php echo ($tos === "AF31" ? "selected='selected'" : "")?>>AF31</option>
				<option <?php echo ($tos === "AF32" ? "selected='selected'" : "")?>>AF32</option>
				<option <?php echo ($tos === "AF33" ? "selected='selected'" : "")?>>AF33</option>
				<option <?php echo ($tos === "AF41" ? "selected='selected'" : "")?>>AF41</option>
				<option <?php echo ($tos === "AF42" ? "selected='selected'" : "")?>>AF42</option>
				<option <?php echo ($tos === "AF43" ? "selected='selected'" : "")?>>AF43</option>
			</select> <?php echo "<span class=\"error\">$err[tos]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_entityid">Entity ID <span>Entity ID, typically the MAC address of the bound interface.</span></label></td>
			<td><input name="entityid" id="dundi_entityid" value="<?php echo $entityid?>" pattern="^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$" maxlength="17" size="40"/> <?php echo "<span class=\"error\">$err[entityid]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_cachetime">Cache time <span>This is the time (in seconds) to cache, sent back with our answers; this does not specify how long answers from remote peers are cached locally.<br/>Defaults to <var>3600</var>.</span></label></td>
			<td><input type="number" name="cachetime" id="dundi_cachetime" value="<?php echo $cachetime?>" min="0" max="999999" step="1" maxlength="6" size="40"/> <?php echo "<span class=\"error\">$err[cachetime]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_ttl">TTL <span>Time to live for queries, this can be used to limit the number of hops a query can take.<br/>Defaults to <var>120</var></span></label></td>
			<td><input type="number" name="ttl" id="dundi_ttl" value="<?php echo $ttl?>" min="1" max="120" step="1" maxlength="3" size="40"/> <?php echo "<span class=\"error\">$err[ttl]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_autokill">Autokill? <span>Lets queries get killed if they take too long. Set to <var>yes</var>, <var>no</var>, or a time in milliseconds.<br/>Defaults to <var>no</var>, equivalent to <var>0</var>; <var>yes</var> is equivalent to <var>2000</var>.</span></label></td>
			<td><input name="autokill" id="dundi_autokill" value="<?php echo $autokill?>" pattern="^(yes|no|\d{1,6})?$" maxlength="6" size="40"/> <?php echo "<span class=\"error\">$err[autokill]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_secretpath">Secret path <span>The location in the database where the secret is stored.<br/>Defaults to <var>dundi</var>.</span></label></td>
			<td><input name="secretpath" id="dundi_secretpath" value="<?php echo $secretpath?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[secretpath]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_storehistory">Store history? <span>Whether or not to store statistics on the last few lookups.<br/>Defaults to <var>no</var>.</span></label></td>
			<td><input type="checkbox" name="storehistory" id="dundi_storehistory" value="on" <?php echo $storehistory_check?>/></td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Module Settings</h3>
				<hr/>
			</td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_nocontextcreate">Don't auto-create contexts <span>Contexts containing all DIDs (<code>dundi-full-did-list</code>) and all extensions (<code>dundi-full-ext-list</code>) are created automatically by this module.<br/>To suppress their creation, check this box; you will need to use an existing context such as <code>ext-did</code>, or create your own contexts either within this module or in <code>extensions_custom.conf</code>.</span></label></td>
			<td><input type="checkbox" name="nocontextcreate" id="dundi_nocontextcreate" value="on" <?php echo $nocontext_check?>/></td>
		</tr>
	</table>
	<p>
		<button class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" role="button">Submit</button>
	</p>
</form>
