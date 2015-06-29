(function($){

	/**
	 * Helper class for dealing with creating
	 * and updating stylesheets.
	 *
	 * @class FLStyleSheet
	 * @since 1.3.3
	 */
	FLStyleSheet = function() {};
	
	/**
	 * Prototype for new instances.
	 *
	 * @since 1.3.3
	 * @property {Object} prototype
	 */ 
	FLStyleSheet.prototype = {
		
		/**
		 * A reference to the stylesheet object.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _sheet
		 */
		_sheet          : null,
		
		/**
		 * A reference to the HTML style element.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _sheetElement
		 */
		_sheetElement   : null,

		/**
		 * Update a rule for this stylesheet.
		 *
		 * @since 1.3.3
		 * @method updateRule
		 * @param {String} selector The CSS selector to update.
		 * @param {String} property The CSS property to update. Can also be an object of key/value pairs.
		 * @param {String} value The value of the property to update. Can be omitted if property is an object.
		 */   
		updateRule: function(selector, property, value)
		{
			this._createSheet();
			
			var rules   = this._sheet.cssRules ? this._sheet.cssRules : this._sheet.rules;
				rule    = null,
				i       = 0;
			
			// Find the rule to update.
			for( ; i < rules.length; i++) {
				
				if(rules[i].selectorText.toLowerCase() == selector.toLowerCase()) {
					rule = rules[i];
				}
			}
			
			// Update the existing rule.
			if(rule) {
			
				if(typeof property == 'object') {
					
					for(i in property) {
						rule.style[this._toCamelCase(i)] = property[i];
					}
				}
				else {
					rule.style[this._toCamelCase(property)] = value;
				}
			}
			
			// No rule found. Add a new one.
			else {
				this.addRule(selector, property, value);
			}
		},
		
		/**
		 * Add a new rule to this stylesheet.
		 *
		 * @since 1.3.3
		 * @method addRule
		 * @param {String} selector The CSS selector to add.
		 * @param {String} property The CSS property to add. Can also be an object of key/value pairs.
		 * @param {String} value The value of the property to add. Can be omitted if property is an object.
		 */   
		addRule: function(selector, property, value)
		{
			this._createSheet();
			
			var styles  = '',
				i       = '';
			
			if(typeof property == 'object') {
					
				for(i in property) {
					styles += i + ':' + property[i] + ';';
				}
			}
			else {
				styles = property + ':' + value + ';';
			}
		
			if(this._sheet.insertRule) {
				this._sheet.insertRule(selector + ' { ' + styles + ' }', this._sheet.cssRules.length);
			}
			else {
				this._sheet.addRule(selector, styles);  
			}
		},
		
		/**
		 * Remove the stylesheet element from the DOM
		 * and the stored object reference.
		 *
		 * @since 1.3.3
		 * @method remove
		 */   
		remove: function() 
		{   
			if(this._sheetElement) {
				this._sheetElement.remove();
				this._sheetElement = null;
			}
			if(this._sheet) {
				this._sheet = null;
			}
		},
		
		/**
		 * Create the style element, add it to the DOM
		 * and save references.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _createSheet
		 */
		_createSheet: function() 
		{
			if(!this._sheet) {
			
				this._sheetElement = $('<style type="text/css"></style>');
				
				$('body').append(this._sheetElement);
			
				this._sheet = document.styleSheets[document.styleSheets.length - 1];
			}
		},
		
		/**
		 * Convert a string to camel case.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _toCamelCase
		 * @param {String} input The string to convert.
		 */   
		_toCamelCase: function(input) 
		{ 
			return input.toLowerCase().replace(/-(.)/g, function(match, group1) {
				return group1.toUpperCase();
			});
		}
	};

})(jQuery);