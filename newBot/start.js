
// Start logic for the bot
const fs = require('fs');
const path = require('path');

/**
 * Loads config, checks required files, and validates credentials.
 * @param {object} options
 * @returns {Promise<{config: object, accounts: object, ready: boolean, error?: string}>}
 */
async function startBot(options = {}) {
	console.log('Starting Instagram bot...');
	const configPath = path.join(__dirname, 'config.json');
	const accountsPath = path.join(__dirname, 'accounts.json');
	let config = {};
	let accounts = {};
	let ready = true;
	let error = '';
	// Load config
	try {
		config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
	} catch (e) {
		error = 'Failed to load config.json';
		ready = false;
	}
	// Prompt for token and id if missing
	const readline = require('readline');
	async function promptInput(query) {
		const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
		return new Promise(resolve => rl.question(query, ans => { rl.close(); resolve(ans); }));
	}
	let configChanged = false;
	if (!config.token) {
		config.token = await promptInput('Enter your Telegram bot token: ');
		configChanged = true;
	}
	if (!config.id) {
		config.id = await promptInput('Enter your Telegram user ID: ');
		configChanged = true;
	}
	if (configChanged) {
		fs.writeFileSync(configPath, JSON.stringify(config, null, 2));
		console.log('Updated config.json with new token and/or id.');
	}
	// Load accounts
	try {
		accounts = JSON.parse(fs.readFileSync(accountsPath, 'utf8'));
	} catch (e) {
		error = 'Failed to load accounts.json';
		ready = false;
	}
	// Validate required config fields
	if (!config.token || !config.id) {
		error = 'Telegram bot token or user ID missing in config.json';
		ready = false;
	}
	// Optionally, check for at least one Instagram account
	if (Object.keys(accounts).length === 0) {
		console.warn('No Instagram accounts found in accounts.json');
	}
	if (ready) {
		console.log('Bot is ready.');
	} else {
		console.error('Bot is not ready:', error);
	}
	return { config, accounts, ready, error };
}

module.exports = { startBot };

/**
 * Process users from the screen file: check emails, classify, update Telegram, and track stats.
 * @param {object} options - { screenPath, configPath, accountsPath, telegramBot (optional) }
 */
async function processScreenUsers(options = {}) {
	const fs = require('fs');
	const path = require('path');
	const util = require('util');
	const sleep = util.promisify(setTimeout);
	const checkMail = require('./emailCheck').checkMail;
	// Stubs for getInfo and inInsta (should be implemented elsewhere)
	const getInfo = options.getInfo || (async (user, cookies, useragent) => ({ mail: user + '@gmail.com', user, f: 100, ff: 50, m: 10 }));
	const inInsta = options.inInsta || (async (mail) => true);
	const configPath = options.configPath || path.join(__dirname, 'config.json');
	const accountsPath = options.accountsPath || path.join(__dirname, 'accounts.json');
	let config = {};
	let accounts = {};
	try {
		config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
		accounts = JSON.parse(fs.readFileSync(accountsPath, 'utf8'));
	} catch (e) {
		throw new Error('Failed to load config or accounts: ' + e.message);
	}
	const screen = options.screenPath || (config.for || 'screen');
	const users = fs.readFileSync(screen, 'utf8').split('\n').filter(Boolean);
	const account = accounts[screen] || {};
	const cookies = (account.cookies || '') + (account.sessionid || '');
	const useragent = account.useragent || '';
	let gmail = 0, hotmail = 0, yahoo = 0, mailru = 0, trueCount = 0, falseCount = 0;
	let i = 0;
	let editAfter = 50;
	// Telegram progress stub
	function telegramProgress(msg) {
		// TODO: Integrate with Telegram bot if needed
		console.log('[Telegram]', msg);
	}
	telegramProgress('- Status:');
	for (const user of users) {
		let info;
		try {
			info = await getInfo(user, cookies, useragent);
		} catch {
			console.log('Not Bussines -', user);
			continue;
		}
		if (!info) {
			console.log('Not Bussines -', user);
			continue;
		}
		const mail = (info.mail || '').trim();
		const usern = info.user;
		const e = mail.split('@');
		if (/(live|hotmail|outlook|yahoo)\.(.*)|(gmail)\.(com)|(mail|bk|yandex|inbox|list)\.(ru)/i.test(mail)) {
			console.log('check', mail);
			if (await checkMail(mail)) {
				if (await inInsta(mail)) {
					console.log(`True - ${user} - ${mail}`);
					if (mail.includes('gmail.com')) gmail++;
					else if (mail.includes('hotmail.') || mail.includes('outlook.') || mail.includes('live.com')) hotmail++;
					else if (mail.includes('yahoo')) yahoo++;
					else if (/(mail|bk|yandex|inbox|list)\.(ru)/i.test(mail)) mailru++;
					const follow = info.f;
					const following = info.ff;
					const media = info.m;
					telegramProgress(`تم صيد حساب جديد  ✅\n━━━━━━━━━━━━\n.❖. اليوزر : [${usern}](instagram.com/${usern})\n.❖.  الايميل : [${mail}]\n. عدد المتابعين : ${follow}\n.❖. عدد المتابعهم : ${following}\n.❖. عدد المنشورات : ${media}\n━━━━━━━━━━━━\nCH :- [@av_vva]`);
					trueCount++;
				} else {
					console.log('No Rest', mail);
				}
			} else {
				console.log('Not Valid 2 -', mail);
			}
		} else {
			console.log('BlackList -', mail);
		}
		await sleep(750);
		i++;
		if (i === editAfter) {
			telegramProgress(`Checked: ${i}\nOn User: ${user}\nGmail: ${gmail}\nYahoo: ${yahoo}\nMailRu: ${mailru}\nHotmail: ${hotmail}\nTrue: ${trueCount}\nFalse: ${falseCount}`);
			editAfter += 1;
		}
	}
}

module.exports.processScreenUsers = processScreenUsers;
