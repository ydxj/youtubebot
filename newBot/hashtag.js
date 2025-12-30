
// Instagram Hashtag logic
const fs = require('fs');
const puppeteer = require('puppeteer');


// Modernized: Scrape hashtag posts, visit each post, get likers (usernames), write to file, return usernames
async function searchHashtag(tag, options = {}) {
	// options: { cookies, userAgent, maxPosts, saveFile, telegramProgress }
	const maxPosts = options.maxPosts || 20;
	const url = `https://www.instagram.com/explore/tags/${encodeURIComponent(tag)}/`;
	const usernames = new Set();
	const postLinks = [];
	console.log(`Searching hashtag #${tag}...`);

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

	// For each post, visit and scrape likers (usernames)
	for (let i = 0; i < postLinks.length; i++) {
		const postUrl = postLinks[i];
		try {
			await page.goto(postUrl, { waitUntil: 'networkidle2' });
			// Wait for like button and open likers modal if possible
			// Instagram UI may change; try to click the likes count if available
			const likeBtn = await page.$('section span a');
			if (likeBtn) {
				await likeBtn.click();
				await page.waitForSelector('div[role="dialog"] ul', { timeout: 5000 });
				// Scrape usernames from likers modal
				const likers = await page.$$eval('div[role="dialog"] ul li', els =>
					els.map(el => {
						const userEl = el.querySelector('a[href^="/"]');
						return userEl ? userEl.textContent : null;
					})
				);
				likers.forEach(u => u && usernames.add(u));
				// Close modal
				await page.keyboard.press('Escape');
			}
			// Always add post owner
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

module.exports = { searchHashtag };
