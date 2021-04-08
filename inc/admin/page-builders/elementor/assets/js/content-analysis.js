contentAnalysisToggle();

var contentAnalysisView = elementor.modules.controls.BaseData.extend({
    onReady: function () {
        if (
            seopressFiltersElementor.resize_panel &&
            seopressFiltersElementor.resize_panel === "1"
        ) {
            elementor.panel.storage.size.width = "495px";
            elementor.panel.setSize();
        }

        contentAnalysis();
        jQuery(document).on("click", "#seopress_launch_analysis", function () {
            contentAnalysis();
        });
    },
});

elementor.addControlView("seopress-content-analysis", contentAnalysisView);
