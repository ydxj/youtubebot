# youtubebot - Instagram Account Checker

youtubebot is a PHP tool to verify the validity of Instagram accounts, check email vulnerabilities, and manage Instagram business account checks. It supports Telegram bot integration for notifications and control.

## Features

- **Email Availability Check:**
   - Checks if the email address associated with an Instagram account is valid and available.
   - Supports Gmail, Yahoo, Hotmail, Mail.ru, and more.
- **Telegram Bot Integration:**
   - Control and receive notifications via Telegram.
- **User Management:**
   - Add, remove, and manage Instagram accounts for checking.
- **Automated Collection:**
   - Collect users from search, hashtags, followers, following, and explore.

## Recent Updates (2025)

### Instagram API Modernization (January 2025)
- **Updated Instagram User-Agent**: Upgraded from Instagram 27.0 (2021) to Instagram 316.0 (2025) for better compatibility
- **Modern API Headers**: Added x-ig-app-id and updated x-ig-capabilities headers
- **Enhanced Authentication**: Implemented encrypted password format and modern login parameters
- **API Signature**: Updated signature key format for 2025 Instagram API requirements
- **Better Error Handling**: Added comprehensive error logging and challenge detection
- **Connection Headers**: Updated X-IG-Connection-Type and Accept-Language headers

### Previous Updates
- Fixed PHP errors and warnings (undefined array keys, variable initialization)
- Improved compatibility for Windows (no Linux-only commands required)
- Enhanced error handling for missing files and configuration keys
- Refactored code for better maintainability and reliability

### What Was Updated
The following files were modernized for 2025 Instagram API compatibility:
- `index.php` - Core Instagram API class with modern headers and authentication
- `bot.php` - Telegram bot integration with updated Instagram connection
- `explore.php` - Explore feature with modern API headers
- `hashtag.php` - Hashtag search with updated capabilities
- All Instagram API calls now use 2025-compatible headers and user agents

### Known Limitations
- Instagram frequently changes their API - if you encounter errors, the API may have changed again
- Two-factor authentication and challenge-required accounts need manual verification
- Rate limiting may apply - use delays between requests to avoid blocks

## Installation & Usage

### Requirements
- PHP 7.4 or newer
- cURL extension enabled
- Telegram bot token and your Telegram user ID

### Steps
1. **Clone the repository:**
    ```bash
    git clone https://github.com/ydxj/youtubebot/
    ```
2. **Navigate to the project directory:**
    ```bash
    cd youtubebot
    ```
3. **Run the tool:**
    ```bash
    php bot.php
    ```
    - On first run, you will be prompted for your Telegram bot token and user ID.

### Notes
- The tool now works on Windows and Linux.
- All major PHP warnings and errors have been fixed.
- If you see any file or config errors, ensure you have run the tool at least once to generate the required files.

## Telegram Setup
- Create a Telegram bot via [BotFather](https://t.me/BotFather) and get the token.
- Get your Telegram user ID (e.g., via [userinfobot](https://t.me/userinfobot)).
- Enter these when prompted on first run.

## Enjoy!
