<?php // phpcs:ignore

namespace SEOPress\Actions\Abilities;

defined( 'ABSPATH' ) || exit;

use SEOPress\Actions\Api\McpConnections;

/**
 * Everything the MCP settings tab displays, computed in PHP.
 *
 * The tab is a renderer: the endpoint URL, the ready-to-paste client snippets,
 * the list of abilities that would actually be served, the readiness checks and
 * the one-line summary the collapsed Diagnostics panel shows are all built here
 * so they can be tested, rather than assembled in JSX where nothing can assert
 * on them.
 *
 * The one thing that is deliberately not here is the Application Password. The
 * tab asks WordPress core for it over core's own route, and core answers the
 * browser directly: nothing in SEOPress ever receives, stores or logs it.
 *
 * @since 10.3.0
 */
class McpSettingsData {

	/**
	 * A check that passed.
	 *
	 * @var string
	 */
	/**
	 * The name every configuration gives this server.
	 *
	 * The clients show the key their own configuration was written with, not
	 * the title this server answers with, so a lowercase key here is what a
	 * reader ends up seeing in their tool list forever. One constant, because
	 * four snippets naming the same server differently is how they drift.
	 *
	 * @since 10.3.0
	 * @var string
	 */
	const SERVER_KEY = 'SEOPress';

	const STATUS_OK = 'ok';

	/**
	 * A check that found something worth saying, but not a blocker.
	 *
	 * @var string
	 */
	const STATUS_WARNING = 'warning';

	/**
	 * A check that found something no client can work around.
	 *
	 * @var string
	 */
	const STATUS_FAIL = 'fail';

	/**
	 * A check that could not run because something it depends on failed first.
	 *
	 * Reported rather than silently passed: "not tested" and "tested and fine"
	 * must not look the same.
	 *
	 * @var string
	 */
	const STATUS_SKIPPED = 'skipped';

	/**
	 * A check whose answer is expected for the way this site is set up.
	 *
	 * OAuth switched off on purpose, for instance, is not something to fix on a
	 * site that connects with an Application Password. Shown so the reader
	 * knows, but not counted as needing attention: a red or amber line there
	 * sent people looking for a problem they did not have.
	 *
	 * @since 10.4.0
	 * @var string
	 */
	const STATUS_INFO = 'info';

	/**
	 * WordPress version that introduced the Abilities API.
	 *
	 * @var string
	 */
	const ABILITIES_API_WP_VERSION = '6.9';

	/**
	 * The token the client snippets carry in place of the Basic credential.
	 *
	 * The tab swaps it for the real Base64 in the browser once an Application
	 * Password has been created, so a finished line can be copied. The password
	 * itself never comes back to PHP.
	 *
	 * @var string
	 */
	const AUTH_PLACEHOLDER = 'BASE64_OF_USERNAME_COLON_APPLICATION_PASSWORD';

	/**
	 * The name the "Create" button pre-fills for a new Application Password.
	 *
	 * Deliberately not translated: it is stored on the user profile and has to
	 * stay recognisable whatever locale the site later switches to.
	 *
	 * @var string
	 */
	const APPLICATION_PASSWORD_NAME = 'SEOPress MCP';

	/**
	 * The WordPress core route that creates an Application Password.
	 *
	 * Core already returns the new password once, in the "password" field of its
	 * own response, so SEOPress calls that route from the browser rather than
	 * reimplementing it behind an endpoint of its own.
	 *
	 * @var string
	 */
	const APPLICATION_PASSWORDS_REST_PATH = '/wp/v2/users/me/application-passwords';

	/**
	 * The whole payload behind the MCP tab.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function get_data() {
		// The checks run first so the walkthrough can follow what they found:
		// a discovery document the host never lets through to WordPress makes
		// OAuth unusable, whatever McpOauth::is_enabled() says.
		$diagnostics = self::diagnostics();

		return array(
			'endpoint'            => self::endpoint(),
			'steps'               => self::steps( $diagnostics ),
			'connection'          => self::connection(),
			'oauth'               => self::oauth(),
			'tools'               => self::tools(),
			'diagnostics'         => $diagnostics,
			'diagnostics_summary' => self::diagnostics_summary( $diagnostics ),
			'troubleshooting'     => self::troubleshooting(),
		);
	}

	/**
	 * The guided walkthrough, from an empty client to a working connection.
	 *
	 * The tab renders these in order and numbers them from the "number" key, so
	 * the wording, the ordering and the per-client branching are all asserted
	 * here rather than being spread across JSX.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Takes the readiness checks that already ran.
	 *
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return array[]
	 */
	public static function steps( $checks = array() ) {
		// Creating an Application Password used to be step 1, for everyone.
		// Then three of the four clients turned out never to want one: they
		// sign in over OAuth from the consent screen. Leaving it first sent
		// most readers to make a credential their client ignores, and left
		// them wondering why nothing ever asked for it. It now belongs to the
		// one client that does need it, inside that client's own branch.
		return array(
			array(
				'id'            => 'mcp.step.client',
				'number'        => 1,
				'title'         => __( 'Set up your AI client', 'wp-seopress' ),
				'summary'       => __( 'Choose the client you use. Each one is set up differently, so only the instructions for the one you choose are shown.', 'wp-seopress' ),
				'instructions'  => array(),
				'snippet_state' => self::snippet_states(),
				'clients'       => self::clients( $checks ),
			),
			array(
				'id'           => 'mcp.step.verify',
				'number'       => 2,
				'title'        => __( 'Check that it works', 'wp-seopress' ),
				'summary'      => __( 'Confirm the client can see this site before you rely on it, so a silent misconfiguration does not look like an empty site.', 'wp-seopress' ),
				'instructions' => array(
					__( 'Ask your client which tools it has. It should list the tools shown under "Exposed tools" further down this page.', 'wp-seopress' ),
					__( 'If nothing appears, run the readiness checks in the Diagnostics panel. Every amber or red line says what to change on this site. A blue line is expected for the way this site is set up.', 'wp-seopress' ),
				),
			),
		);
	}

	/**
	 * The two states step 2 tells apart, honestly, in this account's name.
	 *
	 * Before an Application Password exists the configurations carry a
	 * placeholder, and after one is created they carry the real credential. The
	 * tab says which of the two it is showing, in this account's name, so a
	 * half-finished snippet is never presented as ready to paste.
	 *
	 * @since 10.3.0
	 *
	 * @return array {
	 *     @type array $pending Shown while no password has been created.
	 *     @type array $ready   Shown once one has.
	 * }
	 */
	public static function snippet_states() {
		$username = self::current_username();

		return array(
			// "info", not "warning": this is the state every reader starts in,
			// not a fault, and the amber notice was read as one.
			'pending' => array(
				'status'  => 'info',
				'message' => sprintf(
					/* translators: %s: the WordPress user name the configurations are written for. */
					__( 'Expected until you create an Application Password on this page: the configuration below carries a placeholder instead of a credential for "%s". Create one below and it fills itself in.', 'wp-seopress' ),
					$username
				),
			),
			'ready'   => array(
				'status'  => 'success',
				'message' => sprintf(
					/* translators: %s: the WordPress user name the configurations are written for. */
					__( 'The configurations below carry the Application Password you just created, for the account "%s". Copy one and paste it into your client: nothing is left to replace.', 'wp-seopress' ),
					$username
				),
			),
		);
	}

	/**
	 * Whether an MCP client can sign in to this site over OAuth 2.1.
	 *
	 * Browser clients only ever connect that way, so the browser branch of step
	 * 2 switches from "not possible here" to the connector flow exactly when
	 * this is true. Both halves have to hold: the OAuth server able to run, and
	 * a scheme it is allowed to run over, because a connector offered on a site
	 * that answers over plain HTTP would fail after the reader had pasted the
	 * URL rather than before.
	 *
	 * A third half only shows from outside: the discovery document actually
	 * reaching WordPress. Some hosts answer /.well-known/ themselves, before
	 * PHP runs, and OAuth then cannot work however it is configured here. So
	 * when the readiness checks ran and that one failed, the walkthrough falls
	 * back to the Application Password instead of offering a connector that
	 * would fail at sign-in.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Takes the readiness checks that already ran.
	 *
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return bool
	 */
	public static function oauth_supported( $checks = array() ) {
		return McpOauth::is_enabled() && McpOauth::transport_is_secure() && ! self::discovery_failed( $checks );
	}

	/**
	 * Whether the readiness run found the OAuth discovery document unreachable.
	 *
	 * Only a failure that came back as an HTTP answer counts: the document was
	 * fetched, and something other than WordPress's JSON came back. A site that
	 * cannot call itself at all fails the same check with a transport error,
	 * but that says nothing about what outside clients get, so it must not take
	 * the OAuth setup away from them. A skipped check means OAuth was not
	 * running in the first place, which oauth_supported() already knows.
	 *
	 * @since 10.4.0
	 *
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return bool
	 */
	public static function discovery_failed( $checks ) {
		foreach ( (array) $checks as $check ) {
			if ( isset( $check['id'], $check['status'] ) && 'mcp.oauth_discovery' === $check['id'] ) {
				// check_oauth_discovery() writes "HTTP <code>" as the detail of
				// an answer, and the transport error otherwise.
				$detail = isset( $check['detail'] ) ? (string) $check['detail'] : '';

				return self::STATUS_FAIL === $check['status'] && 0 === strpos( $detail, 'HTTP ' );
			}
		}

		return false;
	}

