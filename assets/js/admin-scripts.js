/**
 * Admin JavaScript for Runthings Current Year Shortcode
 */
(function ($) {
  "use strict";

  /**
   * Opens the help tab when clicking the usage examples link
   */
  window.runthingsCYSOpenHelpTab = function () {
    // Scroll to top
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      200
    );

    // Open help panel if not already open
    if (!$("#contextual-help-wrap").is(":visible")) {
      $("#contextual-help-link").trigger("click");
    }

    // Small delay to ensure panel is open before selecting tab
    setTimeout(function () {
      $("#tab-link-runthings-year-shortcode-help a").trigger("click");
    }, 100);

    return false;
  };
})(jQuery);
