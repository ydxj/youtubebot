// Utility functions for the bot
const fs = require('fs');
const path = require('path');

function loadJson(filePath, fallback = {}) {
	try {
		return JSON.parse(fs.readFileSync(filePath, 'utf8'));
	} catch (e) {
		return fallback;
	}
}

function saveJson(filePath, data) {
	fs.writeFileSync(filePath, JSON.stringify(data, null, 2));
}

function ensureFile(filePath, defaultContent = '{}') {
	if (!fs.existsSync(filePath)) {
		fs.writeFileSync(filePath, defaultContent);
	}
}

function logError(err) {
	console.error('[ERROR]', err.message || err);
}

module.exports = {
	loadJson,
	saveJson,
	ensureFile,
	logError
};
