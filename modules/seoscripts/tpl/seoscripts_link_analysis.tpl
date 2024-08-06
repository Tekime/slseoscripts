<table width="100%" cellpadding="5px" cellspacing="0px" border="0px" class="grid">
<tr>
    <th colspan="3">Outgoing Links ({analysis_outgoing_count} total)</th>
</tr>
<tr>
    <th class="th2" colspan="2">URL Details</th>
    <th class="th2" align="center">Nofollow?</th>
</tr>
<!-- BEGIN BLOCK: analysis_outgoing_links -->
<tr><td valign="top" align="center"><strong>#{row_count}</strong></td><td valign="top"><strong>URL:</strong> {link_href}<br /><strong>Anchor:</strong>{link_anchor_safe}<br /><strong>Link:</strong> <a href="{link_href}">{link_anchor}</a></td><td valign="top" align="center">{link_nofollow}</td></tr>
<!-- END BLOCK: analysis_outgoing_links -->
</table>
<br />

<table width="100%" cellpadding="5px" cellspacing="0px" border="0px" class="grid">
<tr>
    <th colspan="3">Inner Links ({analysis_inner_count} total)</th>
</tr>
<tr>
    <th class="th2" colspan="2">URL Details</th>
    <th class="th2">Nofollow?</th>
</tr>
<!-- BEGIN BLOCK: analysis_inner_links -->
<tr><td valign="top" align="center"><strong>#{row_count}</strong></td><td valign="top"><strong>URL:</strong> {link_href}<br /><strong>Anchor:</strong>{link_anchor_safe}<br /><strong>Link:</strong> <a href="{link_href}">{link_anchor}</a></td><td valign="top" align="center">{link_nofollow}</td></tr>
<!-- END BLOCK: analysis_inner_links -->
</table>
