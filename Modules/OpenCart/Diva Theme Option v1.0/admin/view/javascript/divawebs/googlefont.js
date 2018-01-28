var fonts = [];

$(document).ready(function () {
    $.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCKhdPP0mANQLtEY8Br0H71OqpQHxfu8wo', function (response) {
        for (i = 0; i < response.items.length; i++) {
            font_family_val = response.items[i]['family'].replace(' ', '+');
            fonts[i] = {
                'id'     : i,
                'family' : response.items[i]['family'],
                'variants' : response.items[i]['variants'],
                'subsets': response.items[i]['subsets']
            };
        }

        var options_html = '';
        for (i = 0; i < fonts.length; i++) {
            options_html += "<option value='" + fonts[i]['id'] + "'>" + fonts[i]['family'] +"</option>";
        }

        $('.font-box').append(options_html);
   })
});

var gfont = {
    'chooseFont' : function (font_id, selection) {
        var variants_html = '';
        var subsets_html = '';
        var text_font_weight = $('#text-font-weight').val();
        var text_font_subset = $('#text-font-subset').val();

        var variants = fonts[font_id]['variants'];
        var subsets = fonts[font_id]['subsets'];

        if (variants.length) {
            var length = variants.length;
            var divide_count = 7;

            variants_html += "<label class='control-label'>" + text_font_weight + "</label>";
            variants_html += "<div class='lbl-checkbox row'>";
            if (length <= divide_count) {
                variants_html += "<div class='col-sm-6'>";
                for (i = 0; i < variants.length; i++) {
                    variants_html += "<label class='cbk-container'>" + variants[i];
                    variants_html += "<input type='checkbox' value='" + variants[i] + "'>";
                    variants_html += "<span class='checkmark'></span>";
                    variants_html += "</label>";
                }
                variants_html += "</div>";
            }

            if (length > divide_count) {
                variants_html += "<div class='col-sm-6'>";
                for (i = 0; i < divide_count; i++) {
                    variants_html += "<label class='cbk-container'>" + variants[i];
                    variants_html += "<input type='checkbox' value='" + variants[i] + "'>";
                    variants_html += "<span class='checkmark'></span>";
                    variants_html += "</label>";
                }
                variants_html += "</div>";
                variants_html += "<div class='col-sm-6'>";
                for (i = 7; i < length; i++) {
                    variants_html += "<label class='cbk-container'>" + variants[i];
                    variants_html += "<input type='checkbox' value='" + variants[i] + "'>";
                    variants_html += "<span class='checkmark'></span>";
                    variants_html += "</label>";
                }
                variants_html += "</div>";
            }

            variants_html += "</div>";
        }

        selection.closest('.font-control').find('.font-variant-ckb').html(variants_html);

        if (subsets.length) {
            var length = subsets.length;
            var divide_count = 7;

            subsets_html += "<label class='control-label'>" + text_font_subset + "</label>";
            subsets_html += "<div class='lbl-checkbox row'>";
            if (length <= divide_count) {
                subsets_html += "<div class='col-sm-6'>";
                for (i = 0; i < subsets.length; i++) {
                    subsets_html += "<label class='cbk-container'>" + subsets[i];
                    subsets_html += "<input type='checkbox' value='" + subsets[i] + "'>";
                    subsets_html += "<span class='checkmark'></span>";
                    subsets_html += "</label>";
                }
                subsets_html += "</div>";
            }

            if (length > divide_count) {
                subsets_html += "<div class='col-sm-6'>";
                for (i = 0; i < divide_count; i++) {
                    subsets_html += "<label class='cbk-container'>" + subsets[i];
                    subsets_html += "<input type='checkbox' value='" + subsets[i] + "'>";
                    subsets_html += "<span class='checkmark'></span>";
                    subsets_html += "</label>";
                }
                subsets_html += "</div>";
                subsets_html += "<div class='col-sm-6'>";
                for (i = 7; i < length; i++) {
                    subsets_html += "<label class='cbk-container'>" + subsets[i];
                    subsets_html += "<input type='checkbox' value='" + subsets[i] + "'>";
                    subsets_html += "<span class='checkmark'></span>";
                    subsets_html += "</label>";
                }
                subsets_html += "</div>";
            }
            subsets_html += "</div>";
        }

        selection.closest('.font-control').find('.font-subset-ckb').html(subsets_html);
    }
}