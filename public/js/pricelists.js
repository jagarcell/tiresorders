var keyup
var savePricesFlag

$(document).ready(function pricelistsReady() {
	// body...
	$('#newListButton').click(newListButtonClick)
	$('#deleteListButton').click(deleteListButtonClick)
	$('#goButtonId').click(goButtonClick)

	$('#descriptionSelect').change(descriptionSelectChange)

	$('#newListDescription').hide()
	$('#deletedMessage').hide()

	var newListDescription = document.getElementById('newListDescription')

	// For all the Browsers
	if(newListDescription.addEventListener){
		newListDescription.addEventListener('keyup', function() {
			if(event.key === 'Escape'){
				var id = $('#descriptionSelect').children("option:selected").val()
				getPriceList(id)
				$('#newListDescription').hide()
				$('#priceChangeFactor').show()
				$('#descriptionSelect').show()
				keyup = true
				event.preventDefault()
			}
		})
	}
	// For IE 11 and below
	else{
		if(newListDescription.attachEvent){
			newListDescription.attachEvent('keyup', function() {
				if(event.key === 'Escape'){
					var id = $('#descriptionSelect').children("option:selected").val()
					getPriceList(id)
					$('#newListDescription').hide()
					$('#priceChangeFactor').hide()
					$('#descriptionSelect').show()
					keyup = true
					event.preventDefault()
				}
			})
		}
	}

	savePrices = false

	set_layout()

})

function set_layout() {
	// body...
	$.get('listqty', function listqtyCallBack(data, status) {
		// body...
		if(data.status == 'ok'){

			if(data.listqty > 0){
				$('#thereAreNotLists').hide()
				$('#thereAreLists').show()
			}
			else{
				$('#thereAreNotLists').show()
				$('#thereAreLists').hide()
			}
		}
	})
}

function goButtonClick() {
	// body...
	savePricesFlag = true
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}

	var priceInputs = $('.priceInput')
	var percentage = Number.parseFloat($('#percentage')[0].value)
	var priceChangeType = $('#priceChangeType')[0].value


	$.each(priceInputs, function (index, priceInput) {
		// body...
		var price = Number.parseFloat($(priceInput)[0].value)
		var changeValue = price * (percentage/100)
		if(priceChangeType == 'up'){
			$('.priceInput')[index].value = Number.parseFloat(price + changeValue).toFixed(2)
		}
		else{
			$('.priceInput')[index].value = Number.parseFloat(price - changeValue).toFixed(2)
		}
		$($('.priceInput')[index].parentNode.parentNode).addClass('changed')
		$($('.priceInput')[index].parentNode.parentNode).removeClass('notmodified')

	})
	
	saveAllPrices()
	savePricesFlag = false
}

function newListDescriptionChange() {
	// body...
}

function newListDescriptionKeyUp() {
	// body...0
	console.log(this)
}
function priceValueChange(price) {
	// body...
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}
	price.value = Number.parseFloat(price.value).toFixed(2)
	$(price.parentNode.parentNode).addClass('changed')

	saveChangedPrices()
}

var descriptionChange = false

function newListButtonClick() {
	// body...

	var descriptionSelect = document.getElementById('descriptionSelect')

	if(descriptionChange){
		$('#descriptionSelect').show()
		$('#priceChangeFactor').show()
		$('#newListDescription').hide()
		descriptionChange = false
		return
	}

	$('#descriptionSelect').hide()
	$('#priceChangeFactor').hide()
	$('#newListDescription').show()

	var priceListTableBody = $('#priceListTable tbody')[0]
	var priceListTableRows = $('#priceListTable tbody tr')[0]
	priceListTableBody.innerHTML = ''
	$('#newListDescription').focus()
}

function newListDescriptionChange(description) {
	// body...
	if(keyup){
		keyup = false
		return
	}

	descriptionChange = true

	var descriptionSelect = $('#descriptionSelect')[0]

	var optionElementReference = new Option(description.value, 0, false, true);

	if(description.value.length == 0){
		if(window.confirm('YOU MUST ENTER A DESCIPTION TO CREATE A NEW LIST'))
		{
		}
		else{
			$('#descriptionSelect').hide()
			$('#priceChangeFactor').hide()
			$('#newListDescription').show()
		}
		return
	}

	$('#descriptionSelect').show()
	$('#priceChangeFactor').show()
	$('#newListDescription').hide()

	$.get('/createnewlist', 
		{
			listDescription:description.value
		}, function creteNewListCallBack(data, status) {
		// body...
		if(!Number.parseFloat){
			Number.parseFloat = window.parseFloat
		}

		// check status
		
		if(data.status == 'ok'){
			optionElementReference.value = data.pricelistid
			pricelistlines = data.pricelistlines
			$.each(pricelistlines, function pricelistlnesCallBack(index, line) {
				// body...
				var priceListTableBody = $('#priceListTable tbody')[0]
				var row = priceListTableBody.insertRow(-1)
				row.innerHTML =
					'<td class="itemColumn">' + line.description + '</td>' +
					'<td class="priceColumnValue"><input type="number" value=' + Number.parseFloat(line.price).toFixed(2) + ' class="priceInput"  onchange="priceValueChange(this)"></td>'
				row.id = line.id
				$(row).addClass('notmodified')
			})

			// set layout
			set_layout()
		}
	})

	descriptionSelect.options.add(optionElementReference)
	var lastindex = descriptionSelect.options.length
	descriptionSelect.selectedIndex = lastindex - 1
	description.value = ''
}

