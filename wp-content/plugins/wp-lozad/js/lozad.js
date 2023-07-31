/*! lozad.js - v1.14.0 - 2019-10-31
 * https://github.com/ApoorvSaxena/lozad.js
 * Copyright (c) 2019 Apoorv Saxena; Licensed MIT */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined'
    ? (module.exports = factory())
    : typeof define === 'function' && define.amd
    ? define(factory)
    : (global.lozad = factory());
})(this, function () {
  'use strict';

  /**
   * Detect IE browser
   * @const {boolean}
   * @private
   */
  var isIE = typeof document !== 'undefined' && document.documentMode;

  var defaultConfig = {
    rootMargin: '0px',
    threshold: 0,
    load: function load(element) {
      if (element.nodeName.toLowerCase() === 'picture') {
        var img = document.createElement('img');
        if (isIE && element.getAttribute('data-iesrc')) {
          img.src = element.getAttribute('data-iesrc');
        }

        if (element.getAttribute('data-alt')) {
          img.alt = element.getAttribute('data-alt');
        }

        element.append(img);
      }

      if (element.nodeName.toLowerCase() === 'video' && !element.getAttribute('data-src')) {
        if (element.children) {
          var childs = element.children;
          var childSrc = void 0;
          for (var i = 0; i <= childs.length - 1; i++) {
            childSrc = childs[i].getAttribute('data-src');
            if (childSrc) {
              childs[i].src = childSrc;
            }
          }

          element.load();
        }
      }

      if (element.getAttribute('data-sizes')) {
        element.sizes = element.getAttribute('data-sizes');
      }

      if (element.getAttribute('data-poster')) {
        element.poster = element.getAttribute('data-poster');
      }

      if (element.getAttribute('data-src')) {
        element.src = element.getAttribute('data-src');
      }

      var dataSrcset = element.getAttribute('data-srcset');
      if (dataSrcset) {
        element.setAttribute('srcset', dataSrcset);
        if (isIE && element.nodeName.toLowerCase() === 'img') {
          var ieImgSrc = null;
          dataSrcset.split(',').map(function(e) {
            if (e.indexOf(' 1x') !== -1) {
              ieImgSrc = e.replace(/1x/i, '').trim();
              return true;
            }
          });
          if (ieImgSrc) {
            element.setAttribute('src', ieImgSrc);
          }
        }
      }

      ['height', 'width', 'alt'].forEach(function (item) {
        var requiredClass = 'lozad-' + item;
        if (element.classList.contains(requiredClass)) {
          element.removeAttribute(item);
          element.classList.remove(requiredClass);
        }
      });

      if (element.getAttribute('data-background-image')) {
        element.style.backgroundImage =
          "url('" +
          element.getAttribute('data-background-image').split(',').join("'),url('") +
          "')";
      } else if (element.getAttribute('data-background-image-set')) {
        var imageSetLinks = element.getAttribute('data-background-image-set').split(',');
        var firstUrlLink =
          imageSetLinks[0].substr(0, imageSetLinks[0].indexOf(' ')) || imageSetLinks[0]; // Substring before ... 1x
        firstUrlLink =
          firstUrlLink.indexOf('url(') === -1 ? 'url(' + firstUrlLink + ')' : firstUrlLink;
        if (imageSetLinks.length === 1) {
          element.style.backgroundImage = firstUrlLink;
        } else {
          element.setAttribute(
            'style',
            (element.getAttribute('style') || '') +
              ('background-image: ' +
                firstUrlLink +
                '; background-image: -webkit-image-set(' +
                imageSetLinks +
                '); background-image: image-set(' +
                imageSetLinks +
                ')')
          );
        }
      }

      if (element.getAttribute('data-toggle-class')) {
        element.classList.toggle(element.getAttribute('data-toggle-class'));
      }

      if (
        element.nodeName.toLowerCase() === 'span' &&
        element.getAttribute('data-original_content')
      ) {
        var event;
        var type = 'scroll';
        window.addEventListener(type, {handleEvent: __handlerScroll, element: element});
        if (typeof(CustomEvent) === 'function') {
          event = new CustomEvent(type);
        } else if (typeof(Event) === 'function') {
          event = new Event(type);
        } else {
          event = document.createEvent('Event');
          event.initEvent(type, true, true);
        }
        window.dispatchEvent(event);
      }
    },
    loaded: function loaded() {},
  };

  function __handlerScroll() {
    var element = this.element;
    var tmpDom = new DOMParser().parseFromString(
        window.atob(element.getAttribute('data-original_content')),
        'text/html'
    );
    element.replaceWith(tmpDom.body.firstElementChild);
  }

  function markAsLoaded(element) {
    element.setAttribute('data-loaded', true);
  }

  var isLoaded = function isLoaded(element) {
    return element.getAttribute('data-loaded') === 'true';
  };

  var onIntersection = function onIntersection(load, loaded) {
    return function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.intersectionRatio > 0 || entry.isIntersecting) {
          observer.unobserve(entry.target);

          if (!isLoaded(entry.target)) {
            load(entry.target);
            markAsLoaded(entry.target);
            loaded(entry.target);
          }
        }
      });
    };
  };

  var getElements = function getElements(selector) {
    var root = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;

    if (selector instanceof Element) {
      return [selector];
    }

    if (selector instanceof NodeList) {
      return selector;
    }

    return root.querySelectorAll(selector);
  };

  function lozad() {
    var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.lozad';
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    var _Object$assign = Object.assign({}, defaultConfig, options),
      root = _Object$assign.root,
      rootMargin = _Object$assign.rootMargin,
      threshold = _Object$assign.threshold,
      load = _Object$assign.load,
      loaded = _Object$assign.loaded;

    var observer = void 0;

    if (typeof window !== 'undefined' && window.IntersectionObserver) {
      observer = new IntersectionObserver(onIntersection(load, loaded), {
        root: root,
        rootMargin: rootMargin,
        threshold: threshold,
      });
    }

    return {
      observe: function observe() {
        var elements = getElements(selector, root);

        for (var i = 0; i < elements.length; i++) {
          if (isLoaded(elements[i])) {
            continue;
          }

          if (observer) {
            observer.observe(elements[i]);
            continue;
          }

          load(elements[i]);
          markAsLoaded(elements[i]);
          loaded(elements[i]);
        }
      },
      triggerLoad: function triggerLoad(element) {
        if (isLoaded(element)) {
          return;
        }

        load(element);
        markAsLoaded(element);
        loaded(element);
      },

      observer: observer,
    };
  }

  return lozad;
});

