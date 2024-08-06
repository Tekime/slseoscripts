<a name="top"></a>
<h1>phpLinkBid Manual v1.0</h1>

<p>
This document contains instructions and information on phpLinkBid Admin, and is the official manual
for phpLinkBid v1.0.
</p>

<ol>
<li><a href="#install">Installing phpLinkBid</a></li>
<li><a href="#config">Configuring Your Site</a></li>
<ol style="list-style-type:lower-alpha;">
<li><a href="#cfg-site">Site configuration</a></li>
<li><a href="#cfg-auction">Auction configuration</a></li>
</ol>
<li><a href="#mgsite">Managing Your Site</a></li>
<ol style="list-style-type:lower-alpha;">
<li><a href="#mgsite-pages">Managing site content</a></li>
<li><a href="#mgsite-msg">Editing system messages</a></li>
<li><a href="#mgsite-users">Managing user accounts</a></li>
</ol>
<li><a href="#mgauc">Managing Your Auction</a></li>
<ol style="list-style-type:lower-alpha;">
<li><a href="#mgauc-links">Managing links</a></li>
<li><a href="#mgauc-bids">Managing bids</a></li>
</ol>
<li><a href="#cust">Customizing Your Site</a></li>
<ol style="list-style-type:lower-alpha;">
<li><a href="#cust-styles">Customizing styles</a></li>
<li><a href="#cust-tplinst">Installing templates</a></li>
<li><a href="#cust-tpl">Customizing templates</a></li>
<li><a href="#cust-vars">Template variables list</a></li>
</ol>
<li><a href="#ts">Troubleshooting</a></li>
</ol>

<a name="install"></a>
<h2>Installing phpLinkBid</h2>

<p>
Installing phpLinkBid requires a PHP 4.x enabled server and MySQL. Basic knowledge of FTP
and an FTP client will be needed to upload the software to your server. 
</p>

<p>
Once you have successfully purchased phpLinkBid, you will receive an email containing download
instructions. Follow those instructions to download the latest version of phpLinkBid to your
computer in .ZIP format. <a href="http://www.7-zip.org/" target="_new">Unzip</a> the file on your
desktop or somewhere easy to remember. You should now have a folder named phpLinkBid-x.x.zip (where
`x.x` is the version number).
</p>

<p>
Using your <a href="http://filezilla.sourceforge.net/" target="_new">favorite FTP client</a>,
upload the contents of the new folder to your Web server. Make sure you upload all files to the folder
you want the site to run on. Usually this will be `public_html` or `htdocs`, but you can also upload
the files to a subfolder or subdomain on your site.
</p>

<p>
Once all of your files have been uploaded, you must run the install script to complete installation.
Load up your <a href="http://www.getfirefox.com/">favorite browser</a> and head to:
</p>

<b><code>http://www.yourdomain.com/install/</code></b>

<p>
Make sure you replace `www.yourdomain.com` with the URL where you have uploaded phpLinkBid.
</p>

<p> 
The installer will guide you through the rest of the process. Make sure you delete the `install`
folder when complete.
</p>

<a name="config"></a>
<small> <a href="#top">^ Back to top</a></small><br />
<h2>Configuring Your Site</h2>

<p>
The installer will get all of the basic information needed to get your site running, so you can
test it out immediately. To configure the details of your site, log in to phpLinkBid Admin
with the username and password you created during installation by visiting:
</p>

<b><code>http://www.yourdomain.com/admin/</code></b>

<p>
phpLinkBid Admin is divided into two main categories: Site Admin and Auction Admin. You can
configure site settings in Site Configuration, and manage auction settings in Auction Configuration.
You can configure several important settings from each section.
</p>

<a name="cfg-site"></a>
<h3>Site Configuration</h3>

<p>
All of the required settings for Site Configuration are obtained during installation, but you
can also add META description and keywords and change other details. If you have installed a
custom template, you can switch your active template from Site Configuration as well.
</p>

