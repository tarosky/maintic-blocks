import { execSync } from 'child_process';
import { globSync } from 'glob';
import { existsSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const rootDir = join( __dirname, '..' );
const pluginsDir = join( rootDir, 'plugins' );

if ( ! existsSync( pluginsDir ) ) {
	console.log( 'plugins/ directory does not exist. Nothing to build.' );
	process.exit( 0 );
}

const blockJsonFiles = globSync( '*/src/block.json', { cwd: pluginsDir } );

if ( blockJsonFiles.length === 0 ) {
	console.log( 'No plugins found in plugins/ directory.' );
	process.exit( 0 );
}

const pluginDirs = [ ...new Set( blockJsonFiles.map( ( f ) => f.split( '/' )[ 0 ] ) ) ];

console.log( `Found ${ pluginDirs.length } plugin(s) to build:\n` );

let hasError = false;

for ( const pluginDir of pluginDirs ) {
	console.log( `Building: ${ pluginDir }` );

	const sourcePath = `plugins/${ pluginDir }/src`;
	const outputPath = `plugins/${ pluginDir }/build`;

	try {
		execSync(
			`npx wp-scripts build --source-path=${ sourcePath } --output-path=${ outputPath }`,
			{
				cwd: rootDir,
				stdio: 'inherit',
			}
		);
		console.log( `✓ ${ pluginDir } built successfully\n` );
	} catch ( error ) {
		console.error( `✗ ${ pluginDir } build failed\n` );
		hasError = true;
	}
}

if ( hasError ) {
	console.error( '\nSome builds failed.' );
	process.exit( 1 );
}

console.log( '\nAll builds completed successfully.' );
