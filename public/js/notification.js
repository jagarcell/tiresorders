jQuery(() => {
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
                        var preview = $('.dz-preview')
                        preview[0].style.margin = 0
                        preview[0].style.marginTop = 20

                        var dz = $('.dropzone')
                        dz[0].style.padding = 0
                        dz[0].style.paddingTop = 20
                        
                        filesAccepted = this.getAcceptedFiles()
                        if (filesAccepted.length > 0) {
                            this.removeFile(filesAccepted[0])
                        }
                    })
                },
            }
        })
    })
