/**
 * Up / Downvotes a post or comment
 * @param {HTMLElement} button - the button clicked
 */
function upvote (button) {
    var isPost, container;
    if (container = button.closest('.post')) {
        isPost = true;
    } else {
        container = button.closest('.comment');
        isPost = false;
    }
	const hash = container.dataset.hash;
	const upvote = button.classList == "upvote";
	const data =
		isPost ? { post: hash, upvote } : { comment: hash, upvote };
	$.post(
		"request/upvote.php?t=" + Math.random(),
		data,
		(response, status) => {
			if (status == 401) {
				return alert("Please login!");
			}
			response = JSON.parse(response);
			const { increment, message, error } = response;
			if (error) console.error(message);
			button.querySelector("img").src = `resources/${
				upvote ? "upvote" : "downvote"
			}${increment > 0 ? "_full" : ""}.svg`;
			const likeCount = container.querySelector(".like-count");
			likeCount.innerHTML =
				Number(likeCount.innerHTML) + increment * (upvote ? 1 : -1);
			const other = container.querySelector(
				`.${upvote ? "downvote" : "upvote"} img`
			);
			other.src = `resources/${upvote ? "downvote" : "upvote"}.svg`;
		}
	);
};
$("#feed").click(function (e) {
	const target = e.target;
	const parent = target.parentNode;
	const name = target.classList;
	if (target.closest(".post")) {
		const post_hash = target.closest(".post").dataset.hash;
		if (parent.classList == "upvote" || parent.classList == "downvote") {
            return upvote(parent);
		}
		if (name == "comment-btn") {
			window.location.href = `subs/main/posts/${post_hash}/`;
		}
		if (name == "share-btn") {
			alert('post url copied to clipboard!');
		}
	} else if (target.closest(".comment-wrapper")) {
		const post_hash = target.closest(".comment-wrapper").dataset.postId;
		if (target.closest(".create-comment")) {
			const textarea = target
				.closest(".create-comment")
				.querySelector(".comment-content");
			const content = textarea.value;
			if (name == "comment-btn") {
				$.post(
					`request/create_comment.php?t=${Math.random()}`,
					{ post_hash, content },
					(response, status) => {
						if (!status == 200) return console.error(response);
						const comment = $.createElementFromHTML(response);
						const commentWrapper =
							target.closest(".comment-wrapper");
						commentWrapper.appendChild(comment);
						textarea.value = "";
					}
				);
			}
		}
        if (parent.classList == "upvote" || parent.classList == "downvote") {
            upvote(parent);
        }
	}
});
