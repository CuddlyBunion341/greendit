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
	const upvote = button.getAttribute("name") == "upvote-btn";
	const data = { upvote };
	data[isPost ? "post" : "comment"] = hash;
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
			if (increment > 0) button.classList.add("active");
			else button.classList.remove("active");
			const likeCount = container.querySelector(".like-count");
			likeCount.innerHTML =
				Number(likeCount.innerHTML) + increment * (upvote ? 1 : -1);
			const other = container.querySelector(
				`.${upvote ? "downvote" : "upvote"}`
			);
			other.classList.remove("active");
		}
	);
}
$("#feed").click(function (e) {
	const target = e.target;
	const name = target.getAttribute("name");
	if (target.closest(".post")) {
		const hash = target.closest(".post").dataset.hash;
		if (name == "upvote-btn" || name == "downvote-btn") {
			return upvote(target);
		}
		if (name == "comment-btn") {
			window.location.href = `subs/main/posts/${hash}/`;
		}
		if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/main/posts/${hash}/`
				)
				.then(() => alert("post url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		}
	} else if (target.closest(".comment-wrapper")) {
		const wrapper = target.closest(".comment-wrapper");
		const post = target.closest(".comment-wrapper").dataset.hash;
		const comment = target.closest(".comment");
		if (target.closest(".create-comment")) {
			const composer = target.closest(".create-comment");
			const input = composer.querySelector(".comment-content");
			const content = input.value;
			const error = composer.querySelector(".error");
			if (name == "comment-btn") {
				$.post(
					`request/create_comment.php?t=${Math.random()}`,
					{ post, content },
					(response, status) => {
						if (status == 200) {
							console.log(response);
							const comment = $.createElementFromHTML(response);
							wrapper.appendChild(comment);
							input.value = "";
						}
						if (!status == 200) return console.error(response);
					}
				);
			}
		}
		if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/main/posts/${post}/comment/${comment.dataset.hash}`
				)
				.then(() => alert("comment url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		}
		if (name == "upvote-btn" || name == "downvote-btn") {
			upvote(target);
		}
	}
});

$('.create-comment').on('submit', (e) => {
	e.preventDefault();
	console.log("WORLD!!");
})