	/**
	 * Whether the OAuth server is off on purpose rather than unable to run.
	 *
	 * Everything it depends on is there, so the only thing left that turns it
	 * off is the seopress_mcp_oauth_enabled filter. That is how a site that
	 * connects with Application Passwords only is set up, and the checks treat
	 * it as such instead of as a fault.
	 *
	 * @since 10.4.0
	 *
	 * @return bool
	 */
	public static function oauth_switched_off() {
		return seopress_abilities_api_available()
			&& seopress_abilities_api_rest_enabled()
			&& function_exists( 'wp_fast_hash' )
			&& function_exists( 'wp_verify_fast_hash' )
			&& ! McpOauth::is_enabled();
	}

	/**
	 * Every key a client branch carries, so the tab never meets a missing one.
	 *
	 * @since 10.4.0
	 *
	 * @param array $fields The keys this client sets.
	 *
	 * @return array
	 */
	protected static function client( $fields ) {
		return array_merge(
			array(
				'id'                          => '',
				'label'                       => '',
				'kind'                        => '',
				'summary'                     => '',
				'auth'                        => '',
				'notes'                       => array(),
				'snippet'                     => '',
				'snippet_hint'                => '',
				'extra_snippet'               => '',
				'extra_hint'                  => '',
				'fallback_snippet'            => '',
				'fallback_hint'               => '',
				'fallback_powershell_snippet' => '',
				'fallback_powershell_hint'    => '',
				'instructions'                => array(),
				'locations'                   => array(),
				'sections'                    => array(),
			),
			$fields
		);
	}

	/**
	 * The clients step 2 branches between, in the order they are offered.
	 *
	 * Each client says what kind of thing it is set up with, how it signs in,
	 * which snippet it needs, where its configuration file lives and what to do
	 * with it. They genuinely differ, so stacking all four sets of instructions
	 * would leave the reader to work out which paragraph is theirs.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Takes the readiness checks that already ran, and every
	 *               client says how it signs in.
	 *
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return array[] {
	 *     @type string   $id                          Stable, untranslated identifier.
	 *     @type string   $label                       The name shown on the client's tab.
	 *     @type string   $kind                        One of "command", "file", "oauth", "unavailable".
	 *     @type string   $summary                     One sentence saying what this client is.
	 *     @type string   $auth                        How it signs in, and what that needs.
	 *     @type string[] $notes                       Why part of it cannot connect, when that is so.
	 *     @type string   $snippet                     Which connection snippet it needs, empty when none.
	 *     @type string   $fallback_snippet            A helper shown while no password exists, empty when none.
	 *     @type string   $fallback_hint               What that helper is for.
	 *     @type string   $fallback_powershell_snippet The same helper for Windows, empty when none.
	 *     @type string   $fallback_powershell_hint    What that one is for.
	 *     @type string[] $instructions                What to do, in order.
	 *     @type array[]  $locations                   Where its configuration file lives.
	 *     @type array[]  $sections                    Apps of the same vendor set up apart, each with its own steps.
	 * }
	 */
	public static function clients( $checks = array() ) {
		$oauth = self::oauth_supported( $checks );

		return array(
			self::claude_client( $oauth, $checks ),
			self::browser_client( 'chatgpt', $oauth, $checks ),
			self::cursor_client( $oauth ),
		);
	}

	/**
	 * The Cursor branch.
	 *
	 * Cursor reads a file rather than taking a URL in a dialog. It signs itself
	 * in over OAuth like the rest when it can, and it runs on the reader's own
	 * machine, so when OAuth cannot run here it sends an Application Password
	 * in a header instead of being handed a block that cannot sign in.
	 *
	 * @since 10.4.0
	 *
	 * @param bool $oauth Whether a client can sign in over OAuth here.
	 *
	 * @return array One client, shaped like the others.
	 */
	protected static function cursor_client( $oauth ) {
		$cursor = array(
			'id'        => 'cursor',
			'label'     => 'Cursor',
			'kind'      => 'file',
			'locations' => array(
				array(
					'label' => __( 'This project only', 'wp-seopress' ),
					'path'  => '.cursor/mcp.json',
				),
				array(
					'label' => __( 'Every project', 'wp-seopress' ),
					'path'  => '~/.cursor/mcp.json',
				),
			),
		);

		if ( $oauth ) {
			return self::client(
				array_merge(
					$cursor,
					array(
						'summary'      => __( 'A code editor that reads its MCP servers from a JSON file you edit yourself. It signs you in over OAuth the first time it uses the server, so there is nothing to create beforehand.', 'wp-seopress' ),
						'auth'         => __( 'OAuth, from your own machine, in your browser. Nothing to create beforehand.', 'wp-seopress' ),
						'snippet'      => 'json_config_oauth',
						'snippet_hint' => __( 'Paste this into the file above.', 'wp-seopress' ),
						'instructions' => array(
							__( 'Open, or create, one of the configuration files listed below.', 'wp-seopress' ),
							__( 'Paste the block into it. If the file already has an "mcpServers" object, add the "SEOPress" entry inside it instead of replacing the file.', 'wp-seopress' ),
							__( 'Restart Cursor, open Settings, Customize, MCPs, then Authenticate next to SEOPress and approve the connection here.', 'wp-seopress' ),
						),
					)
				)
			);
		}

		return self::client(
			array_merge(
				$cursor,
				array(
					'summary'                     => __( 'A code editor that reads its MCP servers from a JSON file you edit yourself. This site cannot sign it in over OAuth right now, so it sends an Application Password instead, which you create right below.', 'wp-seopress' ),
					'auth'                        => __( 'Application Password, sent from your own machine in a header. Create one below and the block fills itself in.', 'wp-seopress' ),
					'snippet'                     => 'json_config',
					'snippet_hint'                => __( 'Paste this into the file above.', 'wp-seopress' ),
					'fallback_snippet'            => 'base64_command',
					'fallback_hint'               => __( 'Already created an Application Password from your profile instead? On macOS or Linux, run this in a terminal to get the Base64 that replaces the placeholder above.', 'wp-seopress' ),
					'fallback_powershell_snippet' => 'base64_powershell',
					'fallback_powershell_hint'    => __( 'On Windows, run this in PowerShell instead.', 'wp-seopress' ),
					'instructions'                => array(
						__( 'Open, or create, one of the configuration files listed below.', 'wp-seopress' ),
						__( 'Paste the block into it. If the file already has an "mcpServers" object, add the "SEOPress" entry inside it instead of replacing the file.', 'wp-seopress' ),
						__( 'Restart Cursor and open Settings, Customize, MCPs: SEOPress should be listed with its tools.', 'wp-seopress' ),
					),
				)
			)
		);
	}

	/**
	 * Where the Claude desktop app keeps its configuration file.
	 *
	 * @since 10.4.0
	 *
	 * @return array[]
	 */
	protected static function claude_desktop_locations() {
		return array(
			array(
				'label' => 'macOS',
				'path'  => '~/Library/Application Support/Claude/claude_desktop_config.json',
			),
			array(
				'label' => 'Windows',
				'path'  => '%APPDATA%\\Claude\\claude_desktop_config.json',
			),
			array(
				'label' => 'Linux',
				'path'  => '~/.config/Claude/claude_desktop_config.json',
			),
		);
	}

