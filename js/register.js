function createPfp() {
	let hash = Number(
		Math.random().toString().slice(2, 15) +
			Math.random().toString().slice(2, 15)
	);

	const canvas = document.createElement("canvas");
	const size = 6;
	canvas.width = canvas.height = size;
	const ctx = canvas.getContext("2d");

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
	for (let i = 0; i < size / 2; i++) {
		for (let j = 0; j < size; j++) {
			hash = Math.floor(hash / 10);
			if (hash % 10 < 3) {
				ctx.fillRect(i, j, 1, 1);
				ctx.fillRect((size - i - 1) * 1, j, 1, 1);
			}
		}
	}
	return canvas.toDataURL();
}

$('form').on('submit',() => {
    const dataUrl = createPfp();
    $('#pfp').value(dataUrl);
    return true;
})