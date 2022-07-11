/**
 * @typedef {Object} T_CookiesOptions
 * @property {string} [key] The key for the cookie
 * @property {int} [expires]
 */

 (function() {

  /**
   * @param {string} component Name of the component
   * @param {jQuery} item
   * @constructor
   */
  function ZeroComponent(component, item) {
    this.component = component;
    this.item = item;
  }

  /**
   * @param {string} selector
   * @return {jQuery}
   */
  ZeroComponent.prototype.find = function find(selector) {
    return this.item.find(selector);
  };

  /**
   * @param {string} name
   * @returns {string}
   */
  ZeroComponent.prototype.getElementSelector = function element(name) {
    return '.' + this.component + '__' + name;
  };

  /**
   * @param {string} name
   * @return {jQuery}
   */
  ZeroComponent.prototype.element = function element(name) {
    return this.find(this.getElementSelector(name));
  };

  /**
   * @param {(string|Object)} name
   * @param {boolean} remain
   * @returns {function}
   */
  ZeroComponent.prototype.createTemplate = function createTemplate(name, remain = false) {
    var element = null;
    if (typeof name === 'string') {
      element = this.element(name);
    } else if (name.name) {
      element = this.element(name);
    } else if (name.selector) {
      element = jQuery(name.selector);
    }

    if (element === null || !element.length) return null;
    var template = _.template(_.unescape(element.html()));

    if (!remain) element.remove();
    return template;
  };

  /**
   * @param {string} name
   * @param {boolean} value
   * @return {boolean}
   */
  ZeroComponent.prototype.state = function state(name, value) {
    if (typeof value === 'boolean') {
      if (value) {
        this.item.addClass(this.component + '--' + name);
      } else {
        this.item.removeClass(this.component + '--' + name);
      }
    }
    return this.item.hasClass(this.component + '--' + name);
  };

  /**
   * @param {string} name
   * @param {function} listener
   * @return {string} event name
   */
  ZeroComponent.prototype.on = function on(name, listener) {
    var event = this.component + ':' + name;

    this.item.on(event, listener);
    return event;
  };

  /**
   * @param {string} name
   * @param {function} listener
   * @return {string} event name
   */
  ZeroComponent.prototype.onGlobal = function onGlobal(name, listener) {
    var event = this.component + ':' + name;

    $('body').on(event, listener);
    return event;
  };

  /**
   * @param {string} event
   * @param {function} listener
   * @return {string} event name
   */
  ZeroComponent.prototype.listen = function listen(event, listener) {
    this.item.on(event, listener);
    return event;
  };

  /**
   * @param {string} event
   * @param {function} listener
   * @return {string} event name
   */
  ZeroComponent.prototype.listenGlobal = function listenGlobal(event, listener) {
    $('body').on(event, listener);
    return event;
  };

  /**
   * @return {string} UUID
   */
  ZeroComponent.prototype.uuid = function uuid() {
    return this.item.data('uuid');
  };

  /**
   * Get settings of this item
   *
   * @return {Object} settings
   */
  ZeroComponent.prototype.settings = function settings() {
    var uuid = this.uuid();
    var settings = drupalSettings.zero && drupalSettings.zero.settings && drupalSettings.zero.settings[uuid] || {};
    var data = this.item.data();

    for (var index in data) {
      settings[index] = data[index];
    }
    return settings;
  };

  /**
   * Get the value of a cookie
   *
   * @param {(string|T_CookiesOptions)} key
   *
   * @returns {mixed}
   */
  ZeroComponent.prototype.getCookie = function getCookie(key) {
    if (!window.Cookies) throw new Error('Please add dependency "core/js-cookie" for the component "' + this.component + '"');
    if (typeof key === 'object') {
      key = key.key;
    }
    var value = window.Cookies.get(this.component + (key ? '__' + key : ''));
    try {
      value = JSON.parse(value);
    } catch (e) {}
    return value;
  };

  /**
   * Set the value of a cookie
   *
   * @param {mixed} value
   * @param {T_CookiesOptions} options
   *
   * @returns {mixed}
   */
  ZeroComponent.prototype.setCookie = function setCookie(value, options) {
    if (!window.Cookies) throw new Error('Please add dependency "core/js-cookie" for the component "' + this.component + '"');
    options = options || {};
    window.Cookies.set(this.component + (options.key ? '__' + options.key : ''), JSON.stringify(value), options);
    return value;
  };

  ZeroComponent.prototype.trigger = function trigger() {
    return this.item.trigger.apply(this.item, arguments);
  };

  ZeroComponent.prototype.message = function message(text, type, time) {
    time = time || 5000;
    type = type || 'note';
    var message = $('<div class="zero-message zero-message--' + type + '">' + text + '</div>');
    if (time) {
      setTimeout(function() {
        message.fadeOut(function() {
          message.remove();
        });
      }, time);
    }
    return message;
  };

  window.ZeroComponent = ZeroComponent;

})();
