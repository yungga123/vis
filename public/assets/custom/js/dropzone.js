/**
 * Dropzone initialization
 *
 * @param {string} id       - the id selector name of the elem/form
 * @param {string} route    - the route or url to upload to
 * @param {object} options  - the config options
 * @return {object}         - the dropzone object
 */
function dropzoneInit(id, route, button, options) {
	const _defaultMsgIcon =
		'<div class="text-xl"><i class="fas fa-cloud-upload-alt fa-lg text-primary"></i></div> ';
	const _defaultAcceptedFiles =
		".jpg, .jpeg, .png, .pdf, .doc, .docx, xls, .xlsx, .csv";
	let _defaultMsg = `Drop files here or click to upload.`;

	_defaultMsg = inObjectReturn(options, "dictDefaultMessage") ?? _defaultMsg;

	const _options = {
		// The route or url to upload to
		url: route,
		paramName: inObjectReturn(options, "paramName") ?? "file",
		// Max file size in MB
		maxFilesize: inObjectReturn(options, "maxFilesize") ?? 15,
		// How many files allowed to be selected/uploaded
		maxFiles: inObjectReturn(options, "maxFiles") ?? 5,
		dictDefaultMessage: _defaultMsgIcon + _defaultMsg,
		acceptedFiles:
			inObjectReturn(options, "acceptedFiles") ?? _defaultAcceptedFiles,
		addRemoveLinks: inObjectReturn(options, "addRemoveLinks") ?? true,
		dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
		// Disable auto processing
		autoProcessQueue: inObjectReturn(options, "autoProcessQueue") ?? false,
		uploadMultiple: inObjectReturn(options, "uploadMultiple") ?? true,
		parallelUploads: inObjectReturn(options, "parallelUploads") ?? 10,
		// Added a custom option attribute/key
		// An identifier whether to remove
		// also the file in the backend
		removeFileInBackend: false,
	};

	if (!isEmpty(options)) {
		$.each(options, (key, val) => {
			if (!inObject(_options, key)) _options[key] = val;
		});
	}

	const _dropzone = new Dropzone("#" + id, _options);

	if (_options.uploadMultiple === false) {
		// Added a custom option attribute/key
		// For storing an uploaded file for single upload
		_dropzone.uploadedFile = [];
		_dropzone.element.classList.add("dz-single-upload");
	}

	if (button) {
		dzOnUploadFile(_dropzone, button);
		dzOnSuccessEvent(_dropzone, button);
		dzOnSendingEvent(_dropzone, button);
		dzOnCompleteEvent(_dropzone, button);
		dzOnErrorEvent(_dropzone, button);
		dzOnAddedFileEvent(_dropzone);
	}

	return _dropzone;
}

/**
 * Dropzone on button upload click event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} button 	    the button used to trigger to upload files with selector (id [.] or class [.])
 * @return {void}
 */
function dzOnUploadFile(_dropzone, button) {
	$(button).on("click", function (e) {
		// Make sure that the form isn't actually being sent.
		e.preventDefault();
		e.stopPropagation();

		// Get the rejected files and then check it
		// If there's at least notify user thru swal notif
		const rejected = _dropzone.getRejectedFiles();
		if (rejected.length > 0) {
			const message =
				"There's an error encountered. Please double check your selected file(s)!";
			notifMsgSwal(TITLE.ERROR, message, STATUS.WARNING);
			return;
		}

		// Get the queued files and then check it
		// If there's none notify user thru swal notif
		const queued = _dropzone.getQueuedFiles();
		if (queued.length === 0 || _dropzone.files.length === 0) {
			const message =
				"There's nothing to upload. Please drop or select at least one file!";
			notifMsgSwal(TITLE.ERROR, message, STATUS.WARNING);
			return;
		}

		swalNotifConfirm(
			() => {
				// If yes and has no error, manually trigger the upload
				_dropzone.processQueue();
			},
			TITLE.CONFIRM,
			"continue",
			STATUS.QUESTION
		);
	});
}

/**
 * Dropzone on added/selected/dropped file event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {callable} callback 	the optional callback function
 * @return {void}
 */
