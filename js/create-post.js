// myquery does not support querySelectorAll yet...
document.querySelectorAll(".tabs button").forEach(button => {
	$(button).click(function(e) {
		const parent = this.parentNode;
		const children = [...parent.children];
		children.forEach(sibling => sibling.classList.remove("active"));
		this.classList.add("active");
		const index = children.indexOf(this);

		const contents = document.querySelectorAll('.post-content > div');
		contents.forEach(tab => tab.classList.add("hidden"));
		contents[index].classList.remove("hidden");

		$('#tab-val').value(index);
	});
})

document.querySelectorAll('button[name="upload-btn"]').forEach(button => {
	$(button).click(function(e) {
		const parent = this.parentNode;
		const input = parent.querySelector('input[type="file"]');
		$(input).click();
	});
})

$('button[name="remove-btn"]').click(function(e) {
	const parent = this.parentNode;
	const video = parent.querySelector('video');
	$(this).add("hidden");
	video.classList.add("hidden");
	while (video.firstChild) {
		video.removeChild(video.firstChild);
	}
	// clear files
	const input = parent.querySelector('input[type="file"]');
	const dt = new DataTransfer();
	input.files = dt.files;
	// show video selection
	input.parentNode.classList.remove("hidden");
});

$("#image-input").on("change", () => {
	const preview = $("#preview").element;
	while (preview.firstChild) {
		preview.removeChild(preview.firstChild);
	}
	const input = $("#image-input").element;
	if (input.files.length == 0) $(preview).add("hidden");
	else $(preview).remove("hidden");
	for (const file of input.files) {
		const url = URL.createObjectURL(file);
		const img = $.createElementFromHTML(`
			<picture>
				<button type="button" class="rm-btn">✕</button>
				<img src="${url}">
			</picture>`);
		$(img.querySelector('.rm-btn')).click(function(e) {
			const picture = this.parentNode;
			picture.parentNode.removeChild(picture);
			const dt = new DataTransfer();

			for (const f of input.files) {
				if (file != f) dt.add(f);
			}

			if (dt.files.length == 0) {
				$(preview).add("hidden");
			}

			input.files = dt.files;
		})
		$(preview).append(img);
	}
});

$("#video-input").on("change",function(e) {
	this.parentNode.classList.add("hidden");
	const file = this.files[0];
	const url = URL.createObjectURL(file);

	const video = this.parentNode.parentNode.querySelector('video');

	video.classList.remove('hidden');
	$('button[name="remove-btn"]').remove("hidden");
	const source = $.createElementFromHTML(`<source src="${url}">`);
	video.appendChild(source);
})
