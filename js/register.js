// ---- Profile Picture ------------------------------------------------------------------

function createPfp() {
	let hash = Number(
		Math.random().toString().slice(2, 15) +
			Math.random().toString().slice(2, 15)
	);

	const canvas = document.createElement("canvas");
	const size = 6;
	canvas.width = canvas.height = 128;
	const ctx = canvas.getContext("2d");

	// set white background
	ctx.fillStyle = "#FFF";
	ctx.fillRect(0, 0, 128, 128);

	// set pfp color
	const colors = [
		"#2196F3",
		"#32c787",
		"#00BCD4",
		"#ff5652",
		"#ffc107",
		"#ff85af",
		"#FF9800",
		"#39bbb0",
		"#4CAF50",
		"#ff5e3a",
		"#f39c12",
		"#d4e157",
	];
	ctx.fillStyle = colors[hash % 10];
	ctx.scale(16, 16);

	// add and mirror pixels
	for (let i = 0; i < size / 2; i++) {
		for (let j = 0; j < size; j++) {
			hash = Math.floor(hash / 10);
			if (hash % 10 < 3) {
				ctx.fillRect(i + 1, j + 1, 1, 1);
				ctx.fillRect((size - i) * 1, j + 1, 1, 1);
			}
		}
	}
	return canvas.toDataURL();
}

function updateImg(pfp) {
	document.querySelector("img.pfp").src = pfp;
}

if (!$("#pfp").value()) nextPfp();
updateImg($("#pfp").value());

function nextPfp() {
	const pfp = createPfp();
	$("#pfp").value(pfp);
	updateImg(pfp);
	return pfp;
}
$("#next-pfp-btn").click(nextPfp);

// ---- Captcha --------------------------------------------------------------------------

$("#next-captcha-btn").click(() => {
	$.get("request/new_captcha.php", (base64) => {
		$(".captcha").element.src = `data:image/png;base64,${base64}`;
	});
});
