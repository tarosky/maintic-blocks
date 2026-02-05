const { spawn } = require('child_process');
const { globSync } = require('glob');
const path = require('path');
const fs = require('fs');

const pluginsDir = path.join(__dirname, '..', 'plugins');

if (!fs.existsSync(pluginsDir)) {
	console.log('plugins/ directory does not exist. Nothing to watch.');
	process.exit(0);
}

const blockJsonFiles = globSync('*/src/block.json', { cwd: pluginsDir });

if (blockJsonFiles.length === 0) {
	console.log('No plugins found in plugins/ directory.');
	process.exit(0);
}

const pluginDirs = [...new Set(blockJsonFiles.map((f) => f.split('/')[0]))];

console.log(`Watching ${pluginDirs.length} plugin(s):\n`);

const processes = [];

for (const pluginDir of pluginDirs) {
	const fullPath = path.join(pluginsDir, pluginDir);
	console.log(`Starting watch: ${pluginDir}`);

	const child = spawn('npx', ['wp-scripts', 'start'], {
		cwd: fullPath,
		stdio: 'inherit',
		shell: true,
	});

	child.on('error', (err) => {
		console.error(`Error in ${pluginDir}:`, err);
	});

	processes.push(child);
}

process.on('SIGINT', () => {
	console.log('\nStopping all watchers...');
	processes.forEach((p) => p.kill());
	process.exit(0);
});

process.on('SIGTERM', () => {
	processes.forEach((p) => p.kill());
	process.exit(0);
});
