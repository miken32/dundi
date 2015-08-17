<form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>?display=dundi&amp;action=mappings&amp;item=<?php echo $item?>">
	<table summary="Ugly-ass layout table">
		<tr>
			<td colspan="2">
				<h3>Local Mappings</h3>
				<p>Mappings define how the local system responds to incoming queries from remote peers.<br/>They relate DUNDi contexts to contexts on the local Asterisk system.</p>
				<hr/>
			</td>
		<tr>
			<td><label class="info" for="dundi_mapdescr">Description <span>A description for the mapping (not parsed)</span></label></td>
			<td><input name="mapping_descr" id="dundi_mapdescr" value="<?php echo $descr?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[mapping_descr]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapname">DUNDi context name <span>The name of the DUNDi context; remote peers will use this to query your system.</span></label></td>
			<td><input name="mapping_name" id="dundi_mapname" value="<?php echo $name?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[mapping_name]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapcontext">Asterisk lookup context <span>Which context in your dialplan to look up answers from. For DIDs in a FreePBX setup, try <var>ext-did</var>. For local extensions, try <var>ext-local</var>.</span></label></td>
			<td><input name="mapping_context" id="dundi_mapcontext" value="<?php echo $context?>" pattern="^[a-z0-9A-Z_\-]+$" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[mapping_context]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapweight">Weight <span>If there are multiple peers responding to a query, use different weights to prioritize some responses over others.<br/>Can be specified as a number, an Asterisk global variable (e.g. <code>${VARNAME}</code>) or a dialplan function (e.g. <code>${SHELL(some_script)}</code>.)</span></label></td>
			<td><input name="mapping_weight" id="dundi_mapweight" value="<?php echo $weight?>" pattern="^([0-5]?\d{1,4}|\$\{.*\})$" maxlength="512" size="40"/> <?php echo "<span class=\"error\">$err[mapping_weight]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_maptech">Technology <span>How should remote peers connect to you to complete calls you've answered to.<br/>Typically <var>SIP</var> or <var>IAX</var>, but any valid and registered technology channel driver will do.</span></label></td>
			<td><input name="mapping_tech" id="dundi_maptech" value="<?php echo $tech?>" maxlength="8" size="40"/> <?php echo "<span class=\"error\">$err[mapping_tech]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapdest">Destination <span>Where should remote peers connect to complete calls you've answered to. The variables <var>\${NUMBER}</var>, <var>\${IPADDR}</var>, and <var>\${SECRET}</var> can be used.</span></label></td>
			<td><input name="mapping_dest" id="dundi_mapdest" value="<?php echo $dest?>" maxlength="$dest_length" size="40"/> <?php echo "<span class=\"error\">$err[mapping_dest]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapnounsolicited">Do not accept unsolicited calls <span>Used in public networks. Check the box if this DUNDi context does not accept unsolicited calls.</span></label></td>
			<td><input type="checkbox" name="mapping_nounsolicited" id="dundi_mapnounsolicited" <?php echo $nounsolicited_check?>/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapnocomunsolicit">Do not accept unsolicited commercial calls <span>Used in public networks. Check the box if this DUNDi context does not accept unsolicited commercial calls.</span></label></td>
			<td><input type="checkbox" name="mapping_nocomunsolicit" id="dundi_mapnocomunsolicit" <?php echo $nocomunsolicit_check?>/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapresidential">Residential numbers? <span>Used in public networks. Check the box if this DUNDi context answers for residential numbers.</span></label></td>
			<td><input type="checkbox" name="mapping_residential" id="dundi_mapresidential" <?php echo $residential_check?>/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapcommercial">Commercial numbers? <span>Used in public networks. Check the box if this DUNDi context answers for commercial numbers.</span></label></td>
			<td><input type="checkbox" name="mapping_commercial" id="dundi_mapcommercial" <?php echo $commercial_check?>/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapmobile">Mobile numbers? <span>Used in public networks. Check the box if this DUNDi context answers for mobile numbers.</span></label></td>
			<td><input type="checkbox" name="mapping_mobile" id="dundi_mapmobile" <?php echo $mobile_check?>/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_mapnopartial">Do not respond to partial numbers <span>Check the box if this DUNDi context will not respond to a partial number.</span></label></td>
			<td><input type="checkbox" name="mapping_nopartial" id="dundi_mapnopartial" <?php echo $nopartial_check?>/></td>
		</tr>
	</table>
	<p>
		<input type="hidden" name="item" value="<?php echo $item?>"/>
		<button class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" role="button">Submit</button>
	</p>
</form>
