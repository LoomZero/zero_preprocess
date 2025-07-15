(function () {

  ZeroComponent.prototype.appear = function appear(content, { effect = 'fade', persistent = false, initTime = 50 }, callback = null) {
    content.classList.add(effect + '-enter-from');

    const onAppear = () => {
      content.classList.remove(`${effect}-enter-active`);
      content.removeEventListener('transitioned', onAppear);
      if (!persistent) content.classList.remove(`${effect}-enter-to`);
      if (callback) callback();
    };

    setTimeout(() => {
      content.classList.add(`${effect}-enter-active`, `${effect}-enter-to`);
      content.classList.remove(`${effect}-enter-from`);
      content.addEventListener('transitionend', onAppear);
    }, initTime);

    return content;
  };

  ZeroComponent.prototype.disappear = function disappear(content, { effect = 'fade', persistent = false, initTime = 50 }, callback = null) {
    content.classList.add(`${effect}-leave-from`);

    const onAppear = () => {
      content.classList.remove(`${effect}-leave-active`);
      content.removeEventListener('transitionend', onAppear);
      if (!persistent) content.classList.remove(`${effect}-leave-to`);
      if (callback) callback();
    };

    setTimeout(() => {
      content.classList.add(`${effect}-leave-active`, `${effect}-leave-to`);
      content.classList.remove(`${effect}-leave-from`);
      content.addEventListener('transitionend', onAppear);
    }, initTime);

    return content;
  };

})();