	/**
	 * The one tab every Claude client shares, in two titled blocks.
	 *
	 * Claude Code, the desktop app and claude.ai all take the endpoint URL and
	 * sign in over OAuth when this site can offer it, so they share one tab.
	 * Where they differ is where the URL goes, and readers who had never heard
	 * of Claude Code could not tell which of the two snippets was theirs. So
	 * the tab is split into one block per kind of app, each saying what it is
	 * and carrying its own steps.
	 *
	 * When OAuth cannot run, claude.ai and the desktop app's Connectors screen
	 * are out of reach, because both connect from Anthropic's servers. The two
	 * apps that run on the reader's machine are not: Claude Code sends an
	 * Application Password itself, and the desktop app does through the
	 * mcp-remote bridge, its configuration file only starting local servers.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Split into a desktop app block and a Claude Code block.
	 *
	 * @param bool    $oauth  Whether a client can sign in over OAuth here.
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return array One client, shaped like the others.
	 */
	protected static function claude_client( $oauth, $checks = array() ) {
		if ( $oauth ) {
			return self::client(
				array(
					'id'       => 'claude',
					'label'    => 'Claude',
					'kind'     => 'oauth',
					'summary'  => __( 'Claude comes as a chat app, on claude.ai or installed on your computer, and as Claude Code in a terminal. Both connect with this URL and sign in here in your browser, but from different places: follow the block for the one you use.', 'wp-seopress' ),
					'auth'     => __( 'OAuth only, nothing to create beforehand. claude.ai and the desktop app\'s Connectors screen connect from Anthropic\'s servers, so this site needs a public HTTPS address for them. Claude Code signs in from your own machine.', 'wp-seopress' ),
					'sections' => array(
						array(
							'id'           => 'claude-desktop',
							'title'        => __( 'claude.ai and the Claude desktop app', 'wp-seopress' ),
							'summary'      => __( 'The chat app, in your browser or installed on your computer. Both add this site from the same Connectors screen.', 'wp-seopress' ),
							'instructions' => array(
								__( 'Open Settings, Connectors, then Add custom connector, and paste the URL below. Leave every other field empty: this site registers the client by itself and tells it where to sign in.', 'wp-seopress' ),
								__( 'Sign in to this site when your browser is sent here, then choose whether this connection may only read or may also change data. It works with your own WordPress permissions, and nothing else.', 'wp-seopress' ),
							),
							'snippet'      => 'connect_url',
							'snippet_hint' => __( 'Paste this as the connector URL.', 'wp-seopress' ),
							'locations'    => array(),
						),
						array(
							'id'           => 'claude-code',
							'title'        => __( 'Claude Code (terminal)', 'wp-seopress' ),
							'summary'      => __( 'The command-line tool, run in a terminal or from a code editor.', 'wp-seopress' ),
							'instructions' => array(
								__( 'Run the command below in a terminal.', 'wp-seopress' ),
								__( 'Start Claude Code, type /mcp, choose SEOPress and sign in to this site in the browser window it opens.', 'wp-seopress' ),
							),
							'snippet'      => 'claude_code_oauth',
							'snippet_hint' => __( 'Run this in a terminal.', 'wp-seopress' ),
							'locations'    => array(),
						),
					),
				)
			);
		}

		return self::client(
			array(
				'id'                          => 'claude',
				'label'                       => 'Claude',
				'kind'                        => 'command',
				'summary'                     => __( 'This site cannot sign a client in over OAuth right now, so claude.ai and the desktop app\'s Connectors screen cannot reach it. The Claude desktop app and Claude Code still can, from your own machine, with an Application Password.', 'wp-seopress' ),
				'auth'                        => __( 'Application Password, sent from your own machine. Create one below and both blocks fill themselves in.', 'wp-seopress' ),
				'notes'                       => self::browser_client_blockers( 'claude.ai', $checks ),
				'fallback_snippet'            => 'base64_command',
				'fallback_hint'               => __( 'Already created an Application Password from your profile instead? On macOS or Linux, run this in a terminal to get the Base64 that replaces the placeholder in both blocks.', 'wp-seopress' ),
				'fallback_powershell_snippet' => 'base64_powershell',
				'fallback_powershell_hint'    => __( 'On Windows, run this in PowerShell instead.', 'wp-seopress' ),
				'sections'                    => array(
					array(
						'id'           => 'claude-desktop',
						'title'        => __( 'Claude desktop app', 'wp-seopress' ),
						'summary'      => __( 'The chat app installed on your computer. Its configuration file only starts servers on your own machine, so it reaches this site through mcp-remote, a small bridge that needs Node.js.', 'wp-seopress' ),
						'instructions' => array(
							__( 'Install Node.js, the LTS version from nodejs.org, if this computer does not have it yet. The bridge runs with npx, which comes with it.', 'wp-seopress' ),
							__( 'In the desktop app, open Settings, then Developer, and click Edit Config. On macOS, Settings is in the Claude menu; on Windows, in the menu at the top left of the window, under File. It shows you claude_desktop_config.json, listed below for each system: open it in a text editor.', 'wp-seopress' ),
							__( 'Paste the block below into it. If the file already has an "mcpServers" object, add the "SEOPress" entry inside it instead of replacing the file.', 'wp-seopress' ),
							__( 'Quit the app completely, then open it again: closing its window leaves it running. On macOS, choose Quit Claude in the Claude menu. On Windows, right-click the Claude icon in the system tray, next to the clock, and choose Quit.', 'wp-seopress' ),
							__( 'Open Settings, Developer again: SEOPress should be listed as running. If it shows an error instead, its log, opened from there, says why.', 'wp-seopress' ),
						),
						'snippet'      => 'desktop_config',
						'snippet_hint' => __( 'Paste this into claude_desktop_config.json.', 'wp-seopress' ),
						'locations'    => self::claude_desktop_locations(),
					),
					array(
						'id'           => 'claude-code',
						'title'        => __( 'Claude Code (terminal)', 'wp-seopress' ),
						'summary'      => __( 'The command-line tool, run in a terminal or from a code editor. It sends the Application Password itself, so it needs no bridge.', 'wp-seopress' ),
						'instructions' => array(
							__( 'Run the command below in a terminal.', 'wp-seopress' ),
							__( 'Start Claude Code and type /mcp: SEOPress should be listed as connected.', 'wp-seopress' ),
						),
						'snippet'      => 'claude_code',
						'snippet_hint' => __( 'Run this in a terminal.', 'wp-seopress' ),
						'locations'    => array(),
					),
				),
			)
		);
	}

	/**
	 * What each browser client is called and what its owner has to click.
	 *
	 * Both run on someone else's servers and both sign in over OAuth, but they
	 * are not set up in the same place: ChatGPT hides custom connectors behind
	 * developer mode, and claude.ai does not. Showing one set of instructions
	 * for both would send half the readers looking for a menu they do not have.
	 *
	 * @since 10.3.0
	 *
	 * @return array[] Keyed by client id.
	 */
	protected static function browser_clients() {
		return array(
			'chatgpt' => array(
				'label'        => 'ChatGPT',
				'vendor'       => 'ChatGPT',
				'summary'      => __( 'Added as a custom MCP server. ChatGPT signs you in over OAuth in your browser, so there is nothing to create beforehand.', 'wp-seopress' ),
				// The desktop app and chatgpt.com are not known to reach a
				// site the same way, so only chatgpt.com is said to connect
				// from OpenAI's servers.
				'auth'         => __( 'OAuth only: an Application Password cannot be used. chatgpt.com connects from OpenAI\'s servers, so it needs this site on a public HTTPS address.', 'wp-seopress' ),
				'instructions' => array(
					__( 'In the ChatGPT desktop app, open Settings, then Plugins under Integrations. Click Add, choose Add MCP server, give it a name, choose the Streamable HTTP type, paste the URL below, and save. Leave the bearer token and header fields empty: this site signs the connection in over OAuth by itself. Then click Authenticate next to the server.', 'wp-seopress' ),
					// The button that opens the form is not labelled the same
					// on every account, so the form is described rather than
					// the button.
					__( 'On chatgpt.com, custom MCP servers only show up in developer mode. Open Settings and turn on Developer mode in the security and login section. Then, from the Plugins page, open the form that creates a custom plugin: depending on your account, a + button, or Browse directory then Add. Fill it in with a name and the URL below as the server URL, keep OAuth as the authentication, confirm the risk notice, and create it.', 'wp-seopress' ),
					__( 'Sign in to this site when your browser is sent here, and approve the connection. The connector then works with your own WordPress permissions, and nothing else.', 'wp-seopress' ),
				),
			),
		);
	}

	/**
	 * One browser branch of step 2, which depends on OAuth being served.
	 *
	 * An Application Password cannot reach a client that runs on someone else's
	 * servers, so this branch is the OAuth connector flow when OAuth can run,
	 * and otherwise says which of the halves is missing. "Not implemented",
	 * "implemented, but refused over plain HTTP" and "implemented, but never
	 * reached" are different problems with different fixes, so they must not
	 * be reported with the same sentence.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Takes whether OAuth can run, and the checks that ran.
	 *
	 * @param string  $id     Which browser client to build.
	 * @param bool    $oauth  Whether a client can sign in over OAuth here.
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return array One client, shaped like the others.
	 */
	protected static function browser_client( $id, $oauth, $checks = array() ) {
		$clients = self::browser_clients();
		$client  = $clients[ $id ];

		if ( $oauth ) {
			return self::client(
				array(
					'id'           => $id,
					'label'        => $client['label'],
					'kind'         => 'oauth',
					'summary'      => $client['summary'],
					'auth'         => $client['auth'],
					'snippet'      => 'connect_url',
					'snippet_hint' => __( 'Paste this as the MCP server URL.', 'wp-seopress' ),
					'instructions' => $client['instructions'],
				)
			);
		}

		return self::client(
			array(
				'id'           => $id,
				'label'        => $client['label'],
				'kind'         => 'unavailable',
				'summary'      => __( 'Not possible from this site right now, whatever you paste into the browser.', 'wp-seopress' ),
				'auth'         => $client['auth'],
				'instructions' => self::browser_client_blockers( $client['vendor'], $checks ),
			)
		);
	}

	/**
	 * Why the browser cannot connect, in the order the reader has to fix them.
	 *
	 * @since 10.3.0
	 * @since 10.4.0 Takes the checks that ran, to name an unreachable discovery document.
	 *
	 * @param string  $vendor The name of the client the reader picked.
	 * @param array[] $checks The readiness checks, empty when none ran.
	 *
	 * @return string[]
	 */
	protected static function browser_client_blockers( $vendor, $checks = array() ) {
		$blockers = array(
			sprintf(
				/* translators: %s: the name of the browser client, for instance claude.ai. */
				__( 'A custom connector on %s is opened from that vendor\'s servers, and it signs in over OAuth rather than with an Application Password.', 'wp-seopress' ),
				$vendor
			),
		);

		if ( ! McpOauth::is_enabled() ) {
			$blockers[] = __( 'The OAuth server is not running on this site, so the browser has nothing to sign in against. The Diagnostics panel above says which part is missing.', 'wp-seopress' );
		} elseif ( ! McpOauth::transport_is_secure() ) {
			$blockers[] = sprintf(
				/* translators: %s: the MCP endpoint URL. */
				__( 'OAuth is refused over plain HTTP, and this site is served on %s, so the connector would be turned away. Install a TLS certificate and switch the WordPress Address and Site Address to https.', 'wp-seopress' ),
				McpServer::endpoint_url()
			);
		} elseif ( self::discovery_failed( $checks ) ) {
			$blockers[] = sprintf(
				/* translators: %s: the URL of the OAuth discovery document. */
				__( 'The OAuth discovery document at %s does not reach WordPress, so the connector cannot find out where to sign in. Some hosts answer /.well-known/ addresses themselves, before WordPress runs. The "OAuth discovery document" line in Diagnostics says what to ask your host.', 'wp-seopress' ),
				McpOauth::protected_resource_metadata_url()
			);
		}

		$blockers[] = __( 'Claude Code, the Claude desktop app and Cursor still work in the meantime: all three run on your own machine and can send an Application Password. The Claude and Cursor tabs show how.', 'wp-seopress' );

		return $blockers;
	}

