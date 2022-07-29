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
/**
 * Saves / Unsaves a post or comment
 * @param {HTMLElement} button 
 */
function save(button) {
	var isPost, container;
	if ((container = button.closest(".post"))) {
		isPost = true;
	} else {
		container = button.closest(".comment");
		isPost = false;
	}
	const hash = container.dataset.hash;
	const data = {};
	data[isPost ? "post" : "comment"] = hash;
	$.post(
		"request/save.php?t=" + Math.random(),
		data,
		(response, status) => {
			if (status == 401)  {
				return alert("Please login!");
			} else if (status != 200) {
				return alert("Unknown Error");
			}
			response = JSON.parse(response);
			const {toggle,message} = response;
			if (toggle == -1) console.error(message);
			else button.classList.toggle("active");
		}
	)
}
$("#feed").click(function (e) {
	const target = e.target;
	const name = target.getAttribute("name");
	if (target.closest(".post")) {
		const hash = target.closest(".post").dataset.hash;
		if (name == "upvote-btn" || name == "downvote-btn") {
			return upvote(target);
		}
		else if (name == "comment-btn") {
			window.location.href = `subs/main/posts/${hash}/`;
		}
		else if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/main/posts/${hash}/`
				)
				.then(() => alert("post url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		}
		else if (name == "save-btn") {
			return save(target);
		}
		else if (target.closest(".post.overview")) {
			window.location = `/greendit/subs/main/posts/${hash}/`;
		}
	} else if (target.closest(".comment-wrapper")) {
		const post = target.closest(".comment-wrapper").dataset.hash;
		const comment = target.closest(".comment");
		if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/main/posts/${post}/comment/${comment.dataset.hash}`
				)
				.then(() => alert("comment url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		}
		else if (name == "upvote-btn" || name == "downvote-btn") {
			upvote(target);
		}
		else if (name == "save-btn") {
			return save(target);
		}
	}
});

$(".create-comment")?.on("submit", function(e) {
	e.preventDefault();
	const wrapper = this.closest('.comment-wrapper');
	const post = wrapper.dataset.hash;
	const composer = this.closest(".create-comment");
	const input = composer.querySelector(".comment-content");
	const content = input.value;
	$.post(
		`request/create_comment.php?t=${Math.random()}`,
		{ post, content },
		(response, status) => {
			if (status == 200) {
				const p = document.querySelector('.comment-wrapper > p');
				if (p) wrapper.removeChild(p);
				const comment = $.createElementFromHTML(response);
				wrapper.appendChild(comment);
				input.value = "";
			}
			if (!status == 200) return console.error(response);
		}
	);
});

$(".join-btn")?.click(function(e) {
	const subName = this.dataset.name;
	$.post(
		"request/join.php?t=" + Math.random(),
		{name: subName},
		(response,status) => {
			if (status == 401) {
				return alert("Please login!");
			} else if (status != 200) {
				return alert("Unknown Error");
			}
			response = JSON.parse(response);
			const {toggle,message} = response;
			if (toggle == -1) return console.error(message);
			this.classList.toggle("active");
			const increment = toggle == 0 ? 1 : -1;
			const span = $("span#members").element;
			span.innerHTML = Number(span.innerHTML) + increment;
		}
	)
});
$(".follow-btn")?.click(function(e) {
	const username = this.dataset.username;
	$.post(
		"request/follow.php?t=" + Math.random(),
		{username},
		(response,status) => {
			if (status == 401) {
				return alert("Please login!");
			} else if (status != 200) {
				return alert("Unknown Error");
			}
			response = JSON.parse(response);
			const {toggle,message} = response;
			if (toggle == -1) return console.error(message);
			this.classList.toggle("active");
		}
	);
})