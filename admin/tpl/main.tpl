
{kAppMessages()}

<table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
<tr>
<td width="50%" valign="top">

    {showPanels('admin')}


    <div class="panel1" id="panelhelp">
    <div class="panel1-header">Usage Statistics</div>
    <div class="panel1-body">
    {seoscriptsUsageStats()}
    </div>
    </div>
    
    <p style="padding:0px;margin:0px;height:1px;overflow:hidden;">&nbsp;</p>
    
    <br />
    
</td>
<td width="50%" valign="top">

    <div class="panel1" id="panelhelp">
    <div class="panel1-header">Help and Resources</div>
    <div class="panel1-body">
    <h2><a href="{lang_app_url_manual}" target="_new">{lang_app_name} Manual</a></h2>
    <p>The official manual for {lang_app_title} included with version {app_version}. Contains the latest instructions and information on using {lang_app_name}.</p>

    <h2><a href="{lang_app_url_docs}" target="_new">Online Documentation</a></h2>
    <p>Additional documentation, guides, and the latest manual for {lang_app_name} available online.</p>
    
    <h2><a href="{lang_app_url_forums}" target="_new">{lang_app_name} Forums</a></h2>
    <p>The official {lang_app_name} community forums. Find answers to your questions and discuss {lang_app_name} with other users.</p>

    <h2><a href="{lang_app_url_support}" target="_new">Technical Support</a></h2>
    <p>Contact our support department for technical problems and issues that are not covered in the <a href="{lang_app_url_docs}" target="-new">online documentation</a>.</p>

    </div>
    </div>


    <div class="panel1" id="panelblog">   
    <div class="panel1-header">{lang_app_blog_name}</div>
    <div class="panel1-body">
    {rssFeed({lang_app_blog_feedurl},5,150)}
    </div>
    </div>

    <div class="panel1" id="panelserver">   
    <div class="panel1-header">Server Information</div>
    <div class="panel1-body">
    <h2>PHP Version</h2>
    <p>{stats_phpversion}</p>
    
    <h2>Server Software</h2>
    <p>{stats_serversoftware}</p>
    
    <h2>Loaded PHP Extensions</h2>
    <p>{stats_loadedextensions}</p>
    
    <h2>MySQL Version</h2>
    <p>{stats_mysqlinfo}</p>

    <h2>MySQL Connection Status</h2>
    <p>{stats_mysqlhostinfo}</p>

    </div>
    </div>

    <p style="padding:0px;margin:0px;height:1px;overflow:hidden;">&nbsp;</p>

</td>
</tr>
</table>

<br clear="all" />

