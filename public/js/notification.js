jQuery(() => {

        $('#sendNotiButton').on('click', sendNotification)
        $('#bodyText').on('change keyup paste', messageTextChange)

        // APPLY THE DROPZONES
        /*	Dropzone.discover()
        */
        //	Dropzone.autoDiscover = false
        var dropZones = $('.dropzone')

        $.each(dropZones, function (index, dropzone) {
            Dropzone.options[dropzone.id] = {
                uploadMultiple: false,
                dictDefaultMessage: 'Drop An Image Or Click To Search One',
//				forceFallback : true,
                init: function dropzoneInit() {
                    // body...
                    this.on('addedfile', function (file) {
                        // body...
                        var notiImage = $('#notiImage')
                        notiImage.attr('value', notiImage.val() + '/' + file.name)
                        notiImage.attr('name', 'image')
                        filesAccepted = this.getAcceptedFiles()
                        if (filesAccepted.length > 0) {
                            this.removeFile(filesAccepted[0])
                        }
                    })
                },
            }
        })
    }
)

function messageTextChange() {
    var messageText = $('#bodyText')
    var nChars = $('#nChars')
    nChars[0].value = messageText[0].value.length
}

function sendNotification() {
    var title = document.getElementsByName('title')[0].value
    var body = document.getElementsByName('body')[0].value
    var image = document.getElementsByName('image')[0] == undefined ? null : 
                    document.getElementsByName('image')[0].value
    var to = document.getElementsByName('to')[0].value

    var params = {title:title, body:body, to:to}

    if(image != null){
        params.image = image
    }

    $.post('/fb/sendnotification', 
        params, 
        function (data, status) {
            var results = JSON.parse(data)
            if(results.success > 0)
            {
                $('#nSuccess')[0].textContent = results.success
                $('.successMessagesDiv')[0].style = 'display:inline-block;'
                setTimeout(()=>{$('.successMessagesDiv')[0].style = 'display:none;'}, 5000)
            }
            if(results.failure > 0)
            {
                $('#nFailure')[0].textContent = results.failure
                $('.failureMessagesDiv')[0].style = 'display:inline-block;'
                setTimeout(()=>{$('.failureMessagesDiv')[0].style = 'display:none;'}, 5000)
            }
        }
    )
}