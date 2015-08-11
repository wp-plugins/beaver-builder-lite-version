(function($){

	FLBuilder.registerModuleHelper('video', {

		rules: {
			embed_code: {
				required: true
			},
			video: {
				required: true
			},
		},

		init: function()
		{
			var form = $('.fl-builder-settings'),
				type = form.find('select[name=video_type]');
			
			type.on('change', this._typeChanged);
			this._typeChanged();
		},
		
		_typeChanged: function()
		{
			var form     = $('.fl-builder-settings'),
				embed    = form.find('textarea[name=embed_code]'),
				video    = form.find('input[name=video]'),
				type     = form.find('select[name=video_type]').val();

			embed.rules('remove');
			video.rules('remove');
			
			if(type == 'embed') {
				embed.rules('add', {
					required: true
				});
			} 
			else {
				video.rules('add', {
					required: true
				});
			}
		}
	});

})(jQuery);