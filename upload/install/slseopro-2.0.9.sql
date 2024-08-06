DROP TABLE IF EXISTS `tbl_components`;
CREATE TABLE IF NOT EXISTS `tbl_components` (
  `component_id` smallint(5) unsigned NOT NULL auto_increment,
  `com_name` varchar(50) NOT NULL default '',
  `com_tag` varchar(32) NOT NULL default '',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `createdby` int(11) unsigned NOT NULL default '0',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`component_id`),
  UNIQUE KEY `NDX_TAG` (`com_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tbl_config`;
CREATE TABLE IF NOT EXISTS `tbl_config` (
  `config_id` int(11) unsigned NOT NULL auto_increment,
  `mod_tag` varchar(32) NOT NULL default '',
  `cfg_field` varchar(64) NOT NULL default '',
  `cfg_value` text NOT NULL,
  PRIMARY KEY  (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=67 ;

INSERT INTO `tbl_config` (`config_id`, `mod_tag`, `cfg_field`, `cfg_value`) VALUES
(1, '0', 'template', 'seo'),
(36, '0', 'urlformat_page', '%pg_safename%/'),
(4, '0', 'site_title', 'Scriptalicious SEO Scripts Pro'),
(5, '0', 'site_name', 'Scriptalicious SEO Scripts Pro'),
(6, '0', 'meta_description', ''),
(7, '0', 'meta_keywords', ''),
(25, '0', 'site_email', 'webmaster@example.com'),
(17, '0', 'site_url', ''),
(35, '0', 'list_sep', '»'),
(27, '0', 'site_title_tail', ''),
(28, '0', 'site_email_notify', 'webmaster@example.com'),
(29, '0', 'queue_maxemails', '5'),
(30, '0', 'jobs_autorun', '30'),
(31, '0', 'expire_lastrun', '1244793437'),
(32, '0', 'email_signature', ''),
(33, '0', 'admin_editor', '1'),
(34, '0', 'lang', 'en'),
(37, '0', 'urlformat_search', 'search/%search_term%/'),
(38, 'seoscripts', 'seoscripts_urlformat_tool', 'tools/%seoscripts_tool_safename%/'),
(39, 'seoscripts', 'seoscripts_urlformat_cat', 'tools/cat/%seoscripts_cat_safename%/'),
(60, 'seoscripts', 'seoscripts_maxrequests', '10'),
(59, 'seoscripts', 'seoscripts_menu', 'cat'),
(58, 'seoscripts', 'seoscripts_toolcount', '0'),
(57, 'seoscripts', 'seoscripts_toolcols', '2'),
(56, 'seoscripts', 'seoscripts_urlformat_shorturl', '%seoscripts_shorturl%.url'),
(55, 'seoscripts', 'seoscripts_urlmax', '10'),
(52, 'seoscripts', 'seoscripts_urlformat', 'tools/'),
(53, '', 'kytoo_version', '2.0b2'),
(54, '', 'app_version', '2.0.8'),
(61, '', 'site_welcome_msg', '%site_name% - %seoscripts_info_toolcount% free tools for SEO and website research.'),
(62, '', 'themestyle', 'Default'),
(63, 'seoscripts', 'seoscripts_ipcache', '24'),
(64, '', 'dashboard_rss_display', '1'),
(65, 'seoscripts', 'seoscripts_tool_desc_length', '75'),
(66, 'seoscripts', 'seoscripts_captcha', '0');

DROP TABLE IF EXISTS `tbl_groups`;
CREATE TABLE IF NOT EXISTS `tbl_groups` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `grp_name` varchar(32) NOT NULL default '',
  `grp_datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `grp_createdby` int(11) unsigned NOT NULL default '0',
  `grp_dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `grp_updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `NDX_NAME` (`grp_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tbl_group_permissions`;
CREATE TABLE IF NOT EXISTS `tbl_group_permissions` (
  `group_permissions_id` int(11) unsigned NOT NULL auto_increment,
  `group_id` smallint(6) unsigned NOT NULL default '0',
  `component_id` int(11) unsigned NOT NULL default '0',
  `pcreate` char(1) NOT NULL default '0',
  `pread` tinyint(1) NOT NULL default '0',
  `pupdate` tinyint(1) NOT NULL default '0',
  `pdelete` tinyint(1) NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `createdby` int(11) unsigned NOT NULL default '0',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_permissions_id`),
  UNIQUE KEY `idx_group_permissions` (`group_id`,`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tbl_mailqueue`;
CREATE TABLE IF NOT EXISTS `tbl_mailqueue` (
  `mailqueue_id` int(11) unsigned NOT NULL auto_increment,
  `mq_to` varchar(255) NOT NULL default '',
  `mq_subject` varchar(255) NOT NULL default '',
  `mq_headers` text NOT NULL,
  `mq_message` text NOT NULL,
  `mq_from` varchar(255) NOT NULL default '',
  `mq_sent` tinyint(1) NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`mailqueue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tbl_messages`;
CREATE TABLE IF NOT EXISTS `tbl_messages` (
  `message_id` int(11) unsigned NOT NULL auto_increment,
  `mod_tag` varchar(32) NOT NULL default '',
  `msg_name` varchar(50) NOT NULL default '',
  `msg_title` varchar(255) NOT NULL default '',
  `msg_text` text NOT NULL,
  `msg_subject` varchar(255) NOT NULL default '',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

INSERT INTO `tbl_messages` (`message_id`, `mod_tag`, `msg_name`, `msg_title`, `msg_text`, `msg_subject`, `dateupdated`) VALUES
(3, '', 'msg_email_contactthankyou', 'Thank you for contacting [[site_name]]', 'Thank you for contacting [[site_name]]. Your message has been received, and we will respond as soon as possible.', 'Thank you for contacting [[site_name]]', '0000-00-00 00:00:00'),
(15, '', 'msg_email_contactnotify', '[[site_name]]: New message from [[f_email]]', '[[site_name]] Message\r\nFrom: [[f_email]]\r\n----------------------------------------\r\n\r\n[[f_comments]]', '[[site_name]]: New message from [[f_email]]', '0000-00-00 00:00:00');

DROP TABLE IF EXISTS `tbl_modules`;
CREATE TABLE IF NOT EXISTS `tbl_modules` (
  `module_id` int(11) unsigned NOT NULL auto_increment,
  `mod_tag` varchar(32) NOT NULL default '',
  `mod_name` varchar(32) NOT NULL default '',
  `mod_title` varchar(64) NOT NULL default '',
  `mod_description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`module_id`),
  UNIQUE KEY `mod_name` (`mod_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

INSERT INTO `tbl_modules` (`module_id`, `mod_tag`, `mod_name`, `mod_title`, `mod_description`) VALUES
(11, 'seoscripts', 'SEO Scripts', 'Scriptalicious SEO Scripts', 'Scriptalicious SEO Scripts 2.0 is an advanced suite of search engine optimization scripts.');

DROP TABLE IF EXISTS `tbl_pages`;
CREATE TABLE IF NOT EXISTS `tbl_pages` (
  `page_id` int(11) unsigned NOT NULL auto_increment,
  `parent_id` int(11) unsigned NOT NULL default '0',
  `pg_safename` varchar(127) NOT NULL default '',
  `pg_name` varchar(255) NOT NULL default '',
  `pg_title` varchar(255) NOT NULL default '',
  `pg_contents` text NOT NULL,
  `pg_sort` smallint(5) NOT NULL default '0',
  `pg_status` tinyint(1) NOT NULL default '0',
  `pg_system` tinyint(1) NOT NULL default '0',
  `pg_layout` varchar(32) NOT NULL default '',
  `pg_titletail` tinyint(1) NOT NULL default '1',
  `pg_meta_keywords` text NOT NULL,
  `pg_meta_description` text NOT NULL,
  `pg_script` varchar(100) NOT NULL default '',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`page_id`),
  UNIQUE KEY `pg_safename` (`pg_safename`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

INSERT INTO `tbl_pages` (`page_id`, `parent_id`, `pg_safename`, `pg_name`, `pg_title`, `pg_contents`, `pg_sort`, `pg_status`, `pg_system`, `pg_layout`, `pg_titletail`, `pg_meta_keywords`, `pg_meta_description`, `pg_script`, `datecreated`, `dateupdated`) VALUES
(1, 0, 'home', 'Home', '%site_title%', '<h1>Welcome to {site_name}</h1>\r\n\r\n<p>Use our free SEO tools for your comprehensive search engine optimization, analyis and research.</p><p>{seoscriptsToolMenu(''false'',''true,''{seoscripts_tool_desc_length}'')}</p><p>&nbsp;</p><p>&nbsp;</p>', 1, 2, 1, 'default', 1, 'Yes, it does.', 'Our website rules.', '0', '0000-00-00 00:00:00', '2009-06-10 13:46:46'),
(13, 0, 'tools', 'SEO Tools', 'SEO Tools', '<p>{seoscriptsToolMenu(''false'',''true'',''5000'')}</p>', 3, 0, 0, 'default', 1, '', '', '0', '2009-05-29 16:31:26', '2009-06-11 21:12:53'),
(2, 0, 'about', 'About', 'About Scriptalicious SEO Scripts', '<h1>About Us<br /></h1><p>{site_name} is a powerful suite of search engine optimization tools you can use for serious SEO and website research.</p><p>We currently offer {seoscripts_info_toolcount} tools in {seoscripts_info_catcount} categories.</p><p>Please <a href="{site_url}contact/">contact us</a> with any questions, feedback or comments on the site.</p><p>Sincerely,</p><p>{site_name}</p><p>{site_url}</p>', 2, 2, 0, 'default', 1, '', '', '0', '0000-00-00 00:00:00', '2009-06-10 23:34:46'),
(10, 0, 'contact', 'Contact', 'Contact Us', '<p>Please enter your email address and message below. We will respond to you as soon as possible.</p>', 5, 2, 0, 'default', 1, '', '', 'contact', '2009-05-03 17:57:33', '2009-05-03 18:18:06');

DROP TABLE IF EXISTS `tbl_seotools`;
CREATE TABLE IF NOT EXISTS `tbl_seotools` (
  `tool_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) unsigned NOT NULL default '0',
  `tool_filename` varchar(255) NOT NULL default '',
  `tool_name` varchar(255) NOT NULL default '',
  `tool_title` varchar(255) NOT NULL default '',
  `tool_safename` varchar(255) NOT NULL default '',
  `tool_description` text NOT NULL,
  `tool_instructions` text NOT NULL,
  `tool_type` varchar(255) NOT NULL default '',
  `tool_icon` varchar(255) NOT NULL default '',
  `tool_status` tinyint(1) NOT NULL default '0',
  `tool_sort` smallint(5) NOT NULL default '0',
  `tool_urlmax` smallint(5) NOT NULL default '0',
  `tool_datamax` smallint(5) NOT NULL default '0',
  `tool_stats` varchar(255) NOT NULL default '',
  `tool_captcha` tinyint(1) NOT NULL default '1',
  `tool_featured` tinyint(1) NOT NULL default '0',
  `tool_pkg` tinyint(1) NOT NULL default '0',
  `tool_meta_description` varchar(255) NOT NULL default '',
  `tool_meta_keywords` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tool_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

INSERT INTO `tbl_seotools` (`tool_id`, `category_id`, `tool_filename`, `tool_name`, `tool_title`, `tool_safename`, `tool_description`, `tool_instructions`, `tool_type`, `tool_icon`, `tool_status`, `tool_sort`, `tool_urlmax`, `tool_datamax`, `tool_stats`, `tool_captcha`, `tool_featured`, `tool_pkg`) VALUES
(1, 1, 'fakerank-checker.php', 'Fake Rank Checker', 'Fake Rank Checker', 'fake-rank-checker', 'Use the Fake Rank Checker to find out if a Web site is faking their Google PageRank.', '', 'list', 'ico_fakerank.gif', 1, 1, 1, 0, 'fakerank', 0, 0, 1),
(2, 1, 'alexa-rank-checker.php', 'Alexa Traffic Rank Checker', 'Alexa Traffic Rank Checker', 'alexa-rank-checker', 'Check the Alexa traffic rank for any single website. Alexa traffic rank represents site traffic based on data from the Alexa toolbar and private sources.', '', 'list', 'ico_alexa.gif', 1, 2, 1, 0, 'alexa', 0, 0, 1),
(3, 2, 'multiple-keyword-se-position.php', 'Multiple Keyword Search Engine Position', 'Multiple Keyword Search Engine Position', 'multiple-keyword-se-position', 'Check a website''s search engine position for multiple keywords.', '', 'list', 'ico_serpmultiword.gif', 1, 3, 1, 10, 'position_yahoo,position_google', 0, 0, 1),
(4, 2, 'multi-website-se-position.php', 'Multiple Website Search Engine Position', 'Multiple Website Search Engine Position', 'multi-website-se-position', 'Check multiple websites'' search engine position with a single keyword.', '', 'list', 'ico_serpmultisite.gif', 1, 4, 10, 1, 'position_yahoo,position_google', 0, 0, 1),
(5, 10, 'multi-rankchecker.php', 'Multi-Rank Checker (PageRank and Alexa)', 'Multi-Rank Checker (PageRank and Alexa)', 'multi-rank-checker', 'Check multiple websites'' PageRank and Alexa rank.', '', 'list', 'ico_multirank.gif', 1, 5, 10, 0, 'pagerank,alexa', 0, 0, 1),
(6, 2, 'se-position-checker.php', 'Search Engine Position Checker', 'Search Engine Position Checker', 'search-engine-position-checker', 'Check the position of a single site with a single keyword on various search engines.', '', 'list', 'ico_serp.gif', 1, 6, 1, 0, 'position_yahoo,position_google', 0, 0, 1),
(7, 1, 'siterank-checker.php', 'Site Rank Checker', 'Site Rank Checker', 'site-rank-checker', 'Check PageRank, PageHeat, and Alexa Rank for a site.', '', 'list', 'ico_multirank.gif', 1, 7, 1, 0, 'pagerank,pageheat,alexa', 0, 0, 2),
(8, 2, 'keyword-density-checker.php', 'Keyword Density Checker', 'Keyword Density Checker', 'keyword-density-checker', 'The Keyword Density Checker tool extracts keywords from a website and determines how often they occur. Get a list of one-word, two-word, and three-word keyphrases organized by keyword density. Check the <a href="http://www.gabrielharper.com/seo-faq/#keyworddensity" style="cursor:help">keyword density</a> of a site to see how search engines view your content, optimize your keyword density, and research keyword trends on any site.', 'Enter the URL of a site you want to analyze keywords for.', '', 'ico_kwdensity.gif', 1, 1, 1, 0, '', 0, 0, 1),
(9, 2, 'keyword-suggestion.php', 'Keyword Suggestion Generator', 'Keyword Suggestion Generator', 'keyword-suggestion-generator', 'Check keyword results and get suggestions.', 'Enter a keyword or phrase to find suggestions for.', '', 'ico_kytoo_help.gif', 1, 2, 1, 0, '', 0, 0, 1),
(10, 3, 'http-header-extractor.php', 'HTTP Header Extractor', 'HTTP Header Extractor', 'http-header-extractor', 'Extract the HTTP Headers of a web page.', 'Enter the URL of a website to extract headers from.', '', 'ico_head.gif', 1, 1, 1, 0, '', 0, 0, 1),
(11, 3, 'http-header-viewer.php', 'HTTP Header Viewer', 'HTTP Header Viewer', 'http-header-viewer', 'View the headers of the site of your choice.', '', 'text', 'ico_headviewer.gif', 1, 2, 1, 0, '', 0, 0, 1),
(12, 3, 'meta-tag-extractor.php', 'Meta Tag Extractor', 'Meta Tag Extractor', 'meta-tag-extractor', 'Extract meta-tags information from a web page.', 'Enter the URL of a website to extract META tags from.', '', 'ico_metaextract.gif', 1, 3, 0, 0, '', 0, 0, 1),
(13, 3, 'meta-tag-generator.php', 'Meta-Tag Generator', 'Meta-Tag Generator', 'meta-tag-generator', 'Generate meta-tags for your site.', 'Enter META tag information in some or all of the fields below.', '', 'ico_meta.gif', 1, 4, 0, 0, '', 0, 0, 1),
(14, 4, 'source-code-viewer.php', 'Source Code Viewer', 'Source Code Viewer', 'source-code-viewer', 'View the source code of a website.', '', '', 'ico_sourceviewer.gif', 1, 1, 1, 0, '', 0, 0, 1),
(15, 4, 'md5.php', 'MD5 Encrypter', 'MD5 Encrypter', 'md5-encrypter', 'Encrypt text with MD5 encoding.', 'Enter the text to encrypt in the box below.', '', 'ico_md5.gif', 1, 2, 0, 0, '', 0, 0, 1),
(16, 4, 'html-encrypter.php', 'HTML Encrypter', 'HTML Encrypter', 'html-encrypter', 'Encrypt your HTML code to hide it from visitors.', '', '', 'ico_htmlencrypt.gif', 1, 3, 1, 0, '', 0, 0, 1),
(17, 4, 'html-optimizer.php', 'HTML Optimizer', 'HTML Optimizer', 'html-optimizer', 'Optimize your HTML code.', 'Enter your HTML code in the box below.', '', 'ico_htmloptimize.gif', 1, 4, 0, 0, '', 0, 0, 1),
(18, 4, 'website-speed-test.php', 'Website Speed Test', 'Website Speed Test', 'website-speed-test', 'Find out how fast your website loads.', '', 'text', 'ico_speed.gif', 1, 5, 1, 0, 'sitespeed', 0, 0, 1),
(19, 4, 'whois.php', 'Domain Whois Retriever', 'Domain Whois Retriever', 'domain-whois-retriever', 'Find out the WHOIS details for your domain.', '', '', 'ico_kytoo_info.gif', 1, 6, 0, 0, '', 0, 0, 1),
(20, 5, '', 'Search Engine Saturation Checker', 'Search Engine Saturation Checker', 'se-saturation-checker', 'Check the number of pages search engines have listed from your website.', '', 'list', 'ico_saturation.gif', 1, 1, 1, 0, 'pages_google,pages_yahoo,pages_bing', 0, 0, 1),
(21, 5, 'site-spider-viewer.php', 'Site Spider Viewer', 'Site Spider Viewer', 'site-spider-viewer', 'Find various SEO statistics and spider information about your site.', 'Enter a single URL to check in the form below.', '', 'ico_spiderviewer.gif', 1, 2, 0, 0, '', 0, 0, 1),
(22, 5, 'spider-viewer.php', 'Spider Viewer', 'Spider Viewer', 'spider-viewer', 'Discover how spider bots view your website.', 'Enter a single URL to check in the form below.', '', 'ico_spider.gif', 1, 2, 1, 0, '', 0, 0, 1),
(23, 5, 'search-listing-preview.php', 'Search Engine Listing Preview', 'Search Engine Listing Preview', 'se-listing-preview', 'Preview what your site''s listing will look like', '', '', 'ico_sepreview.gif', 1, 4, 1, 0, '', 0, 0, 1),
(24, 6, '', 'Reverse IP/Look-up', 'Reverse IP/Look-up', 'reverse-ip-lookup', 'Look up the hostname for any IP address.', 'Enter a single IP address to resolve the hostname.', 'list', 'ico_reverseip.gif', 1, 1, 1, 0, 'reverse_ip', 0, 0, 1),
(25, 6, '', 'Ping Domain/IP', 'Ping Domain/IP', 'ping-domain-ip', 'Ping a domain or IP address to find the response time.', 'Enter a single URL or domain name to get the IP address.', 'list', 'ico_ip.gif', 1, 2, 1, 0, 'ip_address', 0, 0, 1),
(26, 7, 'link-popularity-checker.php', 'Link Popularity Checker', 'Link Popularity Checker', 'link-popularity-checker', 'Check the number of links pointing to the website of your choice.', '', 'list', 'ico_linkpop.gif', 1, 1, 1, 0, 'backlinks_google', 0, 0, 1),
(27, 7, 'link-suggestion.php', 'Link Suggestion Generator', 'Link Suggestion Generator', 'link-suggestion-generator', 'Generate links that relate to the keyword/topic of your choosing.', '', '', 'ico_suggest.gif', 1, 2, 1, 0, '', 0, 0, 1),
(28, 7, 'nofollow-finder.php', 'No-Follow Finder', 'No-Follow Finder', 'no-follow-finder', 'Scan a website to find <a href="http://www.gabrielharper.com/2009/06/what-is-relnofollow-and-should-i-use-it/">nofollow links</a> to various other sites. The Nofollow Finder examines all outgoing links for the specified website and flags links that have the nofollow attribute.', '', '', 'ico_nofollow.gif', 1, 3, 1, 0, '', 0, 0, 1),
(29, 7, 'reciprocal-link-checker.php', 'Reciprocal Link Checker', 'Reciprocal Link Checker', 'reciprocal-link-checker', 'Check multiple sites to see if they are really linking back to you.', 'Enter a list of URLs you want to check for backlinks.', 'list', 'ico_reciprocal.gif', 1, 4, 1, 10, 'reciprocal', 0, 0, 1),
(30, 7, 'link-analyzer.php', 'Link Analyzer', 'Link Analyzer', 'link-analyzer', 'Analyze incoming and outgoing links.', '', '', 'ico_linkanalyzer.gif', 1, 5, 1, 0, '', 0, 0, 1),
(31, 7, 'backlink-checker.php', 'Backlink Checker', 'Backlink Checker', 'backlink-checker', 'Find out how many total backlinks there are for any one website. The Backlink Checker tool searches <a href="http://www.yahoo.com/">Yahoo</a> to discover the most current and accurate indicator of how many actual incoming links there are to a domain.', '', 'list', 'ico_backlink.gif', 1, 6, 1, 0, 'backlinks_google', 0, 0, 1),
(32, 8, 'url-shortener.php', 'URL Shortener', 'URL Shortener', 'url-shortener', 'Offer a URL shortening service.', '', '', 'ico_shorturls.gif', 1, 1, 1, 0, '', 0, 0, 1),
(33, 8, 'website-status.php', 'Website Status Checker', 'Website Status Checker', 'website-status-checker', 'Check whether the five major ports are responding.', '', '', 'ico_kytoo_alert.gif', 1, 2, 1, 0, '', 0, 0, 1),
(35, 8, 'browser-details.php', 'Browser Details Tool', 'Browser Details Tool', 'browser-details-tool', 'View your IP address and your browser details.', '', '', 'ico_browser.gif', 1, 4, 1, 0, '', 0, 0, 1),
(38, 8, 'anonymous-emailer.php', 'Send Anonymous Emails', 'Send Anonymous Emails', 'send-anonymous-emails', 'Allows the sending of anonymous e-mails.', '', '', 'ico_emailer.gif', 1, 6, 1, 0, '', 0, 0, 1),
(39, 8, 'convert-timestamp.php', 'Convert UNIX Timestamp', 'Convert UNIX Timestamp', 'convert-unix-timestamp', 'Convert a UNIX timestamp to a date/time string.', '', '', 'ico_timestamp.gif', 1, 7, 1, 0, '', 0, 0, 2),
(63, 8, 'convert-datetime.php', 'Convert Date to Timestamp', 'Convert Date to Timestamp', 'convert-date-to-timestamp', 'Convert a date/time string to a UNIX timestamp.', 'Enter a date & time string of any format to convert it to a UNIX timestamp.', '', 'ico_datetime.gif', 1, 0, 1, 0, '', 0, 0, 2),
(40, 8, 'email2image.php', 'Email to Image Converter', 'Email to Image Converter', 'email-to-image', 'Convert an email address to an image to protect your privacy for use on Web sites.', 'Enter your email address below.\r\nSelect the text size to use for your image.\r\nCheck optional noise option for email harvesting prevention.', '', 'ico_email2image.gif', 1, 8, 1, 0, '', 0, 0, 2),
(41, 10, '', 'Alexa Multiple Rank Checker', 'Alexa Multiple Rank Checker', 'alexa-multiple-rank-checker', 'Check the Alexa traffic rank for multiple sites. Alexa traffic rank represents site traffic based on data from the Alexa toolbar and private sources.', '', 'list', 'ico_alexamulti.gif', 1, 0, 10, 0, 'alexa', 0, 0, 2),
(42, 1, '', 'PageRank Checker', 'PageRank Checker', 'pagerank-checker', 'Check the current PageRank of a single website.', '', 'list', 'ico_pagerank.gif', 1, 0, 1, 0, 'pagerank', 0, 0, 1),
(43, 10, '', 'Multiple PageRank Checker', 'Multiple PageRank Checker', 'multiple-pagerank-checker', 'Check the current PageRank of multiple websites.', '', 'list', 'ico_pagerankmulti.gif', 1, 0, 50, 0, 'pagerank', 0, 0, 2),
(44, 2, 'keyword-extractor.php', 'Keyword Extractor', 'Keyword Extractor', 'keyword-extractor', 'Extract keywords from any website and create a list of single, double, and triple word keyphrases.', '', '', 'ico_kwextract.gif', 1, 0, 1, 0, '', 0, 0, 2),
(45, 7, '', 'Multiple Backlink Checker', 'Multiple Backlink Checker', 'multiple-backlink-checker', 'View the number of backlinks found in search engines for multiple websites.', '', 'list', 'ico_backlink.gif', 1, 0, 10, 0, 'backlinks_google', 0, 0, 2),
(46, 5, 'robots.php', 'Robots.txt Checker', 'Robots.txt Checker', 'robots-txt-checker', 'Check for and view the contents of the robots.txt file for any website.', '', '', 'ico_robots.gif', 1, 0, 1, 0, '', 0, 0, 2),
(47, 9, '', '.edu Backlink Checker', '.edu Backlink Checker', 'edu-backlink-checker', 'Check for backlinks from .edu domains pointing to any website using the .edu Backlink Checker. This tool investigates pages from .edu domains indexed in search engines with backlinks to your site.', '', 'list', 'ico_bledu.gif', 1, 0, 1, 0, 'backlinks_edu', 0, 0, 2),
(48, 9, '', '.gov Backlink Checker', '.gov Backlink Checker', 'gov-backlink-checker', 'Check for backlinks from .edu domains pointing to any website using the .gov Backlink Checker. This tool investigates pages from .gov domains indexed in search engines with backlinks to your site.', '', 'list', 'ico_blgov.gif', 1, 0, 1, 0, 'backlinks_gov', 0, 0, 2),
(49, 9, '', 'DMOZ Backlink Checker', 'DMOZ Backlink Checker', 'dmoz-backlink-checker', 'Find backlinks from DMOZ for any domain name.', '', 'list', 'ico_bldmoz.gif', 1, 0, 1, 0, 'backlinks_dmoz', 0, 0, 2),
(50, 1, '', 'PageHeat Checker', 'PageHeat Checker', 'pageheat-checker', 'Check the current PageHeat website rating for a single domain name.', '', 'list', 'ico_pageheat.gif', 1, 0, 1, 0, 'pageheat', 0, 0, 1),
(51, 10, '', 'Multiple PageHeat Checker', 'Multiple PageHeat Checker', 'multiple-pageheat-checker', 'Check the current PageHeat rating for multiple websites at once.', '', 'list', 'ico_pageheatmulti.gif', 1, 0, 10, 0, 'pageheat', 0, 0, 2),
(52, 10, '', 'Multiple Fake PageRank Checker', 'Multiple Fake PageRank Checker', 'multiple-fake-pagerank-checker', 'Check for fake PageRank on multiple domain names.', '', 'list', 'ico_fakerank.gif', 1, 0, 10, 0, 'fakerank', 0, 0, 2),
(53, 1, '', 'Compete Ranking Checker', 'Compete Ranking Checker', 'compete-ranking-checker', 'Check your current Compete rank for one website.', '', 'list', 'ico_compete.gif', 1, 0, 1, 0, 'compete_rank', 0, 0, 2),
(54, 9, '', 'Multiple .edu Backlink Checker', 'Multiple .edu Backlink Checker', 'multiple-edu-backlink-checker', 'Find authority .edu backlinks for multiple domains.', '', 'list', 'ico_bledu.gif', 1, 0, 10, 0, 'backlinks_edu', 0, 0, 2),
(55, 9, '', 'Multiple .gov Backlink Checker', 'Multiple .gov Backlink Checker', 'multiple-gov-backlink-checker', 'Find authority .gov backlinks for multiple websites.', '', 'list', 'ico_blgov.gif', 1, 0, 10, 0, 'backlinks_gov', 0, 0, 2),
(56, 9, '', 'Multiple DMOZ Backlink Checker', 'Multiple DMOZ Backlink Checker', 'multiple-dmoz-backlink-checker', 'Find backlinks from the DMOZ directory for multiple domains.', '', 'list', 'ico_bldmoz.gif', 1, 0, 10, 0, 'backlinks_dmoz', 0, 0, 2),
(57, 8, 'clock.php', 'JavaScript Clock', 'JavaScript Clock', 'javascript-clock', 'Display a working JavaScript clock on your site.', '', '', 'ico_clock.gif', 1, 0, 0, 0, '', 0, 0, 1),
(58, 6, '', 'Multiple IP Address Checker', 'Multiple IP Address Checker', 'multiple-ip-address-checker', 'Get the IP address for multiple domain names.', '', 'list', 'ico_ip.gif', 1, 0, 10, 0, 'ip_address', 0, 0, 2),
(59, 6, '', 'Multiple Reverse IP Lookup', 'Multiple Reverse IP Lookup', 'multiple-reverse-ip-lookup', 'Get the hostname for multiple IP addresses.', '', 'list', 'ico_reverseip.gif', 1, 0, 10, 0, 'reverse_ip', 0, 0, 2),
(60, 2, 'keyword-domains.php', 'Keyword Rich Domain Finder', 'Keyword Rich Domain Finder', 'keyword-rich-domain-finder', 'Find domains containing specific keywords or phrases.', '', '', 'ico_kwdomain.gif', 1, 0, 1, 0, '', 0, 0, 2),
(61, 4, 'html-validator.php', 'HTML Markup Validator', 'HTML Markup Validator', 'html-markup-validator', 'Validate your HTML/XHTML code against the W3C Markup Validator.', '', '', 'ico_validhtml.gif', 1, 0, 1, 0, '', 0, 0, 2),
(62, 4, 'css-validator.php', 'CSS Validator', 'CSS Validator', 'css-validator', 'Validate your cascading stylesheets with the W3C CSS validator.', '', '', 'ico_validcss.gif', 1, 0, 1, 0, '', 0, 0, 2),
(64, 7, 'link-extractor.php', 'Link Extractor', 'Link Extractor', 'link-extractor', 'Extract all links from the specified domain.', '', '', 'ico_linkextractor.gif', 1, 0, 1, 0, '', 0, 0, 2),
(65, 5, '', 'Google Indexed Pages Checker', 'Google Indexed Pages Checker', 'google-indexed-pages-checker', 'Check how many pages Google has indexed for one website.', '', 'list', 'ico_gip.gif', 1, 0, 1, 0, 'pages_google', 0, 0, 2),
(66, 5, '', 'Yahoo Indexed Pages Checker', 'Yahoo Indexed Pages Checker', 'yahoo-indexed-pages-checker', 'Check how many pages Yahoo has indexed for one domain.', '', 'list', 'ico_yip.gif', 1, 0, 1, 0, 'pages_yahoo', 0, 0, 2),
(67, 10, '', 'Multiple Compete Rank Checker', 'Multiple Compete Rank Checker', 'multiple-compete-rank-checker', 'Check Compete rank for multiple websites.', '', 'list', 'ico_compete.gif', 1, 0, 10, 0, 'compete_rank', 0, 0, 2),
(68, 1, '', 'Compete Statistics Checker', 'Compete Statistics Checker', 'compete-statistics-checker', 'Check Compete rank, uniques, and visitors for one website.', '', 'list', 'ico_compete.gif', 1, 0, 1, 0, 'compete_rank,compete_unique,compete_visitors', 0, 0, 2),
(69, 5, '', 'Bing Indexed Pages Checker', 'Bing Indexed Pages Checker', 'bing-indexed-pages-checker', 'Check the number of indexed pages in Bing for a single website.', '', 'list', 'ico_bip.gif', 1, 0, 1, 0, 'pages_bing', 0, 0, 2),
(70, 5, '', 'MSN Live Indexed Pages Checker', 'MSN Live Indexed Pages Checker', 'msn-live-indexed-pages-checker', 'Check all indexed pages in MSN Live Search for a single domain.', '', 'list', 'ico_mip.gif', 1, 0, 1, 0, 'pages_msn', 0, 0, 2),
(71, 9, '', 'Authority Link Checker', 'Authority Link Checker', 'authority-link-checker', 'Find authority links from DMOZ, Yahoo! Directory, .edu and .gov sites.', '', 'list', 'ico_authoritylinks.gif', 1, 0, 1, 0, 'backlinks_edu,backlinks_gov,backlinks_dmoz,backlinks_yahoodir', 0, 0, 2),
(72, 10, '', 'Multiple Compete Statistics Checker', 'Multiple Compete Statistics Checker', 'multiple-compete-statistics-checker', 'Check Compete rank, unique visitors and traffic statistics for multiple websites.', '', 'list', 'ico_compete.gif', 1, 0, 10, 0, 'compete_rank,compete_unique,compete_visitors', 0, 0, 2),
(73, 11, '', 'Digg Links Checker', 'Digg Links Checker', 'digg-links-checker', 'See how many links a site has on Digg.', '', 'list', 'ico_digglinks.gif', 1, 0, 1, 0, 'backlinks_digg', 0, 0, 2),
(74, 11, '', 'Multiple Digg Links Checker', 'Multiple Digg Links Checker', 'multiple-digg-links-checker', 'Find Digg links for multiple websites.', '', 'list', 'ico_digglinks.gif', 1, 0, 10, 0, 'backlinks_digg', 0, 0, 2),
(75, 11, '', 'Delicious Link Checker', 'Delicious Link Checker', 'delicious-link-checker', 'Find out how many Delicious bookmarks there are for a single site.', '', 'list', 'ico_deliciouslinks.gif', 1, 0, 1, 0, 'backlinks_delicious', 0, 0, 2),
(76, 11, '', 'Multiple Delicious Link Checker', 'Multiple Delicious Link Checker', 'multiple-delicious-link-checker', 'Check multiple sites for links bookmarked in Delicious.', '', 'list', 'ico_deliciouslinks.gif', 1, 0, 10, 0, 'backlinks_delicious', 0, 0, 2),
(77, 11, '', 'Twitter Links Finder', 'Twitter Links Finder', 'twitter-links-finder', 'Find links on Twitter to one website.', '', 'list', 'ico_twitterlinks.gif', 1, 0, 1, 0, 'backlinks_twitter', 0, 0, 2),
(78, 9, '', 'Yahoo Directory Backlink Checker', 'Yahoo Directory Backlink Checker', 'yahoo-directory-backlink-checker', 'See how many backlinks a website has in Yahoo! Directory.', '', 'list', 'ico_yahoodir.gif', 1, 0, 1, 0, 'backlinks_yahoodir', 0, 0, 2),
(79, 4, 'webpage-size.php', 'Webpage Size Checker', 'Webpage Size Checker', 'webpage-size-checker', 'Check the size of any website''s source code in bytes, KB and MB.', '', '', 'ico_webpagesize.gif', 1, 0, 1, 0, '', 0, 0, 2),
(80, 9, '', 'Multiple Authority Link Checker', 'Multiple Authority Link Checker', 'multiple-authority-link-checker', 'Find authority backlinks for multiple sites.', '', 'list', 'ico_authoritylinks.gif', 1, 0, 10, 0, 'backlinks_edu,backlinks_gov,backlinks_dmoz,backlinks_yahoodir', 0, 0, 2);

DROP TABLE IF EXISTS `tbl_seotools_categories`;
CREATE TABLE IF NOT EXISTS `tbl_seotools_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `cat_name` varchar(255) NOT NULL default '',
  `cat_title` varchar(255) NOT NULL default '',
  `cat_safename` varchar(255) NOT NULL default '',
  `cat_description` text NOT NULL,
  `cat_status` tinyint(1) NOT NULL default '0',
  `cat_sort` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

INSERT INTO `tbl_seotools_categories` (`category_id`, `cat_name`, `cat_title`, `cat_safename`, `cat_description`, `cat_status`, `cat_sort`) VALUES
(1, 'Website Rank Checkers', 'Website Rank Checkers', 'website-rank-checkers', 'Website Rank Checkers', 1, 5),
(2, 'Keyword Tools', 'Keyword Tools', 'keyword-tools', 'Keyword Tools', 1, 2),
(3, 'Header/Tag Tools', 'Header/Tag Tools', 'header-tag-tools', 'Header/Tag Tools', 1, 7),
(4, 'Source Code Tools', 'Source Code Tools', 'source-code-tools', 'Source Code Tools', 1, 10),
(5, 'Search Engine Tools', 'Search EngineTools', 'search-engine-tools', 'Search Engine Tools', 1, 4),
(6, 'IP Tools', 'IP Tools', 'ip-tools', 'IP Tools', 1, 8),
(7, 'Link Tools', 'Link Tools', 'link-tools', 'Link-Based Tools', 1, 1),
(8, 'Miscellaneous Tools', 'Miscellaneous Tools', 'misc-tools', 'Miscellaneous Tools', 1, 9),
(9, 'Authority Link Tools', 'Authority Link Tools', 'authority-link-tools', 'Find authority backlinks for websites.', 1, 3),
(10, 'Multi-Site Rank Checkers', 'Multi-Site Rank Checkers', 'multi-site-rank-checkers', 'Multiple site rank checkers.', 1, 6),
(11, 'Social Web Tools', 'Social Web Tools', 'social-web-tools', 'Social networking and social bookmarking tools.', 1, 11);

DROP TABLE IF EXISTS `tbl_seotools_iplog`;
CREATE TABLE IF NOT EXISTS `tbl_seotools_iplog` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `log_ip` varchar(15) NOT NULL default '',
  `log_requests` int(10) unsigned NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`log_id`),
  UNIQUE KEY `log_ip` (`log_ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `tbl_sessions`;
CREATE TABLE IF NOT EXISTS `tbl_sessions` (
  `session_id` varchar(32) NOT NULL default '',
  `sess_user_id` int(11) unsigned NOT NULL default '0',
  `sess_useragent` varchar(255) NOT NULL default '',
  `sess_ip` varchar(15) NOT NULL default '',
  `sess_expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tbl_shorturls`;
CREATE TABLE IF NOT EXISTS `tbl_shorturls` (
  `shorturl_id` int(11) unsigned NOT NULL auto_increment,
  `url_full` varchar(255) NOT NULL default '',
  `url_short` varchar(16) NOT NULL default '',
  `url_ip` varchar(16) NOT NULL default 'unknown',
  `url_hits` int(11) unsigned NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`shorturl_id`),
  UNIQUE KEY `url_short` (`url_short`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

DROP TABLE IF EXISTS `tbl_sitelinks`;
CREATE TABLE IF NOT EXISTS `tbl_sitelinks` (
  `sitelink_id` int(10) unsigned NOT NULL auto_increment,
  `lnk_url` varchar(255) NOT NULL default '',
  `lnk_title` varchar(255) NOT NULL default '',
  `lnk_summary` text NOT NULL,
  `lnk_nofollow` tinyint(1) NOT NULL default '0',
  `lnk_openwin` tinyint(1) NOT NULL default '0',
  `lnk_status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`sitelink_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `tbl_sitelinks` (`sitelink_id`, `lnk_url`, `lnk_title`, `lnk_summary`, `lnk_nofollow`, `lnk_openwin`, `lnk_status`) VALUES
(1, 'http://www.scriptalicious.com/', 'Scriptalicious', 'Scriptalicious - Scripts With Flavor', 0, 0, 1);

DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `usr_username` varchar(32) NOT NULL default '',
  `usr_email` varchar(131) NOT NULL default '',
  `usr_password` varchar(32) NOT NULL default '',
  `usr_email_alerts` tinyint(1) NOT NULL default '1',
  `usr_email_updates` tinyint(1) NOT NULL default '1',
  `usr_fullname` varchar(255) NOT NULL default '',
  `usr_address1` varchar(255) NOT NULL default '',
  `usr_address2` varchar(255) NOT NULL default '',
  `usr_city` varchar(255) NOT NULL default '',
  `usr_state` varchar(255) NOT NULL default '',
  `usr_country` varchar(255) NOT NULL default '',
  `usr_zipcode` varchar(255) NOT NULL default '',
  `usr_lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `usr_admin` tinyint(1) NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `createdby` int(11) unsigned NOT NULL default '0',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `NDX_USERNAME` (`usr_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2042 ;


INSERT INTO `tbl_users` (`user_id`, `usr_username`, `usr_email`, `usr_password`, `usr_email_alerts`, `usr_email_updates`, `usr_fullname`, `usr_address1`, `usr_address2`, `usr_city`, `usr_state`, `usr_country`, `usr_zipcode`, `usr_lastlogin`, `usr_admin`, `datecreated`, `createdby`, `dateupdated`, `updatedby`) VALUES
(1, 'admin', 'admin@example.com', '21232f297a57a5a743894a0e4a801fc3', 1, 1, '', '', '', '', '', '', '', '2008-08-07 11:47:29', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1);

DROP TABLE IF EXISTS `tbl_users_groups`;
CREATE TABLE IF NOT EXISTS `tbl_users_groups` (
  `users_groups_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `group_id` smallint(6) unsigned NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `createdby` int(11) unsigned NOT NULL default '0',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`users_groups_id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `tbl_user_permissions`;
CREATE TABLE IF NOT EXISTS `tbl_user_permissions` (
  `user_permissions_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` smallint(6) unsigned NOT NULL default '0',
  `component_id` int(11) unsigned NOT NULL default '0',
  `pcreate` tinyint(1) NOT NULL default '0',
  `pread` tinyint(1) NOT NULL default '0',
  `pupdate` tinyint(1) NOT NULL default '0',
  `pdelete` tinyint(1) NOT NULL default '0',
  `datecreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `createdby` int(11) unsigned NOT NULL default '0',
  `dateupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `updatedby` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_permissions_id`),
  UNIQUE KEY `idx_user_permissions` (`user_id`,`component_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;


UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'edu-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'gov-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'dmoz-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'multiple-edu-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'multiple-gov-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'multiple-dmoz-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'authority-link-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'yahoo-directory-backlink-checker';
UPDATE tbl_seotools SET tool_description = CONCAT(tool_description, '<p><i><strong>Important:</strong> Data for authority link checker tools is currently not available. Due to changes in search engine results, authority link tools are returning n/a until an alternative data source is implemented. Thank you for your understanding, and please check back soon.</i></p>') WHERE tool_safename = 'multiple-authority-link-checker';

