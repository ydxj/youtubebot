// Status check logic (replacement for PHP status function)
// In Node.js, you can check running processes or use a lock file
const fs = require('fs');

function status(forId) {
  // Example: check if a process lock file exists
  try {
    const pid = fs.readFileSync(`mails${forId}.pid`, 'utf8');
    process.kill(Number(pid), 0); // throws if not running
    return 'Running.';
  } catch (e) {
    return 'Stopped.';
  }
}

module.exports = { status };