function dzOnAddedFileEvent(_dropzone, callback) {
	const isMultiple = dzIsMultipleUpload(_dropzone);

	_dropzone.on("addedfile", function (file) {
		if (!isMultiple && !inObject(file, "_id")) {
			// Loop through all files
			const length = _dropzone.files.length;

			$.each(_dropzone.files, (key, file) => {
				const condition =
					inObject(file, "_id") ||
					(!inObject(file, "_id") && length > 1 && key === 0);
				// Remove the uploaded one
				if (condition) _dropzone.removeFile(file);
			});
		}

		if (isFunction(callback)) callback(file);
	});
}

/**
 * Dropzone on sending event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} button 	    the button used to trigger to upload files with selector (id [.] or class [.])
 * @param {callable} callback 	the optional callback function
 * @return {void}
 */
function dzOnSendingEvent(_dropzone, button, callback) {
	const isMultiple = dzIsMultipleUpload(_dropzone);
	const event = isMultiple ? "sendingmultiple" : "sending";

	_dropzone.on(event, function (files, xhr, formData) {
		showLoading();
		if (button) $(button).attr("disabled", "true");

		if (isFunction(callback)) callback(files, xhr, formData);
	});
}

/**
 * Dropzone on complete event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} button 	    the button used to trigger to upload files with selector (id [.] or class [.])
 * @param {callable} callback 	the optional callback function
 * @return {void}
 */
function dzOnCompleteEvent(_dropzone, button, callback) {
	const isMultiple = dzIsMultipleUpload(_dropzone);
	const event = isMultiple ? "completemultiple" : "complete";

	_dropzone.on(event, function (files) {
		closeLoading();
		if (button) $(button).removeAttr("disabled");

		if (isFunction(callback)) callback(files);
	});
}

/**
 * Dropzone on succes event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} button 	    the button used to trigger to upload files with selector (id [.] or class [.])
 * @param {callable} callback 	the optional callback function
 * to replace the logic/process below
 * @return {void}
 */
function dzOnSuccessEvent(_dropzone, button, callback) {
	const isMultiple = dzIsMultipleUpload(_dropzone);
	const event = isMultiple ? "successmultiple" : "success";

	_dropzone.on(event, function (files, response) {
		if (isFunction(callback)) {
			callback(files, response);
		} else {
			if (inObject(response, "files") && !isEmpty(response.files)) {
				if (isMultiple) {
					// Remove the initial selected files
					$.each(files, (i, _file) => _dropzone.removeFile(_file));
					// Populate the new uploaded files
					$.each(response.files, (i, _file) =>
						dzPopulateFile(_dropzone, _file)
					);
				} else {
					// Remove the initial selected files
					_dropzone.removeFile(files);
					// Populate the new uploaded files
					dzPopulateFile(_dropzone, response.files);
				}
			}

			const message = response.errors ?? response.message;
			notifMsgSwal(response.status, message, response.status);
		}

		if (button) $(button).removeAttr("disabled");
		closeLoading();
	});
}

/**
 * Dropzone on error event
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} button 	    the button used to trigger to upload files with selector (id [.] or class [.])
 * @param {callable} callback 	the optional callback function
 * @return {bool|void}
 */
function dzOnErrorEvent(_dropzone, button, callback) {
	const isMultiple = dzIsMultipleUpload(_dropzone);
	const event = isMultiple ? "errormultiple" : "error";

	_dropzone.on(event, function (files, response) {
		closeLoading();
		notifMsgSwal(
			TITLE.ERROR,
			"It seems there's an error encountered. Please refresh the page and try again! If persist, contact your system administrator. <br/><br/> <strong>Error message:</strong> " +
				response,
			STATUS.ERROR
		);

		if (button) $(button).removeAttr("disabled");
		if (isFunction(callback)) callback(files, response);
	});
}

/**
 * Getting files from backend
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} route 	    the route/url to get files from
 * @param {object} data	        the data to be passed to request
 * @param {string} method	    the method type [default - get] - post, ajax or get
 * @return {bool|void}
 */
function dzGetFiles(_dropzone, route, data, method) {
	showLoading();

	// Remove all files - reset dropzone
	_dropzone.removeAllFiles(true);
	// Set to false for backend files will not be included in the remove
	_dropzone.options.removeFileInBackend = false;

	method = method || METHOD.GET;
	data = method === METHOD.GET ? null : data;

	$.ajax({
		url: route,
		type: method,
		data: data,
		dataType: "json",
		contentType: false,
		cache: false,
		processData: false,
		success: function (res) {
			if (!isEmpty(res.files)) {
				$.each(res.files, (i, file) => {
					// Populate uploaded files to dropzone
					dzPopulateFile(_dropzone, file);
				});
			}

			closeLoading();
		},
		error: (err) => catchErrMsg(err),
	});
}

