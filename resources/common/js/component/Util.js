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

var Util = {
    /* @const String */
    TYPE_UNDEFINED : 'undefined',
    /* @const String */
    TYPE_FUNCTION  : 'function',
    /* @const String */
    TYPE_OBJECT    : 'object',
    /* @const String */
    TYPE_NULL      : null,

    /* @var Boolean initialized  TRUE if the component is initialized */
    initialized : false,

    /**
     * Initialize Component.
     */
    init : function()
    {
        this.initialized = true;
        console.info('  + Util component loaded.');
    },

    /**
     * Determines whether the given argument is defined or not.
     *
     * @param mixed      The variable to check.
     *
     * @return boolean   TRUE if defined, FALSE otherwise.
     */
    isset : function()
    {
        return typeof arguments[0] !== this.TYPE_UNDEFINED;
    },

    /**
     * Checks if the given argument is an array.
     *
     * @param mixed      The variable to check.
     *
     * @return boolean   TRUE if Array, ELSE otherwise.
     */
    isArray : function()
    {
        return (arguments[0] instanceof Array);
    },

    /**
     * Checks if the given argument is an object.
     *
     * @param mixed      The variable to check.
     *
     * @return boolean   TRUE if Object, ELSE otherwise.
     */
    isObject : function()
    {
        return typeof arguments[0] === this.TYPE_OBJECT;
    },

    /**
     * Checks if the given argument is a function.
     *
     * @param mixed      The variable to check.
     *
     * @return boolean   TRUE if Function, ELSE otherwise.
     */
    isFunction : function()
    {
        return typeof arguments[0] === this.TYPE_FUNCTION;
    },

    /**
     * Checks if the given argument is a NULL value.
     *
     * @param mixed      The variable to check.
     *
     * @return boolean   TRUE if NULL, ELSE otherwise.
     */
    isNull : function()
    {
        return arguments[0] === this.TYPE_NULL;
    },

    /**
     * Checks if the given needle is present in haystack array.
     *
     * @param {*} needle       The searched value.
     * @param {*} haystack     The array to search in.
     *
     * @return boolean   TRUE if found, FALSE otherwise.
     */
    inArray : function(needle, haystack)
    {
        var strict = (this.isset(arguments[2])) ? arguments[2] : false;

        if (this.isArray(haystack)) {
            for (var i = 0; i < haystack.length; i++) {
                 if (haystack[i] == needle) {
                    return !(strict && haystack[i] !== needle);
                }
            }
        }

        return false;
    },

    /**
     * Checks if the given needle is present in haystack object.
     *
     * @param {*} needle       The searched value.
     * @param {*} haystack     The object to search in.
     *
     * @return boolean   TRUE if found, FALSE otherwise.
     */
    has : function(needle, haystack)
    {
        if (this.isObject(haystack)) {
            return Object.prototype.hasOwnProperty.call(haystack, needle);
        }

        return false;
    },

    /**
     * Clones an object
     *
     * @param {null|Date|Array|object} obj The object to clone
     *
     * @return {object} The cloned object
     */
    clone : function(obj)
    {
        var copy;

        // Handle the 3 simple types, and null or undefined
        if (null == obj || "object" != typeof obj) return obj;

        // Handle Date
        if (obj instanceof Date) {
            copy = new Date();
            copy.setTime(obj.getTime());
            return copy;
        }

        // Handle Array
        if (obj instanceof Array) {
            copy = [];
            for (var i = 0, len = obj.length; i < len; i++) {
                copy[i] = this.clone(obj[i]);
            }
            return copy;
        }

        // Handle Object
        if (obj instanceof Object) {
            copy = {};
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = this.clone(obj[attr]);
            }
            return copy;
        }

        console.error('Unable to copy obj! Its type isn\'t supported.');
    },

    /**
     * Makes an XmlHttpRequest
     *
     * @param {*} settings
     * @example  {
     *   url: '/index',
     *   method: 'POST',
     *   data: {
     *     name: 'John Doe',
     *     email: 'johndoe@foo.org'
     *   },
     *   async: true,
     *   success: function(data) { alert('Done'); },
     *   failure: function(data) { alert('Failed'); }
     * }
     */
    ajax : function(settings)
    {
        var url = typeof settings.url != 'undefined' ? settings.url : '/';
        var method = typeof settings.method != 'undefined' ? settings.method : 'POST';
        var async = typeof settings.async != 'undefined' ? settings.async : true;
        var data = typeof settings.data != 'undefined' ? settings.data : '';
        var successCallback = typeof settings.success != 'undefined' ? settings.success : function(data) {};
        var failureCallback = typeof settings.failure != 'undefined' ? settings.failure : function(data) {};
        var xhr = new XMLHttpRequest();

        xhr.open(method, url, async);

        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE ) {
                if(xhr.status == 200){
                    successCallback(JSON.parse(xhr.responseText));
                } else {
                    failureCallback(JSON.parse(xhr.responseText));
                }
            }
        };

        if (!data instanceof FormData) {
            if (typeof data == 'array' || typeof data == 'object') {
                data = JSON.stringify(data);
                xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
            } else {
                xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            }
        }

        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    },

    /**
     * Add events to elements
     * @param {NodeList} elementList
     * @param {string}        eventList
     * @param {function}      callback
     */
    addEventListener : function(elementList, eventList, callback)
    {
        var events = eventList.split(' ');

        if (typeof elementList.length == 'undefined') {
            elementList = [elementList];
        }

        for (var i = 0, len = events.length; i < len; i++) {
            for (var j = 0, els = elementList.length; j < els; j++) {
                elementList[j].addEventListener(events[i], callback, false);
            }
        }
    },

    /**
     * Toggle browser fullscreen (experimental)
     */
    toggleFullscreen : function()
    {
        var doc = window.document;
        var docEl = doc.documentElement;

        var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
        var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

        if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
            requestFullScreen.call(docEl);
        }
        else {
            cancelFullScreen.call(doc);
        }
    }
};
