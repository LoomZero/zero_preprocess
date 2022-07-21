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

})();
