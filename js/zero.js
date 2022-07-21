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
        jQuery('.' + component, context).each(function () {
          var item = jQuery(this);
          var comp = null;

          if (item.hasClass(component + '--init')) {
            comp = item.data('z-component');

            comp.attach(context, item);
          } else {
            item.addClass(component + '--init');
            comp = new ZeroComponent(component, item);
            for (var index in object) {
              comp[index] = object[index];
            }
            item.data('z-component', comp);
            comp.attach(context, item);
            comp.init(context, item);
            item.addClass(component + '--inited');
          }
        });
      },
    };
  };

})();
