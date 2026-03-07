/**
 * SEOPress Promotion Modal
 *
 * Standalone React component using wp-components Modal.
 * Loads on SEOPress admin pages to display promotional modals.
 *
 * @package SEOPress
 * @since 9.6.0
 */

(function() {
	'use strict';

	const { createElement, render, useState, useEffect } = wp.element;
	// React 18+ uses createRoot, fallback to render for older versions
	const createRoot = wp.element.createRoot || null;
	const { Modal, Button } = wp.components;

	/**
	 * Promotion Modal Component
	 */
	function PromoModal({ promotion, onDismiss }) {
		const [isOpen, setIsOpen] = useState(true);

		if (!isOpen || !promotion) {
			return null;
		}

		const handleDismiss = () => {
			setIsOpen(false);
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
				padding: '14px 32px',
				borderRadius: '8px',
				textDecoration: 'none',
				fontSize: '15px',
				fontWeight: '600',
				textAlign: 'center',
				cursor: 'pointer'
			};

			switch (buttonStyle) {
				case 'secondary':
					return {
						...baseStyles,
						backgroundColor: 'transparent',
						color: textColor,
						border: `2px solid ${textColor}`
					};
				case 'link':
					return {
						...baseStyles,
						backgroundColor: 'transparent',
						color: textColor,
						border: 'none',
						textDecoration: 'underline',
						padding: '14px 0'
					};
				case 'primary':
				default:
					return {
						...baseStyles,
						backgroundColor: textColor,
						color: bgColor,
						border: 'none'
					};
			}
		};

		return createElement(
			Modal,
			{
				title: null,
				onRequestClose: handleDismiss,
				className: 'seopress-promo-modal',
				overlayClassName: 'seopress-promo-modal-overlay',
				isDismissible: promotion.dismissible !== false,
			},
			createElement(
				'div',
				{
					className: 'seopress-promo-modal-content',
					style: { backgroundColor: bgColor, color: textColor }
				},
				// Icon
				promotion.icon && createElement(
					'div',
					{ className: 'seopress-promo-modal-icon' },
					createElement('span', {
						className: `dashicons dashicons-${promotion.icon}`,
						style: { color: textColor }
					})
				),
				// Title
				promotion.title && createElement(
					'h2',
					{
						className: 'seopress-promo-modal-title',
						style: { color: textColor }
					},
					promotion.title
				),
				// Body
				promotion.body && createElement(
					'p',
					{
						className: 'seopress-promo-modal-body',
						style: { color: textColor, opacity: 0.9 }
					},
					promotion.body
				),
				// Actions
				createElement(
					'div',
					{ className: 'seopress-promo-modal-actions' },
					// CTA Button
					promotion.cta_url && createElement(
						'a',
						{
							href: promotion.cta_url,
							target: '_blank',
							rel: 'noopener noreferrer',
							className: 'seopress-promo-modal-cta',
							style: getButtonStyles(),
							onClick: handleCtaClick
						},
						promotion.cta_text || 'Learn More'
					),
					// Dismiss Button (if dismissible)
					promotion.dismissible !== false && createElement(
						Button,
						{
							variant: 'link',
							onClick: handleDismiss,
							className: 'seopress-promo-modal-dismiss',
							style: { color: textColor, opacity: 0.8 }
						},
						'Remind me later'
					)
				)
			)
		);
	}

	/**
	 * Initialize the modal
	 */
	function init() {
		// Check if promotion data exists
		if (!window.seopressPromoModal || !window.seopressPromoModal.promotion) {
			return;
		}

		const promotion = window.seopressPromoModal.promotion;

		// Create mount point
		const mountPoint = document.createElement('div');
		mountPoint.id = 'seopress-promo-modal-root';
		document.body.appendChild(mountPoint);

		// Handle dismiss
		const handleDismiss = (promoId, duration) => {
			// Send AJAX to save dismissal
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
		};

		// Render (React 18+ uses createRoot, fallback to render for older versions)
		const modalElement = createElement(PromoModal, {
			promotion: promotion,
			onDismiss: handleDismiss
		});

		if (createRoot) {
			const root = createRoot(mountPoint);
			root.render(modalElement);
		} else {
			render(modalElement, mountPoint);
		}
	}

	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
