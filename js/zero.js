(function ($) {

  if (Drupal.zero === undefined) Drupal.zero = {};

  if (window.createComponent !== undefined) return;

  /**
   * @param {string} component Name of the component
   * @param {object} object
   */
  Drupal.zero.createComponent = function(component, object) {
    const compKey = ZeroComponent.getComponentKey(component);
    Drupal.behaviors[compKey] = {
      attach: function(context) {
        $('.' + component).each(function () {
          const item = $(this);
          if (item.hasClass(component + '--init')) {
            const comp = item.data('z-component')[component];

            comp.attach(context, item);
          } else {
            item.addClass(component + '--init');
            const comp = new ZeroComponent(component, item);
            for (const index in object) {
              comp[index] = object[index];
            }

            const register = item.data('z-component') || {};
            register[component] = comp;
            item.data('z-component', register);

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
  window.createComponent = function() {
    console.warn('DEPRECATION: Please use "Drupal.zero.createComponent" instead of "createComponent".');
    Drupal.zero.createComponent(...arguments);
  };

  Drupal.zero.getComponent = function(component, key = 'default') {
    if (component instanceof ZeroComponent) {
      return component;
    } else if (component instanceof $) {
      const register = component.data('z-component') || {};
      if (key === 'default') {
        const keys = Object.keys(register);

        if (keys.length === 0) {
          return null;
        } else if (keys.length === 1) {
          return register[keys[0]];
        } else {
          throw new Error('Multiple components detected but requested is "default". Choose one of this "' + keys.join(', ') + '"');
        }
      }
      return register[key] || null;
    } else {
      return null;
    }
  };

})(jQuery);
