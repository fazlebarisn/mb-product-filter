(function ($) {
  $(document).ready(function () {

    // Define a function to handle the click event
    function toggleChild(parent, child) {
      var parentElement = $(`li#${parent}`);
      var childElement = $(`.mb-child-category-${child}`);
      childElement.hide();

      parentElement.on("click", function () {
        childElement.toggle();
      });
    }

    // Call the function for each pair of parent and child
    toggleChild("mb-parrent-cat-accessory-type", "accessory-type");
    toggleChild("mb-parrent-cat-application", "application");
    toggleChild("mb-parrent-cat-by-category", "by-category");
    toggleChild("mb-parrent-cat-color", "color");
    toggleChild("mb-parrent-cat-color-types", "color-types");
    toggleChild("mb-parrent-cat-hair-needs", "hair-needs");
    toggleChild("mb-parrent-cat-hair-product-type", "hair-product-type");
    toggleChild("mb-parrent-cat-hair-type", "hair-type");
    toggleChild("mb-parrent-cat-lash-color", "lash-color");
    toggleChild("mb-parrent-cat-lash-type", "lash-type");
    toggleChild("mb-parrent-cat-length", "length");
    toggleChild("mb-parrent-cat-nail-color", "nail-color");
    toggleChild("mb-parrent-cat-nail-finish", "nail-finish");
    toggleChild("mb-parrent-cat-price", "price");
    toggleChild("mb-parrent-cat-recipient", "recipient");
    toggleChild("mb-parrent-cat-size", "size");
    toggleChild("mb-parrent-cat-skin-concerns", "skin-concerns");
    toggleChild("mb-parrent-cat-skin-product-type", "skin-product-type");
    toggleChild("mb-parrent-cat-skin-type", "skin-type");
  });

  $('input[name="mb_filter[]"]').change(function () {
    updateSelectedTerms();
});

$('.mb-filter-values').on('click', '.selected-term', function () {
    var termName = $(this).text();
    uncheckCheckbox(termName);
    $(this).remove(); // Remove the clicked span
});

$('.mb-reset-button').click(function () {
    resetCheckboxes();
    $('.mb-filter-values').empty(); // Clear the displayed terms
});

function updateSelectedTerms() {
    var selectedTerms = [];
    $('input[name="mb_filter[]"]:checked').each(function () {
        var selectedTermName = $(this).data('name');
        selectedTerms.push(selectedTermName);
    });

    $('.mb-filter-values').empty();
    selectedTerms.forEach(function (selectedTerm) {
        $('<span title="Remove" class="selected-term">' + selectedTerm + '</span>').appendTo('.mb-filter-values');
    });
}

function uncheckCheckbox(termName) {
    $('input[name="mb_filter[]"]').filter(function () {
        return $(this).data('name') === termName;
    }).prop('checked', false);
}

function resetCheckboxes() {
    $('input[name="mb_filter[]"]').prop('checked', false);
}

// Initial update on page load
updateSelectedTerms();

//   $('#mb-product-filter-form_xx').submit(function(event) {
//     // Prevent the default form submission
//     event.preventDefault();

//     var formData = $(this).serialize();

//     // Perform Ajax request
//     $.ajax({
//         type: 'GET',
//         url: ajaxurl,
//         data: {
//             action: 'mb_product_filter_handler',
//             formData: formData,
//         },
//         success: function(response) {
//           // console.log(response);
//             $('section.mb-archive-page-wrapper ul.products.columns-4').html(response);
//         },
//         error: function(error) {
//           $('section.mb-archive-page-wrapper ul.products.columns-4').html(error);
//         }
//     });
// });

})(jQuery);
