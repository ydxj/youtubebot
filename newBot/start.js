
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
