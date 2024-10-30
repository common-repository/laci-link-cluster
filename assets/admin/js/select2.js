
const wpIL = {
    addSelect2() {
        jQuery('.laci-category-for-post').select2({
            width: '100%',
            minimumResultsForSearch: -1,
            placeholder: 'Select a category',
        });

        // jQuery('.laci-search-input-control').select2({
        //     width: '100%',
        //     tags: true,
        //     options: [
        //     ],
        // });
    },    
}

jQuery(document).ready(function($) {
   wpIL.addSelect2($);
});