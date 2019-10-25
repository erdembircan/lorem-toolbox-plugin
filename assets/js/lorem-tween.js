/* eslint no-param-reassign: ["error", {"props": false}] */
/* eslint no-underscore-dangle: ["error", {"allowAfterThis": true}] */

(function factory(context, func) {
  context.loremTween = func();
})(this, function tween() {
  const safeHasOwnProperty = Object.prototype.hasOwnProperty;

  /**
   * iterate over Objects with property safe checking
   * @function
   * @param {object} obj - object to be iterated over
   * @param {function} callback - callback function with key as arguement
   */
  function propertyCheckAndIterate(obj, callback) {
    Object.keys(obj).map(k => {
      if (safeHasOwnProperty.call(obj, k)) {
        callback(k);
      }
      return null;
    });
  }

  /**
   *  tween a number field in an object within a given amount of time
   * @function
   * @param {number} duration - total amount of time for tween in miliseconds
   * @param {object} parent - parent object/property of the tweened field
   * @param {object} target - object of target fields in parent object with final values as values
   */
  function to(duration, parent, target) {
    const speeds = {};
    propertyCheckAndIterate(target, k => {
      const diff = target[k] - parent[k];
      const s = diff / duration;
      speeds[k] = s;
    });

    let lastTime = null;
    let elapsedTime = 0;

    /**
     * main logic fot animation and callback for requestAnimationFrame
     * @function
     * @param {DOMHighResTimeStamp} now - timestamp for document's time origin
     */
    function animate(now) {
      if (!lastTime) lastTime = now;
      const deltaTime = now - lastTime;
      lastTime = now;
      elapsedTime += deltaTime;

      if (elapsedTime < duration) {
        propertyCheckAndIterate(target, k => {
          const calculated = Number.parseFloat(parent[k]) + speeds[k] * deltaTime;
          parent[k] = calculated;
        });
        // recursive call starts/continues here
        requestAnimationFrame(animate);
      } else {
        propertyCheckAndIterate(target, k => {
          parent[k] = target[k];
        });
      }
    }
    this._context.requestAnimationFrame(animate);
  }

  return {
    to,
    _context: this,
  };
});
