<br />
<h1>Install Modules</h1>

<p>
The following modules are available but have not yet been installed.
</p>

<p>
Click <b>Install</b> to install a module. <b>Any data from a previous install will be <u>destroyed utterly</u>!</b>
</p>

<table cellpadding="5px" cellspacing="0px" border="0px" class="tbl">
<tr>
<th nowrap class="tblhead1" onMouseOver="this.className='tblhead2'" onMouseOut="this.className='tblhead1'">Tag</th>
<th nowrap class="tblhead1" onMouseOver="this.className='tblhead2'" onMouseOut="this.className='tblhead1'">Name</th>
<th nowrap class="tblhead1" onMouseOver="this.className='tblhead2'" onMouseOut="this.className='tblhead1'">Title</th>
<th nowrap class="tblhead1" onMouseOver="this.className='tblhead2'" onMouseOut="this.className='tblhead1'">Description</th>
<th nowrap class="tblhead1" onMouseOver="this.className='tblhead2'" onMouseOut="this.className='tblhead1'"></th>
<!-- BEGIN BLOCK: imod_row -->
<tr onMouseOver="this.style.backgroundColor='#ebf7fe';" onMouseOut="this.style.backgroundColor='#fbfbfb';">
<td class="tblrow1">{mod_tag}</td>
<td class="tblrow1">{mod_name}</td>
<td class="tblrow1">{mod_title}</td>
<td class="tblrow1">{mod_description}</td>
<td class="tblrow1"><a href="{dir_admin_base}modules.php?a=install&mod_tag={mod_tag}">Install</a></td>
</tr>
<!-- END BLOCK: imod_row -->
<!-- BEGIN BLOCK: imod_norow -->
<tr onMouseOver="this.style.backgroundColor='#ebf7fe';" onMouseOut="this.style.backgroundColor='#fbfbfb';">
<td colspan="5" class="tblrow1">
No uninstalled modules were found. Upload a valid Kytoo module to your <code>modules/</code> directory.
</td>
</tr>
<!-- END BLOCK: imod_norow -->
</table>

