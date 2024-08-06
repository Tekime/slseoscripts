<script type="text/javascript">
	$(document).ready(
		function()
		{
			$('#fisheye').Fisheye(
				{
					maxWidth: 30,
					items: 'a',
					itemsText: 'span',
					container: '.fisheyeContainter',
					itemWidth: 20,
					proximity: 30,
					halign : 'center'
				}
			)
		}
	);
</script>

<div style="position:absolute;left:500px;height:20px;padding:1px 0px 0px 0px;">
<div id="fisheye" class="fisheye">
<div class="fisheyeContainter">
    <a href="{dir_admin}main.php" class="fisheyeItem"><img src="{dir_tpl_images}fico_home.png" width="20" /></a>
    <a href="{dir_admin}pages.php" class="fisheyeItem"><img src="{dir_tpl_images}fico_pages.png" width="20" /></a>
    <a href="{dir_admin}pages.php" class="fisheyeItem"><img src="{dir_tpl_images}fico_users.png" width="20" /></a>
    <a href="{dir_admin}pages.php" class="fisheyeItem"><img src="{dir_tpl_images}fico_magnify.png" width="20" /></a>
    <a href="#" class="fisheyeItem"><img src="{dir_tpl_images}fico_email.png" width="20" /></a>
    <a href="#" class="fisheyeItem"><img src="{dir_tpl_images}fico_rss.png" width="20" /></a>
</div>
</div>
</div>