<p>
<i>The URL should always contain a trailing slash (eg. http://www.example.com/)</i>
</p>

<a name="cfg-auc"></a>
<h3>Auction Configuration</h3>

<p>
Auction Configuration allows you to change several details of your auction.
</p>

<table width="90%" cellpadding="2px" cellspacing="0px" border="0px" align="center" class="tbl">
<tr>
<td class="tblrow1" valign="top"><b>PayPal Email Address</b></code></td>
<td class="tblrow2" valign="top">This should be set to the PayPal account that bids will be placed to. Make sure this is set to your
valid PayPal email account and can accept payments.</td>
</tr>

<tr>
<td class="tblrow1" valign="top"><b>PayPal Instant Payment Notification</b></td>
<td class="tblrow2" valign="top">
PayPal Instant Payment Notification (IPN) support automatically notifies phpLinkBid when a bid has
been paid for via PayPal. This allows automatic activation and notification of bids. This should be
enabled unless you really need to turn it off, in which case you must add the bids manually in phpLinkBid
Admin after payment is received.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b># of Links in Top List</b></td>
<td class="tblrow2" valign="top">
This setting lets you specify how many links you want displayed on the homepage. Set to any number
higher than zero (no decimals here, folks).
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b># of Links Per Page</b></td>
<td class="tblrow2" valign="top">
Select how many links you want displayed per page in the directory. Set to any number higher than
zero.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Link Title Max Length</b></td>
<td class="tblrow2" valign="top">
Sets the maximum allowed length for link titles.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Link Description Max Length</b></td>
<td class="tblrow2" valign="top">
Sets the maximum allowed length for each line of link descriptions.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Link Leader</b></td>
<td class="tblrow2" valign="top">
Link leader is the highest bidding link in the directory. Change to display or hide the link leader's sitewide
link.
</td>
</tr>
</table>
<br />


<a name="mgsite"></a>
<small> <a href="#top">^ Back to top</a></small><br />
<h2>Managing Your Site</h2>
<a name="mgsite-pages"></a>
<h3>Managing site content</h3>

<p>
phpLinkBid has a built-in Content Management System for editing existing pages and creating new
content. You can access the CMS under <b>Site Pages</b> in the <b>Site Admin</b> menu. From <b>Site 
Pages</b> you can edit existing pages or create a new page by clicking <b>Create Page</b>.
</p>

<p>
The following fields are available for site pages:
</p>


<table width="90%" cellpadding="2px" cellspacing="0px" border="0px" align="center" class="tbl">
<tr>
<td class="tblrow1" valign="top"><b>Page Name</b></td>
<td class="tblrow2" valign="top">
The name used in the main menu, usually a nice short nice such as "About".
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Page Title</b></td>
<td class="tblrow2" valign="top">
The full name of the page used for the title, for example "About Our Website".
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Safe Name</b></td>
<td class="tblrow2" valign="top">
The URL-safe name for a page, containing no special characters or spaces. For example, "about".
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Page Contents</b></td>
<td class="tblrow2" valign="top">
The contents of the page. Can contain HTML, CSS or JavaScript code.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Sort Order</b></td>
<td class="tblrow2" valign="top">
Sort Order is the position of the page in the main menu, for example <b>1</b> for first position.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Display page in menu?</b></td>
<td class="tblrow2" valign="top">
Lets you display or hide the page in the main menu. Page can still be viewed when hidden by entering the URL.
</td>
</tr>
</table>


<a name="mgsite-msg"></a>
<h3>Editing system messages</h3>

<p>
System messages contains automatic email templates that are used for bid and link notifications.
You can edit these messages to customize them to your auction.
</p>

<table width="90%" cellpadding="2px" cellspacing="0px" border="0px" align="center" class="tbl">
<tr>
<td class="tblrow1" valign="top"><b>Message Title</b></td>
<td class="tblrow2" valign="top">
The title of the message, used for the email subject or notification title.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>Message Text</b></td>
<td class="tblrow2" valign="top">
The contents of the message. Do not use HTML. Message text should be plaintext and email-safe.
</td>
</tr>
</table>

<p>
Dynamic fields are available by surrounding the field name with `[[` and `]]`. For example, 
typing [[site_name]] in the message title or content field will display the site name in the 
final message output.
</p>

<p>
All of the fields in the <a href="#cust-vars">template variables list</a> are available. Bid messages
also have access to <code>bid_amount</code>, <code>bid_url</code> and <code>link_url</code>.
</p>


<a name="mgsite-users"></a>
<h3>Managing user accounts</h3>

<p>
You can access <b>User Accounts</b> from the <b>Site Admin</b> menu. You can create additional user
accounts, and they will have full administration priveleges. Additional accounts should not be created
unless required, such as needing several site editors to review and handle bids.
</p>

<p>
You can change your administrator account information here, including email address, password
and name.
</p> 

<a name="mgauc"></a>
<small> <a href="#top">^ Back to top</a></small><br />
<h2>Managing Your Auction</h2>

<a name="mgauc-links"></a>
<h3>Managing links</h3>

<p>
Administrators can access link management via <b>Manage Links</b> on the <b>Auction Admin</b> menu.
New links can be created by clicking <b>Create Link</b>, and details of existing links can be 
changed with the <b>Edit</b> button for the corresponding link. Links can be deleted with the
corresponding <b>Delete</b> button. Once deleted, a link is permanently removed from the system!
</p>

<p>
Links are not activated in the directory until a bid has been placed.
</p>

<a name="mgauc-bids"></a>
<h3>Managing bids</h3>

<p>
Administrators can access bid management via <b>Manage Bids</b> on the <b>Auction Admin</b> menu.
New bids can be created by clicking <b>Create Bid</b>, and details of existing bids can be 
changed with the <b>Edit</b> button for the corresponding bid. Bids can be deleted with the
corresponding <b>Delete</b> button. Once deleted, a bid is permanently removed from the system!
</p>

<p>
When creating or editing a bid, the link the bid applies to is defined with the selection
box labelled <b>Link</b>. Make sure the correct link has been selected here for the bid.
</p>

<p>
Links require at least one bid to be displayed in the auction directory, so if you are creating a
new link as an administrator remember to add a bid here. If you have disabled IPN you must create
all paid bids here manually.
</p>

<a name="cust"></a>
<small> <a href="#top">^ Back to top</a></small><br />
<h2>Customizing Your Site</h2>

<p>
phpLinkBid uses a custom template system that separates the HTML and CSS of your site from the PHP
code. Templates are basically collections of HTML files with the <code>.tpl</code> extension, 
stylesheet files and image files.
</p>

<p>
Templates reside in the <code>tpl/</code> folder in your script installation directory. Files for each
template are stored in their own folder, and the folder name is used as the template name and identifier
in phpLinkBid. The active template can be set in <b>Site Configuration</b>, and the default template is <code>phpl</code>.
</p>

<a name="cust-styles"></a>
<h3>Customizing styles</h3>

<p>
The easiest way to start customizing phpLinkBid is to edit the <code>style.css</code> file residing
in your template folder. The default location of this file is <code>SCRIPT_PATH/tpl/phplb/style.css</code>,
where SCRIPT_PATH is the path to your installation of phpLinkBid.
</p>

<p>
<code>style.css</code> is divided into several main sections for layout styles, menu styles, link
styles and form styles.
</p>

<a name="cust-tplinst"></a>
<h3>Installing templates</h3>

<p>
If you have downloaded a new template and unzipped it on your computer, you should have a folder
containing all of the template files with the name <code>templatename</code> (replace with whatever
the template happens to be named).
</p>

<p>
To install the template, upload the folder and it's contents to <code>SCRIPT_PATH/tpl/</code>, where
SCRIPT_PATH is the path to your installation of phpLinkBid. Now go to <b>Site Configuration</b>
and you can select the new template from the selection box here and click <b>Save</b> to use it
immediately.
</p>

<a name="cust-tpl"></a>
<h3>Customizing templates</h3>

<p>
You may wish to customize some things that cannot be controlled with the CSS file, which means
editing the template files. You can edit individual template files and save to see changes
immediately.
</p>

<p>
It is recommended to make a copy of the default template folder <code>phplb</code> and work on the
new template, so you can always revert back to the old template or refer to the old template files.
Make a copy of the <code>phplb</code> folder and name it something short and easy to read - no spaces
or special characters. For example, <code>starburst</code> or <code>redwine2</code>. You can then
go to <b>Site Configuration</b> and select the new template name from the selection box to use it
immediately.
</p>

<p>
You can now edit any template file, image or stylesheet you choose. Do not change the names of
any template files or you risk breaking your site. The file structure of a template must remain
intact, and is as follows.
</p>


<table width="90%" cellpadding="2px" cellspacing="0px" border="0px" align="center" class="tbl">
<tr>
<td class="tblrow1" valign="top"><b>/images/</b></td>
<td class="tblrow2" valign="top">Folder containing images for the template.
</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>bid.tpl</b></td>
<td class="tblrow2" valign="top">Bid confirmation screen - the final confirmation before payment.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>error404.tpl</b></td>
<td class="tblrow2" valign="top">The default error message displayed when a visitor requests a page that doesn't exist.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>footer.tpl</b></td>
<td class="tblrow2" valign="top">The overall site footer template.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>functions.js</b></td>
<td class="tblrow2" valign="top">JavaScript file containing a few JS functions.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>header.tpl</b></td>
<td class="tblrow2" valign="top">The overall site header template.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>link.tpl</b></td>
<td class="tblrow2" valign="top">This is the link details template used for individual links in the auction.</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>link_preview.tpl</b></td>
<td class="tblrow2" valign="top">Link preview template - currently unused</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>style.css</b></td>
<td class="tblrow2" valign="top">The main stylesheet for the template.</td>
</tr>
</table>

<p>
Templates use <b>template variables</b> to access common settings and variables from phpLinkBid.
Template variables are identified in a template file with surrounding curly brackets.
For example, adding <code>{ site_title }</code> to a template will display the title of your site as
set in Site Configuration. See the template variables list below for all available global template
variables.
</p>

<a name="cust-vars"></a>
<h3>Template variables list</h3>
<p>
The following table lists some of the variables available in every template.
</p>
<table width="90%" cellpadding="2px" cellspacing="0px" border="0px" align="center" class="tbl">
<tr>
<td colspan="2" class="tblrow1" valign="top" style="background-color:#efefef;"><b>Site variables</b></td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>dir_base</b></td>
<td class="tblrow2" valign="top">Relative path of your site, usually `/`</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>dir_tpl</b></td>
<td class="tblrow2" valign="top">Relative path to the current template folder</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>dir_tpl_images</b></td>
<td class="tblrow2" valign="top">Relative path to the current template images folder</td>
</tr>
<tr>
<td colspan="2" class="tblrow1" valign="top" style="background-color:#efefef;"><b>Configuration variables</b></td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>site_title</b></td>
<td class="tblrow2" valign="top">Full title of your site</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>site_name</b></td>
<td class="tblrow2" valign="top">Name of your site</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>meta_keywords</b></td>
<td class="tblrow2" valign="top">Site keywords</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>meta_description</b></td>
<td class="tblrow2" valign="top">Site description</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>meta_keywords</b></td>
<td class="tblrow2" valign="top">Site keywords</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>site_url</b></td>
<td class="tblrow2" valign="top">Site URL</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>site_email</b></td>
<td class="tblrow2" valign="top">Site administrator email address</td>
</tr>
<tr>
<td colspan="2" class="tblrow1" valign="top" style="background-color:#efefef;"><b>Auction variables</b></td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>paypal_email</b></td>
<td class="tblrow2" valign="top">PayPal email address</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>top_count</b></td>
<td class="tblrow2" valign="top">The number of sites set for Top Sites</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>link_desc_max</b></td>
<td class="tblrow2" valign="top">Maximum link description length</td>
</tr>
<tr>
<td class="tblrow1" valign="top"><b>link_title_max</b></td>
<td class="tblrow2" valign="top">Maximum link title length</td>
</tr>
</table>
<br />

<a name="ts"></a>
<small> <a href="#top">^ Back to top</a></small><br />
<h2>Troubleshooting</h2>

<p>
For additional help visit our <a href="http://www.phplinkbid.com/support/" target="_new">online support</a>.
</p>



<small> <a href="#top">^ Back to top</a></small><br />