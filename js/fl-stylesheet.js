var FLStyleSheet;

(function($){

    /**
     * @class FLStyleSheet
     */
    FLStyleSheet = function() {};
    
    FLStyleSheet.prototype = {
    
        _sheet          : null,
        _sheetElement   : null,

        /**
         * @method updateRule
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
         * @method addRule
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
         * @method remove
         * @private
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
         * @method _createSheet
         * @private
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
         * @method _toCamelCase
         * @private
         */    
        _toCamelCase: function(input) 
        { 
            return input.toLowerCase().replace(/-(.)/g, function(match, group1) {
                return group1.toUpperCase();
            });
        }
    };

})(jQuery);