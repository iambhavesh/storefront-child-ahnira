jQuery(document).ready(function ($) {
  // Price Range Slider Initialization
  var $range = $(".js-range-slider");
  var $instance;

  $range.ionRangeSlider({
    prettify_enabled: false,
    prettify_separator: "",
    onFinish: function (data) {
      updateFilters();
    },
  });
  $instance = $range.data("ionRangeSlider");
});
