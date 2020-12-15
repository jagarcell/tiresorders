jQuery(() => {
        // APPLY THE DROPZONES
        /*	Dropzone.discover()
        */
        //	Dropzone.autoDiscover = false
        var dropZones = $('.dropzone')
        console.log(dropZones)

        $.each(dropZones, function (index, dropzone) {
            console.log(dropzone)
            Dropzone.options[dropzone.id] = {
                uploadMultiple: false,
                dictDefaultMessage: 'Drop An Image Or Click To Search One',
//				forceFallback : true,
                init: function dropzoneInit() {
                    // body...
                    console.log('init')
                    this.on('addedfile', function (file) {
                        // body...
                        var preview = $('.dropzone-preview')
                        console.log(preview)
                        filesAccepted = this.getAcceptedFiles()
                        if (filesAccepted.length > 0) {
                            this.removeFile(filesAccepted[0])
                        }
                    })
                },
            }
        })
    })
