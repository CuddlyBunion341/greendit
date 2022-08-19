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
	const wrapper = button.parentNode;
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
			const likeCount = wrapper.querySelector(".like-count");
			likeCount.innerHTML =
				Number(likeCount.innerHTML) + increment * (upvote ? 1 : -1);
			const other = wrapper.querySelector(
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
	$.post("request/save.php?t=" + Math.random(), data, (response, status) => {
		if (status == 401) {
			return alert("Please login!");
		} else if (status != 200) {
			return alert("Unknown Error");
		}
		response = JSON.parse(response);
		const { toggle, message } = response;
		if (toggle == -1) console.error(message);
		else button.classList.toggle("active");
	});
}

$("#feed")?.click(function (e) {
	const target = e.target;
	const name = target.getAttribute("name");
	if (target.closest(".post")) {
		const hash = target.closest(".post").dataset.hash;
		const sub = target.closest(".post").dataset.sub;
		if (name == "upvote-btn" || name == "downvote-btn") {
			return upvote(target);
		} else if (name == "comment-btn") {
			window.location.href = `subs/${sub}/posts/${hash}/`;
		} else if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/${sub}/posts/${hash}/`
				)
				.then(() => alert("post url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		} else if (name == "save-btn") {
			return save(target);
		} else if (target.closest(".post.overview")) {
			window.location = `/greendit/subs/${sub}/posts/${hash}/`;
		}
	} else if (target.closest(".comment-wrapper")) {
		const post = target.closest(".comment-wrapper").dataset.hash;
		const sub = target.closest(".comment-wrapper").dataset.sub;
		const comment = target.closest(".comment");
		if (name == "share-btn") {
			navigator.clipboard
				.writeText(
					`${window.location.host}/greendit/subs/${sub}/posts/${post}/comment/${comment.dataset.hash}`
				)
				.then(() => alert("comment url copied to clipboard!"))
				.catch(() => alert("something went wrong..."));
		} else if (name == "upvote-btn" || name == "downvote-btn") {
			upvote(target);
		} else if (name == "save-btn") {
			return save(target);
		}
	} else if (target.closest(".user-comment")) {
		const post = target.closest(".user-comment-wrapper__comments").dataset
			.hash;
		const sub = target.closest(".user-comment-wrapper__comments").dataset
			.sub;
		const comment = target.closest(".user-comment").dataset.hash;
		window.location = `/greendit/subs/${sub}/posts/${post}/comment/${comment}/`;
	} else if (target.closest(".community")) {
		const name = target.closest(".community").dataset.name;
		window.location = `/greendit/subs/${name}`;
	}
});

$(".create-comment")?.on("submit", function (e) {
	e.preventDefault();
	const wrapper = this.closest(".comment-wrapper");
	const post = wrapper.dataset.hash;
	const composer = this.closest(".create-comment");
	const input = composer.querySelector(".comment-content");
	const content = input.value;
	$.post(
		`request/create_comment.php?t=${Math.random()}`,
		{ post, content },
		(response, status) => {
			if (status == 200) {
				const p = document.querySelector(".comment-wrapper > p");
				if (p) wrapper.removeChild(p);
				const comment = $.createElementFromHTML(response);
				wrapper.append(comment);
				input.value = "";
			}
			if (!status == 200) return console.error(response);
		}
	);
});

document.querySelectorAll(".join-btn")?.forEach((btn) => {
	$(btn).click(function (e) {
		const subName = this.dataset.name;
		$.post(
			"request/join.php?t=" + Math.random(),
			{ name: subName },
			(response, status) => {
				if (status == 401) {
					return alert("Please login!");
				} else if (status != 200) {
					return alert("Unknown Error");
				}
				response = JSON.parse(response);
				const { toggle, message } = response;
				if (toggle == -1) return console.error(message);
				this.classList.toggle("active");
				const span = $("span#members")?.element;
				if (span) {
					const increment = toggle == 0 ? 1 : -1;
					span.innerHTML = Number(span.innerHTML) + increment;
				}
			}
		);
	});
});

$(".follow-btn")?.click(function (e) {
	const username = this.dataset.username;
	$.post(
		"request/follow.php?t=" + Math.random(),
		{ username },
		(response, status) => {
			if (status == 401) {
				return alert("Please login!");
			} else if (status != 200) {
				return alert("Unknown Error");
			}
			response = JSON.parse(response);
			const { toggle, message } = response;
			if (toggle == -1) return console.error(message);
			this.classList.toggle("active");
		}
	);
});

$(".pfp-select")?.click(() => {
	$("#pfp-input").click();
});

$("#pfp-input")?.on("change", function (e) {
	const file = this.files[0];
	const formData = new FormData();
	formData.append("file", file);
	$.ajax({
		url: "request/upload_pfp.php?t=" + Math.random(),
		type: "POST",
		data: formData,
		enctype:
			"multipart/form-data; charset=utf-8; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
		success: (response) => {
			console.log(response);
			const pfp = $(".pfp-select .pfp").element;
			pfp.src = response;
		},
		error: (err) => {
			console.log(err);
		},
	});
});

$("#fetch-btn")?.click(function (e) {
	const sub = this.dataset.sub;
	const count = this.dataset.count;
	const limit = 5;
	this.dataset.count = +this.dataset.count + limit;
	$.post(
		"request/fetch_posts.php?t=" + Math.random(),
		{ sub, start: count, limit },
		(response, status) => {
			if (status == 200 || status == 204) {
				$("#feed").element.insertAdjacentHTML("beforeend", response);
				$("#feed").append(this);
				if (status == 204) {
					$("#feed").appendHTML("<p>No more posts to show</p>");
					$("#fetch-btn")?.element.remove(); // this.remove() doesn't work
				}
			} else {
				return console.error(response);
			}
		}
	);
});

window.onscroll = () => {
	if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 1) {
		$("#fetch-btn")?.click();
	}
};

$("#search")?.on("keydown", function (e) {
	if (e.keyCode == 13) {
		const query = this.value;
		if (!query) return;
		window.location = `/greendit/search/${query}`;
	}
});
