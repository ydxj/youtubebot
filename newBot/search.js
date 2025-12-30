
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

/**
 * Batch search for Instagram users by generating queries from config.words and a-z/0-9, using the web search API.
 * Writes unique usernames to the output file and logs progress.
 * @param {object} options - { configPath, accountsPath, telegramBot (optional) }
 */
async function batchSearchInstagram(options = {}) {
	const configPath = options.configPath || './config.json';
	const accountsPath = options.accountsPath || './accounts.json';
	const fs = require('fs');
	const path = require('path');
	let config, accounts;
	try {
		config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
		accounts = JSON.parse(fs.readFileSync(accountsPath, 'utf8'));
	} catch (e) {
		throw new Error('Failed to load config or accounts: ' + e.message);
	}
	const file = config.for;
	const id = config.id;
	const words = (config.words || '').split(' ').filter(Boolean);
	const account = accounts[file];
	if (!account) throw new Error('Account not found for file: ' + file);
	const cookies = account.cookies;
	const userAgent = account.useragent;
	const chars = [...'abcdefghijklmnopqrstuvwxyz0123456789'];
	let usernames = new Set();
	if (fs.existsSync(file)) {
		fs.readFileSync(file, 'utf8').split('\n').forEach(u => { if (u) usernames.add(u); });
	}
	let i = 0;
	let e = 15;
	// Telegram progress stub
	function telegramProgress(msg) {
		// TODO: Integrate with Telegram bot if needed
		console.log('[Telegram]', msg);
	}
	telegramProgress(`Collection From ~ [ Search ]\nStatus ~> Working\nUsers ~> ${usernames.size}`);
	const browser = await puppeteer.launch({ headless: 'new' });
	const page = await browser.newPage();
	await page.setUserAgent(userAgent);
	// Set cookies if available
	if (cookies) {
		// Parse cookies string to array
		const cookieArr = cookies.split(';').map(c => {
			const [name, ...rest] = c.trim().split('=');
			return { name, value: rest.join('='), domain: '.instagram.com' };
		});
		await page.setCookie(...cookieArr);
	}
	async function searchQuery(q) {
		const url = `https://www.instagram.com/web/search/topsearch/?query=${encodeURIComponent(q)}`;
		await page.goto(url, { waitUntil: 'networkidle2' });
		const content = await page.content();
		// Extract JSON from <pre> or directly from body
		let json;
		try {
			const pre = await page.$eval('pre', el => el.textContent);
			json = JSON.parse(pre);
		} catch {
			// fallback: try to parse body text
			const body = await page.evaluate(() => document.body.textContent);
			try { json = JSON.parse(body); } catch { json = null; }
		}
		if (!json || !json.users) return [];
		return json.users.map(u => u.user.username);
	}
	for (const word of words) {
		for (const char of chars) {
			const word1 = word + char;
			const found = await searchQuery(word1);
			for (const username of found) {
				if (!usernames.has(username)) {
					fs.appendFileSync(file, username + '\n');
					usernames.add(username);
					i++;
					if (i === e) {
						telegramProgress(`Collection From ~ [ Search ]\nStatus ~> Working\nUsers ~> ${usernames.size}`);
						e += 25;
					}
				}
			}
		}
		for (const char of chars) {
			const word2 = char + word;
			const found = await searchQuery(word2);
			for (const username of found) {
				if (!usernames.has(username)) {
					fs.appendFileSync(file, username + '\n');
					usernames.add(username);
					i++;
					if (i === e) {
						telegramProgress(`Collection From ~ [ Search ]\nStatus ~> Working\nUsers ~> ${usernames.size}`);
						e += 25;
					}
				}
			}
		}
	}
	await browser.close();
	telegramProgress(`Done Collection. All: ${usernames.size}`);
	return Array.from(usernames);
}

module.exports.batchSearchInstagram = batchSearchInstagram;
