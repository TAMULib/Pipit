$(document).ready(function() {
	$(".container,#theModal").on("submit",".do-submit-reset",function() {
		if ($("#newPassword").val() == $(this).find("#confirmNewPassword").val()) {
			formUpdate($(this));
		} else {
			alert("The password and password confirmation fields must match.")
		}
		return false;
	});

	$("#newPassword").change(function() {
		if ($(this).val().length > 0) {
			$("#submitPasswordReset").prop("disabled",false);
		} else {
			$("#submitPasswordReset").prop("disabled",true);
		}
	});
});