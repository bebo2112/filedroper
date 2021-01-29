<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <form id="documents_form">

            <!-- Drop Zone -->
            <div id="dropzone" class="dropzone" onclick="document.getElementById('files').click();">
                <input type="file" name="files[]" id="files" multiple hidden />
                <label>
                    <strong>Click</strong> to add files or <strong>Drag</strong> files here</span>.
                </label>
            </div>

            <!-- File Previews -->
            <div class="dropzone-file-preview"></div>

            <!-- Request Document ID -->
            <input type="number" name="request_document_id" value="1" id="request_document_id" hidden required>

            <input type="text" name="submit_documents" value="q" hidden>

            <!-- Submit Button -->
            <div class="row py-3">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-dark">Upload</button>
                </div>
            </div>

        </form>
    </div>



    <script>
        var uploadList = [];
        var max_upload_size = 5120;
        var dropZone = document.getElementById('dropzone');
        dropZone.addEventListener('dragover', handleDragOver, false);
        dropZone.addEventListener('drop', handleFileSelect, false);

        // Drop File In Dropzone
        function handleFileSelect(evt) {
            evt.stopPropagation();
            evt.preventDefault();

            var files = evt.dataTransfer.files; // File List object.

            processFiles(files);
        }

        // Handel Drag Over
        function handleDragOver(evt) {
            evt.stopPropagation();
            evt.preventDefault();
            evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
        }

        // Add Files To Input
        $('#files').on("change", function(e) {
            var files = $('#files').prop('files');
            processFiles(files);
        });


        function processFiles(files) {
            console.log(files.length)
            // Add Files To uploadList Array And Check If File Size Is Valid
            for (var i = 0; i < files.length; i++) {
                // Check If File Size  Esists
                if ((files[i].size * .000010) > max_upload_size) {
                    // add to error array
                    console.log('file too big');
                } else {
                    var item_index = null;
                    // Check To See If File Already Esists
                    for (var j = 0; j < uploadList.length; j++) {
                        if (files[i].name == uploadList[j].name) {
                            item_index = j;
                        }
                    }
                    // Add File To uploadList Array
                    if (item_index != null) {
                        console.log('replace file');
                        uploadList[item_index] = files[i];
                    } else {
                        console.log('new file');
                        uploadList.push(files[i]);
                    }
                }
            }


            // Render File List Preview
            var files_preview = ''
            for (var i = 0; i < uploadList.length; i++) {
                console.log(uploadList)
                files_preview += `<div class="form-row align-items-center container-fluid py-1">`;
                files_preview += `    <div class="col">`;
                files_preview += `        <div>${uploadList[i].name}</div>`;
                files_preview += `        <p class="small text-muted mb-0"><strong>${parseFloat(parseInt(uploadList[i].size) * .0000010).toFixed(3)}</strong> MB</p>`;
                files_preview += `    </div>`;
                files_preview += `    <div class="col-auto">`;
                files_preview += `        <a class="text-muted-light" onclick="removeFile(${i});" style="cursor: pointer;">`;
                files_preview += `            <i class="material-icons">close</i>`;
                files_preview += `        </a>`;
                files_preview += `    </div>`;
                files_preview += `</div>`;
            }
            $('.dropzone-file-preview').html(files_preview);
            $('#files').files = uploadList;

        }


        function removeFile(index) {
            uploadList.splice(index, 1);
            processFiles(uploadList);
        }

















        $('#documents_form').submit(function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form_data = new FormData();
            for (var i = 0; i < uploadList.length; i++) {
                form_data.append('file_' + i, uploadList[i]);
            }
            form_data.append('submit_documents', "");
            // form_data.append('project_trade_proposal_id', $('#project_trade_proposal_id').val());

            $.ajax({
                url: "./upload.php",
                type: "POST",
                data: form_data,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    if (response['status'] == 'success') {
                        $('#documents_form').trigger('reset');
                        if (response['data']) {
                            alert("The following files are either too lare or are unsupported formats.</br>" +
                                $.each(response['data'], function(key, value) {
                                    value + "</br>"
                                })
                            );
                        }
                    }
                }
            });
        });
    </script>
</body>



</html>