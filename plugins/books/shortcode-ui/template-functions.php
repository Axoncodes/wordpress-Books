<!-- template-functions.php -->
<?php
function de_book_block(
    $title,
    $thumbnail_url,
    $category,
    $publish_year,
    $excerpt_context,
    $author_name,
    $link,
) {
    $thumbnail_block = '';
    if (isset($thumbnail_url)) {
        $thumbnail_block = "<div class=\"thumbnail-cover\">
            <img src=\"$thumbnail_url\" />
        </div>";
    }
    $publish_year = tag_book_block($publish_year);
    $cat = tag_book_block($category);
    $author = tag_book_block($author_name);
    $title = "<h2>$title</h2>";
    $excerpt = "<p>$excerpt_context</p>";
    
    $block = "<article class=\"de-book-block\">
        <a href=\"$link\">
            <nav>
                $author
                $publish_year
                $cat
            </nav>
            $thumbnail_block
            $title
        </a>
        $excerpt
    </article>";

    return $block;
}

function tag_book_block($text = "") {
    return "<div class=\"tag\">
        <span>$text</span>
    </div>";
}