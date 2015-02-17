function confirmAction() {
	if (confirm("Are you sure?")) {
		return true;
	}
	return false;
}

function runDatePicker() {
	$(".date-input-db").datepicker({ dateFormat: 'yy-mm-dd' });
}

function updateSystemOutput(data) {
	$(".sysMsg").html($($(data).filter("#systemBar").find(".sysMsg")).html());
}

$(document).ready(function() {
	runDatePicker();
	$(document).ajaxComplete(function() {
		runDatePicker();
	});

	$("#systemBar").on("click",".sysMsg h4",function() {
		$(this).fadeOut("slow",function() {
			$(this).remove();
		});
	});

	$("#searchResults").click(function() {
		$("#searchStatus a.hidden").fadeIn("fast");
	});

	$("#searchStatus a").click(function(e) {
		e.preventDefault();
		$("#searchTerm").val("");
		$("#doSearch").submit();
		$(this).fadeOut("fast");
	});

	$(".container").on("submit",".do-submit",function() {
		$("#modalContent .do-results").load(app_http+" .do-results > *",$(this).serialize());
		return false;
	});

	$(".container,#theModal").on("click",".do-remove",function() {
		isModal = ($(this).parents("#theModal").length != 0) ? true:false;
		if (confirmAction()) {
			$.ajax({
				type: "POST",
				url: $(this).attr("href")
			}).done(function(data) {
				updateSystemOutput(data);
				if (isModal) {
					$("#theModal .do-close").click();
				} else {
					$("#modalContent .do-results").load(app_http+"/ #modalContent .do-results > *");
				}
			});
		}
		return false;
	});

	$("#theModal").on("submit",".do-submit",function() {
		$.ajax({
			type: "POST",
			url: app_http,
			data: $(this).serialize(),
		}).done(function(data) {
			updateSystemOutput(data);
			$(".sysMsg").html($($(data).filter("#systemBar").find(".sysMsg")).html());
			$(".do-results").load(app_http+" #modalContent .do-results > *",$(this).serialize(),function() {
				$("#theModal .do-close").click();
			});
		});
		return false;
	});

	$(".container,#theModal").on("click",".do-confirm",function() {
		return confirmAction();
	});

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

	$("#theModal .do-close").click(function(e) {
		e.preventDefault();
		$("#theModal").fadeOut("fast",function() {
			$(".container").removeClass("blur");
			$("#theOverlay").fadeOut("fast");
			$("#theModal .content").hide();
		});
	});
});