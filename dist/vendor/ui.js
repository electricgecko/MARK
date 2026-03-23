document.addEventListener('DOMContentLoaded', () => {

	const 
		body = document.body,
		main = document.querySelector('main'),
		aside = document.querySelector('aside'),
		nav = document.querySelector('nav'),
		imgsWrap = document.querySelector('main ul'),
		downloadBtn = document.getElementById('download');

	const 
		mult = 0.3,
		filetypes = ['image/jpeg','image/png','image/gif','image/webp','image/avif'],
		unsortedmsg = 'No unsorted images.';

	let imgSz = parseInt(localStorage.getItem('MARKsz')) || 200,
		activeFilter = localStorage.getItem('MARKfilter') || '*';
	
	const hide = (...elements) => {
		elements.forEach(el => el && (el.style.display = 'none'));
	};
	
	hide(aside, aside.querySelector('#done'), aside.querySelector('#close'));

	if (localStorage.getItem('MARKbg')) {
		body.classList.add(localStorage.getItem('MARKbg'));
	}
	
	const showmessage = (msg) => {
		let p = main.querySelector('p');
		if (!p) {
			p = document.createElement('p');
			main.prepend(p);
		}
		p.innerHTML = msg;
	};
	
	const removemessage = () => {
		main.querySelectorAll('p').forEach(p => p.remove());
	};
	
	const flashDone = () => {
		const done = aside.querySelector('#done');
		done.style.display = 'block';
		setTimeout(() => done.style.display = 'none', 400);
	};
	
	const resizeImg = () => {
	
		if (getComputedStyle(imgsWrap).flexDirection === 'column') return;
	
		document.querySelectorAll('figure > a > img').forEach(img => {
			const ratio = img.width / img.height;
			img.style.height = imgSz + 'px';
			img.style.width = Math.floor(imgSz * ratio) + 'px';
		});
	
		document.querySelectorAll('li > figure')
			.forEach(fig => fig.style.height = imgSz + 'px');
	};
	
	const filterImg = () => {
	
		const all = document.querySelectorAll('main ul li');
	
		all.forEach(li => {
			if (activeFilter === '*' || li.matches(activeFilter)) {
				li.style.display = '';
			} else {
				li.style.display = 'none';
			}
		});
	
		downloadBtn.textContent = 'download ' + activeFilter.substring(1);
		localStorage.setItem('MARKfilter', activeFilter);
	};
	
	const invertBG = () => {
		body.classList.toggle('inv');
		localStorage.setItem('MARKbg', body.className);
	};

	window.addEventListener('keydown', e => {

		if (e.key === '+' || e.key === '=') {
			imgSz = Math.floor(imgSz + imgSz * mult);
			persistSize();
		}

		if (e.key === '-' || e.key === '_') {
			imgSz = Math.floor(imgSz - imgSz * mult);
			persistSize();
		}

		if (e.key.toLowerCase() === 'i') {
			invertBG();
		}
	});

	document.addEventListener('click', e => {

		const li = e.target.closest('main ul li');
		const deleteBtn = e.target.closest('a.del');
		const navItem = e.target.closest('nav ol li');
		const moveTarget = e.target.closest('aside ol li');
		const zoomIn = e.target.closest('#zoomIn');
		const zoomOut = e.target.closest('#zoomOut');
		const mobileInvert = e.target.closest('#mobileInvert');

		if (deleteBtn) {
			handleDelete(deleteBtn);
			return;
		}

		if (navItem) {
			handleNavFilter(navItem);
			return;
		}

		if (moveTarget) {
			moveImage(moveTarget);
			flashDone();
			return;
		}

		if (zoomIn) {
			imgSz = Math.floor(imgSz + imgSz * mult);
			persistSize();
			return;
		}

		if (zoomOut) {
			imgSz = Math.floor(imgSz - imgSz * mult);
			persistSize();
			return;
		}

		if (mobileInvert) {
			invertBG();
			return;
		}

		if (!li) {
			hideFilter();
			return;
		}

		if (e.shiftKey) {
			e.preventDefault();
			li.classList.toggle('selected');

			if (selectedImages().length) showFilter();
			else hideFilter();
		}

	});

	downloadBtn.addEventListener('click', async () => {

		downloadBtn.textContent = 'preparing …';

		const res = await fetch('mark.php', {
			method: 'POST',
			headers: { 'Content-Type':'application/x-www-form-urlencoded' },
			body: new URLSearchParams({
				a:'download',
				d:activeFilter.substring(1)
			})
		});

		const zipFilename = await res.text();
		window.location.replace('./' + zipFilename);

		downloadBtn.textContent = 'download everything';
	});

	const dragUpload =
		('draggable' in document.createElement('div')) &&
		'FormData' in window &&
		'FileReader' in window;

	if (dragUpload) {

		document.documentElement.addEventListener('dragover', e => {
			e.preventDefault();
			body.classList.add('drag');
		});

		document.documentElement.addEventListener('dragleave', e => {
			e.preventDefault();
			body.classList.remove('drag');
		});

		document.documentElement.addEventListener('drop', async e => {
			e.preventDefault();
			body.classList.remove('drag');

			const file = e.dataTransfer.files[0];
			if (!file || !filetypes.includes(file.type)) return;

			const fdata = new FormData();
			fdata.append('u', file);
			fdata.append('a','load');

			const res = await fetch('mark.php', {
				method:'POST',
				body:fdata
			});

			const data = await res.json();
			appendUploadedImg(data);
		});
	}

	const mobileUpload = document.getElementById('mobileUpload');

	if (mobileUpload) {
		mobileUpload.addEventListener('change', async () => {

			const fdata = new FormData();
			fdata.append('u', mobileUpload.files[0]);
			fdata.append('a','load');

			const res = await fetch('mark.php', {
				method:'POST',
				body:fdata
			});

			const data = await res.json();
			appendUploadedImg(data);
		});
	}

	const selectedImages = () =>
		document.querySelectorAll('li.selected');

	const persistSize = () => {
		localStorage.setItem('MARKsz', imgSz);
		resizeImg();
	};

	const showFilter = () => {
		main.style.maxWidth = (window.innerWidth - aside.offsetWidth) + 'px';
		main.style.margin = '60px 0';
		nav.style.display = 'none';

		let n = selectedImages().length;
		if (n < 10) n = '0' + n;

		aside.querySelector('p > span').textContent = n;
		aside.style.display = 'block';
	};

	const hideFilter = () => {
		selectedImages().forEach(li => li.classList.remove('selected'));

		main.style.maxWidth = 'none';
		main.style.margin = '60px auto';
		nav.style.display = 'inline';
		aside.style.display = 'none';
	};

	const moveImage = async (target) => {

		let folder = target.textContent;
		const first = target.parentElement.firstElementChild.textContent;
		if (folder === first) folder = '';

		const images = document.querySelectorAll('li.selected figure a img');

		for (const img of images) {

			const li = img.closest('li');
			const thumb = img.src;
			const file = img.parentElement.href;

			await fetch('mark.php', {
				method:'POST',
				headers:{'Content-Type':'application/x-www-form-urlencoded'},
				body:new URLSearchParams({
					a:'move', f:file, t:thumb, d:folder
				})
			});

			const baseClass = li.classList[0];
			const prefix = baseClass !== 'imgs' ? '' : 'imgs/';

			li.dataset.url = li.dataset.url.replace(baseClass, prefix + folder);
			li.dataset.thumb = li.dataset.thumb.replace(baseClass, prefix + folder);

			img.src = li.dataset.thumb;
			img.parentElement.href = li.dataset.url;

			li.className = folder || 'imgs';
		}

		filterImg();
	};

	const handleNavFilter = (navItem) => {

		document.querySelectorAll('nav ol li')
			.forEach(li => li.classList.remove('active'));

		const first = navItem.parentElement.firstElementChild;
		const last = navItem.parentElement.lastElementChild;

		if (navItem === first) {
			removemessage();
			activeFilter = '*';
		} else if (navItem === last) {
			activeFilter = '.imgs';
			if (!document.querySelectorAll(activeFilter).length) {
				showmessage(unsortedmsg);
			}
		} else {
			removemessage();
			activeFilter = '.' + navItem.textContent;
		}

		navItem.classList.add('active');
		filterImg();
	};

	const handleDelete = (btn) => {

		const li = btn.closest('li');
		const thumb = li.querySelector('img').src;
		const url = li.querySelector('figure a').href;
		console.log(li, thumb, url);
		
		fetch('mark.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: new URLSearchParams({
				a: 'del',
				f: url,
				t: thumb
			})
		})
		.then(response => {
			console.log('Response:', response);
		
			// Check HTTP status
			if (!response.ok) {
				throw new Error(`HTTP error! Status: ${response.status}`);
			}
		
			// Try to read response (adjust depending on your PHP output)
			return response.text(); // or response.json()
		})
		.then(data => {
			console.log('Success:', data);
		})
		.catch(error => {
			console.error('Fetch error:', error);
		});

		li.style.opacity = '0';
		setTimeout(() => li.remove(), 300);
	};

	const appendUploadedImg = (data) => {

		const li = document.createElement('li');
		li.className = 'imgs';
		li.dataset.thumb = data.thumb_name;
		li.dataset.url = data.img_name;

		li.innerHTML = `
			<a class="del" href="javascript:void(0);">×</a>
			<figure style="height:${imgSz}px;">
				<a href="${data.img_name}">
					<img width="${data.img_width}"
							 height="${data.img_height}"
							 src="${data.thumb_name}">
				</a>
			</figure>
		`;

		imgsWrap.prepend(li);
		console.log('New image uploaded');
	};
	
	main.style.display = 'block';
	resizeImg();
	filterImg();

});