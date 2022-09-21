(function () {

  ZeroComponent.prototype.appear = function appear(content, { effect = 'fade', persistent = false }, callback = null) {
    content
      .addClass(effect + '-enter-active')
      .addClass(effect + '-enter-from');

    const onAppear = () => {
      content
        .removeClass(effect + '-enter-active')
        .off('transitionend', onAppear);
      if (!persistent) content.removeClass(effect + '-enter-to');
      if (callback) callback();
    };

    setTimeout(() => {
      content
        .removeClass(effect + '-enter-from')
        .addClass(effect + '-enter-to')
        .on('transitionend', onAppear);
    }, 1);

    return content;
  };

  ZeroComponent.prototype.disappear = function disappear(content, { effect = 'fade', persistent = false }, callback = null) {
    content
      .addClass(effect + '-leave-active')
      .addClass(effect + '-leave-from');

    const onAppear = () => {
      content
        .removeClass(effect + '-leave-active')
        .off('transitionend', onAppear);
      if (!persistent) content.removeClass(effect + '-leave-to');
      if (callback) callback();
    };

    setTimeout(() => {
      content
        .removeClass(effect + '-leave-from')
        .addClass(effect + '-leave-to')
        .on('transitionend', onAppear);
    }, 1);

    return content;
  };

})();
