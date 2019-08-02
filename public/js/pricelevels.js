$(document).ready(function pricelevelsReady() {
	// body...
	$('#addPriceLevel').click(addPriceLevelClick)
	$('#updateMessage').hide()
})

function addPriceLevelClick() {
	// body...
	var pricelevelsTable = $('#pricelevelsTable tbody')
	var row = pricelevelsTable[0].insertRow(-1)
	row.innerHTML = 
		'<td class="firstCol"><input type="text"" onchange="descriptionChange(this)"></td>' +
		'<td class="secondCol"><input type="text"" onchange="percentageChange(this)"></td>' +
		'<td class="thirdCol">' +
			'<select onchange="typeChange(this)">' +
				'<option value="discount">Discount</option>' +
				'<option value="increment">Price Up</option>' +
			'</select>' +
		'</td>'
		row.scrollIntoView()
		row.children[0].children[0].focus()
}

function descriptionChange(element) {
	// body...
	checkAndSave(element)
}

function percentageChange(element) {
	// body...
	checkAndSave(element)
}

function typeChange(element) {
	// body...
	checkAndSave(element)
}

function checkAndSave(element) {
	// body...
	var row = $(element).parent().parent()

	var Cells = row[0].cells

	var OkToSave = true;

	for (var i = 1; i < Cells.length; i++) {
		if(Cells[i].children[0].value.length == 0)
		{
			OkToSave = false;
			break;
		}
	}

	if(OkToSave){

		$.get('/savepricelevel', 
			{
				id:row[0].id,
				description:Cells[0].children[0].value,
				percentage:Cells[1].children[0].value,
				type:Cells[2].children[0].value,
				rowindex:row[0].rowIndex,
			}, 
			function savePriceLevelCallBack(data, status) {
				// body...
				updateMessage(data.message)
				if(data.id != null){
					var pricelevelsTable = $('#pricelevelsTable')
					var row = pricelevelsTable[0].rows[data.rowindex]
					row.id = data.id
				}
			}
		)	
	}
}

function updateMessage(message) {
	// body...
	$('#updateMessage')[0].innerHTML = message
	$('#updateMessage').show()
	window.setTimeout(function(){$('#updateMessage').hide()}, 3000)
}
