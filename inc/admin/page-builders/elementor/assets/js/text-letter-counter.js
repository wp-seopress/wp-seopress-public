jQuery(document).ready(function () {
    jQuery(document).on("click", "#seopress-seo-tab", function (e) {
        jQuery("#elementor-panel-footer-settings").trigger("click");
        jQuery(".elementor-control-seopress_title_settings").trigger("click");
    });
});


var tagClickInitialized = false;

var textLetterCounterView = elementor.modules.controls.BaseData.extend({
    fieldType: "text",
    currentEl: null,

    onReady: function () {
        if (
            seopressFiltersElementor.resize_panel &&
            seopressFiltersElementor.resize_panel === "1"
        ) {
            elementor.panel.storage.size.width = "495px";
            elementor.panel.setSize();
        }

        this.getCurrentElement.value = this.model.get("default");
        this.countLength(true);

        if (!tagClickInitialized) {
            jQuery(document).on("click", ".tag-title", this.addTag.bind(this));
            tagClickInitialized = true;
        }
    },

    events: function () {
        return {
            "change @ui.input": "onValueChange",
            "change @ui.textarea": "onValueChange",
            "input @ui.input": "onValueChange",
            "input @ui.textarea": "onValueChange",
            "paste @ui.input": "onValueChange",
            "paste @ui.textarea": "onValueChange",
        };
    },

    onValueChange: function (event) {
        this.saveValue();
        this.countLength();
        this.updateGooglePreview();
        this.onBaseInputChange(event);
    },

    saveValue: function () {
        let currentValue = this.getCurrentElementValue();

        this.setValue(currentValue);
    },

    updateGooglePreview: function () {
        const $googlePreview = jQuery(
            ".elementor-control-field.google-snippet-box"
        );

        if (!$googlePreview.length) {
            return;
        }

        let value = this.getCurrentElementValue();

        if (this.fieldType === "text") {
            $googlePreview.find(".snippet-title").text(value);
        } else {
            $googlePreview.find(".snippet-description-default").text(value);
        }
    },

    getCurrentElementValue: function () {
        let value = false;

        const el = this.getCurrentElement();

        if (el) {
            value = el.value;
        }

        if (value == "") {
            value = jQuery(el).attr("placeholder");
        }

        return value;
    },

    getCurrentElement: function () {
        let el = false;
        if (this.ui.textarea.length) {
            this.fieldType = "textarea";
            el = this.ui.textarea[0];
        } else {
            this.fieldType = "text";
            el = this.ui.input[0];
        }

        if (this.currentEl) {
            el = this.currentEl;
        }

        return el;
    },

    countLength: function (initial = false, currentEl = false) {
        let $currentElement;

        if (!currentEl) {
            $currentElement = jQuery(this.getCurrentElement());
        } else {
            $currentElement = currentEl;
        }

        let currentValue = $currentElement.val();

        if (currentValue == "") {
            currentValue = $currentElement.attr("placeholder");
        }

        if (typeof currentValue == "undefined") {
            return;
        }

        const $elementParent = $currentElement.parent();

        if (initial) {
            let maxLength;

            if (this.fieldType === "text") {
                maxLength = 60;
            } else {
                maxLength = 160;
            }

            $elementParent
                .find(".seopress_counters")
                .after(
                    `<div class="seopress_counters_val">/ ${maxLength}</div>`
                );
        }

        if (currentValue.length > 0) {
            $elementParent.find(".seopress_counters").text(currentValue.length);
            const pixels =
                this.fieldType === "text"
                    ? this.pixelTitle(currentValue)
                    : this.pixelDesc(currentValue);
            $elementParent.find(".seopress_pixel").text(pixels);
        }

        if (
            (this.fieldType === "text" && currentValue.length > 60) ||
            (this.fieldType === "textarea" && currentValue.length > 160)
        ) {
            $elementParent.find(".seopress_counters").css("color", "red");
        } else {
            $elementParent.find(".seopress_counters").css("color", "#6d7882");
        }

        if (
            (this.fieldType === "text" &&
                this.pixelTitle(currentValue) > 568) ||
            (this.fieldType === "textarea" &&
                this.pixelDesc(currentValue) > 940)
        ) {
            $elementParent.find(".seopress_pixel").css("color", "red");
        } else {
            $elementParent.find(".seopress_pixel").css("color", "#6d7882");
        }

        let progress;
        if (this.fieldType === "text") {
            progress = Math.round((this.pixelTitle(currentValue) / 568) * 100);
        } else {
            progress = Math.round((this.pixelDesc(currentValue) / 940) * 100);
        }

        if (progress >= 100) {
            progress = 100;
        }

        $elementParent
            .find(".seopress_counters_progress")
            .attr("aria-valuenow", progress);
        $elementParent.find(".seopress_counters_progress").text(progress + "%");
        $elementParent
            .find(".seopress_counters_progress")
            .css("width", progress + "%");
    },

    pixelTitle: function (e) {
        inputText = e;
        font = "18px Arial";

        canvas = document.createElement("canvas");
        context = canvas.getContext("2d");
        context.font = font;
        width = context.measureText(inputText).width;

        formattedWidth = Math.ceil(width);

        return formattedWidth;
    },

    pixelDesc: function (e) {
        inputText = e;
        font = "14px Arial";

        canvas = document.createElement("canvas");
        context = canvas.getContext("2d");
        context.font = font;
        width = context.measureText(inputText).width;
        formattedWidth = Math.ceil(width);

        return formattedWidth;
    },

    addTag: function (e) {
        e.stopPropagation();

        let $currentBtn = jQuery(e.target);
        const $mainParent = $currentBtn
            .parents(".seopress-text-letter-counter")
            .first();

        /* Happens the inner span to be click sometimes and if so, find the tag-title span */
        if (!$currentBtn.hasClass("tag-title")) {
            $currentBtn = $currentBtn.parents(".tag-title").first();
        }

        if ($mainParent.find("input[type=text]").length) {
            $el = $mainParent.find("input[type=text]").first();
        } else {
            $el = $mainParent.find("textarea").first();
        }

        const newValue = $el.val() + " " + $currentBtn.data("tag");

        $el.val(newValue);
        $el.trigger("change");
    },
});

elementor.addControlView("seopresstextlettercounter", textLetterCounterView);
