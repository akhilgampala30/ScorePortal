function onSuccess(googleUser) {
	var profile = googleUser.getBasicProfile();
	/*console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
	console.log('Name: ' + profile.getName());
	console.log('Image URL: ' + profile.getImageUrl());
	console.log('Email: ' + profile.getEmail());
	document.location.href = '/include/UserSessionState/CheckID.php?ServiceID=0&LoginID='
		+ googleUser.getAuthResponse().id_token
		+ '&FirstName=' + profile.getGivenName()
		+ '&LastName=' + profile.getFamilyName()
		+ '&Email=' + profile.getEmail();*/

	var xhr = new XMLHttpRequest();
	xhr.open('POST', '/include/UserSessionState/CheckID.php');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.onload = function() {
		document.location.href = xhr.responseText;
		//console.log("Login Post Done: "+xhr.responseText);
	};
	xhr.send('ServiceID=0&LoginID='
		+ googleUser.getAuthResponse().id_token
		+ '&FirstName=' + profile.getGivenName()
		+ '&LastName=' + profile.getFamilyName()
		+ '&Email=' + profile.getEmail());
}

function onFailure(error) {
	console.log(error);
}

function renderButton() {
	gapi.signin2.render('my-signin2', {
		'scope': 'profile email',
		'width': 240,
		'height': 50,
		'longtitle': true,
		'theme': 'dark',
		'onsuccess': onSuccess,
		'onfailure': onFailure
	});
}
