<?php
function arrowWrapperHTML($liked = false, $disliked = false, $count = 0, $horizontal = false, $disabled = false) {
    return '
        <section class="arrow-wrapper' . active($horizontal, 'horizontal') . '">
            <button aria-label="upvote" name="upvote-btn" name="upvote" class="upvote' . active($liked) . '" ' . active($disabled, 'disabled') . '>
                ' . icon('upvote') . '
            </button>
            <span class="like-count">
                ' . $count . '
            </span>
            <button aria-label="downvote" name="downvote-btn" name="downvote" class="downvote' . active($disliked) . '" ' . active($disabled, 'disabled') . '>
                ' . icon('upvote') . '
            </button>
        </section>
        ';
}