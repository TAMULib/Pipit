$(document).ready(function() {
	//Loads the modal box
	//Any anchor tag with a .do-loadmodal class will have the contents of its href loaded into the modal box
	$(".container,#theModal").on("click",".do-loadmodal",function(e) {
		e.preventDefault();
		$clicked = $(this);
		$(".container").addClass("blur");
		$("#theModal").fadeIn("fast",function() {
			$("#theModal .modal-body").load($clicked.attr("href")+" #modalContent > *",function() {
				$('#theModal').modal('show');
			});
		});
	});

	//AJAX form submission for updating app data
	//Listens for submission of any form with a .do-submit class
	$(".container,#theModal").on("submit",".do-submit",function() {
		formUpdate($(this));
		return false;
	});

	/**
	* Parse the system output from the HTML returned after an AJAX POST and pop it into the active DOM
	*
	**/
	function updateSystemOutput(data) {
		$(".sysMsg").html($($(data).filter("#systemBar").find(".sysMsg")).html());
		setTimeout("$(\".sysMsg h4\").fadeOut(\"slow\")",6000);
	}

	/**
	* POSTs a form data update to the server, then updates .do-results with a fresh copy of its contents
	*
	* To keep a modal open and updated after form submission, add a modal_context input field to the modal form. 
	* Its value should be the query string of the content with which you want to update the modal.
	*
	**/

	function formUpdate(theForm) {
		var isModal = (theForm.parents("#theModal").length != 0) ? true:false;
		
		$.ajax({
			type: "POST",
			url: app_http,
			data: theForm.serialize()
		}).done(function(data) {
			updateSystemOutput(data);
			if (isModal) {
				var $modalContextField = theForm.children("input[name=modal_context]");
				if ($modalContextField) {
					var modalContext = $modalContextField.val();
				}
				if (!modalContext) {
					$('#theModal').modal('hide');
				} else {
					$("#theModal .do-results").load(app_http+"?"+modalContext+" #modalContent .do-results > *");
				}
			}
			$("#modalContent .do-results").load(app_http+" #modalContent .do-results > *");
		});
	}
});