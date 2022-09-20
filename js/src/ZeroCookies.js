/**
 * @typedef {Object} T_CookiesOptions
 * @property {string} [key] The key for the cookie
 * @property {int} [expireDays]
 * @property {(boolean|string)} [domain]
 * @property {string} [path]
 */

 (function () {

  if (window.ZeroCookies !== undefined) return;

  window.ZeroCookies = {

    set: function set(name, value, options = {}) {
      let expires = '';
      if (options.expireDays) {
        const date = new Date();
        date.setTime(date.getTime() + (options.expireDays * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
      }

      let domain = '';
      if (options.domain === true) {
        domain = '; domain=' + window.location.hostname;
      } else if (typeof options.domain === 'string') {
        domain = '; domain=' + options.domain;
      }

      let path = '';
      if (options.path) {
        path = '; path=' + options.path;
      }
      document.cookie = name + "=" + encodeURIComponent(JSON.stringify(value)) + expires + path + domain;
    },

    get: function get(name) {
      const result = document.cookie.match("(^|[^;]+)\s*" + name + "\s*=\s*([^;]+)");

      if (result) {
        return JSON.parse(decodeURIComponent(result.pop()));
      }
      return null;
    },

  };

  /**
   * Get the value of a cookie
   *
   * @param {(string|T_CookiesOptions)} key
   *
   * @returns {mixed}
   */
  ZeroComponent.prototype.getCookie = function getCookie(key) {
    if (typeof key === 'object') key = key.key;
    return ZeroCookies.get(this.component + (key ? '__' + key : ''));
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
    ZeroCookies.set(this.component + (options.key ? '__' + options.key : ''), value, options);
    return this;
  };

})();
