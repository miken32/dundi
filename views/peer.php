<form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>?display=dundi&amp;action=peers&amp;item=<?php echo $item?>">
	<table summary="Ugly-ass layout table">
		<tr>
			<td colspan="2">
				<h3>Remote Peers</h3>
				<p>These are the peers (i.e. remote systems) that we trust to <br/>answer our queries, or that we will answer queries from.</p>
				<hr/>
			</td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerdescr">Description <span>A description for the peer (not parsed)</span></label></td>
			<td><input name="peer_descr" id="dundi_peerdescr" value="<?php echo $descr?>" maxlength="80" size="40"/></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerid">Entity ID <span>The remote peer's entity ID</span></label></td>
			<td><input name="peer_peerid" id="dundi_peerid" value="<?php echo $peerid?>" pattern="^(([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2})|(\*)$" maxlength="17" size="40"/> <?php echo "<span class=\"error\">$err[peer_peerid]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerinkey">Inbound key <span>The public key to use for authenticating inbound connections. The file is expected to be in <code><?php echo $amp_conf["ASTVARLIBDIR"]?>/keys</code> with a <code>.pub</code> suffix.</span></label></td>
			<td>
				<select name="peer_inkey" id="dundi_peerinkey">
	<?php foreach(glob("$amp_conf[ASTVARLIBDIR]/keys/*.pub") as $key): $key = basename($key, ".pub"); ?>
					<option value="<?php echo $key?>" <?php if ($inkey === $key) echo "selected=\"selected\""?>><?php echo $key?>.pub</option>
	<?php endforeach;?>
				</select> <?php echo "<span class=\"error\">$err[peer_inkey]</span>"?>
			</td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peeroutkey">Outbound key <span>The private key to use for outbound authentication. The file is expected to be in <code><?php echo $amp_conf["ASTVARLIBDIR"]?>/keys</code> with a <code>.key</code> suffix.</span></label></td>
			<td>
				<select name="peer_outkey" id="dundi_peeroutkey">
	<?php foreach(glob("$amp_conf[ASTVARLIBDIR]/keys/*.key") as $key): $key = basename($key, ".key"); ?>
					<option value="<?php echo $key?>" <?php if ($outkey === $key) echo "selected=\"selected\""?>><?php echo $key?>.key</option>
	<?php endforeach;?>
				</select> <?php echo "<span class=\"error\">$err[peer_outkey]</span>"?>
			</td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerhost">Host <span>The peer's name or IP address, or <var>dynamic</var>.</span></label></td>
			<td><input name="peer_host" id="dundi_peerhost" value="<?php echo $host?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[peer_host]</span>"?></td>
		</tr>
	<?php if (Dundi::peer_ports()): ?>
		<tr>
			<td><label class="info" for="dundi_peerport">Port <span>The peer's UDP port number.</span></label></td>
			<td><input type="number" name="peer_port" id="dundi_peerport" value="<?php echo $port?>" min="1" max="65535" step="1" maxlength="5" size="40"/> <?php echo "<span class=\"error\">$err[peer_port]</span>"?></td>
		</tr>
	<?php endif;?>
		<tr>
			<td><label class="info" for="dundi_peerqualify">Qualify <span>Whether or not to qualify peers. Set to <var>yes</var>, <var>no</var>, or a time in milliseconds.<br/>Defaults to <var>yes</var>, equivalent to <var>2000</var>; <var>no</var> is equivalent to <var>0</var>.</span></label></td>
			<td><input name="peer_qualify" id="dundi_peerqualify" value="<?php echo $qualify?>" pattern="^(yes|no|\d{1,6})?$" maxlength="6" size="40"/> <?php echo "<span class=\"error\">$err[peer_qualify]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerord">Search order <span>The search order for this peer. There can be multiple peers at the same search order.<br/>Defaults to <var>primary</var></span></label></td>
			<td><select name="peer_ord" id="dundi_peerord">
				<option value=""></option>
				<option <?php echo ($ord === "primary" ? "selected='selected'" : "")?>>primary</option>
				<option <?php echo ($ord === "secondary" ? "selected='selected'" : "")?>>secondary</option>
				<option <?php echo ($ord === "tertiary" ? "selected='selected'" : "")?>>tertiary</option>
				<option <?php echo ($ord === "quartiary" ? "selected='selected'" : "")?>>quartiary</option>
			</select> <?php echo "<span class=\"error\">$err[peer_ord]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerinclude">Include in queries <span>Controls queries from this system.<br/>Defaults to <var>all</var> (include this peer in all queries) or use a DUNDi context name to only query the peer for numbers in that DUNDi context.</span></label></td>
			<td><input name="peer_include" id="dundi_peerinclude" value="<?php echo $include?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[peer_include]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peernoinclude">Exclude from queries <span>Controls queries from this system.<br/>Set to <var>all</var> to exclude this peer from all queries, or use a DUNDi context name to query the peer for numbers in all but that DUNDi context.</span></label></td>
			<td><input name="peer_noinclude" id="dundi_peernoinclude" value="<?php echo $noinclude?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[peer_noinclude]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerpermit">Permit lookups <span>Controls queries to this system.<br/>Defaults to <var>all</var> (allow this peer to query all our DUNDi contexts) or use a DUNDi context name to only allow queries to that DUNDi context.</span></label></td>
			<td><input name="peer_permit" id="dundi_peerpermit" value="<?php echo $permit?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[peer_permit]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerdeny">Deny lookups <span>Controls queries to this system.<br/>Set to <var>all</var> to prevent this peer from querying any of our DUNDi contexts, or use a DUNDi context name to only prevent queries to that DUNDi context.</span></label></td>
			<td><input name="peer_deny" id="dundi_peerdeny" value="<?php echo $deny?>" maxlength="80" size="40"/> <?php echo "<span class=\"error\">$err[peer_deny]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peermodel">Model <span>Can this peer only answer us (<var>outbound</var>), only query us (<var>inbound</var>), both (<var>symmetric</var>), or neither (<var>none</var>)?</span></label></td>
			<td><select name="peer_model" id="dundi_peermodel">
				<option value=""></option>
				<option <?php echo ($model === "symmetric" ? "selected='selected'" : "")?>>symmetric</option>
				<option <?php echo ($model === "outbound" ? "selected='selected'" : "")?>>outbound</option>
				<option <?php echo ($model === "inbound" ? "selected='selected'" : "")?>>inbound</option>
				<option <?php echo ($model === "none" ? "selected='selected'" : "")?>>none</option>
			</select> <?php echo "<span class=\"error\">$err[peer_model]</span>"?></td>
		</tr>
		<tr>
			<td><label class="info" for="dundi_peerprecache">Precache model <span>Can this peer get sent precache requests (<var>outbound</var>), do we accept its precache requests (<var>inbound</var>), both (<var>symmetric</var>), or neither (<var>none</var>)?</span></label></td>
			<td><select name="peer_precache" id="dundi_peerprecache">
				<option value=""></option>
				<option <?php echo ($precache === "symmetric" ? "selected='selected'" : "")?>>symmetric</option>
				<option <?php echo ($precache === "outbound" ? "selected='selected'" : "")?>>outbound</option>
				<option <?php echo ($precache === "inbound" ? "selected='selected'" : "")?>>inbound</option>
				<option <?php echo ($precache === "none" ? "selected='selected'" : "")?>>none</option>
			</select> <?php echo "<span class=\"error\">$err[peer_precache]</span>"?></td>
		</tr>
	</table>
	<p>
		<input type="hidden" name="item" value="<?php echo $item?>"/>
		<button class="ui-button ui-widget ui-state-default ui-corner-all" type="submit" role="button">Submit</button>
	</p>
</form>