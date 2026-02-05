import { spawn } from 'child_process';
import { globSync } from 'glob';
import { existsSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const rootDir = join( __dirname, '..' );
const pluginsDir = join( rootDir, 'plugins' );

if ( ! existsSync( pluginsDir ) ) {
	console.log( 'plugins/ directory does not exist. Nothing to watch.' );
	process.exit( 0 );
}

const blockJsonFiles = globSync( '*/src/block.json', { cwd: pluginsDir } );

if ( blockJsonFiles.length === 0 ) {
	console.log( 'No plugins found in plugins/ directory.' );
	process.exit( 0 );
}

const pluginDirs = [ ...new Set( blockJsonFiles.map( ( f ) => f.split( '/' )[ 0 ] ) ) ];

console.log( `Watching ${ pluginDirs.length } plugin(s):\n` );

const processes = [];

for ( const pluginDir of pluginDirs ) {
	console.log( `Starting watch: ${ pluginDir }` );

	const sourcePath = `plugins/${ pluginDir }/src`;
	const outputPath = `plugins/${ pluginDir }/build`;

	const child = spawn(
		'npx',
		[
			'wp-scripts',
			'start',
			`--source-path=${ sourcePath }`,
			`--output-path=${ outputPath }`,
		],
		{
			cwd: rootDir,
			stdio: 'inherit',
			shell: true,
		}
	);

	child.on( 'error', ( err ) => {
		console.error( `Error in ${ pluginDir }:`, err );
	} );

	processes.push( child );
}

process.on( 'SIGINT', () => {
	console.log( '\nStopping all watchers...' );
	processes.forEach( ( p ) => p.kill() );
	process.exit( 0 );
} );

process.on( 'SIGTERM', () => {
	processes.forEach( ( p ) => p.kill() );
	process.exit( 0 );
} );
