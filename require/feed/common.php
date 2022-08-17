<?php
function arrow_wrapper($liked = false, $disliked = false, $count = 0, $horizontal = false, $disabled = false) {
    return '
        <section class="arrow-wrapper' . activeClass($horizontal, 'horizontal') . '">
            <button aria-label="upvote" name="upvote-btn" name="upvote" class="upvote' . activeClass($liked) . '" ' . activeClass($disabled, 'disabled') . '>
                ' . file_get_contents(__DIR__ . '/../resources/upvote.svg') . '
            </button>
            <span class="like-count">
                ' . $count . '
            </span>
            <button aria-label="downvote" name="downvote-btn" name="downvote" class="downvote' . activeClass($disliked) . '" ' . activeClass($disabled, 'disabled') . '>
                ' . file_get_contents(__DIR__ . '/../resources/upvote.svg') . '
            </button>
        </section>
        ';
}