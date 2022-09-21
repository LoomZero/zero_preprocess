(function () {

  ZeroComponent.prototype.appear = function appear(content, { effect = 'fade', persistent = false }, callback = null) {
    content
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
        .addClass(effect + '-enter-active')
        .addClass(effect + '-enter-to')
        .removeClass(effect + '-enter-from')
        .on('transitionend', onAppear);
    }, 10);

    return content;
  };

  ZeroComponent.prototype.disappear = function disappear(content, { effect = 'fade', persistent = false }, callback = null) {
    content
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
        .addClass(effect + '-leave-active')
        .removeClass(effect + '-leave-from')
        .addClass(effect + '-leave-to')
        .on('transitionend', onAppear);
    }, 10);

    return content;
  };

})();
