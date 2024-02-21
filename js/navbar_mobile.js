window.addEventListener("load", function() {
	let shown = false;
	// menu
	const menu = document.querySelector("#navbar-mobile .nav-main-menu");
	// button
	document.getElementById("nav-trigger").addEventListener("click", function() {
		const btn = this;

		menu.style.display = shown ? "none" : "block";

		shown = !shown;
	});
	document.getElementById("menu-trigger").addEventListener("click", function() {
		const btn = this;

		menu.style.display = !shown ? "block" : "none";

		shown = !shown;
	});
});