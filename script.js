(function ($) {
  $(document).ready(function () {
    /**
     * Autoplay video on page load
     */
    $(".autoplay video").each(function () {
      $(this).get(0).play();
    });

    /**
     * Open WP media on site setting form
     */
    $("#open_media_gallery").on("click", function (e) {
      e.preventDefault();

      // Create a new media frame
      let mediaUploader = wp.media({
        title: "Select Image",
        button: {
          text: "Upload",
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
        console.log(attachment);

        // Update the input field with the selected image URL
        $("#client_logo").val(imageUrl);
        $("#img_name").text(attachment.filename);
      });

      mediaUploader.open();
    }); // wp.media

    /**
     * Send site setting form AJAX
     */
    $("#site_editing_form").submit(function (event) {
      event.preventDefault();
      // Get form data
      let nonce = $(this).find("#client_editing_nonce").val();
      let clientLogo = $(this).find("#client_logo").val();
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
          result.html("Please wait...");
        },
        success: function (response) {
          result.text(response.data);
        },
        error: function (response) {
          result.text(response.data);
        },
      });
    }); // form submit
  }); // doc.ready
})(jQuery);