/**
 * Populate/show file to dropzone
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {object|array} file 	the file to populate/show
 * @return {void}
 */
function dzPopulateFile(_dropzone, file) {
	if (file) {
		// Update Dropzone's total upload progress
		_dropzone.options.addedfile.call(_dropzone, file);
		_dropzone.options.complete.call(_dropzone, file);
		if (file.is_img)
			_dropzone.options.thumbnail.call(_dropzone, file, file.url);

		// Push file to dropzone files
		_dropzone.files.push(file);

		if (!dzIsMultipleUpload(_dropzone)) {
			_dropzone.uploadedFile = file;
		}

		// Add download file event listener
		dzAddDownloadFileEvent(_dropzone, file);
	}
}

/**
 * Remove file(s) in the backend
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {string} route 	    the route/url to get files from
 * @param {object} data	        the data to be passed to request
 * @return {bool|void}
 */
function dzOnRemoveFileEvent(_dropzone, route, data) {
	_dropzone.on("removedfile", function (file) {
		const dzDefaultMsgElem = $(_dropzone.element).find(
			".dz-default.dz-message"
		);
		const dzPreview = $(_dropzone.element).find(".dz-preview");

		// Whether to show the default message
		if (dzPreview.length > 0) dzDefaultMsgElem.addClass("d-none");
		else dzDefaultMsgElem.removeClass("d-none");

		// Check if removed file is an uploaded one
		// If yes, remove the corresponding file on the server
		if (inObject(file, "_id")) {
			// Added a timeout to delay
			setTimeout(() => {
				dzRemoveFileInServer(_dropzone, file, route, data);
			}, 100);
		}
	});
}

/**
 * Remove file(s) on the backend
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {object|array} file   the file to remove
 * @param {string} route 	    the route/url to get files from
 * @param {object} data	        the data to be passed to request
 * @return {void}
 */
function dzRemoveFileInServer(_dropzone, file, route, data) {
	if (!_dropzone.options.removeFileInBackend) {
		if (file.previewElement.length > 0) _dropzone.removeFile(file);
		return;
	}

	const statuses = ["uploaded", "success"];
	if (inArray(statuses, file.status) && file.removeFileInBackend) {
		const request = isEmpty(data)
			? {
					id: file._id,
					filename: file.name,
			  }
			: data;

		$.post(route, request)
			.then((res) => {
				const message = res.errors ?? res.message;
				notifMsgSwal(res.status, message, res.status);
				// Reset
				_dropzone.options.removeFileInBackend = false;
			})
			.catch((err) => catchErrMsg(err));
	} else {
		notifMsgSwal(TITLE.INFO, "Removed file hasn't uploaded yet!", STATUS.INFO);
	}
}

/**
 * Add donwload file event when user click the file
 * it will automatically downloaded
 *
 * @param {object} _dropzone 	the dropzone object
 * @param {object|array} file 	the file to add the event
 * @return {void}
 */
function dzAddDownloadFileEvent(_dropzone, file) {
	if (file) {
		file.previewElement.classList.add("dz-uploaded");
		file.previewElement.classList.add("dz-preview-" + file._id);
		file.removeFileInBackend = false;

		for (let node of file.previewElement.children) {
			if (node.className === "dz-details") {
				node.addEventListener("click", () => window.open(file.url, "_blank"));
			}
		}

		for (let removeLink of file.previewElement.querySelectorAll(
			"[data-dz-remove]"
		)) {
			removeLink.addEventListener("click", function (e) {
				e.preventDefault();
				e.stopPropagation();

				_dropzone.options.removeFileInBackend = false;

				// Set true, if file is an uploaded one
				if (file._id) {
					_dropzone.options.removeFileInBackend = true;
					file.removeFileInBackend = true;
				}
			});
		}
	}
}

/**
 * Check if dropzone is a multiple upload
 *
 * @param {object} _dropzone 	the dropzone object
 * @return {bool}
 */
function dzIsMultipleUpload(_dropzone) {
	return _dropzone.options.uploadMultiple === true;
}
