
// Entry point for the Node.js Instagram bot
// This file should route commands to the correct modules
const { exploreInstagram } = require('./explore');
const { getFollowers } = require('./followers');
const { getFollowing } = require('./following');
const { searchHashtag } = require('./hashtag');
const { searchInstagram } = require('./search');
const { postInstagram } = require('./post');

// Example credentials (replace with your own or load from config)
const IG_USER = process.env.IG_USER || '';
const IG_PASS = process.env.IG_PASS || '';

async function main() {
	console.log('Instagram bot started.');

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
}

main();
