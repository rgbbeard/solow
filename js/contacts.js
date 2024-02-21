window.addEventListener("load", function() {
	const 
		name = document.getElementById("name"),
		surname = document.getElementById("surname"),
		email = document.getElementById("email"),
		phone = document.getElementById("phone"),
		regemail = /([a\.\--z\_]*[a0-z9]+@)([a-z]+\.)([a-z]{2,6})/,
		regphone = /(\+39\s?)?((3|0)([0-9]+){9,})/;

	document.getElementById("login_form").addEventListener("submit", function(e) {
		try {
			email.removeAttribute("required");
			phone.removeAttribute("required");
		} catch(ignore) {}

		if(!e) {
			e = window.event;
		}

		e.preventDefault();

		let valid = true;

		if(name.value.empty()) {
			name.reportValidity();
			valid = false;
		} else if(surname.value.empty()) {
			surname.reportValidity();
			valid = false;
		} else {
			if(!email.value.match(regemail) && phone.value.empty()) {
				email.setAttribute("required", "required");
				email.reportValidity();
				valid = false;
			} else if(!phone.value.match(regphone) && email.value.empty()) {
				phone.setAttribute("required", "required");
				phone.reportValidity();
				valid = false;
			}
		}

		if(valid) {
			new Request({
				method: "post",
				url: "contacts/submit",
				data: {
					name: name.value,
					surname: surname.value,
					email: email.value,
					phone: phone.value
				},
				done: function(r) {
					console.log(r);

					if(r.return === "ok") {
						new Toast({
							appearance: "success",
							text: "Richiesta di contatto inviata con successo",
							position: "bottom-center"
						});
					}
				}
			});
		}
	});
});