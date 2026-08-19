/**
 * footer.js
 *
 * Adds a class required to reveal the search in the handheld footer bar.
 * Also hides the handheld footer bar when an input is focused.
 */
(function ($) {
  //   $(".swatches-inserted  a.product-variations-swatch").on(
  //     "click",
  //     function (event) {
  //       event.preventDefault();
  //       var variationImage = $(this).data("variation_image");

  //       var img =
  //         '<img width="324" height="324" src="' +
  //         variationImage +
  //         '" class="variations-swatch-image" alt="" decoding="async" loading="lazy" >';
  //       var productMainThumbnail = $(this)
  //         .closest(".swatches-inserted ")
  //         .find(".product-main-thumbnail img.attachment-main-product-image");
  //       var existingImage = productMainThumbnail.siblings(
  //         ".variations-swatch-image"
  //       );

  //       if (existingImage.length) {
  //         existingImage.replaceWith(img);
  //       } else {
  //         productMainThumbnail.after(img);
  //       }
  //     }
  //   );
  $(document).on(
    {
      click: function (e) {
        e.preventDefault();
        var variationImage = $(this).data("variation_image");
        updateProductImage(variationImage, $(this));
      },
      touch: function (e) {
        e.preventDefault();
        var variationImage = $(this).data("variation_image");
        updateProductImage(variationImage, $(this));
      },
      mouseenter: function (e) {
        e.preventDefault();
        var variationImage = $(this).data("variation_image");
        updateProductImage(variationImage, $(this));
      },

      mouseleave: function (e) {
        e.preventDefault();
        var defaultImage = $(this)
          .closest(".swatches-inserted")
          .find(".product-hover-thumbnail img")
          .attr("src");
        updateProductImage(defaultImage, $(this));
      },
    },
    ".swatches-inserted a.product-variations-swatch"
  );
  // $(document).on(
  //   "mouseenter",
  //   ".swatches-inserted a.product-variations-swatch",
  //   function () {
  //     var variationImage = $(this).data("variation_image");
  //     updateProductImage(variationImage, $(this));
  //   }
  // );

  // $(".swatches-inserted a.product-variations-swatch").on(
  //   "mouseleave",
  //   function () {
  //     var defaultImage = $(this)
  //       .closest(".swatches-inserted")
  //       .find(".product-hover-thumbnail img")
  //       .attr("src");
  //     updateProductImage(defaultImage, $(this));
  //   }
  // );

  function updateProductImage(imageUrl, context) {
    var img =
      '<img width="324" height="324" src="' +
      imageUrl +
      '" class=" variations-swatch-image" alt="" decoding="async" loading="lazy" >';

    var productMainThumbnail = context
      .closest(".swatches-inserted ")
      .find(".product-main-thumbnail img.attachment-main-product-image");
    var existingImage = productMainThumbnail.siblings(
      ".variations-swatch-image"
    );

    if (existingImage.length) {
      existingImage.replaceWith(img);
    } else {
      productMainThumbnail.after(img);
    }
  }
})(jQuery);
