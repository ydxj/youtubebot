
// Instagram Search logic
const fs = require('fs');
const puppeteer = require('puppeteer');

/**
 * Search for Instagram users by query using Puppeteer.
 * @param {string} query
 * @param {object} options - { igUser, igPass, userAgent, maxResults, saveFile }
 * @returns {Promise<Array<{username: string, fullName: string, profileUrl: string}>>}
 */
async function searchInstagram(query, options = {}) {
	const maxResults = options.maxResults || 10;
	const results = [];
	console.log(`Searching Instagram for ${query}...`);

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
	} else {
		await page.goto('https://www.instagram.com/', { waitUntil: 'networkidle2' });
	}

	// Use Instagram's search bar
	await page.waitForSelector('input[placeholder="Search"]', { timeout: 10000 });
	await page.click('input[placeholder="Search"]');
	await page.type('input[placeholder="Search"]', query, { delay: 100 });
	await page.waitForTimeout(2000);

	// Scrape user results
	const users = await page.$$eval('div[role="none"] a[href^="/"]', (links, max) => {
		const seen = new Set();
		const results = [];
		for (const link of links) {
			const username = link.getAttribute('href').replaceAll('/', '');
			const fullName = link.textContent;
			if (!seen.has(username) && username && !username.includes('explore')) {
				results.push({
					username,
					fullName,
					profileUrl: 'https://www.instagram.com/' + username + '/'
				});
				seen.add(username);
			}
			if (results.length >= max) break;
		}
		return results;
	}, maxResults);
	results.push(...users);

	await browser.close();

	// Optionally save to file
	if (options.saveFile) {
		fs.writeFileSync(options.saveFile, JSON.stringify(results, null, 2));
	}

	return results;
}

module.exports = { searchInstagram };
