/**
 * @typedef {Object} T_CookiesOptions
 * @property {string} [key] The key for the cookie
 * @property {int} [expireDays]
 * @property {(boolean|string)} [domain]
 * @property {string} [path]
 */

 (function() {

  if (window.ZeroComponent !== undefined) return;

 /**
  * @param {string} component Name of the component
  * @param {jQuery} item
  * @constructor
  */
 function ZeroComponent(component, item) {
   this.component = component;
   this.item = item;
   this._engines = {};
 }

 ZeroComponent.prototype.checkEngine = function checkEngine(name, fallback, error) {
   if (this._engines[name] === undefined) {
     if (typeof fallback === 'string') {
       this._engines[name] = window[fallback];
     } else {
       this._engines[name] = fallback;
     }
   }
   if (this._engines[name]) return this._engines[name];
   throw new Error(error + ' for the component "' + this.component + '"');
 };

 ZeroComponent.prototype.setCookieEngine = function setCookieEngine(engine) {
   this._engines.cookie = engine;
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
   const Cookies = this.checkEngine('cookie', 'ZeroCookies', 'Please add dependency "zero_preprocess/cookies"');

   if (typeof key === 'object') {
     key = key.key;
   }
   return Cookies.get(this.component + (key ? '__' + key : ''));
 };

 /**
  * Set the value of a cookie
  *
  * @param {mixed} value
  * @param {T_CookiesOptions} options
  *
  * @returns {this}
  */
 ZeroComponent.prototype.setCookie = function setCookie(value, options = {}) {
   /** @var import('./ZeroCookies') Cookies */
   const Cookies = this.checkEngine('cookie', 'ZeroCookies', 'Please add dependency "zero_preprocess/cookies"');

   Cookies.set(this.component + (options.key ? '__' + options.key : ''), value, options);
   return this;
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
