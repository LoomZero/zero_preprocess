(function ($) {

  if (Drupal.zero === undefined) Drupal.zero = {};

  if (window.createComponent !== undefined) return;

  /**
   * @param {string} component Name of the component
   * @param {object} object
   */
  Drupal.zero.createComponent = function (component, object) {
    const compKey = ZeroComponent.getComponentKey(component);
    Drupal.behaviors[compKey] = {
      attach: function(context) {
        $('.' + component).each(function () {
          const item = $(this);
          if (item.hasClass(component + '--init')) {
            const comp = item.data('z-component');

            comp.attach(context, item);
          } else {
            item.addClass(component + '--init');
            const comp = new ZeroComponent(component, item);
            for (const index in object) {
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
    return Drupal.behaviors[compKey];
  };
  /**
   * @deprecated Please use "Drupal.zero.createComponent" instead
   * @type {function}
   */
  window.createComponent = Drupal.zero.createComponent.bind(Drupal.zero);

  Drupal.zero.getComponent = function (component) {
    if (component instanceof ZeroComponent) {
      return component;
    } else if (component instanceof $) {
      return component.data('z-component');
    } else {
      return null;
    }
  };

})(jQuery);
