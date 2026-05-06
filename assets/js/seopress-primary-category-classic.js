'use strict';
/**
 * Inject the SEOPress primary category dropdown into the WordPress
 * Categories (or Product Categories) metabox in the Classic Editor.
 *
 * Self-contained: does not require any source <select> on the page.
 * The localized payload provides the rendered <select> markup (with
 * name="seopress_robots_primary_cat" so it ships with the post form)
 * and the dedicated nonce that the server-side save handler verifies.
 */
document.addEventListener('DOMContentLoaded', function () {
    var data = window.seopressPrimaryCategorySelectData;
    if (!data || !data.selectHTML) {
        return;
    }

    var categoriesMetabox = document.querySelector('#product_catdiv') || document.querySelector('#categorydiv');
    if (!categoriesMetabox) {
        return;
    }

    var inside = categoriesMetabox.querySelector('.inside');
    if (!inside) {
        return;
    }

    if (inside.querySelector('#seopress_robots_primary_cat')) {
        return;
    }

    var wrapper = document.createElement('div');
    wrapper.className = 'seopress-primary-category-wrapper';
    wrapper.innerHTML = data.selectHTML + (data.nonceField || '');
    inside.appendChild(wrapper);

    var select = wrapper.querySelector('#seopress_robots_primary_cat');
    if (select && data.primaryCategory) {
        select.value = data.primaryCategory;
    }
});
