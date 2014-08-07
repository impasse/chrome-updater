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
		//reset li
		$("li").remove();
		$.ajax({
			url : "response.php",
			type : "POST",
			dataType : "xml",
			data : {
				"channel" : $("#channel option:selected").val(),
				"arch" : $("#processor option:selected").val()
			},
			timeout : 30000,
			error : function (xml, type, excp) {
				alert(type);
				loading(false);
			},
			success : function (xml) {
				$(xml).find("info").each(function (id) {
					a = $(this).text();
					$("<li>" + a + "</li>").appendTo("#info");
				});
				$(xml).find("url").each(function (id) {
					a = $(this).text();
					$("<li><a href=\"" + a + "\">" + a + "</a></li>").appendTo("#urls");
				});
				loading(false);
				$("#float").fadeIn();
			}
		});
		return false;
	});

	$("select").bind("change", anim);
	$("#logo_img").bind("click", anim);

});
