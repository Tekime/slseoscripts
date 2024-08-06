<form action="{dialog_form_action}" method="post">
<!-- BEGIN BLOCK: dialog_form_fields -->
<input type="hidden" name="{field_name}" value="{field_value}" /> 
<!-- END BLOCK: dialog_form_fields -->	

<div class="dialog-box">
<div class="dialog-title">{dialog_title}</div>
<div class="dialog-body">{dialog_body}
<!-- BEGIN BLOCK: dialog_form_names -->
{field_name} 
<!-- END BLOCK: dialog_form_names -->	
</div>
<div class="dialog-links">
    <input type="submit" value="Continue" />
	<!-- BEGIN BLOCK: dialog_cancel -->
    <input type="button" value="Cancel" onClick="javascript:window.location.href='{cancel_url}';" />
	<!-- END BLOCK: dialog_cancel -->
</div>
</div>
</form>