	/**
	 * What to look at when a client fails although every check is green.
	 *
	 * The readiness checks call this site from itself, and a firewall or a CDN
	 * in front of it never sees those calls. claude.ai and ChatGPT come from
	 * their vendor's servers, with no browser, and that is what bot protection
	 * is built to stop. The messages they show then name the server URL or
	 * the registration, never the firewall, so each entry starts from what the
	 * reader actually sees.
	 *
	 * The quoted client messages are left untranslated: they are what the
	 * client shows, and what the reader will search for.
	 *
	 * @since 10.4.0
	 *
	 * @return array[] {
	 *     @type string   $id       Stable, untranslated identifier.
	 *     @type string   $title    The question the reader has.
	 *     @type string[] $symptoms What the client shows, verbatim.
	 *     @type string   $cause    Why it happens.
	 *     @type string[] $steps    What to do, in order.
	 *     @type array[]  $urls     The addresses the steps name, as label and url.
	 * }
	 */
	public static function troubleshooting() {
		return array(
			array(
				'id'       => 'mcp.troubleshooting.firewall',
				'title'    => __( 'claude.ai or ChatGPT is refused while every check is green', 'wp-seopress' ),
				'symptoms' => array(
					'Dynamic client registration failed: registration endpoint returned 403',
					'Some app settings were rejected. Check the server URL or tunnel ID, icon, and authentication settings, then try again.',
				),
				'cause'    => __( 'This site only refuses a registration with HTTP 403 when its MCP server or its OAuth server is switched off, and it then answers with a JSON error, access_denied. Any other 403, usually an HTML page or a challenge, comes from something in front of WordPress: a CDN, a firewall or a security plugin. claude.ai and ChatGPT call the registration, the token and the MCP addresses from their own servers, with no browser, which bot protection treats as a bot. ChatGPT\'s message does not mention registration at all, but this is the usual cause.', 'wp-seopress' ),
				'steps'    => array(
					__( 'Cloudflare: open Security, Events, and look for the addresses below. If Bot Fight Mode challenged them, turn Bot Fight Mode off, under Security, Bots: on the Free plan, a custom WAF rule cannot skip it. On a paid plan, a WAF rule that skips bot protection for these addresses is enough.', 'wp-seopress' ),
					__( 'Another firewall, a security plugin such as Wordfence, or your host\'s firewall: allow GET and POST requests to the addresses below from any IP address, and check its log for requests it blocked when you tried to connect.', 'wp-seopress' ),
					__( 'Then add the server to your client again.', 'wp-seopress' ),
				),
				'urls'     => array(
					array(
						'label' => __( 'Registration', 'wp-seopress' ),
						'url'   => McpOauth::rest_endpoint_url( McpOauth::REGISTER_ROUTE ),
					),
					array(
						'label' => __( 'Token', 'wp-seopress' ),
						'url'   => McpOauth::rest_endpoint_url( McpOauth::TOKEN_ROUTE ),
					),
					array(
						'label' => __( 'MCP server', 'wp-seopress' ),
						'url'   => McpServer::endpoint_url(),
					),
				),
			),
			array(
				'id'       => 'mcp.troubleshooting.discovery',
				'title'    => __( 'claude.ai or ChatGPT finds the site, then cannot sign in', 'wp-seopress' ),
				'symptoms' => array(
					'couldn\'t register with … sign-in service',
				),
				'cause'    => __( 'The client could not read the OAuth discovery documents, so it does not know where to sign in. When the "OAuth discovery document" line in Diagnostics is red, your server does not let /.well-known/ addresses through to WordPress: some hosts answer them themselves, before WordPress runs.', 'wp-seopress' ),
				'steps'    => array(
					__( 'Open the two addresses below in a browser. Both must answer with JSON, not a "not found" page.', 'wp-seopress' ),
					__( 'If they do not, ask your host to pass them through to WordPress.', 'wp-seopress' ),
					__( 'Until then, connect Claude Code, the Claude desktop app or Cursor with an Application Password: their tabs switch to that method by themselves while the check is red.', 'wp-seopress' ),
				),
				'urls'     => array(
					array(
						'label' => __( 'Protected resource metadata', 'wp-seopress' ),
						'url'   => McpOauth::protected_resource_metadata_url(),
					),
					array(
						'label' => __( 'Authorization server metadata', 'wp-seopress' ),
						'url'   => McpOauth::authorization_server_metadata_url(),
					),
				),
			),
			array(
				'id'       => 'mcp.troubleshooting.desktop_config',
				'title'    => __( 'The Claude desktop app does not list the server', 'wp-seopress' ),
				'symptoms' => array(),
				'cause'    => __( 'The desktop app\'s configuration file only accepts servers it starts itself, with a command. An entry with a "url" is not supported, and can make the app drop every server in the file. The configuration in the Claude tab starts the mcp-remote bridge instead, which needs Node.js.', 'wp-seopress' ),
				'steps'    => array(
					__( 'Check that Node.js is installed: in a terminal, npx --version should print a version number. If it does not, install the LTS version from nodejs.org.', 'wp-seopress' ),
					__( 'Quit the app completely and open it again: closing its window leaves it running. On Windows, right-click the Claude icon in the system tray and choose Quit.', 'wp-seopress' ),
					__( 'Open Settings, Developer: the server is listed there with its state, and its log says why it failed.', 'wp-seopress' ),
				),
				'urls'     => array(),
			),
			array(
				'id'       => 'mcp.troubleshooting.credential',
				'title'    => __( 'Claude Code, the desktop app or Cursor is refused with HTTP 401', 'wp-seopress' ),
				'symptoms' => array(),
				'cause'    => __( 'The Authorization header has to carry "Basic", a space, then the Base64 of your user name, a colon and the Application Password. A placeholder left in the configuration, another account\'s user name, or a revoked password is refused.', 'wp-seopress' ),
				'steps'    => array(
					__( 'Create a new Application Password in step 1: the configurations fill themselves in with it.', 'wp-seopress' ),
					__( 'Or run the Base64 helper for your system, shown under the configuration, and paste its output in place of the placeholder.', 'wp-seopress' ),
				),
				'urls'     => array(),
			),
		);
	}

	/**
	 * The endpoint URL, and whether it is the pretty or the query string form.
	 *
	 * rest_url() already falls back to the ?rest_route= form when permalinks are
	 * plain, which is the URL a client has to be given on such a site.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function endpoint() {
		return array(
			'url'               => McpServer::endpoint_url(),
			'pretty_url'        => self::pretty_endpoint_url(),
			'pretty_permalinks' => self::uses_pretty_permalinks(),
			'secure'            => self::endpoint_is_https(),
		);
	}

	/**
	 * Whether this site rewrites permalinks.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function uses_pretty_permalinks() {
		return '' !== (string) get_option( 'permalink_structure' );
	}

	/**
	 * The endpoint URL this site would serve with pretty permalinks on.
	 *
	 * Shown next to the query string URL so the gain from switching is visible.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function pretty_endpoint_url() {
		return trailingslashit( home_url() ) . rest_get_url_prefix() . '/' . McpServer::REST_NAMESPACE . McpServer::REST_ROUTE;
	}

	/**
	 * Whether the endpoint URL is served over HTTPS.
	 *
	 * Read off the endpoint URL rather than is_ssl(): what matters is the scheme
	 * the client will be given, not the scheme of the admin request.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function endpoint_is_https() {
		$scheme = wp_parse_url( McpServer::endpoint_url(), PHP_URL_SCHEME );

		return 'https' === strtolower( (string) $scheme );
	}

	/**
	 * Ready-to-paste client configuration, built with this site's real URL.
	 *
	 * No credential is generated, held, stored or logged here. The snippets
	 * carry a placeholder: either the shell expression that encodes the password
	 * in the terminal, or the token the browser rewrites once WordPress core has
	 * handed it a new Application Password. Either way the password never
	 * travels through PHP.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function connection() {
		$url      = McpServer::endpoint_url();
		$username = self::current_username();

		return array(
			'url'                       => $url,
			'username'                  => $username,
			'auth_placeholder'          => self::AUTH_PLACEHOLDER,
			'claude_code_oauth'         => self::claude_code_oauth_command(),
			'claude_code_command'       => self::claude_code_command( self::shell_base64_expression( $username ) ),
			'claude_code_template'      => self::claude_code_command( self::AUTH_PLACEHOLDER ),
			'json_config'               => self::json_config( self::AUTH_PLACEHOLDER ),
			'json_config_oauth'         => self::json_config_oauth(),
			'desktop_config'            => self::desktop_config( self::AUTH_PLACEHOLDER ),
			'base64_command'            => self::base64_command( $username ),
			'base64_powershell'         => self::base64_powershell_command( $username ),
			'application_passwords_url' => admin_url( 'profile.php' ) . '#application-passwords',
			'application_passwords'     => self::application_passwords(),
		);
	}

	/**
	 * The login the snippets are written for.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function current_username() {
		$user = wp_get_current_user();

		return ( $user instanceof \WP_User && '' !== $user->user_login ) ? $user->user_login : 'USERNAME';
	}

	/**
	 * The "claude mcp add" line, with no credential in it.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	public static function claude_code_oauth_command() {
		// --scope user, because the default scope is the directory the command
		// happened to be run in. A site is not a directory: someone who adds
		// SEOPress from one project and then opens another finds it gone, and
		// the server does not even appear in that session's /mcp list, which
		// reads as a broken connection rather than a missing scope.
		return sprintf(
			'claude mcp add --transport http --scope user %s "%s"',
			self::SERVER_KEY,
			McpServer::endpoint_url()
		);
	}

	/**
	 * The "claude mcp add" line that carries a Basic credential.
	 *
	 * Only needed on a site where OAuth cannot run, since Claude Code signs in
	 * over OAuth like the rest of the Claude clients when it can.
	 *
	 * @since 10.3.0
	 *
	 * @param string $authorization The Base64 credential, or the placeholder.
	 *
	 * @return string
	 */
	public static function claude_code_command( $authorization ) {
		// --scope user for the same reason as the OAuth line: without it the
		// server only exists in the directory the command was run in.
		return sprintf(
			'claude mcp add --transport http --scope user %1$s "%2$s" --header "Authorization: Basic %3$s"',
			self::SERVER_KEY,
			McpServer::endpoint_url(),
			$authorization
		);
	}

