/*! Iris Color Picker - v1.0.7 - 2014-11-28
* https://github.com/Automattic/Iris
* Copyright (c) 2014 Matt Wiebe; Licensed GPLv2 */

var FLBuilderColorPicker;

(function( $, undef ){

	// global variable to store color presets
	var FLBuilderColorPresets = [];
	var nonGradientIE, gradientType, vendorPrefixes, UA, isIE, IEVersion;

	// Even IE9 doesn't support gradients. Elaborate sigh.
	UA = navigator.userAgent.toLowerCase();
	isIE = navigator.appName === 'Microsoft Internet Explorer';
	IEVersion = isIE ? parseFloat( UA.match( /msie ([0-9]{1,}[\.0-9]{0,})/ )[1] ) : 0;
	nonGradientIE = ( isIE && IEVersion < 10 );
	gradientType = false;
	// we don't bother with an unprefixed version, as it has a different syntax
	vendorPrefixes = [ '-moz-', '-webkit-', '-o-', '-ms-' ];

	/**
	 * Run some tests to check if the current browser supports CSS3 gradients. 
	 * Sets gradientType accordingly.
	 *
	 * @since 1.6.4
	 * @return void 
	 */
	function testGradientType() {
		var el, base,
			bgImageString = 'backgroundImage';

		// check current browser is an IE version that doesn't support CSS3 Gradients
		if ( nonGradientIE ) {
			// if yes, set gradientType to filter
			gradientType = 'filter';

		} else {

			// if no, runs a quick test to check if the browser supports modern gradient syntax
			el = $( '<div id="iris-gradtest" />' );
			base = 'linear-gradient(top,#fff,#000)';
			$.each( vendorPrefixes, function( i, val ){
				el.css( bgImageString, val + base );

				if ( el.css( bgImageString ).match( 'gradient' ) ) {
					gradientType = i;
					return false;
				}

			});
			// check for legacy webkit gradient syntax
			if ( gradientType === false ) {
				el.css( 'background', '-webkit-gradient(linear,0% 0%,0% 100%,from(#fff),to(#000))' );

				if ( el.css( this.bgImageString ).match( 'gradient' ) ) {
					gradientType = 'webkit';
				}

			}
			el.remove();
		}

	}

	/**
	* Only for CSS3 gradients. oldIE will use a separate function.
	*
	* Accepts as many color stops as necessary from 2nd arg on, or 2nd
	* arg can be an array of color stops
	*
	* @param  {string} origin Gradient origin - top or left, defaults to left.
	* @return {string}        Appropriate CSS3 gradient string for use in
	*/
	function createGradient( origin, stops ) {
		origin = ( origin === 'top' ) ? 'top' : 'left';
		stops = $.isArray( stops ) ? stops : Array.prototype.slice.call( arguments, 1 );
		if ( gradientType === 'webkit' ) {
			return legacyWebkitGradient( origin, stops );
		} else {
			return vendorPrefixes[ gradientType ] + 'linear-gradient(' + origin + ', ' + stops.join(', ') + ')';
		}
	}

	/**
	* Stupid gradients for a stupid browser.
	*/
	function stupidIEGradient( origin, stops ) {
		var type, self, lastIndex, filter, startPosProp, endPosProp, dimensionProp, template, html;

		origin = ( origin === 'top' ) ? 'top' : 'left';
		stops  = $.isArray( stops ) ? stops : Array.prototype.slice.call( arguments, 1 );
		// 8 hex: AARRGGBB
		// GradientType: 0 vertical, 1 horizontal
		type 		  = ( origin === 'top' ) ? 0 : 1;
		self 		  = $( this ); 
		lastIndex 	  = stops.length - 1;
		filter 		  = 'filter';
		startPosProp  = ( type === 1 ) ? 'left' : 'top';
		endPosProp 	  = ( type === 1 ) ? 'right' : 'bottom';
		dimensionProp = ( type === 1 ) ? 'height' : 'width';
		template 	  = '<div class="iris-ie-gradient-shim" style="position:absolute;' + dimensionProp + ':100%;' + startPosProp + ':%start%;' + endPosProp + ':%end%;' + filter + ':%filter%;" data-color:"%color%"></div>';
		html 		  = '';
		// need a positioning context
		if ( self.css('position') === 'static' ) {
			self.css( {position: 'relative' } );
		}

		stops = fillColorStops( stops );
		$.each(stops, function( i, startColor ) {
			var endColor, endStop, filterVal;

			// we want two at a time. if we're on the last pair, bail.
			if ( i === lastIndex ) {
				return false;
			}

			endColor = stops[ i + 1 ];
			//if our pairs are at the same color stop, moving along.
			if ( startColor.stop === endColor.stop ) {
				return;
			}

			endStop = 100 - parseFloat( endColor.stop ) + '%';
			startColor.octoHex = new Color( startColor.color ).toIEOctoHex();
			endColor.octoHex = new Color( endColor.color ).toIEOctoHex();

			filterVal = 'progid:DXImageTransform.Microsoft.Gradient(GradientType=' + type + ', StartColorStr=\'' + startColor.octoHex + '\', EndColorStr=\'' + endColor.octoHex + '\')';
			html += template.replace( '%start%', startColor.stop ).replace( '%end%', endStop ).replace( '%filter%', filterVal );
		});
		self.find( '.iris-ie-gradient-shim' ).remove();
		$( html ).prependTo( self );
	}

	/**
	 * Builds CSS syntax for legacy webkit browsers.
	 *
	 * @see fillColorStops
	 * @since 1.6.4
	 * @param  {String} origin    Where the gradient starts.
	 * @param  {type} colorList   [description]
	 * @return {String}           The correct CSS gradient syntax.
	 */
	function legacyWebkitGradient( origin, colorList ) {
		var stops = [];
		origin = ( origin === 'top' ) ? '0% 0%,0% 100%,' : '0% 100%,100% 100%,';
		colorList = fillColorStops( colorList );
		$.each( colorList, function( i, val ){
			stops.push( 'color-stop(' + ( parseFloat( val.stop ) / 100 ) + ', ' + val.color + ')' );
		});
		return '-webkit-gradient(linear,' + origin + stops.join(',') + ')';
	};

	function fillColorStops( colorList ) {
		var colors 		 = [],
			percs 		 = [],
			newColorList = [],
			lastIndex 	 = colorList.length - 1;

		$.each( colorList, function( index, val ) {
			var color = val,
				perc  = false,
				match = val.match( /1?[0-9]{1,2}%$/ );

			if ( match ) {
				color = val.replace( /\s?1?[0-9]{1,2}%$/, '' );
				perc  = match.shift();
			}
			colors.push( color );
			percs.push( perc );
		});

		// back fill first and last
		if ( percs[0] === false ) {
			percs[0] = '0%';
		}

		if ( percs[lastIndex] === false ) {
			percs[lastIndex] = '100%';
		}

		percs = backFillColorStops( percs );

		$.each( percs, function( i ){
			newColorList[i] = { color: colors[i], stop: percs[i] };
		});
		return newColorList;
	}

	function backFillColorStops( stops ) {
		var first = 0,
			last = stops.length - 1,
			i = 0,
			foundFirst = false,
			incr,
			steps,
			step,
			firstVal;

		if ( stops.length <= 2 || $.inArray( false, stops ) < 0 ) {
			return stops;
		}
		while ( i < stops.length - 1 ) {
			if ( ! foundFirst && stops[i] === false ) {
				first = i - 1;
				foundFirst = true;
			} else if ( foundFirst && stops[i] !== false ) {
				last = i;
				i = stops.length;
			}
			i++;
		}
		steps = last - first;
		firstVal = parseInt( stops[first].replace('%'), 10 );
		incr = ( parseFloat( stops[last].replace('%') ) - firstVal ) / steps;
		i = first + 1;
		step = 1;
		while ( i < last ) {
			stops[i] = ( firstVal + ( step * incr ) ) + '%';
			step++;
			i++;
		}
		return backFillColorStops( stops );
	}

	$.fn.gradient = function() {
		var args = arguments;

		return this.each( function() {
			// this'll be oldishIE
			if ( nonGradientIE ) {
				stupidIEGradient.apply( this, args );
			} else {
				// new hotness
				$( this ).css( 'backgroundImage', createGradient.apply( this, args ) );
			}
		});
	};

	$.fn.raninbowGradient = function( origin, args ) {
		var opts, template, i, steps;

		origin = origin || 'top';
		opts = $.extend( {}, { s: 100, l: 50 }, args );
		template = 'hsl(%h%,' + opts.s + '%,' + opts.l + '%)';
		i = 0;
		steps = [];
		while ( i <= 360 ) {
			steps.push( template.replace('%h%', i) );
			i += 30;
		}
		return this.each(function() {
			$(this).gradient( origin, steps );
		});
	};

	/**
	 * Helper class for Color Picker.
	 *
	 * @class FLBuilderColorPicker
	 * @since 1.6.4
	 */
	FLBuilderColorPicker = function( settings )
	{
		this._html  = '<div class="fl-color-picker-ui"><div class="iris-picker"><div class="iris-picker-inner"><div class="iris-square"><a class="iris-square-value" href="#"><span class="iris-square-handle ui-slider-handle"></span></a><div class="iris-square-inner iris-square-horiz"></div><div class="iris-square-inner iris-square-vert"></div></div><div class="iris-slider iris-strip"><div class="iris-slider-offset"></div></div></div></div></div>';

		// default settings
		var defaults = {
			elements : null,
			color    : '',
			mode     : 'hsl',
			controls : {
				horiz : 's', // horizontal defaults to saturation
				vert  : 'l', // vertical defaults to lightness
				strip : 'h' // right strip defaults to hue
			},
			target : false, // a DOM element / jQuery selector that the element will be appended within. Only used when called on an input.
			width  : 200, // the width of the collection of UI elements
			presets: [],
			labels : {
				colorPresets 		: 'Color Presets',
				colorPicker 		: 'Color Picker',
				placeholder			: 'Paste color here...',
				removePresetConfirm	: 'Are you sure?',
				noneColorSelected	: 'None color selected.',
				alreadySaved		: ' is already a saved preset.',
				noPresets			: 'Add a color preset first.',
				presetAdded			: ' added to presets!',

			}
		};

		// setting plugin options
		this.options = $.extend( {}, defaults, settings );

		// Bail for IE <= 7
		if ( nonGradientIE == false || nonGradientIE == true && IEVersion > 7 ) {

			// initialize the color picker single instance
			this._init();
		}
		
	};

	FLBuilderColorPicker.prototype = {

		/**
		 * Initial markup for the color picker.
		 *
		 * @since 1.6.4 
		 * @property {String} _html
		 */
		_html 				: '',

		/**
		 * Current set color for the color picker.
		 *
		 * @since 1.6.4
		 * @property {String} _color
		 */
		_color 				: '',
		_currentElement 	: '',
		_inited				: false,
		_defaultHSLControls : {
			horiz : 's',
			vert  : 'l',
			strip : 'h'
		},
		_defaultHSVControls : {
			horiz : 'h',
			vert  : 'v',
			strip : 's'
		},
		_scale				: {
			h: 360,
			s: 100,
			l: 100,
			v: 100
		},

		_init: function(){

			var self  = this,
				el    = $( self.options.elements );

			this._color = new Color( '#000000' ).setHSpace( self.options.mode );

			// Set picker color presets
			FLBuilderColorPresets = this.options.presets;

			if ( gradientType === false ) {
				testGradientType();
			}

			// appends color picker markup to the body
			// check if there's already a color picker instance
			if( $('html').hasClass( 'fl-color-picker-init' ) ){
				self.picker = $( '.fl-color-picker-ui' );
			} else {
				self.picker = $( this._html ).appendTo( 'body' );
			}

			// Browsers / Versions
			// Feature detection doesn't work for these, and $.browser is deprecated
			if ( isIE ) {
				if ( IEVersion === 9 ) {
					self.picker.addClass( 'iris-ie-9' );
				} else if ( IEVersion <= 8 ) {
					self.picker.addClass( 'iris-ie-lt9' );
				}
			} else if ( UA.indexOf('compatible') < 0 && UA.indexOf('khtml') < 0 && UA.match( /mozilla/ ) ) {
				self.picker.addClass( 'iris-mozilla' );
			}

			// prep 'em for re-use
			self.controls = {
				square 		: self.picker.find( '.iris-square' ),
				squareDrag  : self.picker.find( '.iris-square-value' ),
				horiz       : self.picker.find( '.iris-square-horiz' ),
				vert        : self.picker.find( '.iris-square-vert' ),
				strip       : self.picker.find( '.iris-strip' ),
				stripSlider : self.picker.find( '.iris-strip .iris-slider-offset' )
			};

			// small sanity check - if we chose hsv, change default controls away from hsl
			if ( self.options.mode === 'hsv' && self._has('l', self.options.controls) ) {
				self.options.controls = self._defaultHSVControls;
			} else if ( self.options.mode === 'hsl' && self._has('v', self.options.controls) ) {
				self.options.controls = self._defaultHSLControls;
			}

			// store it. HSL gets squirrely
			self.hue = self._color.h();

			this._setTemplates();

			// COLOR PRESETS UI -------------------------------------//

			// cache reference to the picker wrapper
			this._ui 	  = $( '.fl-color-picker-ui' );
			this._iris 	  = $( '.iris-picker' );
			this._wrapper = $('.fl-lightbox-content-wrap');

			if( !$('html').hasClass( 'fl-color-picker-init' ) ){
				this._ui
					.prepend( this._hexHtml )
					.append( this._presetsHtml );
			}

			self.element = this._ui.find( '.fl-color-picker-input' );
			self._initControls();
			self.active = 'external';
			self._change();

			// binds listeners to all color picker instances
			self._addInputListeners( self.element );

			// build the presets UI
			this._buildUI();

			// adds needed markup and bind functions to all color fields
			this._prepareColorFields();

			// bind picker control events
			this._pickerControls();

			if( !$('html').hasClass( 'fl-color-picker-init' ) ){
				// bind presets control events
				this._presetsControls();
			}

			// now we know that the picker is already added to the body
			$('html').addClass( 'fl-color-picker-init' );

		},

		_prepareColorFields: function(){

			var self = this;
			// append presets initial html and trigger that toggles the picker
			$('.fl-color-picker-value').each( function(){

				var $this = $( this );
				var $colorTrigger = $this.parent().find( '.fl-color-picker-color' );

				if( $this.val() ){
					// set initial color
					$colorTrigger.css({ backgroundColor: '#' + $this.val().toString() });
				}

			});

			$('.fl-lightbox-content-wrap').on( 'click', '.fl-color-picker-color', function(){
				var val = $( this ).next('.fl-color-picker-value').val();
				self._color = new Color( val ).setHSpace( self.options.mode );
				self.options.color = self._color.toString();
				self._setColor( val );
			} );

		},

		/**
		 * Sets templates to build the color picker markup.
		 *
		 * @since  1.6.4
		 * @return void 
		 */
		_setTemplates: function(){

			this._presetsHtml = '<div class="fl-color-picker-presets">' +
					'<div class="fl-color-picker-presets-toggle">' +
						'<div class="fl-color-picker-presets-open-label fl-color-picker-active">' + this.options.labels.colorPresets + ' <span class="fl-color-picker-icon-arrow-up"></span></div>' +
						'<div class="fl-color-picker-presets-close-label">' + this.options.labels.colorPicker + ' <span class="fl-color-picker-icon-arrow-down"></span></div>' +
					'</div>' +
					'<ul class="fl-color-picker-presets-list"></ul>' +
				'</div>';
	
			this._hexHtml = '<input type="text" class="fl-color-picker-input" maxlength="7" placeholder="' + this.options.labels.placeholder + '">' +
					   '<div class="fl-color-picker-preset-add"></div>';
	
			this._presetsTpl = '<li class="fl-color-picker-preset"><span class="fl-color-picker-preset-color"></span> <span class="fl-color-picker-preset-label"></span> <span class="fl-color-picker-preset-remove fl-color-picker-icon-remove"></span></li>';

			this._noPresetsTpl = '<li class="fl-color-picker-no-preset"><span class="fl-color-picker-preset-label">' + this.options.labels.noPresets + '</span></li>';

		},

		_has: function( needle, haystack ) {
			var ret = false;
			$.each( haystack, function( i, v ){
				if ( needle === v ) {
					ret = true;
					// exit the loop
					return false;
				}
			});
			return ret;
		},

		/**
		 * Builds the UI for color presets
		 *
		 * @see    _addPresetView
		 * @since  1.6.4
		 * @return void
		 */
		_buildUI: function(){
			var self = this;
			self._presetsList = this._ui.find( '.fl-color-picker-presets-list' );
			self._presetsList.html('');

			if( this.options.presets.length > 0 ){
				$.each( this.options.presets, function( index, val ) {
					self._addPresetView( val );
				});				
			} else {
				self._presetsList.append( this._noPresetsTpl );			
			}

		},

		/**
		 * Helper function to build a view for each color preset.
		 *
		 * @since  1.6.4
		 * @param  {string} val the respective hex code for the color preset.
		 * @return void
		 */
		_addPresetView: function( val ){

			var hasEmpty = this._presetsList.find( '.fl-color-picker-no-preset' );

			if( hasEmpty.length > 0 ){
				hasEmpty.remove();
			}

			var tpl   = $( this._presetsTpl ),
				color = Color( val );

			tpl
				.attr( 'data-color', val )
				.find( '.fl-color-picker-preset-color' )
					.css({ backgroundColor: color.toString() })
					.end()
				.find( '.fl-color-picker-preset-label' )
					.html( color.toString() );

			this._presetsList.append( tpl );		
		},

		/**
		 * Shows a visual feedback when a color is added as a preset.
		 *
		 * @since  1.6.4
		 * @return void
		 */
		_addPresetFeedback: function(){

			this._ui.append( '<div class="fl-color-picker-added"><div class="fl-color-picker-added-text"><div class="fl-color-picker-icon-check"></div> "' + this._color.toString() + this.options.labels.presetAdded + '"</div></div>' );
			this._ui
				.find( '.fl-color-picker-added' )
					.hide()
					.fadeIn( 200 )
					.delay( 2000 )
					.fadeOut( 200, function(){
						$(this).remove();
					} );

		},

		/**
		 * Sets some triggers for positioning logic of the picker and color reset.
		 *
		 * @since  1.6.4
		 * @return void
		 */
		_pickerControls: function(){
			var self = this;

			// logic for picker positioning
			this._wrapper.on( 'click', '.fl-color-picker-color', function(){
				var $this = $(this);
				self._currentElement = $this.parent().find('.fl-color-picker-value');

				self._ui.position({
					my: 'left top',
					at: 'left bottom',
					of: $this,
					collision: 'flipfit',
					using: function( position, feedback ){
						self._togglePicker( position );
					}
				})
				
			} );

			this._wrapper.on( 'click', '.fl-color-picker-clear', function(){
				var $this = $(this);
				self._currentElement = $this.parent().find('.fl-color-picker-value');

				$this
					.prev( '.fl-color-picker-color' )
					.css({ backgroundColor: 'transparent' })
					.addClass('fl-color-picker-empty');

				self._setColor( '' );
				self.element.val( '' );
				self._currentElement
					.val( '' )
					.trigger( 'change' );
			} );

			// logic to hide picker when the user clicks outside it
			$( document ).on( 'click', function( event ) {

				if ( 0 === $( event.target ).closest( '.fl-color-picker-ui' ).length ) {
                    $( '.fl-color-picker-ui.fl-color-picker-active' ).removeClass( 'fl-color-picker-active' );
                }
			});

		},

		/**
		 * Logic for color presets UI.
		 *
		 * @see   _addPreset
		 * @see   _removePreset
		 * @since 1.6.4
		 * @return void
		 */
		_presetsControls: function(){
			var self 		      = this,
				addPreset         = self._ui.find( '.fl-color-picker-preset-add' ),
				presets 	      = self._ui.find( '.fl-color-picker-presets' ),
				presetsOpenLabel  = presets.find( '.fl-color-picker-presets-open-label' ),
				presetsCloseLabel = presets.find( '.fl-color-picker-presets-close-label' ),
				presetsList 	  = presets.find( '.fl-color-picker-presets-list' );

			// add preset
			addPreset.on( 'click', function(){
				self._addPreset( self.element.val() );
			} );

			// presets toggle
			presetsList
				.css({ height: ( self.element.innerHeight() + self._iris.innerHeight() + 14 ) + 'px' })
				.hide();
			
			presets
				.on( 'click', '.fl-color-picker-presets-toggle', function(){

					presetsOpenLabel.toggleClass('fl-color-picker-active');
					presetsCloseLabel.toggleClass('fl-color-picker-active');

					presetsList.slideToggle( 500 );
				} )
				// set preset as current color
				.on( 'click', '.fl-color-picker-preset', function( e ){
					var currentColor = new Color( $( this ).data( 'color' ).toString() );
					self._setColor( currentColor );
					self._currentElement
						.parent()
						.find( '.fl-color-picker-color' )
						.css({ backgroundColor: currentColor.toString() })
						.removeClass('fl-color-picker-empty');

					presetsOpenLabel.toggleClass('fl-color-picker-active');
					presetsCloseLabel.toggleClass('fl-color-picker-active');

					presetsList.slideToggle( 500 );
				} )
				// removes a preset
				.on( 'click', '.fl-color-picker-preset-remove', function( e ){
					e.stopPropagation();
					self._removePreset( $( this ).parent().data( 'color' ) );
				});

		},

		/**
		 * Removes a color preset from the array of presets and from the UI.
		 *
		 * @param  {string} preset The respective hex value of the preset.
		 * @return void
		 */
		_removePreset: function( preset ){
			if( confirm( this.options.labels.removePresetConfirm ) ){
				var color = preset.toString()
					index = FLBuilderColorPresets.indexOf( color );

				if( index > -1 ){
					FLBuilderColorPresets.splice( index, 1 );
					this.options.presets = FLBuilderColorPresets;

					this._presetsList
						.find('.fl-color-picker-preset[data-color="'+ color +'"]' )
						.slideUp( function(){
							$( this ).remove();
						});

				}

				if( FLBuilderColorPresets.length < 1 ){
					this._presetsList.append( this._noPresetsTpl );
				}							

				// CALLBACK FOR PRESET REMOVED
				$(this).trigger( 'presetRemoved', { presets: FLBuilderColorPresets } );

			}
		},

		/**
		 * Logic to add a color preset to the array of presets, and to the UI.
		 *
		 * @see    _addPresetView
		 * @see    _addPresetFeedback
		 * @param  {string} preset The respective hex value of the preset.
		 * @since  1.6.4
		 * @return void
		 */
		_addPreset: function( preset ){
			var color = preset.toString().replace( /^#/, '' );

			// check if color is empty
			if( color == '' ){
				alert( this.options.labels.noneColorSelected );
			// check if the color is already added
			} else if( FLBuilderColorPresets.indexOf( color ) > -1 ){
				alert( '#' + color + this.options.labels.alreadySaved );
			// add color to presets, fires visual feedback and triggers an event
			} else {

				this._addPresetView( color );

				this._addPresetFeedback();
				FLBuilderColorPresets.push( color );
				this.options.presets = FLBuilderColorPresets;

				// CALLBACK FOR COLOR ADDED
				$(this).trigger( 'presetAdded', { presets: FLBuilderColorPresets } );			
			}
		},

		/**
		 * Logic for positioning of the color picker.
		 *
		 * 
		 * @param  {Object} position An object containing x and y location for positioning.
		 * @since  1.6.4
		 * @return void
		 */
		_togglePicker: function( position ){
			var self = this;

			// logic for correct order of things
			if( this._ui.hasClass( 'fl-color-picker-active' ) ){
				// if the picker is open, hides first, then changes the position
				this._ui.removeClass( 'fl-color-picker-active' );

				if( position ){
					setTimeout(	function(){
						self._ui.css( position );
						self._ui.addClass( 'fl-color-picker-active' );
						self._setColor( self._currentElement.val() );
					}, 200 );					
				}

			} else {
				if( position ){
					self._ui.css( position );
				}
				// if the picker is closed, changes position first, then shows it
				setTimeout(	function(){
					self._ui.addClass( 'fl-color-picker-active' )
					self._setColor( self._currentElement.val() );
				}, 200 );
			}

		},

		_paint: function() {
			var self = this;
			self._paintDimension( 'right', 'strip' );
			self._paintDimension( 'top', 'vert' );
			self._paintDimension( 'left', 'horiz' );
		},

		_paintDimension: function( origin, control ) {
			var self = this,
				c = self._color,
				mode = self.options.mode,
				color = self._getHSpaceColor(),
				target = self.controls[ control ],
				controlOpts = self.options.controls,
				stops;

			// don't paint the active control
			if ( control === self.active || ( self.active === 'square' && control !== 'strip' ) ) {
				return;
			}

			switch ( controlOpts[ control ] ) {
				case 'h':
					if ( mode === 'hsv' ) {
						color = c.clone();
						switch ( control ) {
							case 'horiz':
								color[controlOpts.vert](100);
								break;
							case 'vert':
								color[controlOpts.horiz](100);
								break;
							case 'strip':
								color.setHSpace('hsl');
								break;
						}
						stops = color.toHsl();
					} else {
						if ( control === 'strip' ) {
							stops = { s: color.s, l: color.l };
						} else {
							stops = { s: 100, l: color.l };
						}
					}

					target.raninbowGradient( origin, stops );
					break;
				case 's':
					if ( mode === 'hsv' ) {
						if ( control === 'vert' ) {
							stops = [ c.clone().a(0).s(0).toCSS('rgba'), c.clone().a(1).s(0).toCSS('rgba') ];
						} else if ( control === 'strip' ) {
							stops = [ c.clone().s(100).toCSS('hsl'), c.clone().s(0).toCSS('hsl') ];
						} else if ( control === 'horiz' ) {
							stops = [ '#fff', 'hsl(' + color.h + ',100%,50%)' ];
						}
					} else { // implicit mode === 'hsl'
						if ( control === 'vert' && self.options.controls.horiz === 'h' ) {
							stops = ['hsla(0, 0%, ' + color.l + '%, 0)', 'hsla(0, 0%, ' + color.l + '%, 1)'];
						} else {
							stops = ['hsl('+ color.h +',0%,50%)', 'hsl(' + color.h + ',100%,50%)'];
						}
					}


					target.gradient( origin, stops );
					break;
				case 'l':
					if ( control === 'strip' ) {
						stops = ['hsl(' + color.h + ',100%,100%)', 'hsl(' + color.h + ', ' + color.s + '%,50%)', 'hsl('+ color.h +',100%,0%)'];
					} else {
						stops = ['#fff', 'rgba(255,255,255,0) 50%', 'rgba(0,0,0,0) 50%', 'rgba(0,0,0,1)'];
					}
					target.gradient( origin, stops );
					break;
				case 'v':
						if ( control === 'strip' ) {
							stops = [ c.clone().v(100).toCSS(), c.clone().v(0).toCSS() ];
						} else {
							stops = ['rgba(0,0,0,0)', '#000'];
						}
						target.gradient( origin, stops );
					break;
				default:
					break;
			}
		},

		_getHSpaceColor: function() {
			return ( this.options.mode === 'hsv' ) ? this._color.toHsv() : this._color.toHsl();
		},

		/**
		 * Logic to listen to events from the main color input and to bind it to the current color field.
		 *
		 * @see _setColor
		 * @since  1.6.4
		 * @return void
		 */
		_addInputListeners: function( input ) {
			var self = this,
				debounceTimeout = 100,
				callback = function( event ){
					var color = new Color( input.val() ),
						val = input.val().replace( /^#/, '' );

					input.removeClass( 'iris-error' );
					// we gave a bad color
					if ( color.error ) {
						// don't error on an empty input - we want those allowed
						if ( val !== '' ) {
							input.addClass( 'iris-error' );
						}
					} else {

						if ( color.toString() !== self._color.toString() ) {
	
							if( event.type === 'keyup' ){
								if( val.match( /^[0-9a-fA-F]{3}$/ ) )
									return;

								self._setColor( val );

								self._currentElement
									.parent()
									.find( '.fl-color-picker-color' )
									.css({ backgroundColor: Color( val ).toString() })
									.removeClass( 'fl-color-picker-empty' );

								self._currentElement
									.val( val )
									.trigger( 'change' );
								
							} else if( event.type === 'paste' ){
								val = event.originalEvent.clipboardData.getData( 'text' ).replace( /^#/, '' );
								hex = Color( val ).toString();

								self._setColor( val );
								input.val( hex );

								self._currentElement
									.parent()
									.find( '.fl-color-picker-color' )
									.css({ backgroundColor: hex })
									.removeClass( 'fl-color-picker-empty' );

								self._currentElement
									.val( val )
									.trigger( 'change' );

								return false;			
							}

						}
					}
				};

			input.on( 'change', callback ).on( 'keyup', self._debounce( callback, debounceTimeout ) );

			// If we initialized hidden, show on first focus. The rest is up to you.
			if ( self.options.hide ) {
				input.one( 'focus', function() {
					self.show();
				});
			}
		},

		_initControls: function() {
			var self = this,
				controls = self.controls,
				square = controls.square,
				controlOpts = self.options.controls,
				stripScale = self._scale[controlOpts.strip];

			controls.stripSlider.slider({
				orientation: 'horizontal',
				max: stripScale,
				slide: function( event, ui ) {
					self.active = 'strip';
					// "reverse" for hue.
					if ( controlOpts.strip === 'h' ) {
						ui.value = stripScale - ui.value;
					}

					self._color[controlOpts.strip]( ui.value );
					self._change.apply( self, arguments );
				}
			});

			controls.squareDrag.draggable({
				containment: controls.square.find( '.iris-square-inner' ),
				zIndex: 1000,
				cursor: 'move',
				drag: function( event, ui ) {
					self._squareDrag( event, ui );
				},
				start: function() {
					square.addClass( 'iris-dragging' );
					$(this).addClass( 'ui-state-focus' );
				},
				stop: function() {
					square.removeClass( 'iris-dragging' );
					$(this).removeClass( 'ui-state-focus' );
				}
			}).on( 'mousedown mouseup', function( event ) {
				var focusClass = 'ui-state-focus';
				event.preventDefault();
				if (event.type === 'mousedown' ) {
					self.picker.find( '.' + focusClass ).removeClass( focusClass ).blur();
					$(this).addClass( focusClass ).focus();
				} else {
					$(this).removeClass( focusClass );
				}
			}).on( 'keydown', function( event ) {
				var container = controls.square,
					draggable = controls.squareDrag,
					position = draggable.position(),
					distance = 2; // Distance in pixels the draggable should be moved: 1 "stop"

				// make alt key go "10"
				if ( event.altKey ) {
					distance *= 10;
				}

				// Reposition if one of the directional keys is pressed
				switch ( event.keyCode ) {
					case 37: position.left -= distance; break; // Left
					case 38: position.top  -= distance; break; // Up
					case 39: position.left += distance; break; // Right
					case 40: position.top  += distance; break; // Down
					default: return true; // Exit and bubble
				}

				// Keep draggable within container
				position.left = Math.max( 0, Math.min( position.left, container.width() ) );
				position.top =  Math.max( 0, Math.min( position.top, container.height() ) );

				draggable.css(position);
				self._squareDrag( event, { position: position });
				event.preventDefault();
			});

			// allow clicking on the square to move there and keep dragging
			square.mousedown( function( event ) {
				var squareOffset, pos;
				// only left click
				if ( event.which !== 1 ) {
					return;
				}

				// prevent bubbling from the handle: no infinite loops
				if ( ! $( event.target ).is( 'div' ) ) {
					return;
				}

				squareOffset = self.controls.square.offset();
				pos = {
						top: event.pageY - squareOffset.top,
						left: event.pageX - squareOffset.left
				};
				event.preventDefault();
				self._squareDrag( event, { position: pos } );
				event.target = self.controls.squareDrag.get(0);
				self.controls.squareDrag.css( pos ).trigger( event );
			});

			// palettes
			if ( self.options.palettes ) {
				self._paletteListeners();
			}
		},

		_squareDrag: function( event, ui ) {
			var self = this,
				controlOpts = self.options.controls,
				dimensions = self._squareDimensions(),
				vertVal = Math.round( ( dimensions.h - ui.position.top ) / dimensions.h * self._scale[controlOpts.vert] ),
				horizVal = self._scale[controlOpts.horiz] - Math.round( ( dimensions.w - ui.position.left ) / dimensions.w * self._scale[controlOpts.horiz] );

			self._color[controlOpts.horiz]( horizVal )[controlOpts.vert]( vertVal );

			self.active = 'square';
			self._change.apply( self, arguments );
		},

		_setColor: function( value ) {
			var self = this,
				oldValue = self.options.color,
				doDimensions = false,
				hexLessColor,
				newColor,
				method;

			// ensure the new value is set. We can reset to oldValue if some check wasn't met.
			self.options.color = value;
			// cast to string in case we have a number
			value = '' + value;
			hexLessColor = value.replace( /^#/, '' );
			newColor = new Color( value ).setHSpace( self.options.mode );
			if ( newColor.error ) {
				self.options.color = oldValue;
			} else {
				self._color = newColor;
				self.options.color = self._color.toString();
				self.active = 'external';
				self._change();
			}

		},

		_squareDimensions: function( forceRefresh ) {
			var square = this.controls.square,
				dimensions,
				control;

			if ( forceRefresh !== undef && square.data('dimensions') ) {
				return square.data('dimensions');
			}

			control = this.controls.squareDrag;
			dimensions = {
				w: square.width(),
				h: square.height()
			};
			square.data( 'dimensions', dimensions );
			return dimensions;
		},

		_isNonHueControl: function( active, type ) {
			if ( active === 'square' && this.options.controls.strip === 'h' ) {
				return true;
			} else if ( type === 'external' || ( type === 'h' && active === 'strip' ) ) {
				return false;
			}

			return true;
		},

		_change: function() {
			var self = this,
				controls = self.controls,
				color = self._getHSpaceColor(),
				actions = [ 'square', 'strip' ],
				controlOpts = self.options.controls,
				type = controlOpts[self.active] || 'external',
				oldHue = self.hue;

			if ( self.active === 'strip' ) {
				// take no action on any of the square sliders if we adjusted the strip
				actions = [];
			} else if ( self.active !== 'external' ) {
				// for non-strip, non-external, strip should never change
				actions.pop(); // conveniently the last item
			}

			$.each( actions, function(index, item) {
				var value, dimensions, cssObj;
				if ( item !== self.active ) {
					switch ( item ) {
						case 'strip':
							// reverse for hue
							value = ( controlOpts.strip === 'h' ) ? self._scale[controlOpts.strip] - color[controlOpts.strip] : color[controlOpts.strip];
							controls.stripSlider.slider( 'value', value );
							break;
						case 'square':
							dimensions = self._squareDimensions();
							cssObj = {
								left: color[controlOpts.horiz] / self._scale[controlOpts.horiz] * dimensions.w,
								top: dimensions.h - ( color[controlOpts.vert] / self._scale[controlOpts.vert] * dimensions.h )
							};

							self.controls.squareDrag.css( cssObj );
							break;
					}
				}
			});

			// Ensure that we don't change hue if we triggered a hue reset
			if ( color.h !== oldHue && self._isNonHueControl( self.active, type ) ) {
				self._color.h(oldHue);
			}

			// store hue for repeating above check next time
			self.hue = self._color.h();

			self.options.color = self._color.toString();

			// only run after the first time
			// if ( self._inited ) {
			// 	self.trigger( 'change', { type: self.active }, { color: self._color } );
			// }

			if ( self.element.is( ':input' ) && ! self._color.error ) {
				self.element.removeClass( 'iris-error' );
				if ( self.element.val() !== self._color.toString() ) {
					self.element.val( self._color.toString() );

					if( self._currentElement ){
						self._currentElement
							.val( self._color.toString().replace( /^#/, '' ) )
							.parent()
							.find( '.fl-color-picker-color' )
							.css({ backgroundColor: self._color.toString() })
							.removeClass( 'fl-color-picker-empty' );
						
						self._currentElement.trigger( 'change' );
					}

				}
			}

			self._paint();
			self._inited = true;
			self.active = false;
		},

		// taken from underscore.js _.debounce method
		_debounce: function( func, wait, immediate ) {
			var timeout, result;
			return function() {
				var context = this,
					args = arguments,
					later,
					callNow;

				later = function() {
					timeout = null;
					if ( ! immediate) {
						result = func.apply( context, args );
					}
				};

				callNow = immediate && !timeout;
				clearTimeout( timeout );
				timeout = setTimeout( later, wait );
				if ( callNow ) {
					result = func.apply( context, args );
				}
				return result;
			};
		}
	};

}( jQuery ));
/*! Color.js - v0.9.11 - 2013-08-09
* https://github.com/Automattic/Color.js
* Copyright (c) 2013 Matt Wiebe; Licensed GPLv2 */
(function(global, undef) {

	var Color = function( color, type ) {
		if ( ! ( this instanceof Color ) )
			return new Color( color, type );

		return this._init( color, type );
	};

	Color.fn = Color.prototype = {
		_color: 0,
		_alpha: 1,
		error: false,
		// for preserving hue/sat in fromHsl().toHsl() flows
		_hsl: { h: 0, s: 0, l: 0 },
		// for preserving hue/sat in fromHsv().toHsv() flows
		_hsv: { h: 0, s: 0, v: 0 },
		// for setting hsl or hsv space - needed for .h() & .s() functions to function properly
		_hSpace: 'hsl',
		_init: function( color ) {
			var func = 'noop';
			switch ( typeof color ) {
					case 'object':
						// alpha?
						if ( color.a !== undef )
							this.a( color.a );
						func = ( color.r !== undef ) ? 'fromRgb' :
							( color.l !== undef ) ? 'fromHsl' :
							( color.v !== undef ) ? 'fromHsv' : func;
						return this[func]( color );
					case 'string':
						return this.fromCSS( color );
					case 'number':
						return this.fromInt( parseInt( color, 10 ) );
			}
			return this;
		},

		_error: function() {
			this.error = true;
			return this;
		},

		clone: function() {
			var newColor = new Color( this.toInt() ),
				copy = ['_alpha', '_hSpace', '_hsl', '_hsv', 'error'];
			for ( var i = copy.length - 1; i >= 0; i-- ) {
				newColor[ copy[i] ] = this[ copy[i] ];
			}
			return newColor;
		},

		setHSpace: function( space ) {
			this._hSpace = ( space === 'hsv' ) ? space : 'hsl';
			return this;
		},

		noop: function() {
			return this;
		},

		fromCSS: function( color ) {
			var list,
				leadingRE = /^(rgb|hs(l|v))a?\(/;
			this.error = false;

			// whitespace and semicolon trim
			color = color.replace(/^\s+/, '').replace(/\s+$/, '').replace(/;$/, '');

			if ( color.match(leadingRE) && color.match(/\)$/) ) {
				list = color.replace(/(\s|%)/g, '').replace(leadingRE, '').replace(/,?\);?$/, '').split(',');

				if ( list.length < 3 )
					return this._error();

				if ( list.length === 4 ) {
					this.a( parseFloat( list.pop() ) );
					// error state has been set to true in .a() if we passed NaN
					if ( this.error )
						return this;
				}

				for (var i = list.length - 1; i >= 0; i--) {
					list[i] = parseInt(list[i], 10);
					if ( isNaN( list[i] ) )
						return this._error();
				}

				if ( color.match(/^rgb/) ) {
					return this.fromRgb( {
						r: list[0],
						g: list[1],
						b: list[2]
					} );
				} else if ( color.match(/^hsv/) ) {
					return this.fromHsv( {
						h: list[0],
						s: list[1],
						v: list[2]
					} );
				} else {
					return this.fromHsl( {
						h: list[0],
						s: list[1],
						l: list[2]
					} );
				}
			} else {
				// must be hex amirite?
				return this.fromHex( color );
			}
		},

		fromRgb: function( rgb, preserve ) {
			if ( typeof rgb !== 'object' || rgb.r === undef || rgb.g === undef || rgb.b === undef )
				return this._error();

			this.error = false;
			return this.fromInt( parseInt( ( rgb.r << 16 ) + ( rgb.g << 8 ) + rgb.b, 10 ), preserve );
		},

		fromHex: function( color ) {
			color = color.replace(/^#/, '').replace(/^0x/, '');
			if ( color.length === 3 ) {
				color = color[0] + color[0] + color[1] + color[1] + color[2] + color[2];
			}

			// rough error checking - this is where things go squirrely the most
			this.error = ! /^[0-9A-F]{6}$/i.test( color );
			return this.fromInt( parseInt( color, 16 ) );
		},

		fromHsl: function( hsl ) {
			var r, g, b, q, p, h, s, l;

			if ( typeof hsl !== 'object' || hsl.h === undef || hsl.s === undef || hsl.l === undef )
				return this._error();

			this._hsl = hsl; // store it
			this._hSpace = 'hsl'; // implicit
			h = hsl.h / 360; s = hsl.s / 100; l = hsl.l / 100;
			if ( s === 0 ) {
				r = g = b = l; // achromatic
			}
			else {
				q = l < 0.5 ? l * ( 1 + s ) : l + s - l * s;
				p = 2 * l - q;
				r = this.hue2rgb( p, q, h + 1/3 );
				g = this.hue2rgb( p, q, h );
				b = this.hue2rgb( p, q, h - 1/3 );
			}
			return this.fromRgb( {
				r: r * 255,
				g: g * 255,
				b: b * 255
			}, true ); // true preserves hue/sat
		},

		fromHsv: function( hsv ) {
			var h, s, v, r, g, b, i, f, p, q, t;
			if ( typeof hsv !== 'object' || hsv.h === undef || hsv.s === undef || hsv.v === undef )
				return this._error();

			this._hsv = hsv; // store it
			this._hSpace = 'hsv'; // implicit

			h = hsv.h / 360; s = hsv.s / 100; v = hsv.v / 100;
			i = Math.floor( h * 6 );
			f = h * 6 - i;
			p = v * ( 1 - s );
			q = v * ( 1 - f * s );
			t = v * ( 1 - ( 1 - f ) * s );

			switch( i % 6 ) {
				case 0:
					r = v; g = t; b = p;
					break;
				case 1:
					r = q; g = v; b = p;
					break;
				case 2:
					r = p; g = v; b = t;
					break;
				case 3:
					r = p; g = q; b = v;
					break;
				case 4:
					r = t; g = p; b = v;
					break;
				case 5:
					r = v; g = p; b = q;
					break;
			}

			return this.fromRgb( {
				r: r * 255,
				g: g * 255,
				b: b * 255
			}, true ); // true preserves hue/sat

		},
		// everything comes down to fromInt
		fromInt: function( color, preserve ) {
			this._color = parseInt( color, 10 );

			if ( isNaN( this._color ) )
				this._color = 0;

			// let's coerce things
			if ( this._color > 16777215 )
				this._color = 16777215;
			else if ( this._color < 0 )
				this._color = 0;

			// let's not do weird things
			if ( preserve === undef ) {
				this._hsv.h = this._hsv.s = this._hsl.h = this._hsl.s = 0;
			}
			// EVENT GOES HERE
			return this;
		},

		hue2rgb: function( p, q, t ) {
			if ( t < 0 ) {
				t += 1;
			}
			if ( t > 1 ) {
				t -= 1;
			}
			if ( t < 1/6 ) {
				return p + ( q - p ) * 6 * t;
			}
			if ( t < 1/2 ) {
				return q;
			}
			if ( t < 2/3 ) {
				return p + ( q - p ) * ( 2/3 - t ) * 6;
			}
			return p;
		},

		toString: function() {
			var hex = parseInt( this._color, 10 ).toString( 16 );
			if ( this.error )
				return '';
			// maybe left pad it
			if ( hex.length < 6 ) {
				for (var i = 6 - hex.length - 1; i >= 0; i--) {
					hex = '0' + hex;
				}
			}
			return '#' + hex;
		},

		toCSS: function( type, alpha ) {
			type = type || 'hex';
			alpha = parseFloat( alpha || this._alpha );
			switch ( type ) {
				case 'rgb':
				case 'rgba':
					var rgb = this.toRgb();
					if ( alpha < 1 ) {
						return "rgba( " + rgb.r + ", " + rgb.g + ", " + rgb.b + ", " + alpha + " )";
					}
					else {
						return "rgb( " + rgb.r + ", " + rgb.g + ", " + rgb.b + " )";
					}
					break;
				case 'hsl':
				case 'hsla':
					var hsl = this.toHsl();
					if ( alpha < 1 ) {
						return "hsla( " + hsl.h + ", " + hsl.s + "%, " + hsl.l + "%, " + alpha + " )";
					}
					else {
						return "hsl( " + hsl.h + ", " + hsl.s + "%, " + hsl.l + "% )";
					}
					break;
				default:
					return this.toString();
			}
		},

		toRgb: function() {
			return {
				r: 255 & ( this._color >> 16 ),
				g: 255 & ( this._color >> 8 ),
				b: 255 & ( this._color )
			};
		},

		toHsl: function() {
			var rgb = this.toRgb();
			var r = rgb.r / 255, g = rgb.g / 255, b = rgb.b / 255;
			var max = Math.max( r, g, b ), min = Math.min( r, g, b );
			var h, s, l = ( max + min ) / 2;

			if ( max === min ) {
				h = s = 0; // achromatic
			} else {
				var d = max - min;
				s = l > 0.5 ? d / ( 2 - max - min ) : d / ( max + min );
				switch ( max ) {
					case r: h = ( g - b ) / d + ( g < b ? 6 : 0 );
						break;
					case g: h = ( b - r ) / d + 2;
						break;
					case b: h = ( r - g ) / d + 4;
						break;
				}
				h /= 6;
			}

			// maintain hue & sat if we've been manipulating things in the HSL space.
			h = Math.round( h * 360 );
			if ( h === 0 && this._hsl.h !== h ) {
				h = this._hsl.h;
			}
			s = Math.round( s * 100 );
			if ( s === 0 && this._hsl.s ) {
				s = this._hsl.s;
			}

			return {
				h: h,
				s: s,
				l: Math.round( l * 100 )
			};

		},

		toHsv: function() {
			var rgb = this.toRgb();
			var r = rgb.r / 255, g = rgb.g / 255, b = rgb.b / 255;
			var max = Math.max( r, g, b ), min = Math.min( r, g, b );
			var h, s, v = max;
			var d = max - min;
			s = max === 0 ? 0 : d / max;

			if ( max === min ) {
				h = s = 0; // achromatic
			} else {
				switch( max ){
					case r:
						h = ( g - b ) / d + ( g < b ? 6 : 0 );
						break;
					case g:
						h = ( b - r ) / d + 2;
						break;
					case b:
						h = ( r - g ) / d + 4;
						break;
				}
				h /= 6;
			}

			// maintain hue & sat if we've been manipulating things in the HSV space.
			h = Math.round( h * 360 );
			if ( h === 0 && this._hsv.h !== h ) {
				h = this._hsv.h;
			}
			s = Math.round( s * 100 );
			if ( s === 0 && this._hsv.s ) {
				s = this._hsv.s;
			}

			return {
				h: h,
				s: s,
				v: Math.round( v * 100 )
			};
		},

		toInt: function() {
			return this._color;
		},

		toIEOctoHex: function() {
			// AARRBBGG
			var hex = this.toString();
			var AA = parseInt( 255 * this._alpha, 10 ).toString(16);
			if ( AA.length === 1 ) {
				AA = '0' + AA;
			}
			return '#' + AA + hex.replace(/^#/, '' );
		},

		toLuminosity: function() {
			var rgb = this.toRgb();
			return 0.2126 * Math.pow( rgb.r / 255, 2.2 ) + 0.7152 * Math.pow( rgb.g / 255, 2.2 ) + 0.0722 * Math.pow( rgb.b / 255, 2.2);
		},

		getDistanceLuminosityFrom: function( color ) {
			if ( ! ( color instanceof Color ) ) {
				throw 'getDistanceLuminosityFrom requires a Color object';
			}
			var lum1 = this.toLuminosity();
			var lum2 = color.toLuminosity();
			if ( lum1 > lum2 ) {
				return ( lum1 + 0.05 ) / ( lum2 + 0.05 );
			}
			else {
				return ( lum2 + 0.05 ) / ( lum1 + 0.05 );
			}
		},

		getMaxContrastColor: function() {
			var lum = this.toLuminosity();
			var hex = ( lum >= 0.5 ) ? '000000' : 'ffffff';
			return new Color( hex );
		},

		getReadableContrastingColor: function( bgColor, minContrast ) {
			if ( ! bgColor instanceof Color ) {
				return this;
			}

			// you shouldn't use less than 5, but you might want to.
			var targetContrast = ( minContrast === undef ) ? 5 : minContrast;
			// working things
			var contrast = bgColor.getDistanceLuminosityFrom( this );
			var maxContrastColor = bgColor.getMaxContrastColor();
			var maxContrast = maxContrastColor.getDistanceLuminosityFrom( bgColor );

			// if current max contrast is less than the target contrast, we had wishful thinking.
			// still, go max
			if ( maxContrast <= targetContrast ) {
				return maxContrastColor;
			}
			// or, we might already have sufficient contrast
			else if ( contrast >= targetContrast ) {
				return this;
			}

			var incr = ( 0 === maxContrastColor.toInt() ) ? -1 : 1;
			while ( contrast < targetContrast ) {
				this.l( incr, true ); // 2nd arg turns this into an incrementer
				contrast = this.getDistanceLuminosityFrom( bgColor );
				// infininite loop prevention: you never know.
				if ( this._color === 0 || this._color === 16777215 ) {
					break;
				}
			}

			return this;

		},

		a: function( val ) {
			if ( val === undef )
				return this._alpha;

			var a = parseFloat( val );

			if ( isNaN( a ) )
				return this._error();

			this._alpha = a;
			return this;
		},

		// TRANSFORMS

		darken: function( amount ) {
			amount = amount || 5;
			return this.l( - amount, true );
		},

		lighten: function( amount ) {
			amount = amount || 5;
			return this.l( amount, true );
		},

		saturate: function( amount ) {
			amount = amount || 15;
			return this.s( amount, true );
		},

		desaturate: function( amount ) {
			amount = amount || 15;
			return this.s( - amount, true );
		},

		toGrayscale: function() {
			return this.setHSpace('hsl').s( 0 );
		},

		getComplement: function() {
			return this.h( 180, true );
		},

		getSplitComplement: function( step ) {
			step = step || 1;
			var incr = 180 + ( step * 30 );
			return this.h( incr, true );
		},

		getAnalog: function( step ) {
			step = step || 1;
			var incr = step * 30;
			return this.h( incr, true );
		},

		getTetrad: function( step ) {
			step = step || 1;
			var incr = step * 60;
			return this.h( incr, true );
		},

		getTriad: function( step ) {
			step = step || 1;
			var incr = step * 120;
			return this.h( incr, true );
		},

		_partial: function( key ) {
			var prop = shortProps[key];
			return function( val, incr ) {
				var color = this._spaceFunc('to', prop.space);

				// GETTER
				if ( val === undef )
					return color[key];

				// INCREMENT
				if ( incr === true )
					val = color[key] + val;

				// MOD & RANGE
				if ( prop.mod )
					val = val % prop.mod;
				if ( prop.range )
					val = ( val < prop.range[0] ) ? prop.range[0] : ( val > prop.range[1] ) ? prop.range[1] : val;

				// NEW VALUE
				color[key] = val;

				return this._spaceFunc('from', prop.space, color);
			};
		},

		_spaceFunc: function( dir, s, val ) {
			var space = s || this._hSpace,
				funcName = dir + space.charAt(0).toUpperCase() + space.substr(1);
			return this[funcName](val);
		}
	};

	var shortProps = {
		h: {
			mod: 360
		},
		s: {
			range: [0,100]
		},
		l: {
			space: 'hsl',
			range: [0,100]
		},
		v: {
			space: 'hsv',
			range: [0,100]
		},
		r: {
			space: 'rgb',
			range: [0,255]
		},
		g: {
			space: 'rgb',
			range: [0,255]
		},
		b: {
			space: 'rgb',
			range: [0,255]
		}
	};

	for ( var key in shortProps ) {
		if ( shortProps.hasOwnProperty( key ) )
			Color.fn[key] = Color.fn._partial(key);
	}

	// play nicely with Node + browser
	if ( typeof exports === 'object' )
		module.exports = Color;
	else
		global.Color = Color;

}(this));