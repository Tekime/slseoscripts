{getHeader()}

<div id="kdoc" class="kdoc1">
  <div id="kheader">
    <div id="klheader">
    <h1><a href="{dir_base}">{site_name}</a></h1>
    <div class="khsub">{site_title}</div>
    </div>
    <div id="krheader">
<!--    <img src="{dir_tpl_images}phplb_ad1-468.gif" alt=""> -->
    </div>
    <br clear="all" />
  </div>
  <div id="kbody">
  <div class="ktmsg">
    {site_welcome_msg}
  </div>
    <div id="kmain">
      <div class="kinner">
        {k_content}
      </div>
    </div>
    <div class="kinner">
                
        <div class="ksmenu">
        <h2>Navigation</h2>
        {kMenu()}
        </div>
        
        <div class="ksmenu">
        <h2>Categories</h2>
        {seoscriptsCatMenu()}
        </div>

        <div class="ksmenu">
        <h2>Links</h2>
        {kSiteLinks()}
        </div>
        
    </div>
    <br clear="all" />
  <div id="kfooter">
    <div class="footercol">
      {site_title}<br />
      &copy;2012 <a href="{site_url}">{site_name}</a>
      
      </div>

      <div class="footercol">
      <!-- ***** DO NOT REMOVE OR EDIT THIS SECTION ***** -->
      <!-- You must own a valid branding removal license from www.scriptalicious.com to remove the footer link. --> 
      {lang_copyright_notice}
      <!-- ***** DO NOT REMOVE OR EDIT THIS SECTION ***** -->
      </div>

  </div>
    <div id="kbtm">
    </div>
  </div>
    <br clear="all" />
</div>

{getFooter()}

