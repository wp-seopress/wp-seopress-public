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
		$diagnostics = self::diagnostics();

		return array(
			'endpoint'            => self::endpoint(),
			'steps'               => self::steps(),
			'connection'          => self::connection(),
			'oauth'               => self::oauth(),
			'tools'               => self::tools(),
			'diagnostics'         => $diagnostics,
			'diagnostics_summary' => self::diagnostics_summary( $diagnostics ),
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
	 *
	 * @return array[]
	 */
	public static function steps() {
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
				'clients'       => self::clients(),
			),
			array(
				'id'           => 'mcp.step.verify',
				'number'       => 2,
				'title'        => __( 'Check that it works', 'wp-seopress' ),
				'summary'      => __( 'Confirm the client can see this site before you rely on it, so a silent misconfiguration does not look like an empty site.', 'wp-seopress' ),
				'instructions' => array(
					__( 'Ask your client which tools it has. It should list the tools shown under "Exposed tools" further down this page.', 'wp-seopress' ),
					__( 'If nothing appears, run the readiness checks in the Diagnostics panel. Every line that is not green says what to change on this site.', 'wp-seopress' ),
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
			'pending' => array(
				'status'  => 'warning',
				'message' => sprintf(
					/* translators: %s: the WordPress user name the configurations are written for. */
					__( 'No Application Password has been created in this page view, so the configuration below still carries a placeholder instead of a credential for "%s". Create one above and it fills itself in.', 'wp-seopress' ),
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
	 * @since 10.3.0
	 *
	 * @return bool
	 */
	public static function oauth_supported() {
		return McpOauth::is_enabled() && McpOauth::transport_is_secure();
	}

	/**
	 * The clients step 2 branches between, in the order they are offered.
	 *
	 * Each client says what kind of thing it is set up with, which snippet it
	 * needs, where its configuration file lives and what to do with it. They
	 * genuinely differ, so stacking all four sets of instructions would leave
	 * the reader to work out which paragraph is theirs.
	 *
	 * @since 10.3.0
	 *
	 * @return array[] {
	 *     @type string   $id           Stable, untranslated identifier.
	 *     @type string   $label        The name shown on the client's tab.
	 *     @type string   $kind         One of "command", "file", "oauth", "unavailable".
	 *     @type string   $summary      One sentence saying what this client is.
	 *     @type string   $snippet          Which connection snippet it needs, empty when none.
	 *     @type string   $fallback_snippet A helper shown while no password exists, empty when none.
	 *     @type string   $fallback_hint    What that helper is for.
	 *     @type string[] $instructions     What to do, in order.
	 *     @type array[]  $locations        Where its configuration file lives.
	 * }
	 */
	public static function clients() {
		return array(
			self::claude_client(),
			self::browser_client( 'chatgpt' ),
			array(
				'id'               => 'cursor',
				'label'            => 'Cursor',
				'kind'             => 'file',
				// Cursor reads a file rather than taking a URL in a dialog, and
				// that is the only thing that sets it apart here: it signs
				// itself in over OAuth like the rest, so the block carries no
				// credential and nothing has to be created first.
				'summary'          => __( 'A code editor that reads its MCP servers from a JSON file you edit yourself. It signs you in over OAuth the first time it uses the server, so there is nothing to create beforehand.', 'wp-seopress' ),
				'snippet'          => 'json_config_oauth',
				'snippet_hint'     => __( 'Paste this into the file above.', 'wp-seopress' ),
				'extra_snippet'    => '',
				'extra_hint'       => '',
				'fallback_snippet' => '',
				'fallback_hint'    => '',
				'instructions'     => array(
					__( 'Open, or create, one of the configuration files listed below.', 'wp-seopress' ),
					__( 'Paste the block into it. If the file already has an "mcpServers" object, add the "SEOPress" entry inside it instead of replacing the file.', 'wp-seopress' ),
					__( 'Restart Cursor, open Settings, Customize, MCPs, then Authenticate next to SEOPress and approve the connection here.', 'wp-seopress' ),
				),
				'locations'        => array(
					array(
						'label' => __( 'This project only', 'wp-seopress' ),
						'path'  => '.cursor/mcp.json',
					),
					array(
						'label' => __( 'Every project', 'wp-seopress' ),
						'path'  => '~/.cursor/mcp.json',
					),
				),
			),
		);
	}

	/**
	 * The one branch every Claude client shares.
	 *
	 * Claude Code, the desktop app and claude.ai are set up on three different
	 * screens, and for a while this page had three tabs to match. They turned
	 * out to do the same thing: measured against all three, each one takes the
	 * endpoint URL, reads the 401 challenge, fetches both discovery documents,
	 * registers itself and signs in over OAuth. None of them is handed an
	 * Application Password. Three tabs saying that three times only hid it.
	 *
	 * So it is one tab, and what differs between them is a single line each:
	 * where the URL goes.
	 *
	 * @since 10.3.0
	 *
	 * @return array One client, shaped like the others.
	 */
	protected static function claude_client() {
		if ( self::oauth_supported() ) {
			return array(
				'id'               => 'claude',
				'label'            => 'Claude',
				'kind'             => 'oauth',
				'summary'          => __( 'Claude Code, the desktop app and claude.ai all connect the same way: you give them this URL and sign in here in your browser. Nothing to create beforehand.', 'wp-seopress' ),
				'snippet'          => 'connect_url',
				'snippet_hint'     => __( 'For claude.ai and the desktop app: paste this as the connector URL.', 'wp-seopress' ),
				'extra_snippet'    => 'claude_code_oauth',
				'extra_hint'       => __( 'In Claude Code, this one command adds the same server. Run /mcp afterwards to sign in.', 'wp-seopress' ),
				'fallback_snippet' => '',
				'fallback_hint'    => '',
				'instructions'     => array(
					__( 'In claude.ai or the desktop app, open Settings, Connectors, then Add custom connector, and paste the URL below. In Claude Code, run the command below instead.', 'wp-seopress' ),
					__( 'Leave every other field empty. This site registers the client by itself and tells it where to sign in.', 'wp-seopress' ),
					__( 'Sign in to this site when your browser is sent here, then choose whether this connection may only read or may also change data. It works with your own WordPress permissions, and nothing else.', 'wp-seopress' ),
				),
				'locations'        => array(),
			);
		}

		// OAuth cannot run here, so claude.ai is out of reach whatever is
		// pasted into it. The two clients that run on the reader's own machine
		// still are not: they can send an Application Password themselves.
		return array(
			'id'               => 'claude',
			'label'            => 'Claude',
			'kind'             => 'command',
			'summary'          => __( 'This site cannot sign a client in over OAuth, so claude.ai cannot reach it at all. Claude Code and the desktop app still can, with an Application Password, which you create right below.', 'wp-seopress' ),
			'snippet'          => 'claude_code',
			'snippet_hint'     => __( 'For Claude Code: run this in a terminal.', 'wp-seopress' ),
			'extra_snippet'    => 'json_config',
			'extra_hint'       => __( 'For the desktop app instead: open Settings, Developer, Edit Config, and paste this into the file it opens.', 'wp-seopress' ),
			'fallback_snippet' => 'base64_command',
			'fallback_hint'    => __( 'Already created an Application Password from your profile instead? Run this to get the Base64 that replaces the placeholder above.', 'wp-seopress' ),
			'instructions'     => self::browser_client_blockers( 'claude.ai' ),
			'locations'        => array(
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
			),
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
				'instructions' => array(
					__( 'In the ChatGPT app, open Settings, Integrations, Plugins, then Add, and choose Add MCP server. The browser version keeps the same screen under a different menu name, look for the one that lists your connectors.', 'wp-seopress' ),
					__( 'Give it a name, choose the Streamable HTTP type, paste the URL below, and save. Leave the bearer token and header fields empty: this site signs the connection in over OAuth by itself.', 'wp-seopress' ),
					__( 'Click Authenticate next to the server, sign in to this site when your browser is sent here, and approve the connection. The connector then works with your own WordPress permissions, and nothing else.', 'wp-seopress' ),
				),
			),
		);
	}

	/**
	 * One browser branch of step 2, which depends on OAuth being served.
	 *
	 * An Application Password cannot reach a client that runs on someone else's
	 * servers, so this branch is the OAuth connector flow when OAuth can run,
	 * and otherwise says which of the two halves is missing. "Not implemented"
	 * and "implemented, but refused over plain HTTP" are different problems with
	 * different fixes, so they must not be reported with the same sentence.
	 *
	 * @since 10.3.0
	 *
	 * @param string $id Which browser client to build.
	 *
	 * @return array One client, shaped like the others.
	 */
	protected static function browser_client( $id ) {
		$clients = self::browser_clients();
		$client  = $clients[ $id ];

		if ( self::oauth_supported() ) {
			return array(
				'id'               => $id,
				'label'            => $client['label'],
				'kind'             => 'oauth',
				'summary'          => $client['summary'],
				'snippet'          => 'connect_url',
				'snippet_hint'     => __( 'Paste this as the MCP server URL.', 'wp-seopress' ),
				'extra_snippet'    => '',
				'extra_hint'       => '',
				'fallback_snippet' => '',
				'fallback_hint'    => '',
				'instructions'     => $client['instructions'],
				'locations'        => array(),
			);
		}

		return array(
			'id'               => $id,
			'label'            => $client['label'],
			'kind'             => 'unavailable',
			'summary'          => __( 'Not possible from this site right now, whatever you paste into the browser.', 'wp-seopress' ),
			'snippet'          => '',
			'snippet_hint'     => '',
			'extra_snippet'    => '',
			'extra_hint'       => '',
			'fallback_snippet' => '',
			'fallback_hint'    => '',
			'instructions'     => self::browser_client_blockers( $client['vendor'] ),
			'locations'        => array(),
		);
	}

	/**
	 * Why the browser cannot connect, in the order the reader has to fix them.
	 *
	 * @since 10.3.0
	 *
	 * @param string $vendor The name of the client the reader picked.
	 *
	 * @return string[]
	 */
	protected static function browser_client_blockers( $vendor ) {
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
		}

		$blockers[] = __( 'Claude Code, the Claude desktop app and Cursor still work in the meantime: all three run on your own machine and can send an Application Password.', 'wp-seopress' );

		return $blockers;
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
			'base64_command'            => self::base64_command( $username ),
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
		return sprintf(
			'claude mcp add --transport http %1$s "%2$s" --header "Authorization: Basic %3$s"',
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
	 * Only needed on a site where OAuth cannot run.
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
			'error_intro'   => __( 'WordPress refused to create the Application Password, so nothing was created and step 2 still carries a placeholder.', 'wp-seopress' ),
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
		$rank = array(
			self::STATUS_OK      => 0,
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
		// unauthenticated caller, which is exactly what it should do.
		if ( 401 === $status ) {
			return self::check( self::STATUS_OK, 'mcp.endpoint', $label, '', 'HTTP 401' );
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
		if ( McpOauth::transport_is_secure() ) {
			return self::check(
				self::STATUS_OK,
				'mcp.https',
				$label,
				sprintf(
					/* translators: %s: the MCP endpoint URL. */
					__( 'This site answers on %s, which only this machine can reach. Claude Code, the Claude desktop app, Cursor and VS Code connect to it normally. claude.ai and ChatGPT run on their own servers and cannot see it, so they need a site with a public HTTPS address.', 'wp-seopress' ),
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

		if ( ! McpOauth::is_enabled() ) {
			return self::check(
				self::STATUS_FAIL,
				'mcp.oauth',
				$label,
				__( 'The OAuth server is switched off on this site, so claude.ai and ChatGPT cannot sign in. Something is filtering seopress_mcp_oauth_enabled to false.', 'wp-seopress' )
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
				__( 'The discovery document at %s is not being served, so a browser client has no way to find out where to sign in. Your server is not routing /.well-known/ to WordPress: choose any permalink structure other than Plain in Settings, Permalinks, and if WordPress is installed in a subdirectory, add a rule that forwards /.well-known/ requests to it.', 'wp-seopress' ),
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
