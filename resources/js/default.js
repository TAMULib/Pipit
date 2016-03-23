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
function runDatePicker() {
	$(".date-input-db").datepicker({ dateFormat: 'yy-mm-dd' });
}

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
**/

function formUpdate(theForm) {
	isModal = (theForm.parents("#theModal").length != 0) ? true:false;
	$.ajax({
		type: "POST",
		url: app_http,
		data: theForm.serialize()
	}).done(function(data) {
		updateSystemOutput(data);
		if (isModal) {
			$("#theModal .do-close").click();
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
	runDatePicker();

	//rebind datepickers after any AJAX calls complete
	$(document).ajaxComplete(function() {
		runDatePicker();
	});

	//remove system messages when clicked on by user
	$("#systemBar").on("click",".sysMsg h4",function() {
		$(this).fadeOut("slow",function() {
			$(this).remove();
		});
	});

	//show the clear search option when a search is active
	$("#searchResults").click(function() {
		$("#searchStatus a.hidden").fadeIn("fast");
	});

	//reset the search results and UI
	$("#searchStatus a").click(function(e) {
		e.preventDefault();
		$("#searchTerm").val("");
		$("#doSearch").submit();
		$(this).fadeOut("fast");
	});

	//AJAX form submission with confirmation
	//Listens for submission of any form with a .do-submit-confirm class
	$(".container,#theModal").on("submit",".do-submit-confirm",function() {
		if (confirmAction()) {
			formUpdate($(this));
		}
		return false;
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

	//Confirmation interceptor
	//Makes a click on any element with a .do-confirm class cancellable by the user
	$(".container,#theModal").on("click",".do-confirm",function() {
		return confirmAction();
	});

	//Loads the modal box
	//Any anchor tag with a .do-loadmodal class will have the contents of its href loaded into the modal box
	$(".container,#theModal").on("click",".do-loadmodal",function(e) {
		e.preventDefault();
		$clicked = $(this);
		$(".container").addClass("blur");
		$("#theModal .loader").fadeIn("fast",function() {
			$("#theOverlay").fadeIn("fast",function() {
				$("#theModal").fadeIn("fast",function() {
					$("#theModal .content").load($clicked.attr("href")+" #modalContent > *",function() {
						$("#theModal .loader").fadeOut("fast",function() {
							$("#theModal .content").fadeIn("fast");
						});
					});
				});
			});
		});
	});

	//Closes the modal box
	$("#theModal .do-close").click(function(e) {
		e.preventDefault();
		$("#theModal").fadeOut("fast",function() {
			$(".container").removeClass("blur");
			$("#theOverlay").fadeOut("fast");
			$("#theModal .content").hide();
		});
	});
});