'use strict';
/**
 * Inject the SEOPress primary term dropdown into every hierarchical
 * taxonomy metabox of the Classic Editor (Categories, Product categories
 * and any custom taxonomy).
 *
 * Self-contained: does not require any source <select> on the page. The
 * localized payload lists one entry per taxonomy, each providing the
 * rendered <select> markup (whose name ships with the post form), the id
 * of the WordPress metabox to inject it into and the name of the term
 * checkboxes to mirror. The nonce is shared by all of them and the
 * server-side save handler verifies it once.
 */
document.addEventListener('DOMContentLoaded', function () {
    var data = window.seopressPrimaryCategorySelectData;
    if (!data) {
        return;
    }

    var taxonomies = data.taxonomies;
    if (!Array.isArray(taxonomies) || !taxonomies.length) {
        return;
    }

    var nonceInjected = false;

    taxonomies.forEach(function (taxonomy) {
        if (!taxonomy || !taxonomy.selectHTML || !taxonomy.metaboxId || !taxonomy.fieldId) {
            return;
        }

        var metabox = document.getElementById(taxonomy.metaboxId);
        if (!metabox) {
            return;
        }

        var inside = metabox.querySelector('.inside');
        if (!inside) {
            return;
        }

        if (inside.querySelector('#' + taxonomy.fieldId)) {
            return;
        }

        var wrapper = document.createElement('div');
        wrapper.className = 'seopress-primary-category-wrapper';
        wrapper.innerHTML = taxonomy.selectHTML + (nonceInjected ? '' : data.nonceField || '');
        inside.appendChild(wrapper);
        nonceInjected = true;

        var select = wrapper.querySelector('#' + taxonomy.fieldId);
        if (!select) {
            return;
        }
        if (taxonomy.value) {
            select.value = taxonomy.value;
        }

        var noneOption = select.querySelector('option[value="none"]').cloneNode(true);
        // Assigned terms the `seopress_primary_term_list` filter left out.
        // Rebuilding the list from the checkboxes used to bring them back.
        var excluded = Array.isArray(taxonomy.excluded) ? taxonomy.excluded.map(String) : [];
        var checkboxSelector = 'input[type="checkbox"][name="' + taxonomy.checkboxName + '"]';

        function syncTerms(changedCheckbox) {
            var previous = select.value;
            var terms = new Map();
            inside.querySelectorAll(checkboxSelector).forEach(function (checkbox) {
                var label = checkbox.closest('label');
                if (checkbox.checked && label) {
                    terms.set(checkbox.value, label.textContent.trim());
                }
            });
            // WordPress synchronizes the All / Most Used copies in its own
            // handler. Honour the changed checkbox even before that runs.
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
            excluded.forEach(function (id) {
                terms.delete(id);
            });
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
                syncTerms(event.target);
            }
        });
        // Newly added terms arrive via AJAX without a change event.
        new MutationObserver(function (records) {
            if (records.some(function (record) { return !wrapper.contains(record.target); })) {
                syncTerms();
            }
        }).observe(inside, { childList: true, subtree: true });
        syncTerms();
    });
});
