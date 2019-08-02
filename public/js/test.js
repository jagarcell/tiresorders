$(document).ready(function testReady() {
	// body...

	$( "#orderDialog" ).dialog({ autoOpen: false });
	$( "#orderDialog" ).dialog({
	  position: { my: "right top", at: "left bottom", of: $("#orderLink") }
	});

	$('#orderLink').click(orderLinkClick)
})

function orderLinkClick() {
	// body...
	$("#orderDialog").dialog('open')
}