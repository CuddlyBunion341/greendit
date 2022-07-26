$(".post-tab").click(() => {
	$(".post-tab").add("active");
	$(".media-tab").remove("active");
	$('.post-content [data-tab="0"]').remove("hidden");
	$('.post-content [data-tab="1"]').add("hidden");
	$('#tab-val').value("0");
});

$(".media-tab").click(() => {
	$(".post-tab").remove("active");
	$(".media-tab").add("active");
	$('.post-content [data-tab="0"]').add("hidden");
	$('.post-content [data-tab="1"]').remove("hidden");
	$('#tab-val').value("1");
});

$("#upload").click(() => {
	console.log("click");
	$('#media').click();
})

$("#media").on("change", () => {
	const preview = $("#preview").element;
	while (preview.firstChild) {
		preview.removeChild(preview.firstChild);
	}
	const input = $("#media").element;
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

			input.files = dt.files;
		})
		$(preview).append(img);
	}
});
