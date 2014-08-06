function closeFloat() {
	$("#float").fadeOut("slow");
}
function loading(close) {
	if (close)
		$("#loading").show("slow");
	else
		$("#loading").hide("slow");
}
function anim() {
	$("#logo_img").stop(true, false).animate({
		"marginTop" : "300px",
		"width" : "290px"
	}, 2000, function () {
		$("#logo_img").css({
			"margin-top" : "0",
			"width" : "auto"
		});
	});
	if ($("#channel option:selected").val() == "canary") {
		$("#logo_img").attr("src", "images/canary.png");
	} else {
		$("#logo_img").attr("src", "images/chrome.png");
	}
}

$(function () {
	$("form").bind("submit", function () {
		loading(true);
		$.ajax({
			url : "response.php",
			type : "POST",
			dataType : "xml",
			data : {
				"channel" : $("#channel option:selected").val(),
				"arch" : $("#processor option:selected").val()
			},
			timeout : 10000,
			error : function (xml, type, excp) {
				alert(type);
				loading(false);
			},
			success : function (xml) {
				$("#info").html($(xml).find("info").html());
				$("#urls").html($(xml).find("urls").html());
				loading(false);
				$("#float").fadeIn();
			}
		});
		return false;
	});

	$("select").bind("change", anim);
	$("#logo_img").bind("click", anim);

});
