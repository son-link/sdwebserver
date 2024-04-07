/* eslint-disable consistent-return */
/* eslint-disable valid-jsdoc */
/* eslint-disable func-names */
/**
 * @name PequeJS
 * @description A small Javascript library for manipulate DOM and send AJAX requests
 * @author Alfonso Saavedra "Son Link"
 * @license GPL-3.0-or-later
 * @url https://son-link.github.io
 * @param {string|object} selector The CSS selector to use or DOM object.
 * @returns {object} PequeJS object.
 * @example $(document)
 * @example $('input#username')
 */
var $ = (function () {
  'use strict';

  /**
   * Create the constructor
   *
   * @param {string} selector The selector to .
   */
  var Constructor = function (selector) {
    if (selector === 'document') {
      this.elements = [document];
    } else if (selector === 'window') {
      this.elements = [window];
    } else {
      this.elements = (typeof selector === 'object') ? [selector] : document.querySelectorAll(selector);
    }
  };

  /**
   * Create a new instance of the constructor
   *
   * @param {string} selector The selector to use.
   * @returns {object} The instance of the library.
   */
  var instance = function (selector) {
    return new Constructor(selector);
  };

  /**
   * Return the HTML of the firs element or set the HTML for one or more elements.
   *
   * @param {string} [html] If set change the HTML of the elements.
   * If not return the HTML of the first element.
   * @returns {string} the HTML of the firts element found if html variable is not set.
   * @example $('p').html();
   * $('p#title').html('<strong>PequeJS</strong>');
   */
  Constructor.prototype.html = function (html) {
    if (!html) return this.elements[0].innerHTML;
    this.each(function (ele) {
      ele.innerHTML = html;
    });
  };

  /**
   * Return the text of the firs element or set the text for one or more elements.
   *
   * @param {string} [txt] If set change the text of the elements.
   * If not return the text of the first element found.
   * @returns {string} the HTML of the found element if html variable is not set.
   * @example console.log($('span').text());
   * $('span#username').text('Son Link');
   */
  Constructor.prototype.text = function (txt) {
    if (!txt) return this.elements[0].textContent;
    this.each(function (ele) {
      ele.textContent = txt;
    });
  };

  /**
   * Add event to the elements.
   *
   * @param {string} event event (click, change, keyup, etc).
   * @param {Function} callback The callback function.
   * @returns {undefined} Returns undefined if event and callback parameters are empty.
   * @example $('button').on('click', function() {
   *  alert('You clicked');
   * });
   */
  Constructor.prototype.on = function (event, selector, callback = null) {
    /* if (!event && !selector) return 'undefined';
    this.each(function (ele) {
      ele.addEventListener(event, callback);
    });
    */
    if (typeof selector === 'string' && typeof callback === 'function') {
      document.addEventListener(event, function (e) {
        for (var target = e.target; target && target !== this; target = target.parentNode) {
          if (target.matches(selector)) {
            callback.call(target, e);
            break;
          }
        }
      }, false);
    } else if (typeof selector === 'function') {
      this.each(function (ele) {
        ele.addEventListener(event, selector);
      });
    } else {
      return false;
    }
  };

  /**
   * Display the element(s).
   *
   * @param {string} [display] Set the display [block, flex, inline-block, etc] (by default is block)
   *
   * @example $('#modal').show();
   * @example $('.blocks').show('flex');
   */
  Constructor.prototype.show = function (display) {
    this.each(function (ele) {
      ele.style.display = (display) ? display : 'block';
    });
  };

  /**
   *  Hide the element(s).
   *
   * @example $('#modal').hide();
   */
  Constructor.prototype.hide = function () {
    this.each(function (ele) {
      ele.style.display = 'none';
    });
  };

  /**
   * Set a element attribute or return the attribute of the firts element found.
   *
   * @param {string} name The attribute name
   * @param {string|number} [value] The value for the attribute.
   * @returns {string} The attribute value of the first element found if value is not set.
   * @example $('#mylink').attr('href', 'https://google.es');
   * link = $('#mylink').attr('href');
   */
  Constructor.prototype.attr = function (name, value = null) {
    if (value === null) return this.elements[0].getAttribute(name);
    this.each(function (ele) {
      ele.setAttribute(name, value);
    });
  };

  /**
   * Remove a element(s) attribute.
   *
   * @param {string} name The attribute name
   * @example $('input#name').removeAttr('disabled');
   */
  Constructor.prototype.removeAttr = function (name) {
    this.each(function (ele) {
      ele.removeAttribute(name);
    });
  };

  /**
   * Append a text or HTML to element(s).
   *
   * @param {string} ele The text or HTML to append
   * @example $('ul#series').append('<li>The Rookie</li>');
   */
  Constructor.prototype.append = function (ele) {
    this.each(function (e) {
      e.innerHTML += ele;
    });
  };

  /**
   * Clear all content of the element(s).
   *
   * @example $('#series').empty();
   */
  Constructor.prototype.empty = function () {
    this.each(function (el) {
      while (el.firstChild) {
        el.removeChild(el.firstChild);
      }
    });
  };

  /**
   * Return if any element have the class name.
   *
   * @param {string} className The class name.
   * @returns {boolean} true if any element has the class or false if not.
   * @example $('#modal').hasClass('show');
   */
  Constructor.prototype.hasClass = function (className) {
    var has = false;
    this.each( function (ele) {
      if (ele.classList.contains(className)) {
        has = true;
        return has;
      }
    });
    return has;
  };

  /**
   * Add class(es) to each element(s). You can add more than one separated by spaces.
   *
   * @param {string} className The class(es) name.
   * @example $('#modal').addClass('show');
   * $('span#username').addClass('bold color-red');
   */
  Constructor.prototype.addClass = function (className) {
    this.each(function (ele) {
      ele.classList.add.apply(ele.classList, className.split(' '));
    });
  };

  /**
   * Remove class(es) on each element. You can remove more than one separated by spaces.
   *
   * @param {string} className The class(es) name
   * @example $('#modal').removeClass('show');
   * $('span#username').removeClass('bold color-red');
   */
  Constructor.prototype.removeClass = function (className) {
    this.each( function (ele) {
      ele.classList.remove.apply(ele.classList, className.split(' '));
    });
  };

  /**
   * Toggle class on each element.
   *
   * @param {string} className The class name to toggle.
   * @example $('#modal').toggleClass('show');
   */
  Constructor.prototype.toggleClass = function (className) {
    this.each( function (ele) {
      if ($(ele).hasClass(className)) {
        $(ele).removeClass(className);
      } else {
        $(ele).addClass(className);
      }
    });
  };

  /**
   * Get or set the value for input, textarea or select.
   *
   * @param {string|number} value If set change the value of the firts find element. If not return her value.
   * @example $('#modal').toggleClass('show');
   */
  Constructor.prototype.val = function (value) {
    if (value) {
      this.elements[0].value = value;
    } else {
      return this.elements[0].value;
    }
  };

  /**
   * Checks if the current set of elements match a selector and returns true if at least one of these elements matches the given arguments.
   * @param {String} selector The CSS selector
   * @returns Bool
   * @example $('input').is(':valid')
   * @example $('#accept-terms').is(':checked')
   */
  Constructor.prototype.is = function (selector) {
    var ele = this.elements[0];
    return (ele.matches || ele.matchesSelector || ele.msMatchesSelector || ele.mozMatchesSelector || ele.webkitMatchesSelector || ele.oMatchesSelector).call(ele, selector);
  };

  /**
   * Trigger a event for the element.
   * @param {eventname} event The event to trigger
   * @example $('#myform').trigger('submit')
   */
   Constructor.prototype.trigger = function (eventname) {
    var ele = this.elements[0];
    var myevent = new Event(eventname);
    ele.dispatchEvent(myevent);
  };

  /**
   * Iterate through every element of the collection.
   * The callback function receive the current element.
   *
   * @param {Function} callback The callback function
   * @returns {object} The actual element
   * @example $('ul#series').each(function(ele) {
   *  series.push($(ele).text());
   * });
   */
  Constructor.prototype.each = function (callback) {
    if (typeof callback !== 'function') return 'undefined';
    this.elements.forEach( function (ele) {
      if (callback.call(this, ele) === false) return false;
    });
  };

  /**
   * Perform an Ajax request.
   *
   * @param {string} url The URL to send the request.
   * @param {array} opts A array to set the options. See below.
   * @param {Fuction} opts.success A callback function when the request was successful.
   * @param {boolean} opts.async [true] Set to false to block execution until you finish receiving the response.
   * @param {object} opts.contentType [application/x-www-form-urlencoded; charset=UTF-8] The content type for the request.
   * @param {object} opts.data [] A object with the variables and her values to send.
   * @param {string} opts.dataType [json] What kind of data do you expect to receive (json, text, html).
   * @param {Function} opts.error [] The callback when the request fail.
   * @param {string} opts.type [GET] The HTTP request method (GET, POST, etc).
   * @example $().ajax('http://localhost/api', {
   *  data: {
   *    user: 'myuser',
   *    passwd: '123456'
   *  },
   *  type: 'POST',
   *  success: function(response) {
   *    console.dir(response);
   *  }
   * });
   */
  Constructor.prototype.ajax = function (url, opts) {
    if (!url && !opts) return false;
    if (!opts.success) return false;

    // Set default options variables
    if (!opts.type) opts.type = 'GET';
    else opts.type = opts.type.toUpperCase();
    if (!opts.dataType) opts.dataType = 'json';
    if (!opts.async) opts.async = true;

    var _url = url;
    var data = [];
    if (opts.data) {
      if (opts.type == 'GET') {
        Object.keys(opts.data).map(function (key) {
          data.push(key + '=' + encodeURIComponent(opts.data[key]));
        });
        data = data.join('&');
      } else {
        data = opts.data;
      }
    }

    var request = new XMLHttpRequest();
    if (opts.type === 'GET') _url += '?' + data;
    request.open(opts.type, _url, opts.async);
    if (opts.contentType) request.setRequestHeader('Content-Type', opts.contentType);

    request.onload = function () {
      if (this.status >= 200 && this.status < 400) {
        if (opts.dataType === 'json') data = JSON.parse(this.response);
        else if (opts.dataType === 'text' || opts.dataType === 'html') data = this.responseText;
        else data = this.response;
        opts.success(data);
      } else if (opts.error) opts.error(this.status, this.statusText);
    };

    request.onerror = function () {
      if (opts.error) opts.error(this.status, this.statusText);
    };

    if (data && opts.type !== 'GET') request.send(data);
    else request.send();
  };

  /**
   * Perform an GET request. This is a alias for $().ajax.
   *
   * @param {string} url The URL to send the request.
   * @param {object|Function} data A object with the variables and her values to send. If is a function take this as the callback.
   * @param {function} callback [] The callback function when the request success or error.
   *
   * @example $().get('http://localhost/api?key=123456789',
   *  function(response) {
   *    console.dir(response);
   *  }
   * );
   * @example $().get('http://localhost/api', {key: '123456789'},
   *  function(response) {
   *    console.dir(response);
   *  }
   * );
   */
  Constructor.prototype.get = function (url, data, callback) {
    var opts = {
      type: 'GET'
    };
    if (typeof (data) === 'function') {
      opts.success = function (resp) {
        return data(resp);
      };
      opts.error = function () {
        return data(false);
      };
    } else {
      opts.data = data;
      opts.success = function (resp) {
        if (typeof (callback) === 'function') callback(resp);
      };
      opts.error = function () {
        if (typeof (callback) === 'function') callback(false);
      };
    }
    this.ajax(url, opts);
  };

  /**
   * Perform an POST request. This is a alias for $().ajax.
   *
   * @param {string} url The URL to send the request.
   * @param {object} data A object with the variables and her values to send. If is a function take this as the callback.
   * @param {function} callback [] The callback function when the request success or error.
   * @example $().post('http://localhost/api', {key: '123456789'},
   *  function(response) {
   *    console.dir(response);
   *  }
   * );
   */
  Constructor.prototype.post = function (url, data, callback) {
    if (!url || !data || !callback) return false;
    var opts = {
      type: 'POST',
      data: data,
      success: function (resp) {
        if (typeof (callback) === 'function') callback(resp);
      },
      error: function () {
        if (typeof (callback) === 'function') callback(false);
      }
    };
    this.ajax(url, opts);
  };

  /**
   * Return the constructor instantiation
   */
  return instance;
})();
