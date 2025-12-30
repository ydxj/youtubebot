
// Instagram Explore logic
const fs = require('fs');
const puppeteer = require('puppeteer');


// Modernized: Scrape explore posts, then for each post, scrape comments/usernames, write to file, return usernames
async function exploreInstagram(options = {}) {
	// options: { cookies, userAgent, maxPosts, saveFile, telegramProgress }
	const maxPosts = options.maxPosts || 20;
	const url = 'https://www.instagram.com/explore/';
	const usernames = new Set();
	const postLinks = [];
	console.log('Exploring Instagram...');

	const browser = await puppeteer.launch({ headless: 'new' });
	const page = await browser.newPage();
	if (options.cookies) {
		await page.setCookie(...options.cookies);
	}
	if (options.userAgent) {
		await page.setUserAgent(options.userAgent);
	}

	await page.goto(url, { waitUntil: 'networkidle2' });
	await page.waitForSelector('article a', { timeout: 10000 });

	// Scrape post links
	const posts = await page.$$eval('article a', (links, max) => {
		return links.slice(0, max).map(link => link.href);
	}, maxPosts);
	postLinks.push(...posts);

	// For each post, visit and scrape comments/usernames
	for (let i = 0; i < postLinks.length; i++) {
		const postUrl = postLinks[i];
		try {
			await page.goto(postUrl, { waitUntil: 'networkidle2' });
			// Wait for comments to load (if any)
			await page.waitForSelector('ul ul li', { timeout: 5000 });
			// Scrape usernames from comments
			const commentUsernames = await page.$$eval('ul ul li h3 a', els => els.map(a => a.textContent));
			commentUsernames.forEach(u => usernames.add(u));
			// Optionally, add post owner
			const postOwner = await page.$eval('header a', el => el.textContent);
			usernames.add(postOwner);
		} catch (e) {
			// Skip if error
		}
		// Optionally update Telegram with progress
		if (options.telegramProgress) {
			await options.telegramProgress({ current: i + 1, total: postLinks.length, usernames: usernames.size });
		}
		if (usernames.size > 5000) break;
	}

	await browser.close();

	// Write usernames to file if requested
	if (options.saveFile) {
		fs.writeFileSync(options.saveFile, Array.from(usernames).join('\n'));
	}

	return Array.from(usernames);
}

module.exports = { exploreInstagram };
