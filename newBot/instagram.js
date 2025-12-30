
// Instagram automation logic
// Provides login and session management for Puppeteer-based modules
const puppeteer = require('puppeteer');

/**
 * Log in to Instagram and return a Puppeteer page with session cookies set.
 * @param {string} username
 * @param {string} password
 * @param {object} [options]
 * @returns {Promise<{browser: object, page: object}>}
 */
async function loginInstagram(username, password, options = {}) {
	const browser = await puppeteer.launch({ headless: true });
	const page = await browser.newPage();
	if (options.userAgent) {
		await page.setUserAgent(options.userAgent);
	}
	await page.goto('https://www.instagram.com/accounts/login/', { waitUntil: 'networkidle2' });
	await page.waitForSelector('input[name="username"]', { timeout: 10000 });
	await page.type('input[name="username"]', username, { delay: 50 });
	await page.type('input[name="password"]', password, { delay: 50 });
	await page.click('button[type="submit"]');
	await page.waitForNavigation({ waitUntil: 'networkidle2' });
	// Optionally, check for login errors here
	// Save cookies for reuse
	const cookies = await page.cookies();
	return { browser, page, cookies };
}

module.exports = { loginInstagram };
