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
 * @package   Script_Component
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

var    Registry = {
    /** @var Boolean initialized  TRUE if the component is initialized */
    initialized : false,
    /** @var Object container   The associative array for key-value pairs */
    container : {},

    /**
     * Initialize Component
     */
    init : function()
    {
        this.initialized = true;
        console.info('  + Registry component loaded.');
    },

    /**
     * Checks whether the the key is present in the container
     *
     * @param String key   The key to check
     * @return boolean     TRUE if exists, FALSE otherwise
     */
    has : function(key)
    {
        if (typeof Util == 'undefined') {
            console.error('The Util class had not been initialized yet');
            return false;
        }

        return Util.isset(this.container[key]);
    },

    /**
     * Retrieves the value for the given key
     *
     * @param String key          The key to look for
     * @param String [optional]   A default value to return if no such key.
     * @return mixed   The value if key is found, NULL/default otherwise
     */
    get : function(key)
    {
        return this.has(key) ? this.container[key] : (arguments[1] != undefined ? arguments[1] : null);
    },

    /**
     * Set the value for a key
     *
     * @param String key    A unique ID
     * @param mixed value   The value to set
     * @return Registry
     */
    set : function(key, value)
    {
        this.container[key] = value;

        return this;
    },

    /**
     * Set key-value pairs
     *
     * @param Object param   Key-value pairs
     * @return Registry
     */
    setConfig : function(param)
    {
        if (Util.isObject(param)) {
            for (var key in param) {
                this.set(key, param[key]);
            }
        }
        return this;
    },

    /**
     * Removes a key from the container
     *
     * @param String key   The key to delete
     * @return Registry
     */
    unset : function(key)
    {
        if (this.has(key)) {
            this.container[key] = undefined;
        }
        return this;
    }
};
