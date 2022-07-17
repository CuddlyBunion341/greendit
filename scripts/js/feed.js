$('#feed').click(function(e) {
    const target = e.target;
    const parent = target.parentNode;
    const name = target.classList;
    // if (target.closest('.comment')) {
    // }
    if (target.closest('.post')) {
        const post_id = target.closest('.post').dataset.id;
        if (parent.classList == 'upvote') {
            $.post('request/upvote.php',{post_id},(response,status) => {
                if (status == 401) {
                    alert('Please login!');
                } else {
                    response = JSON.parse(response);
                    if (response.error) console.error(response.message);
                    const increment = response.increment;
                    target.src = `resources/upvote${increment > 0 ? '_full' : ''}.svg`;
                    const likeCount = parent.parentNode.querySelector('.like-count');
                    likeCount.innerHTML = Number(likeCount.innerHTML) + increment;
                }
            });
        }
        if (name == 'comment-btn') {
            alert()
        }
        if (name == 'share-btn') {
    
        }
    }
});