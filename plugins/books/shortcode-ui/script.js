jQuery(document).ready(function($) {
    $('.books-shortcode-container').each(function() {
        var $booksContainer = $(this);
        var $categoryFilter = $booksContainer.find('.category-filter');
        var page = 1;
        var loading = false;

        function loadBooks() {
            if (loading) return;

            loading = true;

            var category = $categoryFilter.val();

            $.ajax({
                type: 'GET',
                url: ajaxpagination.ajaxurl,
                data: {
                    action: 'load_books',
                    page: page,
                    category: category,
                },
                success: function(response) {
                    $booksContainer.find('.books-container').append(response);
                    page++;
                    loading = false;
                },
            });
        }

        loadBooks();

        $categoryFilter.on('change', function() {
            page = 1;
            $booksContainer.find('.books-container').empty();
            loadBooks();
        });
    });
});
