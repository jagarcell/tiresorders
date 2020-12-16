jQuery(() => {

        $('#sendNotiButton').on('click', sendNotification)

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

function sendNotification() {
    console.log('send')
    var title = document.getElementsByName('title')
    var body = document.getElementsByName('body')
    var image = document.getElementsByName('image')
    var to = document.getElementsByName('to')
    var params = {title:title, body:body, to:to}
    console.log(params)
    if(image != null){
        params.image = image
    }
    $.post('/fb/sendnotification', 
    {title:title, body:body, to:to}, 
        (data, status) => {
            console.log(data)
        }
    )
}