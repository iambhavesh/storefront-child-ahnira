(function ($) {
  "use strict";

  $(document).ready(function () {
    // Standard WooCommerce fragment update handler
    $(document.body).on("removed_coupon", function () {
      $(document.body).trigger("update_cart");
    });

    // Toggle View Available Coupons
    $(document).on("click", ".ahnira-cart-view-coupons-link", function (e) {
      e.preventDefault();
      const $link = $(this);
      const $list = $link.siblings(".ahnira-cart-coupons-list");
      $link.toggleClass("active");
      $list.slideToggle(300);
      $link.find(".toggle-arrow").text($link.hasClass("active") ? "▲" : "▼");
    });

    // Apply Coupon via AJAX
    $(document).on("click", ".ahnira-cart-apply-coupon", function (e) {
      e.preventDefault();
      const $btn = $(this);
      const couponCode = $(".ahnira-cart-coupon-input").val();
      console.log("Coupon Code: ", couponCode);

      if (!couponCode) {
        showNotice("Please enter a coupon code.", "error");
        return;
      }

      applyCoupon(couponCode, $btn);
    });

    // Apply Coupon from list
    $(document).on("click", ".ahnira-cart-coupon-apply-btn", function (e) {
      e.preventDefault();
      const $btn = $(this);
      const couponCode = $btn.closest(".ahnira-cart-coupon-item").data("code");
      applyCoupon(couponCode, $btn);
    });

    // Remove Coupon via AJAX
    $(document).on("click", ".ahnira-cart-remove-coupon", function (e) {
      e.preventDefault();
      const $btn = $(this);
      const couponCode = $btn.data("code");

      if (!couponCode) return;

      removeCoupon(couponCode, $btn);
    });

    function applyCoupon(couponCode, $btn) {
      $btn.addClass("loading").prop("disabled", true);
      $(".ahnira-cart-coupon-section").addClass("opacity-50");
      $.ajax({
        url: ahnira_cart_params.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "ahnira_apply_coupon",
          coupon_code: couponCode,
          nonce: ahnira_cart_params.nonce,
        },
        success: function (response) {
          console.log("Response: ", response);
          if (response.fragments) {
            updateFragments(response.fragments);
            $(document.body).trigger("wc_fragments_refreshed");
            $(document.body).trigger("applied_coupon", [couponCode]);
            $(document.body).trigger("wc_notices_updated");
          }
          // if (response.data) {
          //   console.log("Inside if");
          //   $(".woocommerce-notices-wrapper").html(response.data.notices);
          //   $(document.body).trigger("applied_coupon", [couponCode]);
          //   $(document.body).trigger("wc_notices_updated");
          // }
        },
        error: function (error) {
          console.error("Coupon AJAX error:", error);
          showNotice(
            "An error occurred while processing the coupon. Please try again.",
            "error",
          );
        },
        complete: function () {
          $btn.removeClass("loading").prop("disabled", false);
          $(".ahnira-cart-coupon-section").removeClass("opacity-50");
        },
      });
    }

    function removeCoupon(couponCode, $btn) {
      $btn.addClass("loading").prop("disabled", true);
      $(".ahnira-cart-coupon-section").addClass("opacity-50");

      $.ajax({
        url: ahnira_cart_params.ajax_url,
        type: "POST",
        dataType: "json",
        data: {
          action: "ahnira_remove_coupon",
          coupon_code: couponCode,
          nonce: ahnira_cart_params.nonce,
        },
        success: function (response) {
          if (response && response.fragments) {
            updateFragments(response.fragments);
            $(document.body).trigger("wc_fragments_refreshed");
            $(document.body).trigger("removed_coupon", [couponCode]);
            $(document.body).trigger("wc_notices_updated");
          } else if (
            response &&
            response.success &&
            response.data &&
            response.data.notices
          ) {
            $(".woocommerce-notices-wrapper")
              .first()
              .replaceWith(response.data.notices);
            $(document.body).trigger("removed_coupon", [couponCode]);
            $(document.body).trigger("wc_notices_updated");
          } else if (response && response.notices) {
            $(".woocommerce-notices-wrapper")
              .first()
              .replaceWith(response.notices);
            $(document.body).trigger("removed_coupon", [couponCode]);
            $(document.body).trigger("wc_notices_updated");
          }
        },
        complete: function () {
          $btn.removeClass("loading").prop("disabled", false);
          $(".ahnira-cart-coupon-section").removeClass("opacity-50");
        },
      });
    }

    function updateFragments(fragments) {
      $.each(fragments, function (key, value) {
        $(key).replaceWith(value);
      });
    }

    // No custom notice container; WooCommerce outputs notices at the default top-of-page location,
    // and our global script.js handles auto-hide via the wc_notices_updated event.
    function showNotice(message, type) {
      let noticeHtml = "";

      if (type === "error") {
        noticeHtml = `
                <ul class="woocommerce-error custom-wc-notice notice-error" role="alert">
                    <li>
                        <div class="wc-notice-inner">
                            <i class="fa-solid fa-circle-xmark wc-notice-icon"></i>
                            <div class="woocommerce-notice-content">${message}</div>
                            <button class="woocommerce-notice-close" aria-label="Close">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <div class="wc-notice-progress-bar"></div>
                        </div>
                    </li>
                </ul>`;
      } else {
        const noticeClass =
          type === "success" ? "woocommerce-message" : "woocommerce-info";
        const iconClass =
          type === "success" ? "fa-circle-check" : "fa-circle-info";
        const noticeTypeClass =
          type === "success" ? "notice-success" : "notice-info";

        noticeHtml = `
                <div class="${noticeClass} custom-wc-notice ${noticeTypeClass}" role="alert">
                    <div class="wc-notice-inner">
                        <i class="fa-solid ${iconClass} wc-notice-icon"></i>
                        <div class="woocommerce-notice-content">${message}</div>
                        <button class="woocommerce-notice-close" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <div class="wc-notice-progress-bar"></div>
                    </div>
                </div>`;
      }

      let $wrapper = $(".woocommerce-notices-wrapper").first();
      if (!$wrapper.length) {
        $wrapper = $('<div class="woocommerce-notices-wrapper"></div>');
        $(".cart-page-wrapper").first().before($wrapper);
      }

      $wrapper.append(noticeHtml);
      $(document.body).trigger("wc_notices_updated");
    }
  });

  // Original UI logic
  document.addEventListener("DOMContentLoaded", function () {
    var tdElement = document.querySelector('td[data-title^="Coupon:"]');
    if (tdElement) {
      var couponValue = window.getComputedStyle(tdElement, "::before").content;
      couponValue = couponValue.replace(/^"|"$/g, "");
      var updatedCouponValue = couponValue.split(": ")[1].toUpperCase();
      tdElement.style.setProperty("--coupon-value", '""');
      tdElement.setAttribute("data-title", "Coupon: " + updatedCouponValue);
    }
  });

  function startTimer(duration, display) {
    let timer = duration;
    function updateDisplay() {
      let minutes = parseInt(timer / 60, 10);
      let seconds = parseInt(timer % 60, 10);
      minutes = minutes < 10 ? "0" + minutes : minutes;
      seconds = seconds < 10 ? "0" + seconds : seconds;
      display.textContent = minutes + "m " + seconds + "s";
      if (--timer < 0) {
        timer = duration;
      }
    }
    updateDisplay();
    return setInterval(updateDisplay, 1000);
  }

  let display = document.querySelector("#timer");
  function updateTimer() {
    if (
      typeof wc_cart_params !== "undefined" &&
      wc_cart_params.cart_contents_count > 0
    ) {
      let fiveMinutes = 60 * 5;
      return startTimer(fiveMinutes, display);
    }
  }

  let timerInterval = updateTimer();
  $(document.body).on("updated_cart_totals", function () {
    clearInterval(timerInterval);
    timerInterval = updateTimer();
  });

})(jQuery);
