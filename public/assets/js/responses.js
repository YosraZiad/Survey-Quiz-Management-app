document.getElementById('exportBtn')?.addEventListener('click', () => {
	if (typeof window.toast !== 'undefined') {
		window.toast.info('Exporting responses...');
	} else {
		alert('Exporting responses...');
	}
});

