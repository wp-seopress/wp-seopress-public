/**
 * SEOPress Metabox Promotion Banner
 *
 * Injects a promotional banner into the universal metabox sidebar.
 * Watches for the metabox dialog to open and injects the banner.
 *
 * @package SEOPress
 * @since 9.6.0
 */

(function() {
	'use strict';

	// Check dependencies
	if (!window.wp || !window.wp.element) {
		return;
	}

	const { createElement, useState, render } = wp.element;
	// React 18+ uses createRoot, fallback to render for older versions
	const createRoot = wp.element.createRoot || null;

	/**
	 * Metabox Promo Banner Component
	 */
	function MetaboxPromoBanner({ promotion, onDismiss }) {
		const [isVisible, setIsVisible] = useState(true);

		if (!isVisible || !promotion) {
			return null;
		}

		const handleDismiss = (e) => {
			e.preventDefault();
			e.stopPropagation();
			setIsVisible(false);
			if (onDismiss) {
				onDismiss(promotion.id, promotion.dismiss_duration || 30);
			}
		};

		const handleCtaClick = () => {
			// Track click
			if (window.seopressPromotions && window.seopressPromotions.stats_endpoint) {
				if (navigator.sendBeacon) {
					const data = new FormData();
					data.append('ad_id', promotion.id);
					data.append('action', 'click');
					navigator.sendBeacon(window.seopressPromotions.stats_endpoint, data);
				}
			}
		};

		const bgColor = promotion.styling?.background_color || '#4E21E7';
		const textColor = promotion.styling?.text_color || '#FFFFFF';
		const buttonStyle = promotion.styling?.button_style || 'primary';

		// Button styles based on API setting
		const getButtonStyles = () => {
			const baseStyles = {
				display: 'inline-block',
				padding: '10px 16px',
				borderRadius: '6px',
				textDecoration: 'none',
				fontSize: '12px',
				fontWeight: '600',
				textAlign: 'center',
				marginTop: '4px',
				width: '100%',
				boxSizing: 'border-box',
				cursor: 'pointer',
				border: 'none'
			};

			switch (buttonStyle) {
				case 'secondary':
					// Outline style
					return {
						...baseStyles,
						backgroundColor: 'transparent',
						color: textColor,
						border: `2px solid ${textColor}`
					};
				case 'link':
					// Text link style
					return {
						...baseStyles,
						backgroundColor: 'transparent',
						color: textColor,
						padding: '10px 0',
						textDecoration: 'underline'
					};
				case 'primary':
				default:
					// Filled style (default)
					return {
						...baseStyles,
						backgroundColor: textColor,
						color: bgColor
					};
			}
		};

		// Compact vertical layout for narrow sidebar
		return createElement(
			'div',
			{
				className: 'seopress-metabox-promo-banner',
				style: {
					backgroundColor: bgColor,
					color: textColor,
					padding: '16px',
					borderRadius: '8px',
					position: 'relative',
					display: 'flex',
					flexDirection: 'column',
					alignItems: 'flex-start',
					gap: '8px'
				}
			},
			// Dismiss button (top right)
			promotion.dismissible !== false && createElement(
				'button',
				{
					onClick: handleDismiss,
					style: {
						position: 'absolute',
						top: '8px',
						right: '8px',
						background: 'none',
						border: 'none',
						color: textColor,
						cursor: 'pointer',
						padding: '4px',
						opacity: 0.7,
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
						lineHeight: 1
					},
					'aria-label': 'Dismiss'
				},
				createElement('span', {
					className: 'dashicons dashicons-no-alt',
					style: { fontSize: '16px', width: '16px', height: '16px' }
				})
			),
			// Icon
			promotion.icon && createElement(
				'div',
				{
					style: {
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
						width: '32px',
						height: '32px',
						backgroundColor: 'rgba(255,255,255,0.15)',
						borderRadius: '6px'
					}
				},
				createElement('span', {
					className: `dashicons dashicons-${promotion.icon}`,
					style: {
						color: textColor,
						fontSize: '18px',
						width: '18px',
						height: '18px'
					}
				})
			),
			// Title
			promotion.title && createElement(
				'div',
				{
					style: {
						fontWeight: '600',
						fontSize: '13px',
						lineHeight: '1.4',
						paddingRight: '24px',
						marginTop: '4px'
					}
				},
				promotion.title
			),
			// Body
			promotion.body && createElement(
				'div',
				{
					style: {
						fontSize: '12px',
						opacity: 0.9,
						lineHeight: '1.5'
					}
				},
				promotion.body
			),
			// CTA button
			promotion.cta_url && createElement(
				'a',
				{
					href: promotion.cta_url,
					target: '_blank',
					rel: 'noopener noreferrer',
					onClick: handleCtaClick,
					style: getButtonStyles()
				},
				promotion.cta_text || 'Learn More'
			)
		);
	}

	/**
	 * Handle dismiss action
	 */
	function handleDismiss(promoId, duration) {
		if (window.seopressPromotions && window.seopressPromotions.dismiss_nonce) {
			const formData = new FormData();
			formData.append('action', 'seopress_dismiss_promotion');
			formData.append('promo_id', promoId);
			formData.append('duration', duration);
			formData.append('_ajax_nonce', window.seopressPromotions.dismiss_nonce);

			fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: formData
			});

			// Track dismiss
			if (window.seopressPromotions.stats_endpoint) {
				if (navigator.sendBeacon) {
					const data = new FormData();
					data.append('ad_id', promoId);
					data.append('action', 'dismiss');
					navigator.sendBeacon(window.seopressPromotions.stats_endpoint, data);
				}
			}
		}
	}

	/**
	 * Inject the banner into the metabox
	 */
	function injectBanner(container) {
		// Check if already injected anywhere in the document
		if (document.querySelector('.seopress-metabox-promo-root')) {
			return;
		}

		const promotion = window.seopressMetaboxPromo?.promotion;
		if (!promotion) {
			return;
		}

		// Create mount point
		const mountPoint = document.createElement('div');
		mountPoint.className = 'seopress-metabox-promo-root';
		mountPoint.style.cssText = 'padding: 16px;';

		// Find the main flex container (the one with height: 100%)
		const mainFlex = container.querySelector('.flex[style*="height: 100%"], .flex[style*="height:100%"]');

		let sidebar = null;

		if (mainFlex) {
			// The sidebar is the first child of this flex
			sidebar = mainFlex.firstElementChild;

			// Verify it looks like a sidebar (narrow width)
			if (sidebar) {
				const rect = sidebar.getBoundingClientRect();
				if (rect.width > 300) {
					// Too wide, might not be the sidebar
					sidebar = null;
				}
			}
		}

		// Alternative: find by looking for styled-component with narrow width
		if (!sidebar) {
			const styledDivs = container.querySelectorAll('[class*="sc-"]');
			for (const div of styledDivs) {
				const rect = div.getBoundingClientRect();
				const text = div.textContent || '';
				// Sidebar should be narrow and contain the tabs
				if (rect.width > 100 && rect.width < 280 && rect.height > 200 && text.includes('OVERVIEW')) {
					sidebar = div;
					break;
				}
			}
		}

		if (sidebar) {
			// Fix sidebar height to show full banner (remove 85% height restriction)
			sidebar.style.height = 'auto';
			sidebar.style.paddingBottom = '35px';

			mountPoint.style.cssText = 'padding: 12px; margin-top: auto;';
			sidebar.appendChild(mountPoint);
		} else {
			// Fallback: insert in the content area
			const scrollArea = container.querySelector('[class*="scroll"]');
			if (scrollArea) {
				mountPoint.style.cssText = 'padding: 16px;';
				scrollArea.insertBefore(mountPoint, scrollArea.firstChild);
			} else {
				container.appendChild(mountPoint);
			}
		}

		// Render the banner (React 18+ uses createRoot, fallback to render for older versions)
		const bannerElement = createElement(MetaboxPromoBanner, {
			promotion: promotion,
			onDismiss: handleDismiss
		});

		if (createRoot) {
			const root = createRoot(mountPoint);
			root.render(bannerElement);
		} else {
			render(bannerElement, mountPoint);
		}
	}

	/**
	 * Check if metabox is visible
	 */
	function isMetaboxVisible(element) {
		if (!element) return false;
		const style = window.getComputedStyle(element);

		// Check basic visibility
		if (style.display === 'none' || style.visibility === 'hidden') {
			return false;
		}

		// For fixed-positioned elements, offsetParent is always null
		// So we need a different check - verify it has dimensions
		if (style.position === 'fixed') {
			return element.offsetWidth > 0 && element.offsetHeight > 0;
		}

		return element.offsetParent !== null;
	}

	/**
	 * Find and inject into metabox
	 */
	function tryInject() {
		// Look for the universal metabox dialog
		const metabox = document.querySelector('.sp-seo-metabox');

		if (metabox && isMetaboxVisible(metabox)) {
			injectBanner(metabox);
			return true;
		}

		// Also try classic editor metabox
		const classicMetabox = document.querySelector('#seopress_cpt .inside');
		if (classicMetabox && isMetaboxVisible(classicMetabox)) {
			injectBanner(classicMetabox);
			return true;
		}

		return false;
	}

	/**
	 * Initialize with MutationObserver
	 */
	function init() {
		// Check if promotion data exists
		if (!window.seopressMetaboxPromo || !window.seopressMetaboxPromo.promotion) {
			return;
		}

		// Try to inject immediately
		tryInject();

		// Set up MutationObserver to watch for metabox visibility changes
		const observer = new MutationObserver((mutations) => {
			for (const mutation of mutations) {
				// Check for added nodes
				if (mutation.type === 'childList') {
					for (const node of mutation.addedNodes) {
						if (node.nodeType === Node.ELEMENT_NODE) {
							if (node.classList?.contains('sp-seo-metabox') || node.querySelector?.('.sp-seo-metabox')) {
								setTimeout(tryInject, 100);
							}
						}
					}
				}
				// Check for style/attribute changes (dialog opening)
				if (mutation.type === 'attributes') {
					if (mutation.target.classList?.contains('sp-seo-metabox')) {
						setTimeout(tryInject, 100);
					}
				}
			}
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true,
			attributes: true,
			attributeFilter: ['style', 'class']
		});

		// Also poll periodically as a fallback
		let attempts = 0;
		const maxAttempts = 60; // 30 seconds
		const pollInterval = setInterval(() => {
			attempts++;
			if (tryInject() || attempts >= maxAttempts) {
				clearInterval(pollInterval);
			}
		}, 500);

		// Also try on Gutenberg editor ready
		if (wp.domReady) {
			wp.domReady(() => {
				setTimeout(tryInject, 500);
				setTimeout(tryInject, 1500);
			});
		}

		// Timeout to disconnect observer after 60 seconds
		setTimeout(() => {
			observer.disconnect();
		}, 60000);
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
