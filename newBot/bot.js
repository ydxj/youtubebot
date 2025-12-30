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

bot.start((ctx) => {
	ctx.reply('اهلا بك عزيزي المشترك . \n - في اداة صيد المتاحات الخاصه بك. \n\n By ~ @zerhounicnal');
});

// TODO: Add more command and callback handling here

bot.launch();
console.log('Telegram bot started.');
