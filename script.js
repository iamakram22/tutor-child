(function ($) {
  $(document).ready(function () {
    /**
     * Autoplay video on page load
     */
    $(".autoplay video").each(function () {
      $(this).get(0).play();
    });

    /**
     * Open WP media gallery and handle the selected image
     *
     * @param {string} triggerSelector - The selector for the element that triggers the media gallery.
     * @param {string} inputSelector - The selector for the input field to update with the image URL.
     * @param {string} displaySelector - The selector for the element to display the image name.
     * @param {string} title - The title for the media uploader.
     * @param {string} buttonText - The text for the upload button.
     */
    function openMediaGallery(triggerSelector, inputSelector, displaySelector, title = "Select Image", buttonText = "Upload") {
      $(triggerSelector).on("click", function (e) {
        e.preventDefault();

        // Create a new media frame
        let mediaUploader = wp.media({
          title: title,
          button: {
            text: buttonText,
          },
          multiple: false,
          library: { type: "image" },
        });

        // Open media uploader
        mediaUploader.on("select", function () {
          let attachment = mediaUploader
            .state()
            .get("selection")
            .first()
            .toJSON();
          let imageUrl = attachment.url;

          // Update the input field with the selected image URL
          $(inputSelector).val(imageUrl);
          $(displaySelector).text(attachment.filename);
        });

        mediaUploader.open();
      });
    }

    // Open media selector on client logo
    openMediaGallery(
      "#open_media_gallery",
      "#client_logo",
      "#img_name",
    );

    /**
     * Send site setting form AJAX
     */
    $("#site_editing_form").submit(function (event) {
      event.preventDefault();
      // Get form data
      let nonce = $(this).find("#client_editing_nonce").val();
      let clientLogo = $(this).find("#remove_image").is(":checked")
        ? ""
        : $(this).find("#client_logo").val();
      let clientName = $(this).find("#client_name").val();
      let clientPhone = $(this).find("#client_phone").val();
      let clientEmail = $(this).find("#client_email").val();
      let clientAddress = $(this).find("#client_address").val();
      let clientDesc = $(this).find("#client_desc").val();
      let result = $("#result");

      const data = {
        client_logo: clientLogo,
        client_name: clientName,
        client_phone: clientPhone,
        client_email: clientEmail,
        client_address: clientAddress,
        client_desc: clientDesc,
      };

      // Send AJAX request
      $.ajax({
        type: "POST",
        url: myAjax.ajax_url,
        data: {
          action: "client_editing_website",
          nonce: nonce,
          data: data,
        },
        beforeSend: function () {
          result.text("Please wait...");
        },
        success: function (response) {
          result.text(response.data);
        },
        error: function (response) {
          result.text(response.data);
        },
      });
    }); // form submit

    // Append testimonial on home page
    if ($('body').hasClass('home')) {
      const spinner = '<div id="testimonialLoader" class="d-flex justify-content-center mb-2"><img src="'+ myAjax.spinner +'"></div>';
      $.ajax({
        type: 'POST',
        url: myAjax.ajax_url,
        data: {
          action: 'load_franchise_testimonials'
        },
        beforeSend: function() {
          $('footer').before(spinner);
        },
        success: function(response) {
          if (response.success) {
            $('#testimonialLoader').remove();
            $('footer').before(response.data);
          } else {
            console.error('error', response.data);
          }
        },
        error: function(xhr, status, error) {
          $('#testimonialLoader').remove();
          console.error('Error: ' + error);
        }
      });
    }

    // Course list color
    const bgColors = ["#68b9d8", "#f07f1a", "#b1c642", "#ff5d52", "#fedc09"];
    const courseList = $("#course_list .tutor-card-body");
    let startColor = 0;
    courseList.each(function () {
      $(this).css({ "background-color": bgColors[startColor] + "40" });
      startColor = (startColor + 1) % bgColors.length;
    });

  }); // doc.ready
})(jQuery);
