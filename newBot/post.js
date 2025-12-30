
// Instagram Post logic
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

/**
 * Post an image to Instagram with a caption using Puppeteer.
 * @param {object} content - { image: string, caption: string }
 * @param {object} options - { igUser, igPass, userAgent }
 * @returns {Promise<{success: boolean, error?: string}>}
 */
async function postInstagram(content, options = {}) {
	// content: { image, caption }
	// options: { igUser, igPass, userAgent }
	if (!content.image || !content.caption) {
		return { success: false, error: 'Image and caption required.' };
	}
	if (!options.igUser || !options.igPass) {
		return { success: false, error: 'Instagram credentials required.' };
	}
	console.log('Posting to Instagram...');
	const browser = await puppeteer.launch({ headless: false }); // headless: false for file upload UI
	const page = await browser.newPage();
	if (options.userAgent) {
		await page.setUserAgent(options.userAgent);
	}
	await page.goto('https://www.instagram.com/accounts/login/', { waitUntil: 'networkidle2' });
	await page.waitForSelector('input[name="username"]', { timeout: 10000 });
	await page.type('input[name="username"]', options.igUser, { delay: 50 });
	await page.type('input[name="password"]', options.igPass, { delay: 50 });
	await page.click('button[type="submit"]');
	await page.waitForNavigation({ waitUntil: 'networkidle2' });

	// Go to home and click the new post button
	await page.goto('https://www.instagram.com/', { waitUntil: 'networkidle2' });
	await page.waitForSelector('svg[aria-label="New post"]', { timeout: 10000 });
	await page.click('svg[aria-label="New post"]');

	// File input for image upload
	const [fileChooser] = await Promise.all([
		page.waitForFileChooser(),
		// Click the file input button
		page.click('div[role="dialog"] input[type="file"]'),
	]);
	await fileChooser.accept([path.resolve(content.image)]);

	// Wait for image to load and click Next
	await page.waitForSelector('button[type="button"]:not([disabled])', { timeout: 10000 });
	await page.click('button[type="button"]:not([disabled])'); // Next
	await page.waitForTimeout(1000);
	await page.click('button[type="button"]:not([disabled])'); // Next again

	// Enter caption
	await page.waitForSelector('textarea[aria-label="Write a caption…"]', { timeout: 10000 });
	await page.type('textarea[aria-label="Write a caption…"]', content.caption, { delay: 30 });

	// Share post
	await page.waitForSelector('button[type="button"]:not([disabled])', { timeout: 10000 });
	await page.click('button[type="button"]:not([disabled])'); // Share

	// Wait for post to complete (look for post dialog to close)
	await page.waitForTimeout(5000);
	await browser.close();
	return { success: true };
}

module.exports = { postInstagram };
