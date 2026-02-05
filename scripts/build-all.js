const { execSync } = require('child_process');
const { globSync } = require('glob');
const path = require('path');
const fs = require('fs');

const pluginsDir = path.join(__dirname, '..', 'plugins');

if (!fs.existsSync(pluginsDir)) {
	console.log('plugins/ directory does not exist. Nothing to build.');
	process.exit(0);
}

const blockJsonFiles = globSync('*/src/block.json', { cwd: pluginsDir });

if (blockJsonFiles.length === 0) {
	console.log('No plugins found in plugins/ directory.');
	process.exit(0);
}

const pluginDirs = [...new Set(blockJsonFiles.map((f) => f.split('/')[0]))];

console.log(`Found ${pluginDirs.length} plugin(s) to build:\n`);

let hasError = false;

for (const pluginDir of pluginDirs) {
	const fullPath = path.join(pluginsDir, pluginDir);
	console.log(`Building: ${pluginDir}`);

	try {
		execSync('npx wp-scripts build', {
			cwd: fullPath,
			stdio: 'inherit',
		});
		console.log(`✓ ${pluginDir} built successfully\n`);
	} catch (error) {
		console.error(`✗ ${pluginDir} build failed\n`);
		hasError = true;
	}
}

if (hasError) {
	console.error('\nSome builds failed.');
	process.exit(1);
}

console.log('\nAll builds completed successfully.');
