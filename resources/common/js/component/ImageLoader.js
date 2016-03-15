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

var ImageLoader = {
    /** @var Boolean initialized  TRUE if the component is initialized */
    initialized : false,

    /**
     * Initialize Component
     */
    init : function()
    {
        this.initialized = true;
        console.info('  + Image Loader component loaded.');

        this.initLazyLoadImages();
    },

    /**
     * Collect and initializa Ajax Forms
     */
    initLazyLoadImages : function()
    {
        var lazyLoadImages = document.querySelectorAll('img[data-src]');

        for (var i = 0, len = lazyLoadImages.length; i < len; i++) {
            var imageHandler = new LazyLoadImage(lazyLoadImages[i]);
            imageHandler.init();
        }
    }
};

/**
 * Lazy Load Image handler class.
 *
 * @param HTMLElement
 * @constructor
 */
function LazyLoadImage(HTMLElement) {
    /** @var HTMLElement */
    this.HTMLImage = HTMLElement;
    /** @var Boolean */
    this.loaded = false;
}

LazyLoadImage.prototype = (function() {
    /**
     * Retrive the HTML element
     *
     * @returns {HTMLElement}
     */
    function getTarget() {
        return this.HTMLImage;
    }

    /**
     * Checks if the image is in the viewPort
     *
     * @returns {boolean}
     */
    function isImageInViewport() {
        var rect = this.HTMLImage.getBoundingClientRect()

        return (
            rect.top >= 0
            && rect.left   >= 0
            && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
        );
    }

    /**
     * Load image
     * @param event
     */
    function loadImage(event) {
        if (this.loaded) {
            return;
        }

        if (this.isImageInViewport()) {
            var src = this.HTMLImage.getAttribute('data-src');
            var preload = new Image;
            var imageLoader = this;

            preload.onerror = function() {
                console.warn('Image ' + src + ' cannot be loaded.');
            };

            preload.onload = function() {
                imageLoader.HTMLImage.src = src;
                imageLoader.HTMLImage.removeAttribute('data-src');
                imageLoader.loaded = true;
                console.info('      + a Lazy Load Image is loaded');
            };

            try {
                preload.src = src;
            } catch (exp) {}
        }
    }

    /**
     * Set event handler for the HTML element
     */
    function init()
    {
        window.addEventListener('scroll', loadImage.bind(this), true);
        console.info('    + a Lazy Load Image is initialized');
        this.loadImage();
    }

    return {
        /**
         * Constructor
         */
        constructor:LazyLoadImage,

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
        getTarget: function() { return this._(getTarget)(); },
        isImageInViewport: function() { return this._(isImageInViewport)(); },
        loadImage: function() { return this._(loadImage)(); }
    };
})();
