(function () {

  if (Drupal.zero !== undefined) return;
  Drupal.zero = {};

  /**
   * @param {string} component Name of the component
   * @param {object} object
   */
  window.createComponent = function (component, object) {
    return {
      attach: function(context) {
        jQuery('.' + component + ':not(.' + component + '--init)', context).each(function () {
          var item = jQuery(this);

          item.addClass(component + '--init');
          var comp = new ZeroComponent(component, item);
          for (var index in object) {
            comp[index] = object[index];
          }
          comp.init(context, item);
          item.addClass(component + '--inited');
        });
      },
    };
  };

})();
