jQuery(() => {

        /* Events Handlers */
        var d = new Date()
        var n = d.getTimezoneOffset()

        window.onresize = resizeWindow


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
                        filesAccepted = this.getAcceptedFiles()
                        if (filesAccepted.length > 0) {
                            this.removeFile(filesAccepted[0])
                        }
                    })
                },
            }
        })
    })