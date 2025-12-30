
// Instagram Explore logic
const fs = require('fs');
const puppeteer = require('puppeteer');

async function exploreInstagram(options = {}) {
	// options: { session, cookies, userAgent, maxPosts }
	const maxPosts = options.maxPosts || 20;
	const results = [];
	const url = 'https://www.instagram.com/explore/';
	console.log('Exploring Instagram...');

	const browser = await puppeteer.launch({ headless: true });
	const page = await browser.newPage();
	if (options.cookies) {
		await page.setCookie(...options.cookies);
	}
	if (options.userAgent) {
		await page.setUserAgent(options.userAgent);
	}

	await page.goto(url, { waitUntil: 'networkidle2' });
	// Wait for posts to load
	await page.waitForSelector('article a', { timeout: 10000 });

	// Scrape post links and usernames
	const posts = await page.$$eval('article a', (links, max) => {
		return links.slice(0, max).map(link => ({
			postUrl: link.href
		}));
	}, maxPosts);

	// Optionally, fetch usernames for each post
	for (let post of posts) {
		try {
			await page.goto(post.postUrl, { waitUntil: 'networkidle2' });
			const username = await page.$eval('header a', el => el.textContent);
			post.username = username;
			results.push(post);
		} catch (e) {
			// Skip if error
		}
		if (results.length >= maxPosts) break;
	}

	await browser.close();

	// Optionally save to file
	if (options.saveFile) {
		fs.writeFileSync(options.saveFile, JSON.stringify(results, null, 2));
	}

	return results;
}

module.exports = { exploreInstagram };
