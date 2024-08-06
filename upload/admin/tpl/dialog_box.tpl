
<div class="dialog-box">
<div class="dialog-title">{dialog_title}</div>
<div class="dialog-body">{dialog_body}</div>
<div class="dialog-links">
	<!-- BEGIN BLOCK: dialog_ok -->
	<a href="{ok_url}">Continue</a> &nbsp; 
	<!-- END BLOCK: dialog_ok -->	
	<!-- BEGIN BLOCK: dialog_cancel -->
	<a href="{cancel_url}">Cancel</a> &nbsp; 
	<!-- END BLOCK: dialog_cancel -->
</div>
</div>
<!-- BEGIN BLOCK: js_redirect -->
<script language="javascript">
setTimeout("location='{js_redirect_url}'",{js_redirect_time})
</script>
<!-- END BLOCK: js_redirect -->
