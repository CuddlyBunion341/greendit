/**
 * Up / Downvotes a post or comment
 * @param {HTMLElement} button - the button clicked
 */
function upvote(button) {
	var isPost, container;
	if ((container = button.closest(".post"))) {
		isPost = true;
	} else {
		container = button.closest(".comment");
		isPost = false;
	}
	const hash = container.dataset.hash;
	const upvote = button.classList == "upvote";
	const data = isPost ? { post: hash, upvote } : { comment: hash, upvote };
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
}
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
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/main/posts/${post_hash}/`
				)
				.then(() => alert("post url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		}
	} else if (target.closest(".comment-wrapper")) {
		const wrapper = target.closest(".comment-wrapper");
		const post = target.closest(".comment-wrapper").dataset.hash;
		if (target.closest(".create-comment")) {
			const composer = target.closest(".create-comment");
			const textarea = composer.querySelector(".comment-content");
			const content = textarea.value;
			const error = composer.querySelector(".error");
			if (name == "comment-btn") {
				$.post(
					`request/create_comment.php?t=${Math.random()}`,
					{ post, content },
					(response, status) => {
						console.log(status);
						if (status == 400)
							error.innerHTML = "Comment must not be empty";
						if (status == 200) {
							const comment = $.createElementFromHTML(response);
							wrapper.appendChild(comment);
							textarea.value = "";
							error.innerHTML = "";
						}
						if (!status == 200) return console.error(response);
					}
				);
			}
		}
		if (parent.classList == "upvote" || parent.classList == "downvote") {
			upvote(parent);
		}
	}
});
