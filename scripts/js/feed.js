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
                    target.src = `resources/upvote${increment > 0 ? '_full' : ''}.svg`;
                    const likeCount = parent.parentNode.querySelector('.like-count');
                    likeCount.innerHTML = Number(likeCount.innerHTML) + increment * (upvote ? 1 : -1);
                    const other = parent.parentNode.querySelector(`.${upvote ? 'downvote' : 'upvote'} img`);
                    other.src = `resources/upvote.svg`;
                }
            });
        }
        if (name == 'comment-btn') {
        }
        if (name == 'share-btn') {
        }
    }
});