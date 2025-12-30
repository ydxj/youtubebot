// Telegram bot logic using Telegraf
const { Telegraf } = require('telegraf');
const fs = require('fs');
const path = require('path');

// Load config
const configPath = path.join(__dirname, 'config.json');
let config = {};
try {
	config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
} catch (e) {
	console.error('Failed to load config.json:', e);
	process.exit(1);
}

if (!config.token) {
	console.error('Telegram bot token missing in config.json');
	process.exit(1);
}

const bot = new Telegraf(config.token);


// Helper: load and save config/accounts
function loadConfig() {
	return JSON.parse(fs.readFileSync(configPath, 'utf8'));
}
function saveConfig(cfg) {
	fs.writeFileSync(configPath, JSON.stringify(cfg, null, 2));
}
const accountsPath = path.join(__dirname, 'accounts.json');
function loadAccounts() {
	if (!fs.existsSync(accountsPath)) return {};
	return JSON.parse(fs.readFileSync(accountsPath, 'utf8'));
}
function saveAccounts(acc) {
	fs.writeFileSync(accountsPath, JSON.stringify(acc, null, 2));
}

// Main menu keyboard
function mainMenu() {
	return {
		reply_markup: {
			inline_keyboard: [
				[{ text: ' اضافة حسابات', callback_data: 'login' }],
				[{ text: 'ادارة اليوزرات والبحث', callback_data: 'grabber' }],
				[
					{ text: 'بدأ الفحص', callback_data: 'run' },
					{ text: 'ايقاف الفحص', callback_data: 'stop' }
				],
				[{ text: 'حالة الاداة', callback_data: 'status' }]
			]
		}
	};
}

// /start command
bot.start((ctx) => {
	ctx.reply(
		'اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @zerhounicnal',
		mainMenu()
	);
});

// Text message handler (account add/login, search, etc.)
bot.on('text', async (ctx) => {
	const config = loadConfig();
	const accounts = loadAccounts();
	const text = ctx.message.text;
	const chatId = ctx.chat.id;

	// Only respond to owner
	if (String(chatId) !== String(config.id)) return;

	if (config.mode === 'addL') {
		// Add account: expects user:pass
		const [user, pass] = text.split(':');
		// TODO: Implement real Instagram login and cookie fetch
		// For now, just store dummy data
		accounts[text] = {
			cookies: 'dummy_cookie',
			useragent: 'Instagram 27.0.0.7.97 Android (23/6.0.1; 640dpi; 1440x2392; LGE/lge; RS988; h1; h1; en_US)'
		};
		saveAccounts(accounts);
		ctx.reply(`*تم اضافة الحساب الى الاداة.*\n _Username_ : [${user}](instagram.com/${user})`, { parse_mode: 'Markdown' });
		config.mode = null;
		saveConfig(config);
		return;
	}
	// Add more text-based actions as needed (search, followers, etc.)
});

// Callback query handler (menu navigation)
bot.on('callback_query', async (ctx) => {
	const config = loadConfig();
	const accounts = loadAccounts();
	const data = ctx.callbackQuery.data;
	const chatId = ctx.chat.id;
	const mid = ctx.callbackQuery.message.message_id;

	if (data === 'login') {
		// Show add account menu
		let keyboard = [
			[{ text: 'اضافة حساب جديد', callback_data: 'addL' }]
		];
		Object.keys(accounts).forEach((acc) => {
			keyboard.push([
				{ text: acc, callback_data: 'ddd' },
				{ text: 'Logout', callback_data: `del&${acc}` }
			]);
		});
		keyboard.push([{ text: 'Main Page', callback_data: 'back' }]);
		await ctx.editMessageText('Accounts Control Page.', {
			reply_markup: { inline_keyboard: keyboard }
		});
	} else if (data === 'addL') {
		config.mode = 'addL';
		saveConfig(config);
		await ctx.reply('ارسل الحساب الوهمي : \n `يوزر:باسوورد`', { parse_mode: 'Markdown' });
	} else if (data === 'grabber') {
		// Show grabber menu (stub)
		await ctx.editMessageText('هذه ادارة اليوزرات والبحث.', mainMenu());
	} else if (data === 'back') {
		await ctx.editMessageText(
			'اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @omarzerhouni',
			mainMenu()
		);
	} else if (data.startsWith('del&')) {
		// Delete account
		const acc = data.split('&')[1];
		delete accounts[acc];
		saveAccounts(accounts);
		await ctx.answerCbQuery('Account deleted.');
		// Refresh menu
		let keyboard = [
			[{ text: 'Add New Accounts', callback_data: 'addL' }]
		];
		Object.keys(accounts).forEach((acc) => {
			keyboard.push([
				{ text: acc, callback_data: 'ddd' },
				{ text: 'Logout', callback_data: `del&${acc}` }
			]);
		});
		keyboard.push([{ text: 'Main Page', callback_data: 'back' }]);
		await ctx.editMessageText('صفحة ادارة الحسابات.', {
			reply_markup: { inline_keyboard: keyboard }
		});
	}
	// Add more callback actions as needed (search, followers, following, etc.)
});

bot.launch();
console.log('Telegram bot started.');
