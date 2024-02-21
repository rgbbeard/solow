window.addEventListener("load", function() {
	let shown = false;
	// debugger
	const dbggr = document.getElementById("debugger-content");
	// button
	document.getElementById("debugger-bar").children[0].addEventListener("click", function() {
		const btn = this;

		if(shown) {
			dbggr.style.display = "none";
			btn.textContent = "Show debugger";
		} else {
			dbggr.style.display = "block";
			btn.textContent = "Hide debugger";
		}

		shown = !shown;
	});
});