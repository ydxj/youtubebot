
// Instagram Followers logic
const fs = require('fs');
const puppeteer = require('puppeteer');

async function getFollowers(username, options = {}) {
	// options: { igUser, igPass, cookies, userAgent, maxFollowers, saveFile }
	const maxFollowers = options.maxFollowers || 50;
	const results = [];
	console.log(`Fetching followers for ${username}...`);

	const browser = await puppeteer.launch({ headless: 'new' });
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

	// Open followers modal
	await page.waitForSelector('a[href$="/followers/"]', { timeout: 10000 });
	await page.click('a[href$="/followers/"]');
	await page.waitForSelector('div[role="dialog"] ul', { timeout: 10000 });

	// Scroll and collect followers
	let prevCount = 0;
	while (results.length < maxFollowers) {
		const newFollowers = await page.$$eval('div[role="dialog"] ul li', els =>
			els.map(el => {
				const userEl = el.querySelector('a[href^="/"]');
				return userEl ? userEl.textContent : null;
			})
		);
		for (const user of newFollowers) {
			if (user && !results.includes(user)) results.push(user);
			if (results.length >= maxFollowers) break;
		}
		if (results.length === prevCount) break; // No more new followers
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

module.exports = { getFollowers };
