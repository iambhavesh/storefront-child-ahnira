/**
 * footer.js
 *
 * Adds a class required to reveal the search in the handheld footer bar.
 * Also hides the handheld footer bar when an input is focused.
 */
(function ($) {
  var breakpoint = 1200;
  var sidebar = document.querySelector(".sidebar-left");
  var sidebarContent = document.querySelector(".sidebar-content");
  var filterCloseButton = document.querySelector(".btn-close-off-sidebar");
  var $single_product_delivery = $(".delivery-info-wrap .header");
  Fancybox.bind('[data-fancybox="gallery"]', {});

  $(".ahnira-copy-coupon").click(function () {
    var codeToCopy = $(this).data("code");
    var $item = $(this);

    if (codeToCopy) {
      copyToClipboard(codeToCopy);

      // Visual feedback
      $item.addClass("copied");

      // Reset after 2 seconds
      setTimeout(function () {
        $item.removeClass("copied");
      }, 2000);
    }
  });

  // Function to copy text to clipboard using Clipboard API
  function copyToClipboard(text) {
    navigator.clipboard
      .writeText(text)
      .then(function () {
        console.log("Text copied to clipboard");
      })
      .catch(function (err) {
        console.error("Error copying text to clipboard: ", err);
      });
  }
  liveViewsVisitors();
  modifyDeliveryElement($single_product_delivery);

  $("svg.dgwt-wcas-ico-magnifier-handler").replaceWith(
    '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.8438 19.3203C21.0781 19.5286 21.0781 19.75 20.8438 19.9844L19.9844 20.8438C19.75 21.0781 19.5286 21.0781 19.3203 20.8438L14.5938 16.1172C14.4896 16.013 14.4375 15.9089 14.4375 15.8047V15.2578C12.901 16.5859 11.1302 17.25 9.125 17.25C6.88542 17.25 4.97135 16.4557 3.38281 14.8672C1.79427 13.2786 1 11.3646 1 9.125C1 6.88542 1.79427 4.97135 3.38281 3.38281C4.97135 1.79427 6.88542 1 9.125 1C11.3646 1 13.2786 1.79427 14.8672 3.38281C16.4557 4.97135 17.25 6.88542 17.25 9.125C17.25 11.1302 16.5859 12.901 15.2578 14.4375H15.8047C15.9349 14.4375 16.0391 14.4896 16.1172 14.5938L20.8438 19.3203ZM4.71094 13.5391C5.9349 14.763 7.40625 15.375 9.125 15.375C10.8438 15.375 12.3151 14.763 13.5391 13.5391C14.763 12.3151 15.375 10.8438 15.375 9.125C15.375 7.40625 14.763 5.9349 13.5391 4.71094C12.3151 3.48698 10.8438 2.875 9.125 2.875C7.40625 2.875 5.9349 3.48698 4.71094 4.71094C3.48698 5.9349 2.875 7.40625 2.875 9.125C2.875 10.8438 3.48698 12.3151 4.71094 13.5391Z" fill="#000000"></path></svg>',
  );

  // Function to perform the modifications on the $delivery element
  function modifyDeliveryElement($delivery) {
    //var $delivery = $(".delivery-info-wrap .header");
    $delivery.prepend(
      '<div class="icon"><i class="fa-solid fa-truck-fast" style="color: #000000;"></i></div>',
    );
    $delivery
      .find("h6")
      .replaceWith('<div class="label">Estimated Delivery:</div>');
    $delivery
      .find(".cash_on_delivery")
      .replaceWith(
        '<div class="icon"><i class="fa-solid fa-indian-rupee-sign" style="color: #000000;"></i></i><div class="cash_on_delivery" style="padding: 0px 15px;">Cash On Delivery Available</div></div>',
      );
  }
  // Callback function to be executed when the element with the class 'woosq-popup' is added to the DOM
  function onElementLoaded(mutationsList) {
    for (const mutation of mutationsList) {
      if (mutation.type === "childList") {
        const addedNodes = mutation.addedNodes;
        for (const node of addedNodes) {
          if (node.classList && node.classList.contains("woosq-popup")) {
            // If the 'woosq-popup' element is added, execute the code to modify the $delivery element
            var $delivery = $(node).find(".delivery-info-wrap .header");
            if ($delivery.length > 0) {
              modifyDeliveryElement($delivery);
            }
          }
        }
      }
    }
  }

  // Start observing the body for changes
  const popupobserver = new MutationObserver(onElementLoaded);
  popupobserver.observe(document.body, { childList: true, subtree: true });

  $(document).on("change", "input.qty", function () {
    var form = $(this).closest("form");
    if (form.hasClass("woocommerce-cart-form")) {
      form
        .find("button[name='update_cart']")
        .prop("disabled", false)
        .trigger("click");
    }
  });

  $(document).on(
    "click",
    "form.woocommerce-cart-form button.increase, form.woocommerce-cart-form button.decrease, form.cart button.increase, form.cart button.decrease",
    function () {
      // Get current quantity values
      var form = $(this).closest("form");
      var qty = $(this).closest(".quantity").find(".qty");
      var val = parseFloat(qty.val());
      var max = parseFloat(qty.attr("max"));
      var min = parseFloat(qty.attr("min"));
      var step = parseFloat(qty.attr("step"));

      // Change the value if increase or decrease
      if ($(this).is(".increase")) {
        if (max && max <= val) {
          qty.val(max);
        } else {
          qty.val(val + step);
        }
      } else {
        if (min && min >= val) {
          qty.val(min);
        } else if (val > 1) {
          qty.val(val - step);
        } else {
          qty.val(0); // Set quantity to 0 if val <= 1
        }
      }

      // Enable and trigger the update button for the cart form

      if (form.hasClass("woocommerce-cart-form")) {
        form
          .find("button[name='update_cart']")
          .prop("disabled", false)
          .trigger("click");
      }
    },
  );

  $(document).on(
    "click touch",
    ".woofc-item-qty .decrease,.woofc-item-qty .increase",
    function () {
      // get values
      var $qty = $(this).closest(".woofc-item-qty").find(".qty"),
        val = parseFloat($qty.val()),
        max = parseFloat($qty.attr("max")),
        min = parseFloat($qty.attr("min")),
        step = $qty.attr("step");

      // format values
      if (!val || val === "" || val === "NaN") {
        val = 0;
      }

      if (max === "" || max === "NaN") {
        max = "";
      }

      if (min === "" || min === "NaN") {
        min = 0;
      }

      if (
        step === "any" ||
        step === "" ||
        step === undefined ||
        parseFloat(step) === "NaN"
      ) {
        step = 1;
      } else {
        step = parseFloat(step);
      }

      // change the value
      if ($(this).is(".woofc-item-qty .increase")) {
        if (max && (max === val || val > max)) {
          $qty.val(max);
        } else {
          $qty.val((val + step).toFixed(woofc_decimal_places(step)));
        }
      } else {
        if (val - step <= 0) {
          // remove item
          if (woofc_vars.confirm_remove === "yes") {
            if (confirm(woofc_vars.confirm_remove_text)) {
              woofc_remove_item($qty.closest(".woofc-item"));
            }
          } else {
            woofc_remove_item($qty.closest(".woofc-item"));
          }

          return false;
        }

        if (min && (min === val || val < min)) {
          $qty.val(min);
        } else if (val > 0) {
          $qty.val((val - step).toFixed(woofc_decimal_places(step)));
        }
      }

      // trigger change event
      $qty.trigger("change");
    },
  );

  var filterOpenButton = document.querySelector(
    ".mobile-filter-button-wrapper",
  );
  const hamburger = document.querySelector(".page-open-mobile-menu");
  // const siteNav=document.querySelector('.page-mobile-main-menu');
  const closeButton = document.querySelector(".page-close-mobile-menu");

  hamburger.addEventListener("click", (event) => {
    event.preventDefault();
    document.body.classList.add("page-mobile-menu-opened", "handheld");
  });

  closeButton.addEventListener("click", (event) => {
    event.preventDefault();
    document.body.classList.remove("page-mobile-menu-opened", "handheld");
  });

  if (sidebar && sidebar.classList.contains("sidebar")) {
    var prevWW = window.innerWidth;
    toggleSidebar();

    window.addEventListener("resize", function () {
      var isToggle = true;
      if (prevWW !== window.innerWidth) {
        // Horizontal resize only.
        if (
          (prevWW <= breakpoint && window.innerWidth <= breakpoint) ||
          (prevWW > breakpoint && window.innerWidth > breakpoint)
        ) {
          isToggle = false;
        }

        if (isToggle) {
          toggleSidebar();
        }
      }
      prevWW = window.innerWidth;
    });
  }
  function toggleSidebar() {
    if (window.innerWidth <= breakpoint) {
      sidebar.classList.add("sidebar-off", "sidebar-switching");
      sidebarContent.style.height = window.innerHeight + "px";
    } else {
      sidebar.classList.add("sidebar-switching");
      sidebar.classList.remove("sidebar-off");
      sidebarContent.style.height = "auto";
    }
    setTimeout(function () {
      sidebar.classList.remove("sidebar-switching");
    }, 350);
  }

  if (filterCloseButton !== null) {
    filterCloseButton.addEventListener("click", (event) => {
      event.preventDefault();
      sidebar.classList.remove("sidebar-active");
    });
  }
  if (filterOpenButton !== null) {
    filterOpenButton.addEventListener("click", (event) => {
      event.preventDefault();
      sidebar.classList.add("sidebar-active");
    });
  }

  const RelatedProductswiper = new Swiper(".related-product-swiper", {
    slidesPerView: 2.3,
    spaceBetween: 10,
    breakpoints: {
      // when window width is >= 769px
      769: {
        slidesPerView: 3.5,
      },
      577: {
        slidesPerView: 3,
      },
      991: {
        slidesPerView: 4,
      },
    },
    // Navigation arrows
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });
  const UpSellSwiper = new Swiper(".up-sells-swiper", {
    slidesPerView: 2.3,
    spaceBetween: 10,
    breakpoints: {
      // when window width is >= 769px
      769: {
        slidesPerView: 3.5,
      },
      577: {
        slidesPerView: 3,
      },
      991: {
        slidesPerView: 4,
      },
    },
    // Navigation arrows
    navigation: {
      nextEl: ".upsell-swiper-button-next",
      prevEl: ".upsell-swiper-button-prev",
    },
  });
  const CrossSellSwiper = new Swiper(".cross-sells-swiper", {
    slidesPerView: 2.3,
    spaceBetween: 10,
    breakpoints: {
      // when window width is >= 769px
      769: {
        slidesPerView: 2,
      },
      577: {
        slidesPerView: 2.3,
      },
      991: {
        slidesPerView: 2.3,
      },
    },
    // Navigation arrows
    navigation: {
      nextEl: ".cross-swiper-button-next",
      prevEl: ".cross-swiper-button-prev",
    },
  });
 
  function ahniraGoToGallerySlide(index) {
    var $gallery = $(".woocommerce-product-gallery");
    if (!$gallery.length) return;

    // flexslider API (WooCommerce default)
    if ($gallery.data("flexslider")) {
      $gallery.data("flexslider").flexAnimate(index, true);
      return;
    }

    // Fallback: show the matching WC gallery image directly
    var $images = $gallery.find(".woocommerce-product-gallery__image");
    $images.hide().eq(index).show();
  }

  /**
   * Update which thumbnail is highlighted as active.
   */
  function ahniraSetActiveThumb(index) {
    $(".ahnira-thumb-slide")
      .removeClass("swiper-slide-thumb-active")
      .eq(index)
      .addClass("swiper-slide-thumb-active");
  }

  // Thumbnail click handler
  $(document).on("click keydown", ".ahnira-thumb-slide", function (e) {
    // Allow keyboard navigation (Enter / Space)
    if (e.type === "keydown" && e.which !== 13 && e.which !== 32) return;
    e.preventDefault();

    var idx = parseInt($(this).data("index"), 10);
    ahniraGoToGallerySlide(idx);
    ahniraSetActiveThumb(idx);
  });

  // Keep custom thumb strip in sync when WC gallery slide changes
  $(document).on(
    "woocommerce_gallery_init_zoom woocommerce_update_product_gallery",
    function () {
      var $gallery = $(".woocommerce-product-gallery");
      if ($gallery.length && $gallery.data("flexslider")) {
        var currentIdx = $gallery.data("flexslider").currentSlide || 0;
        ahniraSetActiveThumb(currentIdx);
      }
    },
  );
$(document).on("mouseup touchend click", ".woocommerce-product-gallery__wrapper", function () {
    var $gallery = $(".woocommerce-product-gallery");
    
    // Use a tiny 50ms delay to let Flexslider finish moving and assign classes
    setTimeout(function() {
        var currentIdx = 0;
        
        // Approach A: Check Flexslider instance properties
        if ($gallery.data("flexslider")) {
            currentIdx = $gallery.data("flexslider").currentSlide || 0;
        } else {
            // Approach B: Find the slide that WooCommerce marked active
            var $activeSlide = $gallery.find(".woocommerce-product-gallery__image.flex-active-slide");
            if ($activeSlide.length) {
                currentIdx = $activeSlide.index();
            }
        }
        
        // Apply the index to your thumbnail active state
        ahniraSetActiveThumb(currentIdx);
    }, 50);
});
  // Sync thumb when flexslider fires its after-animation callback
  $(document).on("woocommerce_gallery_after_slide", function (e, slider) {
    ahniraSetActiveThumb(slider.currentSlide || 0);
  });
  // ─── Variation image switching ────────────────────────────────────────────
  // When a product variation is selected, jump the gallery to the
  // variation's image. Uses WC's native flexslider API.
  $(document).on("found_variation", function (event, variation) {
    if (!variation.image || !variation.image.full_src) return;

    var $gallery = $(".woocommerce-product-gallery");
    if (!$gallery.length) return;

    var targetSrc = variation.image.full_src;
    var matchedIdx = 0;

    $gallery.find(".woocommerce-product-gallery__image").each(function (i) {
      var imgSrc =
        $(this).find("a").attr("href") ||
        $(this).find("img").attr("data-large_image") ||
        $(this).find("img").attr("src") ||
        "";
      if (imgSrc && imgSrc === targetSrc) {
        matchedIdx = i;
        return false; // break
      }
    });

    ahniraGoToGallerySlide(matchedIdx);
    ahniraSetActiveThumb(matchedIdx);
  });

  // Reset thumb highlight when variation is cleared
  $(document).on("reset_image", function () {
    ahniraGoToGallerySlide(0);
    ahniraSetActiveThumb(0);
  });

  function liveViewsVisitors() {
    var $wrap = $("#live-viewing-visitors");

    if (0 >= $wrap.length) {
      return;
    }
    var { min, max, duration, labels } = $wrap.data("settings");
    var $text = $wrap.find(".text");
    min = parseInt(min);
    max = parseInt(max);

    setInterval(function () {
      var current = Math.floor(Math.random() * (max - min + 1)) + min,
        text = current > 1 ? labels.plural : labels.singular;
      text = text.replace("%s", '<span class="count">' + current + "</span>");
      $text.html(text);
    }, duration);
  }

  var scrollToReviews = function () {
    $("html, body").animate(
      { scrollTop: $("#reviews").offset().top - 100 },
      "easeInOutExpo",
    );
  };
  $('button[name="buy-now"]').removeClass("single_add_to_cart_button");
  // $("li.variable-item").on("click", () => {
  //   console.log($("div.woocommerce-variation").length);
  //   if ($("div.woocommerce-variation").length > 0) {
  //     $$("div.woocommerce-variation").find(".woocommerce-variation-description").css("display", "none");
  //   }
  // });

  $(".single-product .woocommerce-review-link").on("click", (e) => {
    e.preventDefault();
    var reviewTab, activeTab;

    if ($(window).width() < 700) {
      reviewTab = $("#tab-reviews");
      activeTab = $(".accordion-item div.show");
      var reviewButton = reviewTab.parent().find("button.accordion-button");

      if (
        activeTab.length === 0 ||
        reviewTab[0] !== activeTab.parent().data("reviewTab")
      ) {
        var activeButton = activeTab.parent().find("button.accordion-button");

        activeButton.addClass("collapsed").attr("aria-expanded", "false");
        activeTab.removeClass("show");

        reviewButton.removeClass("collapsed").attr("aria-expanded", "true");
        reviewTab.addClass("show");

        scrollToReviews();
      } else {
        scrollToReviews();
      }
    } else {
      reviewTab = $("#nav-reviews-tab");
      activeTab = $(".nav-tabs button.active");

      if (reviewTab[0] !== activeTab.data("reviewTab")) {
        activeTab.removeClass("active");
        $(".nav-tabs button.reviews").addClass("active");

        $("#nav-tabContent div.show").removeClass("active show");
        $("#nav-reviews").addClass("active show");
      }
      scrollToReviews();
    }
  });
  $(document).on("click", ".woosq-popup .woocommerce-review-link", (e) => {
    e.preventDefault();
  });
  jQuery.fn.getParent = function (num) {
    var last = this[0];
    for (var i = 0; i < num; i++) {
      last = last.parentNode;
    }
    return jQuery(last);
  };
  var menuToggle = $(".mobile-menu").find("li.menu-item-has-children");
  menuToggle.each(function () {
    $(this)
      .children("a")
      .append(
        "<span class='toggle-sub-menu'><i class='fa-solid fa-chevron-down'></i></span>",
      );
  });

  $(".toggle-sub-menu").click(function (e) {
    e.preventDefault();
    e.stopPropagation();
    var parentLi = $(this).closest("li");
    var subMenu = parentLi.children(".sub-menu");

    subMenu.slideToggle(300);
    parentLi.toggleClass("opened");
  });

  // $(".image-gallery-sticky").hcSticky({
  //   stickTo: "#product-image-parent",
  // });

  $("#btn-toggle-account-nav").on("click", function () {
    $(".account-nav").toggleClass("opened");
  });

  $(document.body).on("woofc_cart_loaded", function () {
    var $suggested = $(".woofc-suggested");
    var $subtotal = $(".woofc-subtotal.woofc-data");
    if ($suggested.length && $subtotal.length) {
      $suggested.insertBefore($subtotal);
    }
  });
})(jQuery);
