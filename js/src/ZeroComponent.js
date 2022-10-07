(function($) {

  if (window.ZeroComponent !== undefined) return;

  let _settings = null;

  /**
   * @param {string} component Name of the component
   * @param {jQuery} item
   * @constructor
   */
  function ZeroComponent(component, item) {
    this.component = component;
    this.item = item;
  }

  ZeroComponent.getComponentKey = function getComponentKey(key) {
    return 'component__' + key.replace(/([^a-z]+)/g, '_');
  };

  /**
   * @param context
   * @param {jQuery} item
   */
  ZeroComponent.prototype.attach = function attach(context, item) { };

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
  ZeroComponent.prototype.getElementSelector = function getElementSelector(name) {
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
    let element = null;
    if (typeof name === 'string') {
      element = this.element(name);
    } else if (name.name) {
      element = this.element(name);
    } else if (name.selector) {
      element = $(name.selector);
    }

    if (element === null || !element.length) return null;
    const template = element.html();

    if (!remain) element.remove();
    return (params) => new Function(...Object.keys(params), `return \`${template}\`;`)(...Object.values(params));
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
    const event = this.component + ':' + name;

    this.item.on(event, listener);
    return event;
  };

  /**
   * @param {string} name
   * @param {function} listener
   * @return {string} event name
   */
  ZeroComponent.prototype.onGlobal = function onGlobal(name, listener) {
    const event = this.component + ':' + name;

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
    if (_settings === null) {
      const uuid = this.uuid();
      const data = this.item.data();
      _settings = drupalSettings.zero && drupalSettings.zero.settings && drupalSettings.zero.settings[uuid] || {};

      for (const index in data) {
        _settings[index] = data[index];
      }
    }
    return _settings;
  };

  ZeroComponent.prototype.trigger = function trigger() {
    return this.item.trigger.apply(this.item, arguments);
  };

  ZeroComponent.prototype.message = function message(text, type = 'note', time = 5000) {
    const message = $('<div class="zero-message zero-message--' + type + '">' + text + '</div>');
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

})(jQuery);
