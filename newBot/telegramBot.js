// Telegram bot integration using telegraf

const { Telegraf } = require('telegraf');
const fs = require('fs/promises');
const config = require('./config.json');
const { exploreInstagram } = require('./explore');
const { getFollowers } = require('./followers');
const { getFollowing } = require('./following');
const { searchHashtag } = require('./hashtag');
const { checkMail } = require('./emailCheck');
const utils = require('./utils');

const bot = new Telegraf(config.token);

// Only allow commands from the configured Telegram user ID
function isAuthorized(ctx) {
  return String(ctx.from.id) === String(config.id);
}

bot.start((ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  ctx.reply('Welcome to the Node.js Instagram Bot!\nCommands: /explore, /followers, /following, /hashtag, /checkmail, /uuid, /guid');
});

bot.command('explore', async (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  ctx.reply('Exploring Instagram...');
  try {
    const results = await exploreInstagram({ maxPosts: 5 });
    ctx.reply('Explore results:\n' + results.map(r => r.username || r).join('\n'));
  } catch (e) {
    ctx.reply('Explore error: ' + e.message);
  }
});

bot.command('followers', async (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  const igUser = config.igUser || process.env.IG_USER;
  const igPass = config.igPass || process.env.IG_PASS;
  if (!igUser || !igPass) return ctx.reply('IG_USER and IG_PASS not set.');
  ctx.reply('Getting followers...');
  try {
    const results = await getFollowers(igUser, { igUser, igPass, maxFollowers: 5 });
    ctx.reply('Followers:\n' + results.join('\n'));
  } catch (e) {
    ctx.reply('Followers error: ' + e.message);
  }
});

bot.command('following', async (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  const igUser = config.igUser || process.env.IG_USER;
  const igPass = config.igPass || process.env.IG_PASS;
  if (!igUser || !igPass) return ctx.reply('IG_USER and IG_PASS not set.');
  ctx.reply('Getting following...');
  try {
    const results = await getFollowing(igUser, { igUser, igPass, maxFollowing: 5 });
    ctx.reply('Following:\n' + results.join('\n'));
  } catch (e) {
    ctx.reply('Following error: ' + e.message);
  }
});

bot.command('hashtag', async (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  const args = ctx.message.text.split(' ');
  const tag = args[1] || 'nature';
  ctx.reply('Searching hashtag #' + tag + '...');
  try {
    const results = await searchHashtag(tag, { maxPosts: 5 });
    ctx.reply('Hashtag results:\n' + results.join('\n'));
  } catch (e) {
    ctx.reply('Hashtag error: ' + e.message);
  }
});

bot.command('checkmail', async (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  const args = ctx.message.text.split(' ');
  const email = args[1] || config.email;
  if (!email) return ctx.reply('No email provided.');
  ctx.reply('Checking email: ' + email);
  try {
    const isValid = await checkMail(email);
    ctx.reply(`Email ${email} valid: ${isValid}`);
  } catch (e) {
    ctx.reply('Email check error: ' + e.message);
  }
});

bot.command('uuid', (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  ctx.reply('UUID: ' + utils.getUUID());
});

bot.command('guid', (ctx) => {
  if (!isAuthorized(ctx)) return ctx.reply('Unauthorized.');
  ctx.reply('GUID: ' + utils.getGUID());
});

function launchBot() {
  bot.launch();
  console.log('Telegram bot started.');
}

module.exports = { bot, launchBot };