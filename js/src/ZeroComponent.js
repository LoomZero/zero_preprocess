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
   * @return {jQuery}
   */
  ZeroComponent.prototype.element = function element(name) {
    return this.find('.' + this.component + '__' + name);
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
