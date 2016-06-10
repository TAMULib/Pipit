/**
* Simple wrapper for the built in confirm()
*
*/
function confirmAction() {
	if (confirm("Are you sure?")) {
		return true;
	}
	return false;
}

/**
* Rebinds the datepickers. Use when a datepicker element may have been loaded asynchronously
*
**/
/*
function runDatePicker() {
	$(".date-input-db").datepicker({ dateFormat: 'yy-mm-dd' });
}*/

/**
* Parse the system output from the HTML returned after an AJAX POST and pop it into the active DOM
*
**/
function updateSystemOutput(data) {
	$(".sysMsg").html($($(data).filter("#systemBar").find(".sysMsg")).html());
	setTimeout("$(\".sysMsg .alert\").fadeOut(\"slow\")",6000);
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

/**
* Requests results from the server and updates .do-results with the results
**/

function formGet(theForm) {
	$("#modalContent .do-results").load(app_http+" #modalContent .do-results > *",theForm.serialize());
}

$(document).ready(function() {
	//bind datepickers on page load
//	runDatePicker();

	//rebind datepickers after any AJAX calls complete
/*
	$(document).ajaxComplete(function() {
		runDatePicker();
	});
*/
	//remove system messages when clicked on by user
	$("#systemBar").on("click",".sysMsg .alert",function() {
		$(this).fadeOut("slow",function() {
			$(this).remove();
		});
	});

	//show the clear search option when a search is active
	$("#searchResults").click(function() {
		$("#searchStatus a").toggle();
	});

	//reset the search results and UI
	$("#searchStatus a").click(function(e) {
		e.preventDefault();
		$("#searchTerm").val("");
		$("#doSearch").submit();
		$(this).fadeOut("fast");
	});

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

	//AJAX form submission with confirmation
	//Listens for submission of any form with a .do-submit-confirm class
	$(".container,#theModal").on("submit",".do-submit-confirm",function() {
		if (confirmAction()) {
			formUpdate($(this));
		}
		return false;
	});

	//Confirmation interceptor
	//Makes a click on any element with a .do-confirm class cancellable by the user
	$(".container,#theModal").on("click",".do-confirm",function() {
		return confirmAction();
	});

	//AJAX form submission for updating app data
	//Listens for submission of any form with a .do-submit class
	$(".container,#theModal").on("submit",".do-submit",function() {
		formUpdate($(this));
		return false;
	});

	//AJAX form submission for getting app data
	//Listens for submission of any form with a .do-get class
	$(".container,#theModal").on("submit",".do-get",function() {
		formGet($(this));
		return false;
	});

});