function descriptionSelectChange() {
	// body...
	priceListHeaderId = this.value

	saveChangedPrices()

	getPriceList(priceListHeaderId)
}

function saveChangedPrices(argument) {
	// body...
	var changedPrices = $('.changed')

	var prices = {}

	if(changedPrices.length > 0){
		$.each(changedPrices, function (index, changedPrice) {
			// body...
			$(changedPrice).removeClass('changed')
			$(changedPrice).removeClass('notmodified')
			var priceInput = $($(changedPrice).children('.priceColumnValue')[0]).children('.priceInput')[0]
			var price = priceInput.value
			var row = priceInput.parentNode.parentNode
			prices[row.id] = price
		})

		$.get('/updateprices', {prices:prices}, function updatePricesCallBack(data, status) {
			// body...
			if(data.status == 'fail'){
				alert("THERE WHERE SOME PRICES THAT COULDN'T BE UPDATED");
			}
		})
	}
}

function saveAllPrices() {
	// body...
	var changedPrices = $('.changed')
	changedPricesChunks = chunkArray(changedPrices, 10)
	var status = 'ok'
	var nChunks = changedPricesChunks.length

	for(var i = 0; i < changedPricesChunks.length; i++){
		var changedPrices = changedPricesChunks[i]

		var prices = {}

		if(changedPrices.length > 0){
			$.each(changedPrices, function (index, changedPrice) {
				// body...
				$(changedPrice).removeClass('changed')
				var priceInput = $($(changedPrice).children('.priceColumnValue')[0]).children('.priceInput')[0]
				var price = priceInput.value
				var row = priceInput.parentNode.parentNode
				prices[row.id] = price
			})

			$.get('/updateprices', {prices:prices}, function updatePricesCallBack(data, status) {
				// body...
				if(data.status == 'fail'){
					status = 'fail'
				}
				nChunks--
				if(nChunks == 0 && status == 'fail'){
					alert("THERE WHERE SOME PRICES THAT COULDN'T BE UPDATED");
				}
			})
		}
	}
}

function getPriceList(priceListHeaderId) {
	// body...

	$.get('/pricelistbyid', {id:priceListHeaderId}, function priceListByIdCallBack(data, status) {
		// body...
		if(data.status == 'ok'){
			pricelistlines = data.pricelist.lines
			var priceListTableBody = $('#priceListTable tbody')[0]
			priceListTableBody.innerHTML = ''
			$.each(pricelistlines, function pricelistlnesCallBack(index, line) {
				// body...
				var row = priceListTableBody.insertRow(-1)
				row.innerHTML =
					'<td class="itemColumn">' + line.description + '</td>' +
					'<td class="priceColumnValue"><input type="number" value=' + Number.parseFloat(line.price).toFixed(2) + ' class="priceInput"  onchange="priceValueChange(this)"></td>'
				row.id = line.id
				$(row).addClass('listTableBodyRow')
				if(line.modified == 0){
					$(row).addClass('notmodified')
				}
			})
		}
		else
		{
			if(data.message == 'LIST NOT FOUND'){
				var priceListTableBody = $('#priceListTable tbody')[0]
				priceListTableBody.innerHTML = ''
			}
		}
	})	
}

function deleteListButtonClick() {
	// body...
	var id = $('#descriptionSelect').children("option:selected").val()

	var selectedIndex = $('#descriptionSelect').children("option:selected").index()
	
	$.get('/findusersbypricelist', {pricelistid:id}, function findusersbypricelistCallBack(data, status) {
		// body...
		if(data.status == 'ok'){
			if(data.users.length > 0){
				if(!confirm('THIS PRICE LIST IS ASIGNED TO SOME USERS. IF YOU DELETE IT THOSE USERS WILL NEED A NEW ASSIGNMENT')){
					return
				}
			}
			
			$.get('/deletelistbyid', {id:id}, function deletelistbyidCallBack(data, status) {
				// body...
				if(data.status == 'ok'){
					$('#deletedMessage').show()
					setTimeout(function(){
						$('#deletedMessage').hide()
					}, 3000)

					$('#descriptionSelect option[value =' + id + ']').remove()
					selectedIndex--
					if(selectedIndex < 0){
						selectedIndex = 0
					}
					$('#descriptionSelect')[0].selectedIndex = selectedIndex
					id = $('#descriptionSelect').children("option:selected").val()
					getPriceList(id)

					set_layout()
				}
			})
		}
		else{
			alert('SOMETHING WENT WRONG, PLEASE TRY AGAIN\nSERVER MESSAGE: ' + data.message)
		}
	})
}

/**
 * Returns an array with arrays of the given size.
 *
 * @param myArray {Array} array to split
 * @param chunk_size {Integer} Size of every group
 */
function chunkArray(myArray, chunk_size){
    var index = 0;
    var arrayLength = myArray.length;
    var tempArray = [];
    
    for (index = 0; index < arrayLength; index += chunk_size) {
        myChunk = myArray.slice(index, index+chunk_size);
        // Do something if you want with the group
        tempArray.push(myChunk);
    }

    return tempArray;
}