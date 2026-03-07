/**
 * SEOPress Promotions JavaScript
 *
 * Handles dismissal functionality and modal interactions for the promotions system.
 *
 * @package SEOPress
 * @since 9.6.0
 */

(function($) {
	'use strict';

	/**
	 * SEOPress Promotions module.
	 */
	var SEOPressPromotions = {

		/**
		 * Initialize the module.
		 */
		init: function() {
			this.bindEvents();
			this.initModal();
		},

		/**
		 * Bind event handlers.
		 */
		bindEvents: function() {
			// Dismiss promotion buttons.
			$(document).on('click', '.promo-dismiss', this.handleDismiss);

			// Track CTA clicks.
			$(document).on('click', '.promo-cta', this.handleCtaClick);

			// Toggle all promotions.
			$(document).on('change', '#seopress-toggle-promotions', this.handleToggleAll);

			// Modal dismiss button.
			$(document).on('click', '[data-dismiss="license-modal"]', this.handleModalDismiss);

			// Close modal on overlay click.
			$(document).on('click', '.seopress-modal-overlay', this.handleOverlayClick);
		},

		/**
		 * Handle promotion dismissal.
		 *
		 * @param {Event} e Click event.
		 */
		handleDismiss: function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $button = $(this);
			var $promo = $button.closest('.seopress-promo-banner, .seopress-promo-card, .seopress-metabox-promo-banner, .seopress-contextual-promo');
			var promoId = $button.data('promo-id') || $promo.data('promo-id');
			var duration = $button.data('dismiss-duration') || 30;

			if (!promoId) {
				console.warn('SEOPress Promotions: No promo ID found for dismissal.');
				return;
			}

			// Disable button and show loading state.
			$button.prop('disabled', true).css('opacity', '0.5');

			// Track dismissal stats (fire and forget).
			SEOPressPromotions.trackStat(promoId, 'dismiss');

			// Send AJAX request to store dismissal locally.
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'seopress_dismiss_promotion',
					promo_id: promoId,
					duration: duration,
					_ajax_nonce: seopressPromotions.dismiss_nonce
				},
				success: function(response) {
					if (response.success) {
						// Check if this is the top banner.
						var isTopBanner = $promo.hasClass('seopress-promo-banner');

						// Animate out and remove.
						$promo.slideUp(300, function() {
							$(this).remove();

							// Remove body class if top banner was dismissed.
							if (isTopBanner) {
								$('body').removeClass('has-promo-banner');
							}

							// Check if promotions panel is now empty.
							var $panel = $('#seopress-promotions-panel');
							if ($panel.length && $panel.find('.seopress-promo-card').length === 0 && $panel.find('.affiliate-card').length === 0) {
								$panel.slideUp(300, function() {
									$(this).remove();
								});
							}
						});
					} else {
						// Re-enable button on error.
						$button.prop('disabled', false).css('opacity', '1');
						console.error('SEOPress Promotions: Failed to dismiss promotion.', response);
					}
				},
				error: function(xhr, status, error) {
					// Re-enable button on error.
					$button.prop('disabled', false).css('opacity', '1');
					console.error('SEOPress Promotions: AJAX error.', error);
				}
			});
		},

		/**
		 * Handle CTA button click - track before navigating.
		 *
		 * @param {Event} e Click event.
		 */
		handleCtaClick: function(e) {
			var $link = $(this);
			var $promo = $link.closest('.seopress-promo-banner, .seopress-promo-card, .seopress-metabox-promo-banner, .seopress-contextual-promo');
			var promoId = $promo.data('promo-id');

			if (!promoId) {
				return; // Let click proceed normally.
			}

			// Track click stat (fire and forget - don't block navigation).
			SEOPressPromotions.trackStat(promoId, 'click');
		},

		/**
		 * Send stat to remote API.
		 *
		 * @param {string} promoId Promotion ID.
		 * @param {string} action  Action type (click, dismiss).
		 */
		trackStat: function(promoId, action) {
			if (!seopressPromotions.stats_endpoint) {
				return;
			}

			// Use sendBeacon for reliable tracking that doesn't block navigation.
			if (navigator.sendBeacon) {
				var data = new FormData();
				data.append('ad_id', promoId);
				data.append('action', action);
				navigator.sendBeacon(seopressPromotions.stats_endpoint, data);
			} else {
				// Fallback to async fetch.
				fetch(seopressPromotions.stats_endpoint, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ ad_id: promoId, action: action }),
					keepalive: true
				}).catch(function() {
					// Silently fail - stats are non-critical.
				});
			}
		},

		/**
		 * Handle toggling all promotions on/off.
		 *
		 * @param {Event} e Change event.
		 */
		handleToggleAll: function(e) {
			var $checkbox = $(this);
			var disableAll = $checkbox.is(':checked') ? '1' : '0';

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'seopress_toggle_promotions',
					disable_all: disableAll,
					_ajax_nonce: seopressPromotions.toggle_nonce
				},
				success: function(response) {
					if (response.success) {
						// Reload page to reflect changes.
						if (disableAll === '1') {
							$('.seopress-promo-banner, .seopress-promotions, .seopress-contextual-promo').slideUp(300);
						} else {
							location.reload();
						}
					}
				},
				error: function(xhr, status, error) {
					console.error('SEOPress Promotions: AJAX error.', error);
					// Revert checkbox state.
					$checkbox.prop('checked', !$checkbox.is(':checked'));
				}
			});
		},

		/**
		 * Initialize license renewal modal.
		 */
		initModal: function() {
			var $modal = $('#seopress-license-modal');

			if (!$modal.length) {
				return;
			}

			// Check if modal was already dismissed today.
			var dismissedAt = localStorage.getItem('seopress_license_modal_dismissed');

			if (dismissedAt) {
				var dismissedDate = new Date(parseInt(dismissedAt, 10));
				var now = new Date();
				var hoursDiff = (now - dismissedDate) / (1000 * 60 * 60);

				// Show again after 24 hours.
				if (hoursDiff < 24) {
					$modal.addClass('hidden');
					return;
				}
			}

			// Show modal with slight delay for better UX.
			setTimeout(function() {
				$modal.removeClass('hidden');
			}, 1000);
		},

		/**
		 * Handle modal dismiss button click.
		 *
		 * @param {Event} e Click event.
		 */
		handleModalDismiss: function(e) {
			e.preventDefault();

			var $modal = $('#seopress-license-modal');

			// Store dismissal timestamp.
			localStorage.setItem('seopress_license_modal_dismissed', Date.now().toString());

			// Animate out.
			$modal.css({
				'opacity': '0',
				'transition': 'opacity 0.3s ease'
			});

			setTimeout(function() {
				$modal.addClass('hidden').css('opacity', '1');
			}, 300);
		},

		/**
		 * Handle click on modal overlay.
		 *
		 * @param {Event} e Click event.
		 */
		handleOverlayClick: function(e) {
			// Only close if clicking directly on overlay, not modal content.
			if ($(e.target).hasClass('seopress-modal-overlay')) {
				$('[data-dismiss="license-modal"]').trigger('click');
			}
		},

	};

	// Initialize on document ready.
	$(document).ready(function() {
		SEOPressPromotions.init();
	});

})(jQuery);