if (typeof Object.assign != 'function') {
  // Must be writable: true, enumerable: false, configurable: true
  Object.defineProperty(Object, 'assign', {
    value: function assign(target, varArgs) {
      // .length of function is 2
      'use strict';
      if (target == null) {
        // TypeError if undefined or null
        throw new TypeError('Cannot convert undefined or null to object');
      }

      var to = Object(target);

      for (var index = 1; index < arguments.length; index++) {
        var nextSource = arguments[index];

        if (nextSource != null) {
          // Skip over if undefined or null
          for (var nextKey in nextSource) {
            // Avoid bugs when hasOwnProperty is shadowed
            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
              to[nextKey] = nextSource[nextKey];
            }
          }
        }
      }
      return to;
    },
    writable: true,
    configurable: true,
  });
}

// polyfill for replaceWith() method which is not supported in Internet Explorer
function ReplaceWithPolyfill() {
  'use-strict'; // For safari, and IE > 10
  var parent = this.parentNode,
    i = arguments.length,
    currentNode;
  if (!parent) return;
  if (!i)
    // if there are no arguments
    parent.removeChild(this);
  while (i--) {
    // i-- decrements i and returns the value of i before the decrement
    currentNode = arguments[i];
    if (typeof currentNode !== 'object') {
      currentNode = this.ownerDocument.createTextNode(currentNode);
    } else if (currentNode.parentNode) {
      currentNode.parentNode.removeChild(currentNode);
    }
    // the value of "i" below is after the decrement
    if (!i)
      // if currentNode is the first argument (currentNode === arguments[0])
      parent.replaceChild(currentNode, this);
    // if currentNode isn't the first
    else parent.insertBefore(currentNode, this.nextSibling);
  }
}

if (!Element.prototype.replaceWith) Element.prototype.replaceWith = ReplaceWithPolyfill;
if (!CharacterData.prototype.replaceWith) CharacterData.prototype.replaceWith = ReplaceWithPolyfill;
if (!DocumentType.prototype.replaceWith) DocumentType.prototype.replaceWith = ReplaceWithPolyfill;
