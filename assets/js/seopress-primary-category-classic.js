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
    if (!select) {
        return;
    }
    if (data.primaryCategory) {
        select.value = data.primaryCategory;
    }

    var noneOption = select.querySelector('option[value="none"]').cloneNode(true);
    var checkboxSelector = 'input[type="checkbox"][name="post_category[]"], input[type="checkbox"][name="tax_input[product_cat][]"]';

    function syncCategories(changedCheckbox) {
        var previous = select.value;
        var terms = new Map();
        inside.querySelectorAll(checkboxSelector).forEach(function (checkbox) {
            var label = checkbox.closest('label');
            if (checkbox.checked && label) {
                terms.set(checkbox.value, label.textContent.trim());
            }
        });
        // WordPress synchronizes the All / Most Used copies in its own handler.
        // Honour the changed checkbox even before that synchronization runs.
        if (changedCheckbox) {
            if (changedCheckbox.checked) {
                var label = changedCheckbox.closest('label');
                if (label) {
                    terms.set(changedCheckbox.value, label.textContent.trim());
                }
            } else {
                terms.delete(changedCheckbox.value);
            }
        }
        select.replaceChildren(noneOption.cloneNode(true));
        terms.forEach(function (name, id) {
            var option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            select.appendChild(option);
        });
        select.value = terms.has(previous) ? previous : 'none';
    }

    inside.addEventListener('change', function (event) {
        if (event.target.matches(checkboxSelector)) {
            syncCategories(event.target);
        }
    });
    // Newly added categories arrive via AJAX without a change event.
    new MutationObserver(function (records) {
        if (records.some(function (record) { return !wrapper.contains(record.target); })) {
            syncCategories();
        }
    }).observe(inside, { childList: true, subtree: true });
    syncCategories();
});
