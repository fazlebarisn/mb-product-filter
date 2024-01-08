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
})(jQuery);
