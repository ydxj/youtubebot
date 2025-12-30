// Entry point for the Node.js Instagram bot
// This file should route commands to the correct modules
const { exploreInstagram } = require('./explore');
const { getFollowers } = require('./followers');
const { getFollowing } = require('./following');
const { searchHashtag } = require('./hashtag');
const { checkMail } = require('./emailCheck');
const utils = require('./utils');
const { launchBot } = require('./telegramBot'); // If you want to launch the Telegram bot

// Example credentials (replace with your own or load from config)
const IG_USER = process.env.IG_USER || '';
const IG_PASS = process.env.IG_PASS || '';

async function main() {
	console.log('Instagram bot started.');

	// Launch Telegram bot (uncomment if you want to run it)
	// launchBot();

	// Email validation demo (get email from config or tool)
	try {
		// Example: load email from config.json or prompt user
		const fs = require('fs');
		let email = '';
		try {
			const config = JSON.parse(fs.readFileSync('./config.json', 'utf8'));
			email = config.email || '';
		} catch (err) {
			console.warn('Could not load email from config.json:', err.message);
		}
		if (!email) {
			console.warn('No email found in config.json. Skipping email check.');
		} else {
			const isValid = await checkMail(email);
			console.log(`Email ${email} valid:`, isValid);
		}
	} catch (e) {
		console.error('Email check error:', e.message);
	}

	// Explore page demo
	try {
		const explore = await exploreInstagram({ maxPosts: 5 });
		console.log('Explore results:', explore);
	} catch (e) {
		console.error('Explore error:', e.message);
	}

	// Hashtag search demo
	try {
		const hashtag = await searchHashtag('nature', { maxPosts: 5 });
		console.log('Hashtag results:', hashtag);
	} catch (e) {
		console.error('Hashtag error:', e.message);
	}

	// Followers demo (requires login)
	if (IG_USER && IG_PASS) {
		try {
			const followers = await getFollowers(IG_USER, { igUser: IG_USER, igPass: IG_PASS, maxFollowers: 5 });
			console.log('Followers:', followers);
		} catch (e) {
			console.error('Followers error:', e.message);
		}
		// Following demo
		try {
			const following = await getFollowing(IG_USER, { igUser: IG_USER, igPass: IG_PASS, maxFollowing: 5 });
			console.log('Following:', following);
		} catch (e) {
			console.error('Following error:', e.message);
		}
	} else {
		console.log('Set IG_USER and IG_PASS env vars for followers/following demo.');
	}

	       // Utility demo
	       if (typeof utils.getUUID === 'function') {
		       console.log('UUID:', utils.getUUID());
	       }
	       if (typeof utils.getGUID === 'function') {
		       console.log('GUID:', utils.getGUID());
	       }
}



// Launch Telegram bot as the main controller
launchBot();
console.log('Send commands to your Telegram bot to control the Instagram bot.');
