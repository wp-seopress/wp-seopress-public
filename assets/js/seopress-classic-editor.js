(function ($, wpLinkL10n, wp) {
    let editor = null;
    const inputs = {};
    const getLink = () => editor ? editor.$('a[data-wplink-edit="true"]') : null;
    $(document).on('wplink-open', function (event, wrap) {
        if (!wpLink.isMCE()) return;

        if (!inputs.sponsored) {
            $('#link-options').append(
                `<div class="link-sponsored">
                <label><span></span>
                <input type="checkbox" id="wp-link-sponsored" />${seopressI18n.sponsored}</label>
            </div>`
            );
        }
        if (!inputs.nofollow) {
            $('#link-options').append(
                `<div class="link-no-follow">
                    <label><span></span>
                    <input type="checkbox" id="wp-link-no-follow" />${seopressI18n.nofollow}</label>
                </div>`
            );
        }
        if (!inputs.ugc) {
            $('#link-options').append(
                `<div class="link-ugc">
                    <label><span></span>
                    <input type="checkbox" id="wp-link-ugc" />${seopressI18n.ugc}</label>
                </div>`
            );
        }

        inputs.sponsored = $('#wp-link-sponsored');
        inputs.nofollow = $('#wp-link-no-follow');
        inputs.ugc = $('#wp-link-ugc');
        inputs.openInNewTab = $('#wp-link-target');
        inputs.url = $('#wp-link-url');

        if (typeof window.tinymce !== 'undefined') {
            const ed = window.tinymce.get(window.wpActiveEditor);
            editor = ed && !ed.isHidden() ? ed : null;
            const linkNode = getLink();
            if (linkNode) {
                inputs.sponsored.prop('checked', undefined !== linkNode.attr('rel') && linkNode.attr('rel')?.includes('sponsored'));
                inputs.nofollow.prop('checked', undefined !== linkNode.attr('rel') && linkNode.attr('rel')?.includes('nofollow'));
                inputs.ugc.prop('checked', undefined !== linkNode.attr('rel') && linkNode.attr('rel')?.includes('ugc'));
            }
        }

        window.wpLink.getAttrs = function () {
            wpLink.correctURL();

            const attrs = {
                href: inputs.url.val().trim(),
                target: inputs.openInNewTab.prop('checked') ? '_blank' : null,
            }

            let rel = '';
            rel += inputs.sponsored.prop('checked') ? 'sponsored ' : ''
            rel += inputs.nofollow.prop('checked') ? 'nofollow ' : ''
            rel += inputs.ugc.prop('checked') ? 'ugc ' : ''
            attrs.rel = rel ? rel : null;

            return attrs;
        };

        window.wpLink.buildHtml = function (attrs) {
            var html = '<a href="' + attrs.href + '"';

            let rel = '';
            if (attrs.target) {
                html += ' target="' + attrs.target + '"';
                rel += 'noopener ';
            }

            if (attrs.rel) rel += attrs.rel;
            if (rel) html += ' rel="' + rel.trim() + '"';

            return html + '>';
        };
    });
})(jQuery, window.wpLinkL10n, window.wp);
