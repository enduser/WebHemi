/**
 * WebHemi
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category  Script
 * @package   Script
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */


function WebHemiComponents(options) {
    this.initialized = false;
    this.options = options;
    this.defaultOptions = {
        path: 'component/',
        components: [
            'Util',
            'Registry',
            'Form',
            'ImageLoader'
        ],
        event: null
    };
}

WebHemiComponents.prototype = (function() {
    /**
     * Initialize components
     */
    function init()
    {
        if (this.initialized) {
            return;
        }

        this.defaultOptions.event = new Event('WebHemiComponentsLoaded');

        if (typeof this.options == 'undefined') {
            this.options = {};
        }

        for (var i in this.defaultOptions) {
            if (typeof this.options[i] == 'undefined') {
                this.options[i] = this.defaultOptions[i];
            }
        }

        // correct path to absolute
        this.options.path = document.querySelector('script[src*="WHComponents.js"]').getAttribute('src').replace(/WHComponents\.js/, '') + this.options.path;

        loadComponents(this);

        this.initialized = true;
    }

    /**
     * Iterate through the components list and load each if possible.
     *
     * @param componentHandler
     */
    function loadComponents(componentHandler)
    {
        // there will be index upon recursive call
        var index = typeof arguments[1] != 'undefined' ? arguments[1] : 0;

        // Start loading components
        if (!index) {
            console.info('Start loading components...');
        }

        // if the component exists in the list
        if (typeof componentHandler.options.components[index] != 'undefined') {
            var componentName = componentHandler.options.components[index];

            // if the component is not loaded
            if (typeof window[componentName] == 'undefined') {
                // try to load the specified component
                (function(sourceElement, tag) {
                    var tags = sourceElement.getElementsByTagName(tag)[0];
                    var newTag = sourceElement.createElement(tag);

                    newTag.async = 1;
                    tags.parentNode.insertBefore(newTag, tags);

                    newTag.onload = newTag.onreadystatechange = function( _, isAbort ) {
                        if(isAbort || !newTag.readyState || /loaded|complete/.test(newTag.readyState) ) {
                            newTag.onload = newTag.onreadystatechange = null;
                            newTag = undefined;

                            if(!isAbort) {
                                window[componentName].init();
                                loadComponents(componentHandler, index + 1);
                            }
                        }
                    };

                    newTag.src = componentHandler.options.path + componentName + '.js';
                }(document, 'script'));
            }
            // if the component is loaded but not initialized
            else if (!window[componentName].initialized){
                window[componentName].init();
                loadComponents(componentHandler, index + 1);
            }
            // skip to the next component
            else {
                loadComponents(componentHandler, index + 1);
            }
        } else {
            console.info('All components are loaded.');
            document.dispatchEvent(componentHandler.options.event);
        }
    }

    return {
        /**
         * Constructor
         */
        constructor:WebHemiComponents,

        /**
         * Private method caller
         * @param callback
         * @returns {Function}
         * @private
         */
        _:function(callback){

            // instance referer
            var self = this;

            // callback that will be used
            return function(){
                return callback.apply(self, arguments);
            };
        },
        init: function() { return this._(init)(); }
    }
})();

var WebHemiComponents = new WebHemiComponents();

// Kindly wait for the Material Design Light library to load
if (typeof window.componentHandler != 'undefined') {
    document.addEventListener('mdl-componentupgraded', function() {
        WebHemiComponents.init();
    }, false);
} else {
    WebHemiComponents.init();
}
