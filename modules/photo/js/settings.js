(function($){

	FLBuilder.registerModuleHelper('photo', {

		rules: {
			photo: {
				required: true
			}
		},

		init: function()
		{
			var form            = $('.fl-builder-settings'),
				photoSource     = form.find('select[name=photo_source]'),
				librarySource   = form.find('select[name=photo_src]'),
				urlSource       = form.find('input[name=photo_url]'),
				align           = form.find('select[name=align]');

			// Init validation events.
			this._photoSourceChanged();

			// Validation events.
			photoSource.on('change', this._photoSourceChanged);
		},

		_photoSourceChanged: function()
		{
			var form            = $('.fl-builder-settings'),
				photoSource     = form.find('select[name=photo_source]').val(),
				photo           = form.find('input[name=photo]'),
				photoUrl        = form.find('input[name=photo_url]'),
				linkType        = form.find('select[name=link_type]');

			photo.rules('remove');
			photoUrl.rules('remove');
			linkType.find('option[value=page]').remove();

			if(photoSource == 'library') {
				photo.rules('add', { required: true });
				linkType.append('<option value="page">' + FLBuilderStrings.photoPage + '</option>');
			}
			else {
				photoUrl.rules('add', { required: true });
			}
		}
	});

})(jQuery);