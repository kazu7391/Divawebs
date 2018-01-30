var fonts = [];

$(document).ready(function () {
    $.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCKhdPP0mANQLtEY8Br0H71OqpQHxfu8wo', function (response) {
        for (i = 0; i < response.items.length; i++) {
            font_family_val = response.items[i]['family'].replace(' ', '+');
            fonts[i] = {
                'id'     : i,
                'family' : response.items[i]['family'],
                'family_val' : font_family_val,
                'variants' : response.items[i]['variants'],
                'subsets': response.items[i]['subsets'],
                'category' : response.items[i]['category']
            };
        }

        var options_html = '';
        for (i = 0; i < fonts.length; i++) {
            options_html += "<option value='" + fonts[i]['id'] + "'>" + fonts[i]['family'] +"</option>";
        }

        $('.font-box').append(options_html);
    });
});

var gfont = {
    'chooseBodyFont' : function (font_id) {
        var variants = fonts[font_id]['variants'];
        var subsets = fonts[font_id]['subsets'];
        var variants_attr = '';
        var subsets_attr = '';

        $('#body-font-family-id').val(font_id);
        $('#body-font-family-name').val(fonts[font_id]['family']);
        $('#body-font-family-cate').val(fonts[font_id]['category']);

        var family_val = fonts[font_id]['family_val'];

        if (variants.length) {
            variants_attr = variants.join(',');
        }

        if (subsets.length) {
            subsets_attr = subsets.join(',');
        }

        var font_css_link = 'https://fonts.googleapis.com/css?family=' + family_val + ":" + variants_attr + '&subset=' + subsets_attr;

        $('#body-font-family-link').val(font_css_link);
    },

    'chooseHeadingFont' : function (font_id) {
        var variants = fonts[font_id]['variants'];
        var subsets = fonts[font_id]['subsets'];
        var variants_attr = '';
        var subsets_attr = '';

        $('#heading-font-family-id').val(font_id);
        $('#heading-font-family-name').val(fonts[font_id]['family']);
        $('#heading-font-family-cate').val(fonts[font_id]['category']);

        var family_val = fonts[font_id]['family_val'];

        if (variants.length) {
            variants_attr = variants.join(',');
        }

        if (subsets.length) {
            subsets_attr = subsets.join(',');
        }

        var font_css_link = 'https://fonts.googleapis.com/css?family=' + family_val + ":" + variants_attr + '&subset=' + subsets_attr;

        $('#heading-font-family-link').val(font_css_link);
    }
}