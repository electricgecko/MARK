(function () {

	const initMARK = () => {

		window.MARK = (() => {

			const notify = document.createElement('span');
			notify.id = 'MARK-notify';
			notify.className = 'MARK-notify';
			notify.textContent = 'Saved to MARK';

			Object.assign(notify.style, {
				display: 'none',
				background: '#fff',
				color: '#000',
				position: 'fixed',
				padding: '10px',
				fontSize: '20px',
				fontFamily: 'Arial',
				bottom: 0,
				left: 0,
				zIndex: 9999
			});

			document.body.appendChild(notify);
			const images = document.querySelectorAll('img');

			images.forEach(img => {

				Object.assign(img.style, {
					position: 'relative',
					zIndex: '9999',
					border: '5px solid rgba(255, 230, 0, 1)',
					cursor: 'pointer'
				});

				const anchor = img.closest('a');
				if (anchor) {
					anchor.href = 'javascript:void(0);';
					anchor.dataset.url = '#';
				}

				img.addEventListener('click', async (e) => {
					e.preventDefault();
					e.stopPropagation();

					const timg = new Image();
					timg.src = img.src;

					let src = img.src;
					let url;

					if (src.indexOf('?') > -1) {
						url = src.substring(0, src.indexOf('?'));
					} else {
						url = src;
					}

					if (!url.startsWith('https://')) {url = url.replace('//', 'https://');}

					try {
						const response = await fetch(installdir + '/mark.php', {
							method: 'POST',
							headers: {'Content-Type': 'application/x-www-form-urlencoded'},
							body: new URLSearchParams({
								a: 'load',
								f: url
							})
						});

						const data = await response.text();
						notify.style.display = 'block';
						notify.style.opacity = '1';

						setTimeout(() => {
							notify.style.transition = 'opacity 0.3s ease';
							notify.style.opacity = '0';

							setTimeout(() => {
								notify.style.display = 'none';
								notify.style.transition = '';
							}, 300);
							
						}, 300);
					} catch (err) {console.error(err);}
				});
			});
		})();
	};
	initMARK();

})();