	/**
	 * A shell expression that encodes the password the site owner types.
	 *
	 * The encoding happens in the terminal, so the password never travels
	 * through WordPress when this form of the command is used.
	 *
	 * @since 10.3.0
	 *
	 * @param string $username The login to encode alongside the password.
	 *
	 * @return string
	 */
	public static function shell_base64_expression( $username ) {
		return sprintf(
			'$(printf \'%%s\' \'%s:YOUR APPLICATION PASSWORD\' | base64)',
			$username
		);
	}

	/**
	 * The stand-alone helper that prints the Base64 to paste by hand.
	 *
	 * @since 10.3.0
	 *
	 * @param string $username The login to encode alongside the password.
	 *
	 * @return string
	 */
	public static function base64_command( $username ) {
		// It prompts rather than carrying a placeholder to replace. A command
		// that is meant to be copied and run, with a hole in the middle, gets
		// run with the hole still in it: the encoded result looks perfectly
		// valid and WordPress answers "invalid application password", which
		// says nothing about what went wrong. Reading it here also keeps the
		// password out of the shell history.
		return sprintf(
			'read -rsp \'Application password: \' SEOPRESS_PW && printf \'%%s\' "%s:$SEOPRESS_PW" | base64 && unset SEOPRESS_PW',
			$username
		);
	}

	/**
	 * The same helper for Windows, where neither read nor base64 exists.
	 *
	 * It prompts for the password for the same reason the bash one does, reads
	 * it as a secure string so it is not echoed, and drops it afterwards.
	 *
	 * @since 10.4.0
	 *
	 * @param string $username The login to encode alongside the password.
	 *
	 * @return string
	 */
	public static function base64_powershell_command( $username ) {
		// Single quotes are literal in PowerShell, and a quote inside one is
		// written twice.
		return sprintf(
			'$pw = Read-Host \'Application password\' -AsSecureString; [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes(\'%s:\' + [Net.NetworkCredential]::new(\'\', $pw).Password)); Remove-Variable pw',
			str_replace( "'", "''", $username )
		);
	}

