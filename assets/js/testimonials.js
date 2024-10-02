(function ($) {
  $(document).ready(function () {
    // Lead testimonials on page load
    loadTestimonials();
    /**
     * Create new testimonial
     */
    $("#testimonial_form").submit(function (event) {
      event.preventDefault();

      // Get testimonial data
      const form = $(this);
      let nonce = $(this).find("#testimonial_form_nonce").val();
      let testimonialContent = $(this).find("#testimonial_content").val();
      let testimonialName = $(this).find("#testimonial_name").val();
      let testimonialOther = $(this).find("#testimonial_other").val();
      let result = $("#result");

      const data = {
        testimonial_content: testimonialContent,
        testimonial_name: testimonialName,
        testimonial_other: testimonialOther,
      };

      $.ajax({
        type: "POST",
        url: myAjax.ajax_url,
        data: {
          action: "create_testimonial",
          nonce: nonce,
          data: data,
        },
        beforeSend: function () {
          result.text("Adding new testimonial...");
        },
        success: function (response) {
          result.text(response.data);
          setTimeout(function() {result.text(''); form[0].reset();}, 3000);
          loadTestimonials();
        },
        error: function (xhr, status, error) {
          result.text(error);
        },
      });
    });
    
    $(document).on('click', '#testimonial_clear', function(event) {
      event.preventDefault();
      let result = $("#result");
      $.ajax({
        type: 'POST',
        url: myAjax.ajax_url,
        data: {
          action: 'clear_testimonial_transient',
        },
        beforeSend: function() {
          result.text('Clearing cache...');
        },
        success: function(response) {
          result.text(response.data);
          setTimeout(function() {result.text(''); form[0].reset();}, 3000);
          loadTestimonials();
        },
        error: function(xhr, status, error) {
          result.text(error);
        }
      })
    });

    /**
     * Load testimonials function with pagination
     */
    function loadTestimonials(page = 1) {
      const testimonialList = $("#testimonials-list");
      const spinner = '<img src="'+ myAjax.spinner +'">';
      $.ajax({
        type: "POST",
        url: myAjax.ajax_url,
        data: {
          action: "load_testimonials",
          page: page,
        },
        beforeSend: function () {
          testimonialList.html(spinner)
        },
        success: function (response) {
          testimonialList.html(response.data);
        },
        error: function (xhr, status, error) {
          testimonialList.html(
            '<span class="text-danger">' + error + "</span>"
          );
        },
      });
    }

    /**
     * Delete testimonial
     */
    function deleteTestimonial(id, parent){
      const spinner = '<img src="'+ myAjax.spinner +'"> Deleting...';
      const message = $('#message');
      $.ajax({
        type: "POST",
        url: myAjax.ajax_url,
        data: {
          action: "delete_testimonial",
          id: id
        },
        beforeSend:function () {
          message.text('');
          parent.html(spinner);
        },
        success: function (response) {
          message.text(response.data);
          setTimeout(function(){message.text('')}, 3000);
          loadTestimonials();
        },
        error: function (xhr, status, error) {
          message.addClass('text-danger').text(error)
        }
      })
    }

    /**
     * Load paginated testimonials
     */
    $(document).on('click', '.page-link', function() {
      let page = $(this).data('page');
      loadTestimonials(page);
    });

    /**
     * trigger delete testimonial
     */
    $(document).on('click', '.cross', function() {
      let id = $(this).data('post_id');
      let parent = $(this).parent();
      deleteTestimonial(id, parent);
    });
  }); // document.ready
})(jQuery);
