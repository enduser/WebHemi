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
 * @package   Script
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

var path = '/resources/theme/webhemi/img/login/';
var images = [
    '7-themes-com-7028725-green-foliage-branches.jpg',
    '7-themes-com-7033392-autumn-red-leaves.jpg',
    '7-themes-com-7038256-autumn-colors-leaves.jpg',
    '7-themes-com-7041505-tree-red-leaves.jpg',
    '7-themes-com-7041410-magnolia-flowers.jpg'
];
var cache = [];
var min = 0;
var max = images.length - 2;
var loadedIndex = Math.floor(Math.random()*(max - min + 1) + min);

// When the DOM is ready, we make some customization
document.addEventListener("DOMContentLoaded", function(event) {
    // Pre-loading the images after the page is rendered
    for (var i = 0; i < images.length; i++) {
        cache[i] = document.createElement('img');
        cache[i].src = path + images[i];
    }

    document.body.style.backgroundImage = 'url(' + path + images[loadedIndex] + ')';

    // create background switcher element with different image as the body's
    var switcher = document.createElement('div');
    switcher.setAttribute('id', 'switcher');
    document.body.appendChild(switcher);

    // create license element
    var license = document.createElement('div');
    license.setAttribute('id', 'license');
    license.innerHTML = 'Images: <a href="http://7-themes.com" target="_blank">7-themes.com</a>';
    document.body.appendChild(license);

    // get transition duration for the switcher
    var sleep = parseFloat(window.getComputedStyle(switcher).transitionDuration) * 1000;
    // set an interval of 10 second + animation time for a clear view of the background
    var interval = sleep + 10100;

    // start the background switcher loop
    setInterval(function() {
        // increment/reset background index
        if(++loadedIndex >= images.length) {
            loadedIndex = 0;
        }
        switcher.style.backgroundImage = 'url(' + path + images[loadedIndex] + ')';
        // run (CSS) transition
        switcher.classList.add('show');

        // wait for the css transition
        setTimeout(function(){
            // set the switcher's background for the body as well
            document.body.style.backgroundImage = 'url(' + path + images[loadedIndex] + ')';
            // "reset" the switcher
            switcher.classList.remove('show');
        }, sleep + 100);
    }, interval);
});

// Start using the Web Hemi Components from here
document.addEventListener("WebHemiComponentsLoaded", function(event) {
    var handler = Form.getAjaxFormHandler('login');

    if (typeof handler != 'undefined') {
        // Add progress indicator and set inputs inactive
        handler.onBeforeSubmit = function(event) {
            // add MDL progressbar
            if (typeof componentHandler != 'undefined') {
                var submitButton = event.target.querySelector('button[type=submit]');
                var progressBar = document.createElement('div');
                progressBar.setAttribute('id','formSubmitProgress');
                progressBar.setAttribute('class', 'mdl-progress mdl-js-progress mdl-progress__indeterminate');
                submitButton.parentNode.insertBefore(progressBar, submitButton);

                // apply MDL on new elements
                componentHandler.upgradeDom();
            }

            // Disable elements
            document.getElementById('identification').setAttribute('disabled',true);
            document.getElementById('password').setAttribute('disabled',true);
            document.getElementById('submit').setAttribute('disabled',true);


            return true;
        };

        // On error remove progress, set inputs active
        handler.onFailure = function() {
            var progress = document.getElementById('formSubmitProgress');
            if (progress) {
                progress.parentNode.removeChild(progress);
            }

            // Enable elements
            document.getElementById('identification').removeAttribute('disabled',true);
            document.getElementById('password').removeAttribute('disabled',true);
            document.getElementById('submit').removeAttribute('disabled',true);
        };

        // On response remove progress, set inputs active and handle the response
        handler.onSuccess = function(data) {
            var i, j;

            var progress = document.getElementById('formSubmitProgress');
            if (progress) {
                progress.parentNode.removeChild(progress);
            }

            // Enable elements
            document.getElementById('identification').removeAttribute('disabled',true);
            document.getElementById('password').removeAttribute('disabled',true);
            document.getElementById('submit').removeAttribute('disabled',true);

            if (data.success) {
                location.href = '//' + DOMAIN + '/';
            } else {
                // remove all previous errors
                var errorElements = document.querySelectorAll('form div.error');

                if (errorElements.length > 0) {
                    for (var i in errorElements) {
                        errorElements[i].parentNode.removeChild(errorElements[i]);
                    }
                }

                // for all elements with errors
                for (i in data.error) {
                    // if we have form error
                    if (data.error.hasOwnProperty(i) && i.lastIndexOf('Form') != -1) {
                        var formId = i;

                        // for all form elements with errors
                        for (j in data.error[i]) {
                            if (data.error[i].hasOwnProperty(j)) {
                                var elementId = j;
                                var errorBlock = null;
                                var message = data.error[i][j];

                                // check for element
                                var errorElement = document.querySelector('#' + formId + ' div.element.' + elementId + ' div.error');

                                if (!errorElement) {
                                    errorElement = document.createElement('div');
                                    errorElement.classList.add('error');
                                    errorElement.innerHTML = '<ul></ul>';

                                    document.querySelector('#' + formId + ' div.element.' + elementId).appendChild(errorElement);
                                }

                                if (!errorElement.classList.contains('hide')) {
                                    errorElement.classList.add('hide');
                                }

                                if (errorBlock == null) {
                                    errorBlock = document.querySelector('#' + formId + ' div.element.' + elementId + ' div.error ul');
                                }

                                var errorMessageElement = document.createElement('li');
                                var errorMessage = document.createTextNode(message);
                                errorMessageElement.appendChild(errorMessage);
                                errorBlock.appendChild(errorMessageElement);
                            }
                        }
                    } else {
                        // it's not a form error, so redirect to index page to see
                        location.href = '//' + DOMAIN + '/';
                    }
                }

                var errorElements = document.querySelectorAll('form div.error.hide');

                if (errorElements.length > 0) {
                    for (var i = 0, len = errorElements.length; i < len; i++) {
                        errorElements[i].classList.remove('hide');
                    }
                }
            }
        };

        // Remove error messages upon input events
        Util.addEventListener(document.querySelectorAll('#loginForm input'), 'focus select change', function(){
            var errorElements = document.querySelectorAll('form div.error');

            if (errorElements.length > 0) {
                for (var i = 0, len = errorElements.length; i < len; i++) {
                    errorElements[i].classList.add('hide');
                }

                setTimeout(function() {
                    var removeElements = document.querySelectorAll('form div.error.hide');

                    for (var i = 0, len = removeElements.length; i < len; i++) {
                        removeElements[i].parentNode.removeChild(removeElements[i]);
                    }
                }, 1200);
            }
        });
    }
});
