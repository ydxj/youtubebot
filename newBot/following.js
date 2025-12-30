
// Instagram Following logic
const fs = require('fs');
const puppeteer = require('puppeteer');

async function getFollowing(username, options = {}) {
	// options: { igUser, igPass, cookies, userAgent, maxFollowing, saveFile }
	const maxFollowing = options.maxFollowing || 50;
	const results = [];
	console.log(`Fetching following for ${username}...`);

	const browser = await puppeteer.launch({ headless: true });
	const page = await browser.newPage();
	if (options.userAgent) {
		await page.setUserAgent(options.userAgent);
	}

	// Login if credentials provided
	if (options.igUser && options.igPass) {
		await page.goto('https://www.instagram.com/accounts/login/', { waitUntil: 'networkidle2' });
		await page.waitForSelector('input[name="username"]', { timeout: 10000 });
		await page.type('input[name="username"]', options.igUser, { delay: 50 });
		await page.type('input[name="password"]', options.igPass, { delay: 50 });
		await page.click('button[type="submit"]');
		await page.waitForNavigation({ waitUntil: 'networkidle2' });
	} else if (options.cookies) {
		await page.setCookie(...options.cookies);
		await page.goto(`https://www.instagram.com/${username}/`, { waitUntil: 'networkidle2' });
	} else {
		await page.goto(`https://www.instagram.com/${username}/`, { waitUntil: 'networkidle2' });
	}

	// Open following modal
	await page.waitForSelector('a[href$="/following/"]', { timeout: 10000 });
	await page.click('a[href$="/following/"]');
	await page.waitForSelector('div[role="dialog"] ul', { timeout: 10000 });

	// Scroll and collect following
	let prevCount = 0;
	while (results.length < maxFollowing) {
		const newFollowing = await page.$$eval('div[role="dialog"] ul li', els =>
			els.map(el => {
				const userEl = el.querySelector('a[href^="/"]');
				return userEl ? userEl.textContent : null;
			})
		);
		for (const user of newFollowing) {
			if (user && !results.includes(user)) results.push(user);
			if (results.length >= maxFollowing) break;
		}
		if (results.length === prevCount) break; // No more new following
		prevCount = results.length;
		await page.evaluate(() => {
			const dialog = document.querySelector('div[role="dialog"] ul');
			dialog.parentNode.scrollTop = dialog.parentNode.scrollHeight;
		});
		await new Promise(r => setTimeout(r, 1000));
	}

	await browser.close();

	// Optionally save to file
	if (options.saveFile) {
		fs.writeFileSync(options.saveFile, JSON.stringify(results, null, 2));
	}

	return results;
}

module.exports = { getFollowing };
