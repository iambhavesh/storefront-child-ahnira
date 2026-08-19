jQuery(document).ready(function ($) {
  // Select all menu items that have children
  const menuItemsWithChildren = document.querySelectorAll(
    ".primary-navigation li.menu-item-has-children",
  );
  const overlay = document.querySelector(".ahnira-menu-overlay");

  menuItemsWithChildren.forEach((item) => {
    // When mouse enters the specific LI
    item.addEventListener("mouseenter", () => {
      overlay.classList.add("is-active");
    });

    // When mouse leaves the specific LI
    item.addEventListener("mouseleave", () => {
      overlay.classList.remove("is-active");
    });
  });

  // Related Products Swiper
  var relatedProducts = new Swiper(".related-product-swiper", {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
      nextEl: ".related-swiper-button-next",
      prevEl: ".related-swiper-button-prev",
    },
    breakpoints: {
      320: {
        slidesPerView: 2.5,
        spaceBetween: 10,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 15,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });

  // Up-sells Swiper
  var upsellsProducts = new Swiper(".up-sells-swiper", {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
      nextEl: ".upsell-swiper-button-next",
      prevEl: ".upsell-swiper-button-prev",
    },
    breakpoints: {
      320: {
        slidesPerView: 2.5,
        spaceBetween: 10,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 15,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });

  // Cross-sells Swiper
  var crossSellsProducts = new Swiper(".cross-sells-swiper", {
    slidesPerView: 4,
    spaceBetween: 20,
    navigation: {
      nextEl: ".cross-swiper-button-next",
      prevEl: ".cross-swiper-button-prev",
    },
    breakpoints: {
      320: {
        slidesPerView: 2.5,
        spaceBetween: 10,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 15,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });

  // new Carousel(container, options, { Thumbs });
  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });
 

  // Checkbox Filter Change
  $(".filter-checkbox").on("change", function () {
    updateFilters();
  });

  // Clear All Filters
  $(".clear-all-filters").on("click", function (e) {
    e.preventDefault();
    window.location.href = $(this).attr("href");
  });

  function updateFilters() {
    // Determine the base URL. If we have categories selected, we should probably
    // stay on the shop page to allow multi-category filtering easily,
    // OR respect the current category page if only attributes are changed.

    var shopUrl = $(".clear-all-filters").attr("href");
    var currentUrl = window.location.href.split("?")[0];
    var baseUrl = currentUrl;

    // Reset pagination - remove "/page/X/" from URL if present
    baseUrl = baseUrl.replace(/\/page\/\d+\/?$/, "/");
    var params = new URLSearchParams(window.location.search);

    // Reset relevant params
    params.delete("min_price");
    params.delete("max_price");

    var attrKeys = [];
    params.forEach((value, key) => {
      if (key.startsWith("filter_") || key.startsWith("query_type_")) {
        attrKeys.push(key);
      }
    });
    attrKeys.forEach((key) => params.delete(key));

    // Handle Price
    if ($instance) {
      params.set("min_price", $instance.result.from);
      params.set("max_price", $instance.result.to);
    }

    // Attribute & Category Params
    var filters = {};
    var selectedCats = [];

    $(".filter-checkbox:checked").each(function () {
      var tax = $(this).data("taxonomy");
      var slug = $(this).data("slug");

      if (tax === "product_cat") {
        selectedCats.push(slug);
      } else {
        var key = "filter_" + tax.replace("pa_", "");
        if (!filters[key]) filters[key] = [];
        filters[key].push(slug);
        params.set("query_type_" + tax.replace("pa_", ""), "or");
      }
    });

    // Add Attributes to Params
    Object.keys(filters).forEach(function (key) {
      params.set(key, filters[key].join(","));
    });

    // Handle Category parameter
    if (selectedCats.length > 0) {
      params.set("product_cat", selectedCats.join(","));
      // If we are on a specific category page, it's safer to move to the shop page URL
      // when using the product_cat parameter to support multi-category filtering.
      if (currentUrl.indexOf("product-category") !== -1) {
        baseUrl = shopUrl;
      }
    } else {
      params.delete("product_cat");
      // If we were on a category page and unchecked the category, we must redirect to the general shop.
      if (currentUrl.indexOf("product-category") !== -1) {
        baseUrl = shopUrl.replace(/\/$/, "");
      }
    }

    var newQuery = params.toString();
    // Strip trailing slash from baseUrl before appending query parameters
    baseUrl = baseUrl.replace(/\/$/, "");
    window.location.href = baseUrl + (newQuery ? "?" + newQuery : "");
  }

  // Remove Filter Chip Click
  $(document).on("click", ".active-filter-chip", function () {
    var tax = $(this).data("taxonomy");
    var slug = $(this).data("slug");
    var type = $(this).data("type");

    if (type === "price") {
      // Reset price slider
      if ($instance) {
        $instance.update({
          from: $range.data("min"),
          to: $range.data("max"),
        });
        updateFilters();
      }
    } else {
      // Uncheck sidebar checkbox
      $(
        '.filter-checkbox[data-taxonomy="' +
          tax +
          '"][data-slug="' +
          slug +
          '"]',
      ).prop("checked", false);
      updateFilters();
    }
  });

  // Accordion Icon Toggle
  $(".filter-head").on("click", function () {
    $(this).find("i").toggleClass("fa-chevron-up fa-chevron-down");
  });

  // Sidebar Toggle
  $(".btn-open-sidebar, .filter-button-wrapper").on("click", function (e) {
    e.preventDefault();
    $("body").addClass("sidebar-opened");
  });

  $(".btn-close-off-sidebar, body").on("click", function (e) {
    if (
      $(e.target).closest(
        ".sidebar-left, .btn-open-sidebar, .filter-button-wrapper",
      ).length === 0 ||
      $(e.target).closest(".btn-close-off-sidebar").length > 0
    ) {
      $("body").removeClass("sidebar-opened");
    }
  });
  function convertVariationsToChips() {
    $(".variations select").each(function () {
      var $select = $(this);

      // Don't convert if already converted or not visible
      if ($select.hasClass("chips-converted")) return;

      var $parent = $select.parent();

      // Hide original select
      $select.addClass("d-none chips-converted");

      // Create chips container
      var $chipsWrap = $('<div class="variations-grid mt-2"></div>');

      $select.find("option").each(function () {
        var val = $(this).val();
        var text = $(this).text();

        if (val === "") return; // Skip "Choose an option"

        var $chip = $(
          '<div class="variation-chip" data-value="' +
            val +
            '">' +
            text +
            "</div>",
        );

        if ($select.val() === val) {
          $chip.addClass("active");
        }

        $chip.on("click", function () {
          $select.val(val).trigger("change");
          $chipsWrap.find(".variation-chip").removeClass("active");
          $(this).addClass("active");
        });

        $chipsWrap.append($chip);
      });

      $parent.append($chipsWrap);
    });
  }

  // Sync chips with select when select changes (e.g. by Reset button)
  $(document).on("change", ".variations select", function () {
    var $select = $(this);
    var val = $select.val();
    var $chipsWrap = $select.parent().find(".variations-grid");
    $chipsWrap.find(".variation-chip").removeClass("active");
    if (val) {
      $chipsWrap
        .find('.variation-chip[data-value="' + val + '"]')
        .addClass("active");
    }
  });

  // Initialize chips on load and when variations change (for dependent attributes)
  convertVariationsToChips();

  $(document).on("woocommerce_variation_has_changed", function () {});

  function convertFBTVariationsToChips() {
    $(".fbt-variation-select").each(function () {
      var $container = $(this);
      var $select = $container.find(".fbt-variation-dropdown");

      // Skip if already converted or no select found
      if (!$select.length || $select.hasClass("fbt-chips-converted")) return;

      // Hide the select but keep it in the DOM (plugin JS still needs it)
      $select.addClass("fbt-chips-converted").hide();

      // Build chips wrapper — reuse the same CSS class as the WC chips
      var $chipsWrap = $(
        '<div class="variations-grid fbt-variations-grid mt-2"></div>',
      );

      $select.find("option").each(function () {
        var val = $(this).val();
        var fullText = $(this).text().trim();

        if (val === "") return; // skip placeholder

        // Show only the variation attributes part (strip trailing price) for compact chips.
        // Option format from PHP: "Red - Small - ₹500.00"  →  keep "Red - Small"
        var labelText = fullText.replace(/\s*[-–]\s*<[^>]+>.*$/, "").trim();
        // Fallback: strip anything after the last " - " that starts with a currency symbol
        if (labelText === fullText) {
          labelText = fullText
            .replace(/\s*[-–]\s*[\₹\$\€£¥][\d,. ]+$/, "")
            .trim();
        }
        if (!labelText) labelText = fullText; // final fallback

        var $chip = $(
          '<div class="variation-chip fbt-variation-chip" data-value="' +
            val +
            '">' +
            labelText +
            "</div>",
        );

        $chip.on("click", function () {
          $select.val(val).trigger("change"); // keeps plugin price logic in sync
          $chipsWrap.find(".fbt-variation-chip").removeClass("active");
          $(this).addClass("active");
        });

        $chipsWrap.append($chip);
      });

      // Activate the currently-selected option (or the first one)
      var currentVal = $select.val();
      var $active = currentVal
        ? $chipsWrap.find(
            '.fbt-variation-chip[data-value="' + currentVal + '"]',
          )
        : $();

      if (!$active.length) {
        $active = $chipsWrap.find(".fbt-variation-chip").first();
      }

      if ($active.length) {
        $active.addClass("active");
        // Sync select to the activated chip value and fire change so prices update
        var activatedVal = $active.data("value");
        if ($select.val() !== activatedVal) {
          $select.val(activatedVal).trigger("change");
        }
      }

      $container.append($chipsWrap);
    });
  }

  // Run immediately in case FBT markup is already in the DOM on page load
  convertFBTVariationsToChips();

  // Also re-run whenever the FBT plugin fires its own ready / update events
  $(document).on("fbt_ready fbt_updated", function () {
    convertFBTVariationsToChips();
  });

  // MutationObserver safety net — fires if the FBT section is injected into
  // the DOM after jQuery(document).ready() has already run (e.g. late AJAX).
  if (typeof MutationObserver !== "undefined") {
    var fbtObserver = new MutationObserver(function (mutations) {
      var needsConversion = false;
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType === 1) {
            // Check if the added node itself or any descendant is an FBT container
            if (
              $(node).hasClass("fbt-variation-select") ||
              $(node).find(".fbt-variation-select").length
            ) {
              needsConversion = true;
            }
          }
        });
      });
      if (needsConversion) {
        convertFBTVariationsToChips();
      }
    });

    fbtObserver.observe(document.body, { childList: true, subtree: true });
  }

  // WooCommerce Notices Functionality
  function initWCNotices() {
    const $notices = $(
      ".woocommerce-error, .woocommerce-message, .woocommerce-info, .custom-wc-notice",
    );

    if ($notices.length === 0) return;

    $notices.each(function () {
      const $notice = $(this);
      const $progressBar = $notice.find(".wc-notice-progress-bar");

      // Skip if already initialized or if it's already being closed
      if ($notice.data("notice-timer")) return;

      // Animate progress bar (shrink to 0) over 5 seconds
      if ($progressBar.length > 0) {
        $progressBar.animate(
          {
            width: "0%",
          },
          5000,
          "linear",
        );
      }

      // Auto close after 5 seconds
      const timer = setTimeout(function () {
        $notice.fadeOut(300, function () {
          $(this).remove();
        });
      }, 5000);

      $notice.data("notice-timer", timer);

      // Manual close button
      $notice.find(".woocommerce-notice-close").on("click", function (e) {
        e.preventDefault();
        clearTimeout($notice.data("notice-timer"));
        if ($progressBar.length > 0) {
          $progressBar.stop();
        }
        $notice.fadeOut(300, function () {
          $(this).remove();
        });
      });
    });
  }

  // Initialize notices on page load
  initWCNotices();

  // Listen for custom trigger to re-initialize notices (useful for AJAX updates)
  $(document.body).on("wc_notices_updated", function () {
    initWCNotices();
  });

  /**
   * -----------------------------
   * AJAX Cart (quantities/remove)
   * -----------------------------
   * Uses a full-screen loader overlay instead of interim notices
   * so the rest of the UI is blocked until the request finishes.
   */

  // Remember the cart URL so we can safely reload (e.g. when the cart becomes empty).
  var ahniraCartUrl =
    $(".woocommerce-cart-form").first().attr("action") || window.location.href;

  function ahniraShowCartLoader() {
    if ($("#ahnira-cart-loader-overlay").length) return;

    var loaderSvg =
      '<svg width="56" height="56" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">' +
      '<circle cx="25" cy="25" r="20" fill="none" stroke="#333" stroke-width="4" stroke-linecap="round" stroke-dasharray="31.4 31.4">' +
      '<animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite" />' +
      "</circle>" +
      "</svg>";

    var $overlay = $(
      '<div id="ahnira-cart-loader-overlay"><div class="ahnira-cart-loader-inner">' +
        loaderSvg +
        "</div></div>",
    );

    $overlay.css({
      position: "fixed",
      top: 0,
      left: 0,
      width: "100%",
      height: "100%",
      background: "rgba(255,255,255,0.7)",
      zIndex: 9999,
      display: "flex",
      alignItems: "center",
      justifyContent: "center",
      cursor: "wait",
    });

    $overlay.find(".ahnira-cart-loader-inner").css({
      padding: "16px",
      borderRadius: "50%",
      background: "rgba(255,255,255,0.9)",
      boxShadow: "0 0 20px rgba(0,0,0,0.1)",
    });

    $("body").append($overlay);
    // Optional: visually dim the page behind if more emphasis is needed.
    $("body").addClass("ahnira-cart-loading");
  }

  function ahniraHideCartLoader() {
    $("#ahnira-cart-loader-overlay").remove();
    $("body").removeClass("ahnira-cart-loading");
  }

  function ahniraRefreshCartFromHTML(response) {
    if (!response || typeof response !== "string") return;

    var $html = $("<div>").html(response);

    // If WooCommerce returned the empty-cart template, render it directly without reloading.
    var $empty = $html
      .find(
        ".woocommerce-empty-cart.cart-empty, .woocommerce-cart-empty, .cart-empty",
      )
      .first();
    if ($empty.length) {
      // Remove existing cart UI and show the empty-cart block from the response.
      $(".woocommerce-cart-form").remove();
      $(".demand-alert").remove();

      var $target = $(".cart-page-wrapper .container").first();
      if ($target.length) {
        $target.append($empty.clone(true));
      } else {
        $("body").append($empty.clone(true));
      }

      // Update notices at the default WooCommerce location, if any.
      var $noticesWrapperEmpty = $html
        .find(".woocommerce-notices-wrapper")
        .first();
      console.log("Notices Wrapper Empty: ", $noticesWrapperEmpty);
      if ($noticesWrapperEmpty.length) {
        var $currentNotices = $(".woocommerce-notices-wrapper").first();
        if ($currentNotices.length) {
          $currentNotices.replaceWith($noticesWrapperEmpty);
        } else {
          $(".cart-page-wrapper").first().before($noticesWrapperEmpty);
        }
        $(document.body).trigger("wc_notices_updated");
      }

      $(document.body).trigger("updated_wc_div");
      return;
    }

    var $newForm = $html.find(".woocommerce-cart-form").first();

    // Full-page HTML often isn't fully parsed when put in a div; try to get body content.
    if (!$newForm.length && response.indexOf("<body") !== -1) {
      var bodyContent = response
        .replace(/^[\s\S]*?<body[^>]*>/i, "")
        .replace(/<\/body>[\s\S]*$/i, "");
      $html = $("<div>").html(bodyContent);
      $newForm = $html.find(".woocommerce-cart-form").first();
    }

    if ($newForm.length) {
      $(".woocommerce-cart-form").first().replaceWith($newForm);
    } else {
      // Fallback: replace cart sections individually so the UI still updates.
      var $newItems = $html.find(".cart-items-container").first();
      if ($newItems.length) {
        $(".cart-items-container").first().replaceWith($newItems.clone(true));
      }
      var $newSidebar = $html.find(".cart-sidebar").first();
      if ($newSidebar.length) {
        $(".cart-sidebar").first().replaceWith($newSidebar.clone(true));
      }
      var $newCross = $html.find(".cross-sells-container").first();
      if ($newCross.length) {
        $(".cross-sells-container").first().replaceWith($newCross.clone(true));
      }
      var $newShipping = $html.find(".shipping-progress-wrapper").first();
      if ($newShipping.length) {
        var $current = $(".shipping-progress-wrapper").first();
        if ($current.length) {
          $current.replaceWith($newShipping.clone(true));
        } else {
          $(".cart-items-container").first().before($newShipping.clone(true));
        }
      } else {
        $(".shipping-progress-wrapper").remove();
      }
    }

    // Optimized WooCommerce notices handling
    var $noticesWrapper = $html.find(".woocommerce-notices-wrapper").first();
    console.log("Notices Wrapper: ", $noticesWrapper);
    var $currentNotices = $(".woocommerce-notices-wrapper").first();

    if ($noticesWrapper.length && $currentNotices.length) {
      $currentNotices.html($noticesWrapper.html());

      $(document.body).trigger("wc_notices_updated");
    } else if ($noticesWrapper.length) {
      $(".cart-page-wrapper").first().before($noticesWrapper);
      $(document.body).trigger("wc_notices_updated");
    }

    $(document.body).trigger("updated_wc_div");
  }

  function ahniraAjaxUpdateCart(extraData) {
    var $form = $(".woocommerce-cart-form");
    if (!$form.length) return;

    var data = $form.serializeArray();

    // Make sure WooCommerce processes quantities.
    data.push({
      name: "update_cart",
      value: "1",
    });

    if (extraData && extraData.length) {
      data = data.concat(extraData);
    }

    // Show full-screen loader instead of interim notices.
    ahniraShowCartLoader();

    $.ajax({
      url: $form.attr("action"),
      type: "POST",
      data: $.param(data),
      success: function (response) {
        ahniraRefreshCartFromHTML(response);
        ahniraHideCartLoader();
        // Refresh WooCommerce fragments so header cart count & mini cart stay in sync.
        $(document.body).trigger("wc_fragment_refresh");
      },
      error: function () {
        ahniraHideCartLoader();
        window.alert(
          "There was an error updating your cart. Please refresh the page and try again.",
        );
      },
    });
  }

  // Only bind cart AJAX handlers on cart page.
  if ($(".woocommerce-cart-form").length) {
    // Quantity change -> AJAX update.
    $(document).on("change", ".woocommerce-cart-form .qty", function () {
      ahniraAjaxUpdateCart();
    });

    // "Update cart" button (if used) -> AJAX instead of full reload.
    $(document).on(
      "click",
      ".woocommerce-cart-form .update-cart",
      function (e) {
        e.preventDefault();
        ahniraAjaxUpdateCart();
      },
    );

    // Remove item via AJAX: set row quantity to 0 then POST update (reliable, same response as qty change).
    $(document).on(
      "click",
      ".woocommerce-cart-form .remove-link",
      function (e) {
        e.preventDefault();

        var $row = $(this).closest(".cart_item, .cart-item-row");
        var $qty = $row.find("input.qty");
        if (!$qty.length) {
          // Fallback: use remove URL if no qty input (e.g. sold individually hidden input).
          var href = $(this).attr("href");
          if (href && href.indexOf("remove_item") !== -1) {
            ahniraShowCartLoader();
            $.ajax({
              url: href,
              type: "GET",
              success: function (response) {
                ahniraRefreshCartFromHTML(response);
                ahniraHideCartLoader();
                $(document.body).trigger("wc_fragment_refresh");
              },
              error: function () {
                ahniraHideCartLoader();
                window.alert(
                  "There was an error removing the item. Please refresh the page and try again.",
                );
              },
            });
          }
          return;
        }

        $qty.val(0);
        ahniraAjaxUpdateCart();
      },
    );

    // Wishlist: trigger TI WooCommerce Wishlist using product/variation data from the link.
    $(document).on(
      "click",
      ".woocommerce-cart-form .wishlist-link",
      function (e) {
        e.preventDefault();

        var $link = $(this);
        var productId = $link.attr("data-product-id");
        var variationId = $link.attr("data-variation-id") || 0;
        var productType = $link.attr("data-product-type") || "simple";
        var productName =
          $link.closest(".cart-item-row").find(".product-info h5 a").text() ||
          $link.closest(".cart-item-row").find(".product-info h5").text() ||
          "Product";

        if (!productId || typeof jQuery.fn.tinvwl_to_wishlist !== "function") {
          var productUrl = $link.attr("href");
          if (productUrl && productUrl !== "#") {
            window.location.href = productUrl;
          }
          return;
        }

        // Show global loader while wishlist request is in progress.
        ahniraShowCartLoader();

        // Listen once for TI Wishlist AJAX response to show toast and toggle icon.
        $(document.body)
          .off("tinvwl_wishlist_ajax_response.ahnira_cart")
          .one(
            "tinvwl_wishlist_ajax_response.ahnira_cart",
            function (event, el, response) {
              ahniraHideCartLoader();

              // Determine if the product was added or removed.
              var wasAdded = true;
              if (response && response.make_remove) {
                wasAdded = response.make_remove;
              } else {
                wasAdded = response.make_remove;
              }
              console.log(response);
              console.log("Clicked", wasAdded);
              if (wasAdded) {
                $link.find("i").removeClass("fa-regular").addClass("fa-solid");
                $link
                  .contents()
                  .filter(function () {
                    return this.nodeType === 3;
                  })
                  .last()
                  .replaceWith(" Wishlisted");
              } else {
                $link.find("i").removeClass("fa-solid").addClass("fa-regular");
                $link
                  .contents()
                  .filter(function () {
                    return this.nodeType === 3;
                  })
                  .last()
                  .replaceWith(" Wishlist");
              }
            },
          );

        // Build a fake TI Wishlist button and trigger click so the plugin's delegated handler runs.
        var productVariations =
          productType === "variation" && variationId ? [variationId] : [];
        var $fakeBtn = $(
          '<a role="button" tabindex="0" class="tinvwl_add_to_wishlist_button" ' +
            'data-tinv-wl-list="[]" data-tinv-wl-product="' +
            productId +
            '" data-tinv-wl-productvariation="' +
            variationId +
            '" data-tinv-wl-productvariations="' +
            JSON.stringify(productVariations) +
            '" data-tinv-wl-producttype="' +
            productType +
            '" data-tinv-wl-action="add">Add to Wishlist</a>',
        )
          .css({ position: "absolute", left: "-9999px", visibility: "hidden" })
          .appendTo("body");

        $fakeBtn.trigger("click");
        $fakeBtn.remove();
      },
    );
  }

  var ahniraWishlistUserClicked = false;

  $(document).on(
    "click",
    ".tinvwl_add_to_wishlist_button, .wishlist-link",
    function () {
      ahniraWishlistUserClicked = true;
    },
  );

  $(document.body).on(
    "tinvwl_wishlist_ajax_response",
    function (event, el, response) {
      // Skip toasts that fire during TI Wishlist's page-load sync.
      if (!ahniraWishlistUserClicked) return;
      ahniraWishlistUserClicked = false;
      // Suppress TI Wishlist's own modal popup (if it somehow still renders).
      setTimeout(function () {
        $("body > .tinv-wishlist .tinv-modal").remove();
      }, 50);

      // Determine add vs remove.
      var wasAdded = true;
      if (response && response.action == "remove") {
        wasAdded = !response.status;
      } else {
        wasAdded = response.status;
      }
      console.log(response);
      var message = wasAdded
        ? "Successfully added to your Wishlist!"
        : "Removed from your Wishlist.";
      var icon = wasAdded ? "fa-heart" : "fa-heart-crack";
      var toastType = wasAdded ? "success" : "removed";

      ahniraShowWishlistToast(message, icon, toastType);
    },
  );

  /**
   * Show a small toast notification that slides in from the left.
   * Auto-dismisses after 4 seconds with a progress bar.
   */
  function ahniraShowWishlistToast(message, icon, type) {
    // Create container if it doesn't exist.
    var $container = $("#ahnira-wishlist-toast-container");
    if (!$container.length) {
      $container = $('<div id="ahnira-wishlist-toast-container"></div>');
      $("body").append($container);
    }

    var typeClass =
      type === "success" ? "ahnira-toast--success" : "ahnira-toast--removed";

    var $toast = $(
      '<div class="ahnira-wishlist-toast ' +
        typeClass +
        '" role="alert">' +
        '<div class="ahnira-toast__icon">' +
        '<i class="fa-solid ' +
        icon +
        '"></i>' +
        "</div>" +
        '<div class="ahnira-toast__body">' +
        '<span class="ahnira-toast__message">' +
        message +
        "</span>" +
        "</div>" +
        '<button class="ahnira-toast__close" aria-label="Close">' +
        '<i class="fa-solid fa-xmark"></i>' +
        "</button>" +
        '<div class="ahnira-toast__progress"></div>' +
        "</div>",
    );

    $container.append($toast);

    // Trigger slide-in after a frame so the CSS transition fires.
    requestAnimationFrame(function () {
      $toast.addClass("ahnira-toast--visible");
    });

    // Auto-dismiss after 4s.
    var autoClose = setTimeout(function () {
      dismissToast($toast);
    }, 4000);

    // Manual close.
    $toast.find(".ahnira-toast__close").on("click", function () {
      clearTimeout(autoClose);
      dismissToast($toast);
    });
  }

  function dismissToast($toast) {
    $toast.removeClass("ahnira-toast--visible");
    setTimeout(function () {
      $toast.remove();
    }, 400); // matches CSS transition duration
  }
});
