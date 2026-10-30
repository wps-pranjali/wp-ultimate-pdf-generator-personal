/**
 * Custom font manager: upload .ttf/.woff/.woff2 files or install Google Fonts.
 */
jQuery(function ($) {
	var $manager = $('#mwbpdf-font-manager');
	if (!$manager.length || typeof mwbpdf_fonts_obj === 'undefined') {
		return;
	}
	var i18n = mwbpdf_fonts_obj.i18n;
	var $status = $manager.find('.mwbpdf-font-status');
	var fontSelects = '#content_font_pdf, #header_font_pdf, #footer_font_pdf, #water_font';
	var knownCustom = {};

	function setStatus(message, type) {
		$status.removeClass('is-error is-success is-busy').addClass(type ? 'is-' + type : '').text(message || '');
	}

	function setBusy(busy) {
		$manager.find('button, input, select').not('.mwbpdf-google-variant[value=regular]').prop('disabled', busy);
	}

	function renderFonts(data) {
		var $body = $manager.find('.mwbpdf-font-table tbody').empty();
		if (!data.rows.length) {
			$body.append($('<tr/>').append($('<td colspan="5"/>').text(i18n.no_fonts)));
		}
		$.each(data.rows, function (i, row) {
			$body.append(
				$('<tr/>')
					.append($('<td/>').text(row.label))
					.append($('<td/>').append($('<code/>').text(row.name)))
					.append($('<td/>').text($.map(row.variants, function (v) { return i18n['variant_' + v] || v; }).join(', ')))
					.append($('<td/>').text(i18n['source_' + row.source] || row.source))
					.append($('<td/>').append(
						$('<button type="button" class="button-link mwbpdf-font-delete"/>').attr('data-key', row.key).text(i18n.remove)
					))
			);
		});

		// Refresh every PDF font dropdown: drop removed custom fonts, add new ones.
		var names = {};
		$.each(data.dropdown, function (label, name) {
			names[name] = true;
		});
		$(fontSelects).each(function () {
			var $select = $(this);
			$select.find('option').each(function () {
				if (knownCustom[this.value] && !names[this.value]) {
					$(this).remove();
				}
			});
			$.each(data.dropdown, function (label, name) {
				if (!$select.find('option').filter(function () { return this.value === name; }).length) {
					$select.append($('<option/>').val(name).text(label));
				}
			});
		});
		knownCustom = names;
	}

	function request(formData) {
		formData.append('nonce', mwbpdf_fonts_obj.nonce);
		return $.ajax({
			url: mwbpdf_fonts_obj.ajaxurl,
			type: 'POST',
			data: formData,
			dataType: 'json',
			cache: false,
			processData: false,
			contentType: false
		}).then(function (response) {
			if (!response || !response.success) {
				return $.Deferred().reject(response && response.data && response.data.message ? response.data.message : i18n.error);
			}
			renderFonts(response.data);
			return response.data;
		}, function (xhr) {
			var json = xhr.responseJSON;
			return $.Deferred().reject(json && json.data && json.data.message ? json.data.message : i18n.error);
		});
	}

	function readFile(file) {
		return new Promise(function (resolve, reject) {
			var reader = new FileReader();
			reader.onload = function () { resolve(new Uint8Array(reader.result)); };
			reader.onerror = reject;
			reader.readAsArrayBuffer(file);
		});
	}

	// WOFF2 is Brotli-compressed with transformed glyph tables, which PHP cannot decode
	// on most hosts, so it is converted to TrueType in the browser before upload.
	function prepareFile(file) {
		if (!/\.woff2$/i.test(file.name)) {
			return Promise.resolve({ blob: file, name: file.name });
		}
		if (!window.mwbpdfWoff2) {
			return Promise.reject(i18n.woff2_failed);
		}
		setStatus(i18n.converting, 'busy');
		return readFile(file).then(function (bytes) {
			return window.mwbpdfWoff2.decompress(bytes);
		}).then(function (ttf) {
			return {
				blob: new Blob([ttf], { type: 'font/ttf' }),
				name: file.name.replace(/\.woff2$/i, '.ttf')
			};
		}).catch(function () {
			return Promise.reject(i18n.woff2_failed);
		});
	}

	$('#mwbpdf-font-upload').on('click', function () {
		var input = document.getElementById('mwbpdf-font-file');
		var file = input.files && input.files[0];
		if (!file) {
			setStatus(i18n.choose_file, 'error');
			return;
		}
		if (!/\.(ttf|woff2?)$/i.test(file.name)) {
			setStatus(i18n.bad_type, 'error');
			return;
		}
		if (file.size > mwbpdf_fonts_obj.max_size) {
			setStatus(i18n.too_big, 'error');
			return;
		}
		setBusy(true);
		var label = $.trim($('#mwbpdf-font-label').val()) ||
			file.name.replace(/\.(ttf|woff2?)$/i, '').replace(/[-_]+/g, ' ');

		prepareFile(file).then(function (prepared) {
			setStatus(i18n.uploading, 'busy');
			var data = new FormData();
			data.append('action', 'mwbpdf_upload_font');
			data.append('font_file', prepared.blob, prepared.name);
			data.append('font_label', label);
			data.append('font_variant', $('#mwbpdf-font-variant').val());
			return request(data);
		}).then(function () {
			input.value = '';
			setStatus(i18n.installed, 'success');
		}, function (message) {
			setStatus(typeof message === 'string' ? message : i18n.error, 'error');
		}).then(function () {
			setBusy(false);
		});
	});

	$('#mwbpdf-google-install').on('click', function () {
		var family = $.trim($('#mwbpdf-google-family').val());
		if (!family) {
			setStatus(i18n.enter_family, 'error');
			return;
		}
		var data = new FormData();
		data.append('action', 'mwbpdf_install_google_font');
		data.append('family', family);
		$manager.find('.mwbpdf-google-variant:checked').each(function () {
			data.append('variants[]', this.value);
		});
		setBusy(true);
		setStatus(i18n.installing, 'busy');
		request(data).then(function () {
			$('#mwbpdf-google-family').val('');
			setStatus(i18n.installed, 'success');
		}, function (message) {
			setStatus(message, 'error');
		}).always(function () {
			setBusy(false);
		});
	});

	$manager.on('click', '.mwbpdf-font-delete', function () {
		if (!window.confirm(i18n.confirm_delete)) {
			return;
		}
		var data = new FormData();
		data.append('action', 'mwbpdf_delete_font');
		data.append('key', $(this).attr('data-key'));
		setBusy(true);
		setStatus('');
		request(data).then(function () {
			setStatus(i18n.deleted, 'success');
		}, function (message) {
			setStatus(message, 'error');
		}).always(function () {
			setBusy(false);
		});
	});

	// The manager sits inside the advanced settings form; Enter must not submit it.
	$manager.on('keydown', 'input[type=text]', function (e) {
		if (e.key === 'Enter') {
			e.preventDefault();
			$(this).closest('.mwbpdf-font-panel').find('.button-primary').trigger('click');
		}
	});

	renderFonts(mwbpdf_fonts_obj.fonts);
});
