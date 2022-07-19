$('#feed').click(function(e) {
    const target = e.target;
    const parent = target.parentNode;
    const name = target.classList;
    if (target.closest('.post')) {
        const post_id = target.closest('.post').dataset.id;
        if (parent.classList == 'upvote' || parent.classList == 'downvote') {
            const upvote = parent.classList == 'upvote';
            $.post('request/upvote.php?t=' + Math.random(),{post_id,upvote},(response,status) => {
                if (status == 401) {
                    alert('Please login!');
                } else {
                    response = JSON.parse(response);
                    const {increment,message,error} = response;
                    if (error) console.error(message);
                    target.src = `resources/${upvote ? 'upvote' : 'downvote'}${increment > 0 ? '_full' : ''}.svg`;
                    const likeCount = parent.parentNode.querySelector('.like-count');
                    likeCount.innerHTML = Number(likeCount.innerHTML) + increment * (upvote ? 1 : -1);
                    const other = parent.parentNode.querySelector(`.${upvote ? 'downvote' : 'upvote'} img`);
                    other.src = `resources/${upvote ? 'downvote' : 'upvote'}.svg`;
                }
            });
        }
        if (name == 'comment-btn') {
            $.get(`request/post_comments.php?post_id=${post_id}`,(response,status) => {
                if ($(`[data-post-id="${post_id}"]`)) {
                    $(`[data-post-id="${post_id}"]`).toggle();
                    return;
                }
                const commentWrapper = $.createElement('div',{
                    class:'comment-wrapper',
                    "data-post-id":post_id
                });
                $(commentWrapper).html(response);
                const post = target.closest('.post');
                post.parentNode.insertBefore(commentWrapper, post.nextSibling);
            });

        }
        if (name == 'share-btn') {
        }
    } else if (target.closest('.comment-wrapper')) {
        const post_id = target.closest('.comment-wrapper').dataset.postId;
        if (target.closest('.create-comment')) {
            const textarea = target.closest('.create-comment').querySelector('.comment-content') 
            const content = textarea.value;
            if (name == 'comment-btn') {
                $.post(`request/create_comment.php?t=${Math.random()}`, {post_id,content}, (response,status) => {
                    if (!status == 200) return console.error(response);
                    const comment = $.createElementFromHTML(response);
                    const commentWrapper = target.closest('.comment-wrapper');
                    commentWrapper.appendChild(comment);
                    textarea.value = '';
                });
            }
        }
    }
});