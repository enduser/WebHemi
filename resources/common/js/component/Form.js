/**
 * WebHemi
 *
 * PHP version 5.4
 *
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
 * @package   Script_Component
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

var Form = {
    /** @var Boolean initialized  TRUE if the component is initialized */
    initialized : false,

    ajaxFormContainer : {},

    /**
     * Initialize Component
     */
    init : function()
    {
        // Fix chrome's ignore on deactivated auto complete
        var noAutocompleteElements = document.querySelectorAll('input[autocomplete=off]')
        for (var i = 0, len = noAutocompleteElements.length; i < len; i++) {
            var original = noAutocompleteElements[i];
            var copy = original.cloneNode(true);

            copy.setAttribute('value', '');
            copy.classList.add('cloned');

            // actually it is an insertAfter...
            original.parentNode.insertBefore(copy, original.nextSibling);

            // Make sure that the original will not be visible and will not make validation/navigation/reference errors
            original.style.display = 'none';
            original.removeAttribute('id');
            original.removeAttribute('required');
            original.removeAttribute('class');
            original.removeAttribute('tabindex');
            original.removeAttribute('accesskey');
        }
        this.initialized = true;
        console.info('  + Form component loaded.');

        this.initAjaxForms();
    },

    /**
     * Retrieve the ajax form handler object.
     *
     * @param identifier
     * @returns {AjaxForm}
     */
    getAjaxFormHandler : function(identifier) {
        return this.ajaxFormContainer[identifier];
    },

    /**
     * Collect and initializa Ajax Forms
     */
    initAjaxForms : function()
    {
        var ajaxForms = document.querySelectorAll('form.ajax');
        for (var i = 0, len = ajaxForms.length; i < len; i++) {
            var id = ajaxForms[i].getAttribute('id');
            var formHandler = new AjaxForm(ajaxForms[i]);
            formHandler.init();
            this.ajaxFormContainer[id] = formHandler;
        }
    }
};

/**
 * Ajax Form handler class.
 *
 * @param HTMLElement
 * @constructor
 */
function AjaxForm(HTMLElement) {
    /**
     * @var HTMLElement
     */
    this.HTMLForm = HTMLElement;
}

AjaxForm.prototype = (function() {
    /**
     * Retrive the HTML element
     *
     * @returns {HTMLElement}
     */
    function getTarget() {
        return this.HTMLForm;
    }

    /**
     * Submit event handler
     *
     * @param {Event} event
     * @private
     */
    function ajaxSubmit(event) {
        event.preventDefault();

        var targetUrl = this.HTMLForm.getAttribute('action');
        var formData = new FormData(this.HTMLForm);
        var handler = this;

        if (!handler.onBeforeSubmit(event)) {
            return false;
        }

        Util.ajax({
            url: targetUrl,
            data: formData,
            success: handler.onSuccess,
            failure: handler.onFailure
        });

    }

    /**
     * Set event handler for the HTML element
     */
    function init()
    {
        this.HTMLForm.addEventListener('submit', ajaxSubmit.bind(this), true);
        console.info('    + an Ajax Form is initialized');
    }

    return {
        /**
         * Constructor
         */
        constructor:AjaxForm,

        /**
         * Last chance to make changes before submit
         *
         * @param Event event
         * @return boolean
         */
        onBeforeSubmit : function(event)
        {
            return true;
        },

        /**
         * Default success event handler
         *
         * @param {string} data
         */
        onSuccess: function(data)
        {
            console.log(data);
        },

        /**
         * Default failure event handler
         *
         * @param {string} data
         */
        onFailure: function(data)
        {
            console.error(data);
        },

        /**
         * Private method caller
         * @param {requestCallback} callback
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
        init: function() { return this._(init)(); },
        getTarget: function() { return this._(getTarget)(); }
    };
})();