	/**
	 * The MCP configuration file block, for clients configured from JSON.
	 *
	 * @since 10.3.0
	 *
	 * @param string $authorization What follows "Basic " in the header.
	 *
	 * @return string
	 */
	public static function json_config_oauth() {
		$json = wp_json_encode(
			array(
				'mcpServers' => array(
					self::SERVER_KEY => array(
						'type' => 'http',
						'url'  => McpServer::endpoint_url(),
					),
				),
			),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		return false === $json ? '' : $json;
	}

	/**
	 * The MCP configuration file block, carrying a Basic credential.
	 *
	 * Only needed on a site where OAuth cannot run, and only for a client that
	 * accepts a remote server in its file, such as Cursor. The Claude desktop
	 * app does not: it gets desktop_config() instead.
	 *
	 * @since 10.3.0
	 *
	 * @param string $authorization What follows "Basic " in the header.
	 *
	 * @return string
	 */
	public static function json_config( $authorization ) {
		$json = wp_json_encode(
			array(
				'mcpServers' => array(
					self::SERVER_KEY => array(
						'type'    => 'http',
						'url'     => McpServer::endpoint_url(),
						'headers' => array(
							'Authorization' => 'Basic ' . $authorization,
						),
					),
				),
			),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		return false === $json ? '' : $json;
	}

	/**
	 * The claude_desktop_config.json block, through the mcp-remote bridge.
	 *
	 * The desktop app's configuration file only accepts servers it starts
	 * itself, with a command. An entry with a url is not supported, and was
	 * reported to make the app drop the whole mcpServers section on start, so
	 * the block json_config() builds could never work there. mcp-remote is
	 * the stdio bridge that forwards to a remote server, run with npx.
	 *
	 * The header value goes through an environment variable: written straight
	 * into args, the space after "Basic" splits it into two arguments on
	 * Windows.
	 *
	 * @since 10.4.0
	 *
	 * @param string $authorization What follows "Basic " in the header.
	 *
	 * @return string
	 */
	public static function desktop_config( $authorization ) {
		$args = array( '-y', 'mcp-remote', McpServer::endpoint_url(), '--header', 'Authorization:${AUTH_HEADER}' );

		// mcp-remote refuses a plain HTTP URL unless told to, except on
		// localhost. A site that reached this branch over HTTP is one the
		// reader chose to run that way.
		if ( ! self::endpoint_is_https() ) {
			$args[] = '--allow-http';
		}

		$json = wp_json_encode(
			array(
				'mcpServers' => array(
					self::SERVER_KEY => array(
						'command' => 'npx',
						'args'    => $args,
						'env'     => array(
							'AUTH_HEADER' => 'Basic ' . $authorization,
						),
					),
				),
			),
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		return false === $json ? '' : $json;
	}

	/**
	 * What the tab needs to offer creating an Application Password in place.
	 *
	 * SEOPress asks WordPress core for the password over core's own route and
	 * never sees it: the answer goes straight to the browser, which keeps it in
	 * memory for that page view. Nothing here stores, logs or returns one.
	 *
	 * @since 10.3.0
	 *
	 * @return array {
	 *     @type bool   $available     Whether the button may be offered at all.
	 *     @type string $reason        Why it may not be, empty when it may.
	 *     @type string $default_name  The name the field is pre-filled with.
	 *     @type string $rest_path     The core route that creates one.
	 *     @type string $manage_url    Where existing passwords are revoked.
	 *     @type string $name_required Why the button is disabled on an empty name.
	 *     @type string $error_intro   What to say when core refuses the request.
	 *     @type string $shown_once    The warning beside a password just created.
	 * }
	 */
	public static function application_passwords() {
		$available = self::application_passwords_available();

		return array(
			'available'     => $available,
			'reason'        => $available ? '' : self::application_passwords_unavailable_reason(),
			'default_name'  => self::APPLICATION_PASSWORD_NAME,
			'rest_path'     => self::APPLICATION_PASSWORDS_REST_PATH,
			'manage_url'    => admin_url( 'profile.php' ) . '#application-passwords',
			'name_required' => __( 'Give this password a name before creating it, so you can recognise it on your profile later.', 'wp-seopress' ),
			'error_intro'   => __( 'WordPress refused to create the Application Password, so nothing was created and the configurations below still carry a placeholder.', 'wp-seopress' ),
			'shown_once'    => __( 'This is the only time this password is shown. Copy it now, or leave this page and create another one. SEOPress does not store it, and it is gone as soon as you reload.', 'wp-seopress' ),
		);
	}

	/**
	 * May this account create an Application Password right now.
	 *
	 * Both of core's gates are asked, the site-wide one and the per-user one, so
	 * a button is never offered where core would refuse the request.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function application_passwords_available() {
		if ( ! function_exists( 'wp_is_application_passwords_available' ) || ! wp_is_application_passwords_available() ) {
			return false;
		}

		if ( ! function_exists( 'wp_is_application_passwords_available_for_user' ) ) {
			return false;
		}

		return (bool) wp_is_application_passwords_available_for_user( wp_get_current_user() );
	}

	/**
	 * Why Application Passwords cannot be created, in the site owner's words.
	 *
	 * The site-wide sentence is the one the readiness check shows, so the tab
	 * and the diagnostics never explain the same situation two different ways.
	 *
	 * @since 10.3.0
	 *
	 * @return string Empty when they can be created.
	 */
	public static function application_passwords_unavailable_reason() {
		if ( ! function_exists( 'wp_is_application_passwords_available' ) || ! wp_is_application_passwords_available() ) {
			return __( 'Application Passwords are not available on this site, so no AI client can authenticate. WordPress only offers them over HTTPS, and a security plugin or the wp_is_application_passwords_available filter can switch them off. Serve the site over HTTPS, or re-enable them, then create one from your profile.', 'wp-seopress' );
		}

		if ( ! self::application_passwords_available() ) {
			return __( 'Application Passwords are switched off for this account, so no AI client can authenticate as you. Ask an administrator to allow them for your user, or sign in with an account that still has them, then create one from your profile.', 'wp-seopress' );
		}

		return '';
	}

	/**
	 * Everything the tab needs to offer the browser-based connection.
	 *
	 * The URL a site owner pastes into claude.ai is the MCP endpoint itself:
	 * the client fetches it, is answered 401 with a WWW-Authenticate naming the
	 * resource metadata, and finds its way to the authorization server from
	 * there. The two metadata URLs are shown as well, because when discovery is
	 * what is broken they are the two addresses to open in a browser.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function oauth() {
		return array(
			'enabled'                       => McpOauth::is_enabled(),
			'secure'                        => McpOauth::transport_is_secure(),
			'connect_url'                   => McpServer::endpoint_url(),
			'issuer'                        => McpOauth::issuer(),
			'authorize_url'                 => McpOauthConsent::authorize_url(),
			'protected_resource_metadata'   => McpOauth::protected_resource_metadata_url(),
			'authorization_server_metadata' => McpOauth::authorization_server_metadata_url(),
			'connections'                   => self::connections(),
			'revoke_path'                   => McpConnections::rest_path(),
		);
	}

	/**
	 * The current account's live connections, ready to render.
	 *
	 * @since 10.3.0
	 *
	 * @return array[]
	 */
	public static function connections() {
		$rows = array();

		foreach ( McpOauthStore::connections_for_user( get_current_user_id() ) as $connection ) {
			$connection['created_label']   = self::timestamp_label( $connection['created'] );
			$connection['last_used_label'] = 0 === $connection['last_used']
				? __( 'Never used yet', 'wp-seopress' )
				: self::timestamp_label( $connection['last_used'] );

			$rows[] = $connection;
		}

		return $rows;
	}

	/**
	 * A timestamp in the site's own date and time format.
	 *
	 * @since 10.3.0
	 *
	 * @param int $timestamp The Unix timestamp.
	 *
	 * @return string
	 */
	protected static function timestamp_label( $timestamp ) {
		$timestamp = (int) $timestamp;

		if ( $timestamp <= 0 ) {
			return '';
		}

		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}

	/**
	 * The abilities that would be served right now, and those the scoping holds back.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	public static function tools() {
		$server = new McpServer();

		// Every ability the namespace scoping allows, not only the ones being
		// served: a switched off tool has to stay in the table, or it could be
		// switched off once and never found again.
		$served   = McpExposure::exposed_abilities();
		$rows     = self::describe_abilities( McpExposure::abilities_in_scope_of_namespace(), $server );
		$disabled = McpExposure::disabled_abilities();

		foreach ( $rows as $index => $row ) {
			$rows[ $index ]['enabled'] = ! isset( $disabled[ $row['ability'] ] );
		}

		return array(
			'exposed'             => $rows,
			'served_count'        => count( $served ),
			'withheld'            => self::describe_abilities( McpExposure::withheld_abilities(), $server ),
			'foreign_enabled'     => McpExposure::foreign_namespaces_enabled(),
			'exposed_namespaces'  => McpExposure::exposed_namespaces(),
			'foreign_option_key'  => McpExposure::FOREIGN_OPTION_KEY,
			'disabled_option_key' => McpExposure::DISABLED_OPTION_KEY,
			'disabled'            => array_keys( $disabled ),
		);
	}

	/**
	 * Turn abilities into rows the tab can render.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability[] $abilities The abilities to describe.
	 * @param McpServer     $server    The server, used for the tool name mapping.
	 *
	 * @return array[] Sorted by tool name.
	 */
	protected static function describe_abilities( $abilities, $server ) {
		$rows = array();

		foreach ( $abilities as $ability ) {
			$name = $ability->get_name();

			$rows[] = array(
				'tool'        => $server->tool_name( $name ),
				'ability'     => $name,
				'namespace'   => McpExposure::ability_namespace( $name ),
				'title'       => (string) $ability->get_label(),
				'description' => (string) $ability->get_description(),
				'access'      => self::ability_access( $ability ),
			);
		}

		usort(
			$rows,
			function ( $left, $right ) {
				return strcmp( $left['tool'], $right['tool'] );
			}
		);

		return $rows;
	}

	/**
	 * Whether an ability only reads, or can write.
	 *
	 * An ability is treated as read-only only when it says so. Anything that did
	 * not declare the annotation is reported as a write tool, because under-
	 * reporting what an AI client can change is the dangerous direction.
	 *
	 * @since 10.3.0
	 *
	 * @param \WP_Ability $ability The ability to inspect.
	 *
	 * @return string Either "read" or "write".
	 */
	public static function ability_access( $ability ) {
		$meta = $ability->get_meta();

		if ( isset( $meta['annotations']['readonly'] ) && true === (bool) $meta['annotations']['readonly'] ) {
			return 'read';
		}

		return 'write';
	}

	/**
	 * Build one readiness check result.
	 *
	 * @since 10.3.0
	 *
	 * @param string  $status  One of the STATUS_* constants.
	 * @param string  $id      Stable, untranslated identifier.
	 * @param string  $label   Translated name of what was tested.
	 * @param string  $message Translated explanation, saying what to do about it.
	 * @param string  $detail  Untranslated support detail, such as an HTTP status.
	 * @param array[] $links   Where to go to act on it, as structured data.
	 *
	 * @return array
	 */
	protected static function check( $status, $id, $label, $message = '', $detail = '', $links = array() ) {
		return array(
			'id'      => (string) $id,
			'label'   => (string) $label,
			'status'  => (string) $status,
			'message' => (string) $message,
			'detail'  => (string) $detail,
			'links'   => self::links( $links ),
		);
	}

	/**
	 * One place a check's message sends the reader, as data rather than markup.
	 *
	 * A URL or an admin screen named in a sentence has to be clickable, and an
	 * anchor tag inside a translated string is an escaping trap and a burden on
	 * every translator. So the sentence stays plain text and the destination
	 * travels beside it, for the tab to render as a real link.
	 *
	 * @since 10.3.0
	 *
	 * @param string $url     Where the link goes.
	 * @param string $label   The translated link text.
	 * @param bool   $new_tab Whether it leaves the admin, and so opens elsewhere.
	 *
	 * @return array
	 */
	protected static function link( $url, $label, $new_tab = false ) {
		return array(
			'url'     => (string) $url,
			'label'   => (string) $label,
			'new_tab' => (bool) $new_tab,
		);
	}

	/**
	 * Keep only the links that would actually resolve.
	 *
	 * A link with no URL or no text would render as an empty anchor, which reads
	 * as a broken page rather than as a missing link.
	 *
	 * @since 10.3.0
	 *
	 * @param array[] $links The links to filter.
	 *
	 * @return array[]
	 */
	protected static function links( $links ) {
		$kept = array();

		foreach ( (array) $links as $link ) {
			if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['label'] ) ) {
				continue;
			}

			$kept[] = $link;
		}

		return $kept;
	}

	/**
	 * The link to the MCP endpoint itself, for a check that names it.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function endpoint_link() {
		return self::link( McpServer::endpoint_url(), McpServer::endpoint_url(), true );
	}

	/**
	 * Run every readiness check.
	 *
	 * @since 10.3.0
	 *
	 * @return array[]
	 */
	public static function diagnostics() {
		$checks = array();

		$abilities_available = seopress_abilities_api_available();
		$checks[]            = self::check_abilities_api( $abilities_available );
		$checks[]            = self::check_exposure();

		$route_registered = $abilities_available && self::route_is_registered();
		$checks[]         = self::check_route( $abilities_available, $route_registered );
		$checks[]         = self::check_endpoint( $route_registered );
		$checks[]         = self::check_application_passwords();
		$checks[]         = self::check_https();
		$checks[]         = self::check_permalinks();
		$checks[]         = self::check_oauth();
		$checks[]         = self::check_oauth_discovery();

		return $checks;
	}

	/**
	 * The worst status among a set of checks.
	 *
	 * @since 10.3.0
	 *
	 * @param array[] $checks The checks to rank.
	 *
	 * @return string One of the STATUS_* constants.
	 */
	public static function worst_status( $checks ) {
		// An expected state ranks with a pass: it is not something to fix,
		// so it must never colour the collapsed panel's header.
		$rank = array(
			self::STATUS_OK      => 0,
			self::STATUS_INFO    => 0,
			self::STATUS_SKIPPED => 1,
			self::STATUS_WARNING => 2,
			self::STATUS_FAIL    => 3,
		);

		$worst = self::STATUS_OK;

		foreach ( (array) $checks as $check ) {
			$status = isset( $check['status'] ) ? $check['status'] : self::STATUS_OK;

			if ( isset( $rank[ $status ] ) && $rank[ $status ] > $rank[ $worst ] ) {
				$worst = $status;
			}
		}

		return $worst;
	}

	/**
	 * The one line the collapsed Diagnostics panel shows.
	 *
	 * The panel is collapsed by default, so the header has to carry the verdict
	 * on its own: a hidden failure would be worse than no panel at all.
	 *
	 * @since 10.3.0
	 *
	 * @param array[] $checks The checks to summarise.
	 *
	 * @return array {
	 *     @type string $status    The worst status among the checks.
	 *     @type int    $total     How many checks ran.
	 *     @type int    $attention How many of them are not OK.
	 *     @type array  $counts    How many checks per status.
	 *     @type string $label     The translated summary sentence.
	 * }
	 */
	public static function diagnostics_summary( $checks ) {
		$checks = (array) $checks;

		$counts = array(
			self::STATUS_OK      => 0,
			self::STATUS_WARNING => 0,
			self::STATUS_FAIL    => 0,
			self::STATUS_SKIPPED => 0,
			self::STATUS_INFO    => 0,
		);

		foreach ( $checks as $check ) {
			$status = isset( $check['status'] ) ? (string) $check['status'] : self::STATUS_OK;

			if ( isset( $counts[ $status ] ) ) {
				++$counts[ $status ];
			}
		}

		$attention = $counts[ self::STATUS_WARNING ] + $counts[ self::STATUS_FAIL ] + $counts[ self::STATUS_SKIPPED ];

		if ( 0 === $attention ) {
			$label = __( 'All checks passed', 'wp-seopress' );
		} else {
			$label = sprintf(
				/* translators: %d: how many readiness checks are not green. */
				_n(
					'%d check needs attention',
					'%d checks need attention',
					$attention,
					'wp-seopress'
				),
				$attention
			);
		}

		return array(
			'status'    => self::worst_status( $checks ),
			'total'     => count( $checks ),
			'attention' => $attention,
			'counts'    => $counts,
			'label'     => $label,
		);
	}

	/**
	 * Is the WordPress Abilities API on this site at all.
	 *
	 * @since 10.3.0
	 *
	 * @param bool $available Whether the API is available.
	 *
	 * @return array
	 */
	protected static function check_abilities_api( $available ) {
		$label = __( 'WordPress Abilities API', 'wp-seopress' );

		if ( $available ) {
			return self::check( self::STATUS_OK, 'mcp.abilities_api', $label );
		}

		return self::check(
			self::STATUS_FAIL,
			'mcp.abilities_api',
			$label,
			sprintf(
				/* translators: 1: this site's WordPress version. 2: the WordPress version that introduced the Abilities API. */
				__( 'This site runs WordPress %1$s. The MCP server is built on the Abilities API, which ships with WordPress %2$s, so update WordPress before connecting an AI client.', 'wp-seopress' ),
				self::wp_version(),
				self::ABILITIES_API_WP_VERSION
			),
			'wp ' . self::wp_version(),
			array(
				self::link( admin_url( 'update-core.php' ), __( 'Updates', 'wp-seopress' ) ),
			)
		);
	}

	/**
	 * Is the exposure opt-in on.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_exposure() {
		$label = __( 'Abilities exposed to external clients', 'wp-seopress' );

		if ( seopress_abilities_api_rest_enabled() ) {
			return self::check( self::STATUS_OK, 'mcp.exposure', $label );
		}

		return self::check(
			self::STATUS_FAIL,
			'mcp.exposure',
			$label,
			sprintf(
				/* translators: %s: the label of the setting that exposes abilities. */
				__( 'Exposure is off, so the endpoint refuses every request. Turn on "%s" under Abilities API in the Advanced tab, and save.', 'wp-seopress' ),
				__( 'Expose abilities to AI agents and external tools', 'wp-seopress' )
			),
			'',
			array(
				// The setting lives one tab away and there was no way to get
				// there from here: a check that says what to change has to be
				// able to take you to it.
				self::link(
					admin_url( 'admin.php?page=seopress-advanced#tab=tab_seopress_advanced_advanced' ),
					__( 'Advanced tab, Abilities API', 'wp-seopress' )
				),
			)
		);
	}

	/**
	 * Is the MCP route on the REST server.
	 *
	 * @since 10.3.0
	 *
	 * @param bool $abilities_available Whether the Abilities API is available.
	 * @param bool $registered          Whether the route is registered.
	 *
	 * @return array
	 */
	protected static function check_route( $abilities_available, $registered ) {
		$label = __( 'MCP route registered', 'wp-seopress' );

		if ( ! $abilities_available ) {
			return self::check(
				self::STATUS_SKIPPED,
				'mcp.route',
				$label,
				__( 'Not checked: the WordPress Abilities API is not available on this site.', 'wp-seopress' )
			);
		}

		if ( $registered ) {
			return self::check( self::STATUS_OK, 'mcp.route', $label, '', McpServer::route_path() );
		}

		return self::check(
			self::STATUS_FAIL,
			'mcp.route',
			$label,
			sprintf(
				/* translators: %s: the internal REST route path of the MCP endpoint. */
				__( 'The %s route is missing from the REST API. Another plugin is removing routes from the WordPress REST API, or SEOPress did not finish loading. Deactivate REST API restrictions for this route, then reload this page.', 'wp-seopress' ),
				McpServer::route_path()
			)
		);
	}

	/**
	 * Whether the MCP route is registered on the REST server.
	 *
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function route_is_registered() {
		$routes = rest_get_server()->get_routes();

		return is_array( $routes ) && array_key_exists( McpServer::route_path(), $routes );
	}

	/**
	 * Does the endpoint answer from outside WordPress.
	 *
	 * @since 10.3.0
	 *
	 * @param bool $route_registered Whether the route is registered.
	 *
	 * @return array
	 */
	protected static function check_endpoint( $route_registered ) {
		$label = __( 'MCP endpoint answering', 'wp-seopress' );
		$url   = McpServer::endpoint_url();

		if ( ! $route_registered ) {
			return self::check(
				self::STATUS_SKIPPED,
				'mcp.endpoint',
				$label,
				__( 'Not checked: the MCP route is not registered, so there is nothing to answer yet.', 'wp-seopress' )
			);
		}

		$probe = self::probe_endpoint();

		if ( '' !== $probe['error'] ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.endpoint',
				$label,
				sprintf(
					/* translators: %s: the transport error returned while calling the endpoint. */
					__( 'This site could not call its own MCP endpoint: %s. That usually means loopback requests are blocked. Ask your host to allow this site to make HTTP requests to itself, then run the checks again.', 'wp-seopress' ),
					$probe['error']
				),
				$probe['error'],
				array( self::endpoint_link() )
			);
		}

		$status = (int) $probe['status'];

		// 401 is the healthy answer: the route ran, and it refused an
		// unauthenticated caller, which is exactly what it should do. Said in
		// words, because a bare "HTTP 401" next to a green dot was read as an
		// error by readers who know that code as "unauthorized".
		if ( 401 === $status ) {
			return self::check(
				self::STATUS_OK,
				'mcp.endpoint',
				$label,
				__( 'HTTP 401 is the expected answer: this check calls the endpoint without credentials, and the endpoint rightly asks for them.', 'wp-seopress' ),
				'HTTP 401'
			);
		}

		// SEOPress's own refusal when exposure is off. The route is alive, and
		// the exposure check above already says what to do.
		if ( 403 === $status && false !== strpos( $probe['body'], 'seopress_mcp_disabled' ) ) {
			return self::check( self::STATUS_OK, 'mcp.endpoint', $label, '', 'HTTP 403 seopress_mcp_disabled' );
		}

		if ( 404 === $status ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.endpoint',
				$label,
				sprintf(
					/* translators: %s: the MCP endpoint URL. */
					__( 'The endpoint answered "not found". A security plugin or a server rule is blocking the WordPress REST API, so no AI client can reach %s. Allow POST requests to that URL.', 'wp-seopress' ),
					$url
				),
				'HTTP 404',
				array( self::endpoint_link() )
			);
		}

		if ( 403 === $status ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.endpoint',
				$label,
				sprintf(
					/* translators: %s: the MCP endpoint URL. */
					__( 'Something answered "forbidden" before SEOPress saw the request, so no AI client can reach %s. Allow POST requests to that URL in your security plugin, your firewall or your CDN.', 'wp-seopress' ),
					$url
				),
				'HTTP 403',
				array( self::endpoint_link() )
			);
		}

		if ( 200 === $status ) {
			return self::check(
				self::STATUS_WARNING,
				'mcp.endpoint',
				$label,
				__( 'The endpoint served an unauthenticated request instead of asking for credentials. Check that no plugin on this site is bypassing WordPress REST API authentication.', 'wp-seopress' ),
				'HTTP 200'
			);
		}

		return self::check(
			self::STATUS_WARNING,
			'mcp.endpoint',
			$label,
			sprintf(
				/* translators: %d: the HTTP status code the endpoint answered with. */
				__( 'The endpoint answered HTTP %d, where an unauthenticated call should get 401. Ask your host whether something in front of WordPress is rewriting this request.', 'wp-seopress' ),
				$status
			),
			'HTTP ' . $status,
			array( self::endpoint_link() )
		);
	}

	/**
	 * Call the MCP endpoint from this site, without credentials.
	 *
	 * Deliberately anonymous: the point is to see what an AI client gets, and an
	 * unauthenticated call is answered before anything executes.
	 *
	 * @since 10.3.0
	 *
	 * @return array {
	 *     @type int    $status The HTTP status code, 0 when the call failed.
	 *     @type string $error  The transport error, empty when there was none.
	 *     @type string $body   The response body, empty when the call failed.
	 * }
	 */
	public static function probe_endpoint() {
		$response = wp_remote_post(
			McpServer::endpoint_url(),
			array(
				'timeout'   => 10,
				// A reachability probe, not a security check: a self-signed
				// certificate on a staging or local site must not be reported
				// as a blocked endpoint. WordPress core's own loopback health
				// check does the same.
				'sslverify' => false,
				'headers'   => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				),
				'body'      => wp_json_encode(
					array(
						'jsonrpc' => '2.0',
						'id'      => 1,
						'method'  => 'ping',
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'status' => 0,
				'error'  => $response->get_error_message(),
				'body'   => '',
			);
		}

		return array(
			'status' => (int) wp_remote_retrieve_response_code( $response ),
			'error'  => '',
			'body'   => (string) wp_remote_retrieve_body( $response ),
		);
	}

	/**
	 * Can a client authenticate at all.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_application_passwords() {
		$label = __( 'Application Passwords', 'wp-seopress' );

		if ( self::application_passwords_available() ) {
			return self::check( self::STATUS_OK, 'mcp.application_passwords', $label );
		}

		return self::check(
			self::STATUS_FAIL,
			'mcp.application_passwords',
			$label,
			self::application_passwords_unavailable_reason(),
			'',
			array(
				self::link( admin_url( 'profile.php' ) . '#application-passwords', __( 'Application Passwords on your profile', 'wp-seopress' ) ),
			)
		);
	}

	/**
	 * Is the endpoint served over HTTPS.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_https() {
		$label = __( 'HTTPS', 'wp-seopress' );

		if ( self::endpoint_is_https() ) {
			return self::check( self::STATUS_OK, 'mcp.https', $label );
		}

		// A site on localhost is not an insecure site, it is a site nobody
		// else can reach. WordPress allows Application Passwords there, and
		// this server allows OAuth there, so warning about HTTPS greeted every
		// local install with a problem it does not have and cannot fix. Say
		// what is actually true instead: the clients on this machine work, the
		// ones on someone else's servers cannot see localhost at all.
		//
		// chatgpt.com only: whether the ChatGPT desktop app reaches a local
		// site has not been established, so it is not lumped in with it.
		if ( McpOauth::transport_is_secure() ) {
			return self::check(
				self::STATUS_OK,
				'mcp.https',
				$label,
				sprintf(
					/* translators: %s: the MCP endpoint URL. */
					__( 'This site answers on %s, which only this machine can reach. Claude Code, the Claude desktop app, Cursor and VS Code connect to it normally. claude.ai and chatgpt.com run on their own servers and cannot see it, so they need a site with a public HTTPS address.', 'wp-seopress' ),
					McpServer::endpoint_url()
				)
			);
		}

		return self::check(
			self::STATUS_WARNING,
			'mcp.https',
			$label,
			sprintf(
				/* translators: %s: the MCP endpoint URL. */
				__( 'The endpoint URL is %s, over plain HTTP. Most AI clients refuse an MCP server that is not served over HTTPS, WordPress only offers Application Passwords over HTTPS, and OAuth is refused outright, so this site cannot be added to claude.ai or ChatGPT as a custom connector. Install a TLS certificate and switch the WordPress Address and Site Address to https.', 'wp-seopress' ),
				McpServer::endpoint_url()
			),
			'',
			array(
				self::endpoint_link(),
				self::link( admin_url( 'options-general.php' ), __( 'Settings, General', 'wp-seopress' ) ),
			)
		);
	}

	/**
	 * Are permalinks pretty, and therefore is the URL the short form.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_permalinks() {
		$label = __( 'Permalinks', 'wp-seopress' );

		if ( self::uses_pretty_permalinks() ) {
			return self::check( self::STATUS_OK, 'mcp.permalinks', $label );
		}

		return self::check(
			self::STATUS_WARNING,
			'mcp.permalinks',
			$label,
			sprintf(
				/* translators: 1: the query string endpoint URL. 2: the endpoint URL with pretty permalinks on. */
				__( 'Permalinks are set to Plain, so the endpoint URL is the query string form %1$s. That URL works, and it is the one shown above, so copy it as it is. Choosing any other structure in Settings, Permalinks gives the shorter %2$s instead, which some clients handle better.', 'wp-seopress' ),
				McpServer::endpoint_url(),
				self::pretty_endpoint_url()
			),
			'',
			array(
				self::link( admin_url( 'options-permalink.php' ), __( 'Settings, Permalinks', 'wp-seopress' ) ),
			)
		);
	}

	/**
	 * Is the OAuth server able to run at all.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_oauth() {
		$label = __( 'OAuth for browser clients', 'wp-seopress' );

		if ( ! seopress_abilities_api_rest_enabled() ) {
			return self::check(
				self::STATUS_SKIPPED,
				'mcp.oauth',
				$label,
				__( 'Not checked: exposure is off, and OAuth follows the MCP server itself.', 'wp-seopress' )
			);
		}

		if ( ! function_exists( 'wp_fast_hash' ) ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.oauth',
				$label,
				sprintf(
					/* translators: %s: this site's WordPress version. */
					__( 'This site runs WordPress %s. OAuth tokens are hashed with the function WordPress added in 6.8, so update WordPress before connecting claude.ai or ChatGPT.', 'wp-seopress' ),
					self::wp_version()
				),
				'wp ' . self::wp_version()
			);
		}

		if ( ! McpOauth::transport_is_secure() ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.oauth',
				$label,
				McpOauth::insecure_transport_message()
			);
		}

		// Everything OAuth needs is there, so the filter is what turned it
		// off: the way a site that connects with Application Passwords only
		// is set up. Expected, then, not a failure.
		if ( self::oauth_switched_off() ) {
			return self::check(
				self::STATUS_INFO,
				'mcp.oauth',
				$label,
				__( 'The OAuth server is switched off on this site: something is filtering seopress_mcp_oauth_enabled to false. That is expected if you connect with an Application Password, from Claude Code, the Claude desktop app or Cursor. Only claude.ai and ChatGPT need OAuth, and they cannot sign in until it is back on.', 'wp-seopress' )
			);
		}

		if ( ! McpOauth::is_enabled() ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.oauth',
				$label,
				__( 'The OAuth server cannot run on this site, so claude.ai and ChatGPT cannot sign in. The WordPress Abilities API line above says what is missing.', 'wp-seopress' )
			);
		}

		return self::check( self::STATUS_OK, 'mcp.oauth', $label );
	}

	/**
	 * Can a client actually fetch the discovery document.
	 *
	 * RFC 9728 fixes the document at /.well-known/oauth-protected-resource,
	 * which is above the REST API prefix. A server that only routes the
	 * permalink structure to WordPress, or an install in a subdirectory, will
	 * answer 404 there, and OAuth then fails with nothing in any log. So it is
	 * fetched rather than assumed.
	 *
	 * @since 10.3.0
	 *
	 * @return array
	 */
	protected static function check_oauth_discovery() {
		$label = __( 'OAuth discovery document', 'wp-seopress' );
		$url   = McpOauth::protected_resource_metadata_url();

		if ( McpOauth::transport_is_secure() && self::oauth_switched_off() ) {
			return self::check(
				self::STATUS_INFO,
				'mcp.oauth_discovery',
				$label,
				__( 'Not needed: OAuth is switched off on this site, so there is no discovery document to serve. Only claude.ai and ChatGPT would read it.', 'wp-seopress' )
			);
		}

		if ( ! McpOauth::is_enabled() || ! McpOauth::transport_is_secure() ) {
			return self::check(
				self::STATUS_SKIPPED,
				'mcp.oauth_discovery',
				$label,
				__( 'Not checked: the OAuth server is not running on this site yet.', 'wp-seopress' )
			);
		}

		$probe = self::probe_discovery( $url );

		if ( '' !== $probe['error'] ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.oauth_discovery',
				$label,
				sprintf(
					/* translators: %s: the transport error returned while fetching the document. */
					__( 'This site could not fetch its own OAuth discovery document: %s. That usually means loopback requests are blocked. Ask your host to allow this site to make HTTP requests to itself, then run the checks again.', 'wp-seopress' ),
					$probe['error']
				),
				$probe['error']
			);
		}

		$document = json_decode( $probe['body'], true );

		if ( 200 === (int) $probe['status'] && is_array( $document ) && ! empty( $document['authorization_servers'] ) ) {
			return self::check( self::STATUS_OK, 'mcp.oauth_discovery', $label, '', $url );
		}

		return self::check(
			self::STATUS_FAIL,
			'mcp.oauth_discovery',
			$label,
			sprintf(
				/* translators: %s: the URL of the OAuth discovery document. */
				__( 'The discovery document at %s is not being served, so a browser client has no way to find out where to sign in. Your server is not routing /.well-known/ to WordPress: choose any permalink structure other than Plain in Settings, Permalinks, and if WordPress is installed in a subdirectory, add a rule that forwards /.well-known/ requests to it. Some hosts answer /.well-known/ addresses themselves, before WordPress runs: ask yours to pass /.well-known/oauth-protected-resource and /.well-known/oauth-authorization-server through to WordPress. Until then, Claude Code, the Claude desktop app and Cursor can connect with an Application Password, which the setup steps below now show.', 'wp-seopress' ),
				$url
			),
			'HTTP ' . (int) $probe['status']
		);
	}

	/**
	 * Fetch the OAuth discovery document from outside WordPress.
	 *
	 * @since 10.3.0
	 *
	 * @param string $url The document URL.
	 *
	 * @return array {
	 *     @type int    $status The HTTP status code, 0 when the call failed.
	 *     @type string $error  The transport error, empty when there was none.
	 *     @type string $body   The response body, empty when the call failed.
	 * }
	 */
	public static function probe_discovery( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout'   => 10,
				// A reachability probe, not a security check: a self-signed
				// certificate on a staging site must not read as a broken
				// document. Core's own loopback health check does the same.
				'sslverify' => false,
				'headers'   => array( 'Accept' => 'application/json' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'status' => 0,
				'error'  => $response->get_error_message(),
				'body'   => '',
			);
		}

		return array(
			'status' => (int) wp_remote_retrieve_response_code( $response ),
			'error'  => '',
			'body'   => (string) wp_remote_retrieve_body( $response ),
		);
	}

	/**
	 * This site's WordPress version.
	 *
	 * @since 10.3.0
	 *
	 * @return string
	 */
	protected static function wp_version() {
		global $wp_version;

		return isset( $wp_version ) ? (string) $wp_version : '';
	}
}
