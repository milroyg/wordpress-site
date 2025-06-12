(function ($) {
  /**
   * @param $scope The Widget wrapper element as a jQuery element
   * @param $ The jQuery alias
   */
  var WcfCounter = function WcfCounter($scope, $) {
    var _this = this;
    var $counter = $('.wcf--counter-number', $scope);
    this.intersectionObserver = elementorModules.utils.Scroll.scrollObserver({
      callback: function callback(event) {
        if (event.isInViewport) {
          _this.intersectionObserver.unobserve($counter[0]);
          var data = $counter.data(),
            decimalDigits = data.toValue.toString().match(/\.(.*)/);
          if (decimalDigits) {
            data.rounding = decimalDigits[1].length;
          }
          $counter.numerator(data);
        }
      }
    });
    this.intersectionObserver.observe($counter[0]);
  };

  // Make sure you run this code under Elementor.
  $(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/wcf--counter.default', WcfCounter);
  });
})(jQuery);